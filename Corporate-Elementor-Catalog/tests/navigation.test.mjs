import { test } from 'node:test';
import assert from 'node:assert/strict';
import { initializeMenus, fitSubmenu } from '../radmanct/src/js/navigation/menu.js';

class Element {
  constructor(tag = 'div', classes = []) {
    this.tagName = tag.toUpperCase(); this.classes = classes; this.children = []; this.attributes = new Map(); this.listeners = new Map(); this.dataset = {}; this.textContent = ''; this.hidden = false;
    this.classList = { contains: (value) => this.classes.includes(value), add: (value) => { if (!this.classes.includes(value)) this.classes.push(value); }, remove: (value) => { this.classes = this.classes.filter((entry) => entry !== value); } };
    this.style = { values: new Map(), setProperty(key, value) { this.values.set(key, value); }, removeProperty(key) { this.values.delete(key); } };
  }
  get parentElement() { return this.parent; }
  append(child) { child.parent = this; this.children.push(child); return child; }
  contains(child) { return child === this || this.children.some((entry) => entry.contains(child)); }
  setAttribute(key, value) { this.attributes.set(key, value); }
  getAttribute(key) { return this.attributes.get(key); }
  hasAttribute(key) { return this.attributes.has(key); }
  removeAttribute(key) { this.attributes.delete(key); }
  remove() { if (this.parent) this.parent.children.splice(this.parent.children.indexOf(this), 1); }
  removeEventListener(key, callback) { this.listeners.set(key, (this.listeners.get(key) || []).filter((entry) => entry !== callback)); }
  addEventListener(key, callback) { this.listeners.set(key, [...(this.listeners.get(key) || []), callback]); }
  fire(key, fields = {}) { const event = { target: this, preventDefault() { this.defaultPrevented = true; }, ...fields }; event.pending = Promise.all((this.listeners.get(key) || []).map((callback) => callback(event))); return event; }
  focus() { document.activeElement = this; }
  getClientRects() { return this.hidden || this.visible === false || (this.parent && !this.parent.getClientRects().length) ? [] : [{}]; }
  closest(selector) { if (selector === '[data-rdct-menu]') return this.hasAttribute('data-rdct-menu') ? this : this.parent?.closest(selector); return selector?.startsWith('.') ? (this.classes.includes(selector.slice(1)) ? this : this.parent?.closest(selector)) : (this.tagName === 'A' && this.hasAttribute('href') ? this : this.parent?.closest(selector)); }
  insertBefore(child, reference) { child.parent = this; this.children.splice(this.children.indexOf(reference), 0, child); }
  querySelector(selector) { return this.querySelectorAll(selector)[0]; }
  querySelectorAll(selector) {
    const direct = selector.startsWith(':scope > '); selector = selector.replace(/^:scope > /,'');
    const descendants = direct ? this.children : this.children.flatMap((child) => [child, ...child.querySelectorAll('*')]);
    return selector === '*' ? descendants : descendants.filter((child) => selector.startsWith('[data-') ? child.hasAttribute(selector.slice(1,-1)) : selector === 'a[href]' ? child.tagName === 'A' && child.hasAttribute('href') : child.classes.includes(selector.slice(1)));
  }
}

function fixture(expanded = false, nested = false, mode = {}) {
  const root = new Element(); root.setAttribute('data-rdct-menu',''); root.dataset.submenuLabel = 'Toggle submenu';
  if (mode.devices) { root.dataset.drawerDevices = mode.devices; root.setAttribute('data-rdct-drawer', ''); }
  if (mode.mega) root.dataset.submenuStyle = 'mega';
  if (mode.hover) root.dataset.submenuTrigger = 'hover';
  if (expanded) root.setAttribute('data-mobile-expanded', '');
  const button = root.append(new Element('button', ['rdct-menu-toggle'])); button.setAttribute('aria-expanded', 'false');
  const nav = root.append(new Element('nav', ['rdct-nav'])); nav.id = 'test-menu';
  const items = Array.from({ length: 2 }, (_, index) => {
    const item = nav.append(new Element('li', ['menu-item-has-children']));
    const link = item.append(new Element('a')); link.textContent = `Parent ${index}`; link.setAttribute('href', `/parent-${index}`);
    const submenu = item.append(new Element('ul', ['sub-menu']));
    const child = submenu.append(new Element('a')); child.setAttribute('href', '#section');
    return { item, link, submenu, child };
  });
  if (mode.mega) items[0].item.classes.push('rdct-submenu--mega');
  let branch;
  if (nested) {
    const item = items[mode.normalBranch ? 1 : 0].submenu.append(new Element('li', ['menu-item-has-children']));
    const link = item.append(new Element('a')); link.textContent = 'Nested parent'; link.setAttribute('href', '/nested');
    const submenu = item.append(new Element('ul', ['sub-menu']));
    const child = submenu.append(new Element('a')); child.setAttribute('href', '/deep');
    branch = { item, link, submenu, child };
  }
  const doc = new Element(); doc.created = []; doc.querySelectorAll = () => [root]; doc.createElement = (tag) => { doc.created.push(tag); return new Element(tag); }; doc.activeElement = null; doc.documentElement = { clientWidth: 1024, style: {} }; doc.body = {style:{overflow:'auto'}};
  const media = new Element(); media.matches = false;
  const laptop = new Element(); const desktop = new Element();
  const main = new Element('main');
  doc.getElementById = (id) => id === 'main-content' ? main : null;
  globalThis.document = doc; globalThis.matchMedia = (query) => query.includes('64rem') ? laptop : query.includes('90rem') ? desktop : media;
  globalThis.window = new Element();
  globalThis.getComputedStyle = (node) => ({ position: node.position || 'static', direction: node.direction || 'ltr' });
  const cleanup = initializeMenus();
  return { root, button, nav, items, doc, media, laptop, desktop, main, branch, cleanup };
}

test('mega menu exposes its grouped links without creating nested disclosure controls', () => {
  const { items, branch } = fixture(false, true, { mega: true });
  items[0].item.children[1].fire('click');
  assert.equal(items[0].submenu.hidden, false);
  assert.equal(branch.submenu.hidden, false);
  assert.equal(branch.item.children.length, 2);
});

test('an ordinary branch beside a mega branch retains its nested disclosures', () => {
  const { items, branch } = fixture(false, true, { mega: true, normalBranch: true });
  items[1].item.children[1].fire('click');
  assert.equal(branch.item.children.length, 3);
  branch.item.children[1].fire('click');
  assert.equal(items[1].submenu.hidden, false);
  assert.equal(branch.submenu.hidden, false);
});

test('desktop drawer loads only for its selected device and breakpoint changes close its opening state', async () => {
  const { root, button, doc, media, laptop, desktop } = fixture(false, false, { devices: 'desktop' });
  await button.fire('click').pending;
  assert.equal(doc.created.includes('dialog'), false);
  button.fire('click');
  media.matches = true; laptop.matches = true; desktop.matches = true;
  desktop.fire('change');
  await button.fire('click').pending;
  assert.equal(doc.created.includes('dialog'), true);
  assert.equal(button.getAttribute('aria-expanded'), 'true');
  desktop.matches = false; desktop.fire('change');
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  assert.equal(root.dataset.enhanced, 'true');
});

test('nested submenu keeps its ancestors open and Escape closes the deepest branch first', () => {
  const { root, items, branch, doc } = fixture(false, true);
  const parentToggle = items[0].item.children[1];
  const nestedToggle = branch.item.children[1];
  parentToggle.fire('click'); nestedToggle.fire('click');
  assert.equal(items[0].submenu.hidden, false);
  assert.equal(branch.submenu.hidden, false);
  root.fire('keydown', { key: 'Escape', target: branch.child });
  assert.equal(branch.submenu.hidden, true);
  assert.equal(items[0].submenu.hidden, false);
  assert.equal(doc.activeElement, nestedToggle);
  nestedToggle.fire('click'); parentToggle.fire('click');
  assert.equal(branch.submenu.hidden, true);
  assert.equal(nestedToggle.getAttribute('aria-expanded'), 'false');
  parentToggle.fire('click'); nestedToggle.fire('click');
  items[1].item.children[1].fire('click');
  assert.equal(items[0].submenu.hidden, true);
  assert.equal(branch.submenu.hidden, true);
  assert.equal(items[1].submenu.hidden, false);
});

test('desktop dropdowns fit either viewport edge while static mobile lists keep their natural layout', () => {
  fixture();
  const submenu = new Element('ul'); submenu.position = 'absolute';
  submenu.getBoundingClientRect = () => ({ left: 900, right: 1100 });
  fitSubmenu(submenu); assert.equal(submenu.style.values.get('--rdct-menu-shift'), '-84px');
  submenu.getBoundingClientRect = () => ({ left: -60, right: 140 });
  fitSubmenu(submenu); assert.equal(submenu.style.values.get('--rdct-menu-shift'), '68px');
  submenu.direction = 'rtl';
  fitSubmenu(submenu); assert.equal(submenu.style.values.get('--rdct-menu-shift'), '-68px');
  submenu.getBoundingClientRect = () => ({ left: 300, right: 500 });
  fitSubmenu(submenu); assert.equal(submenu.style.values.has('--rdct-menu-shift'), false);
  submenu.style.setProperty('--rdct-menu-shift', '-84px'); submenu.position = 'static';
  fitSubmenu(submenu); assert.equal(submenu.style.values.has('--rdct-menu-shift'), false);
});

test('tall dropdowns stay inside the viewport and release their local scrolling on a larger screen', () => {
  fixture();
  document.documentElement.clientHeight = 600;
  const submenu = new Element('ul'); submenu.position = 'absolute';
  submenu.getBoundingClientRect = () => ({ left: 10, right: 210, top: 550, bottom: 1450, height: 900 });
  fitSubmenu(submenu);
  assert.equal(submenu.style.values.get('--rdct-menu-block-shift'), '-542px');
  assert.equal(submenu.style.values.get('--rdct-menu-height'), '584px');
  assert.equal(submenu.classList.contains('rdct-submenu--scroll'), true);
  document.documentElement.clientHeight = 1600;
  fitSubmenu(submenu);
  assert.equal(submenu.style.values.has('--rdct-menu-block-shift'), false);
  assert.equal(submenu.style.values.has('--rdct-menu-height'), false);
  assert.equal(submenu.classList.contains('rdct-submenu--scroll'), false);
  submenu.position = 'static';
  document.documentElement.clientHeight = 600;
  fitSubmenu(submenu);
  assert.equal(submenu.classList.contains('rdct-submenu--scroll'), false);
});

test('leaving a page closes dropdown state before bfcache restoration', () => {
  const { button, items } = fixture();
  button.fire('click'); items[0].item.children[1].fire('click');
  window.fire('pagehide');
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  assert.equal(items[0].submenu.hidden, true);
});

test('laptop and desktop hiding close menus and move focus out of an invisible header row', () => {
  const { root, button, items, doc, media, laptop, desktop, main } = fixture();
  media.matches = true;
  const toggle = items[0].item.children[1];
  toggle.fire('click'); items[0].child.focus();
  root.visible = false;
  laptop.fire('change');
  assert.equal(items[0].submenu.hidden, true);
  assert.equal(doc.activeElement, main);
  root.visible = true;
  toggle.fire('click'); items[0].child.focus();
  desktop.fire('change');
  assert.equal(doc.activeElement, toggle);
  assert.equal(button.getAttribute('aria-expanded'), 'false');
});

test('expanded mobile footer keeps links accessible without focusing a hidden menu trigger', () => {
  const { button, nav, items, doc, media } = fixture(true);
  assert.equal(button.hidden, true);
  const link = items[0].link;
  link.focus();
  nav.fire('click', { target: link, button: 0 });
  assert.equal(doc.activeElement, link);
  media.fire('change');
  assert.equal(doc.activeElement, link);
  const toggle = items[0].item.children[1];
  toggle.fire('click');
  assert.equal(items[0].submenu.hidden, false);
  items[0].child.focus();
  media.fire('change');
  assert.equal(items[0].submenu.hidden, true);
  assert.equal(doc.activeElement, toggle);
});

test('drawer loading falls back to dropdown when native dialogs are unavailable', async () => {
  const { root, button } = fixture();
  root.setAttribute('data-rdct-drawer', '');
  await button.fire('click').pending;
  assert.equal(button.getAttribute('aria-expanded'), 'true');
});

test('outside dismissal cancels a menu opening while its drawer module loads', async () => {
  const { root, button, doc } = fixture();
  root.setAttribute('data-rdct-drawer', '');
  const event = button.fire('click');
  assert.equal(doc.body.style.overflow, 'hidden');
  doc.fire('click', { target: new Element() });
  await event.pending;
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  assert.equal(doc.body.style.overflow, 'auto');
});

test('drawer pointer press locks before native focus and cancellation restores scrolling', () => {
  const { root, button, doc } = fixture();
  root.setAttribute('data-rdct-drawer', '');
  const press = button.fire('pointerdown', {button:0});
  assert.equal(press.defaultPrevented, true);
  assert.equal(doc.body.style.overflow, 'hidden');
  button.fire('pointercancel');
  assert.equal(doc.body.style.overflow, 'auto');
  button.fire('pointerdown', {button:0});
  doc.fire('pointerup', {target:new Element()});
  assert.equal(doc.body.style.overflow, 'auto');
});

test('a second click cancels a pending drawer opening and clears its busy state', async () => {
  const { root, button } = fixture();
  root.setAttribute('data-rdct-drawer', '');
  const first = button.fire('click');
  assert.equal(button.getAttribute('aria-busy'), 'true');
  await button.fire('click').pending;
  await first.pending;
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  assert.equal(button.hasAttribute('aria-busy'), false);
  await button.fire('click').pending;
  assert.equal(button.getAttribute('aria-expanded'), 'true');
});

test('menu teardown removes listeners and generated controls before a clean reinitialization', async () => {
  const { root, items, button, doc, media, cleanup } = fixture();
  root.setAttribute('data-rdct-drawer', '');
  const pending = button.fire('click');
  cleanup(); cleanup(); await pending.pending;
  assert.equal(root.dataset.enhanced, undefined);
  assert.equal(items[0].item.children.length, 2);
  assert.equal(items[0].submenu.hidden, false);
  assert.equal(doc.listeners.get('click').length, 0);
  assert.equal(media.listeners.get('change').length, 0);
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  assert.equal(button.hasAttribute('aria-busy'), false);
  const remove = initializeMenus();
  assert.equal(items[0].item.children.length, 3);
  assert.equal(doc.listeners.get('click').length, 1);
  await button.fire('click').pending;
  assert.equal(button.getAttribute('aria-expanded'), 'true');
  remove();
});

test('submenu disclosure preserves parent links and independent keyboard focus', () => {
  const { root, items, doc } = fixture();
  const [first, second] = items;
  const toggle = first.item.children[1];
  assert.equal(first.link.getAttribute('href'), '/parent-0');
  assert.equal(toggle.getAttribute('aria-controls'), first.submenu.id);
  assert.notEqual(first.submenu.id, second.submenu.id);
  toggle.fire('click');
  assert.equal(first.submenu.hidden, false);
  second.item.children[1].fire('click');
  assert.equal(first.submenu.hidden, true);
  const event = root.fire('keydown', { key: 'Escape', target: second.child });
  assert.equal(event.defaultPrevented, true);
  assert.equal(second.submenu.hidden, true);
  assert.equal(doc.activeElement, second.item.children[1]);
});

test('mobile anchor selection releases focus; modified clicks retain the open menu', () => {
  const { button, nav, items, doc } = fixture();
  const link = items[0].child;
  button.fire('click'); link.focus();
  nav.fire('click', { target: link, button: 0, ctrlKey: true });
  assert.equal(button.getAttribute('aria-expanded'), 'true');
  nav.fire('click', { target: link, button: 0 });
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  assert.equal(doc.activeElement, button);
});

test('outside click, focus departure and breakpoint change reset menu state', () => {
  const { root, button, nav, items, doc, media } = fixture();
  button.fire('click'); items[0].item.children[1].fire('click');
  doc.fire('click', { target: new Element() });
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  assert.equal(items[0].submenu.hidden, true);
  button.fire('click'); nav.focus(); media.fire('change');
  assert.equal(doc.activeElement, button);
  button.fire('click'); root.fire('focusout', { relatedTarget: new Element() });
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  initializeMenus();
  assert.equal(doc.listeners.get('click').length, 1);
});


test('optional desktop hover retains click access and ignores touch pointer entry',() => {
  const {items,media,cleanup} = fixture(false,false,{hover:true});
  media.matches = true;
  items[0].item.fire('pointerenter',{pointerType:'touch'});
  assert.equal(items[0].submenu.hidden,true);
  items[0].item.fire('pointerenter',{pointerType:'mouse'});
  assert.equal(items[0].submenu.hidden,false);
  items[0].item.children[1].fire('click');
  assert.equal(items[0].submenu.hidden,true);
  media.matches = false;
  items[0].item.fire('pointerenter',{pointerType:'mouse'});
  assert.equal(items[0].submenu.hidden,true);
  cleanup();
});


test('ordinary submenu keyboard entry and inert state preserve links and restore on cleanup',()=>{
  const {items,cleanup}=fixture(); const {item,link,submenu,child}=items[0];
  assert.equal(submenu.inert,true);
  const event=link.fire('keydown',{key:'ArrowDown'});
  assert.equal(event.defaultPrevented,true);assert.equal(submenu.hidden,false);assert.equal(submenu.inert,false);assert.equal(document.activeElement,child);
  item.children[1].fire('click');assert.equal(submenu.inert,true);
  link.fire('keydown',{key:'ArrowUp'});assert.equal(document.activeElement,child);
  cleanup();assert.equal(submenu.inert,undefined);assert.equal(submenu.hidden,false);assert.equal(link.getAttribute('href'),'/parent-0');
});


test('opening scaled dropdowns fit their layout height before their visual scale settles',()=>{
  fixture(); document.documentElement.clientHeight=600;
  const submenu=new Element('ul');submenu.position='absolute';submenu.offsetHeight=900;
  submenu.getBoundingClientRect=()=>({left:10,right:210,top:550,bottom:550,height:0});
  fitSubmenu(submenu);
  assert.equal(submenu.style.values.get('--rdct-menu-block-shift'),'-542px');
  assert.equal(submenu.style.values.get('--rdct-menu-height'),'584px');
  assert.equal(submenu.classList.contains('rdct-submenu--scroll'),true);
});
