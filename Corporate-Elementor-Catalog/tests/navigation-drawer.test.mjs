import test from 'node:test';
import assert from 'node:assert/strict';
import { createDrawer } from '../radmanct/src/js/navigation/drawer.js';

class Element {
  constructor(tag) { this.tagName = tag; this.children = []; this.attributes = new Map(); this.events = new Map(); this.dataset = {}; this.open = false; this.style = {setProperty() {}}; }
  querySelector(selector) { selector = selector.replace(/^:scope > /,''); return this.children.find((node) => selector === '.' + node.className) ?? null; }
  remove() { if (this.parent) this.parent.children.splice(this.parent.children.indexOf(this), 1); }
  contains(node) { return node === this || this.children.some(child=>child.contains(node)); }
  append(child) { child.remove(); child.parent = this; this.children.push(child); }
  insertBefore(child, sibling) { child.remove(); child.parent = this; this.children.splice(this.children.indexOf(sibling), 0, child); }
  setAttribute(key, value) { this.attributes.set(key, value); }
  getAttribute(key) { return this.attributes.get(key); }
  removeAttribute(key) { this.attributes.delete(key); }
  addEventListener(key, callback) { this.events.set(key, callback); }
  fire(key, fields = {}) { const event = { target: this, preventDefault() { this.defaultPrevented = true; }, ...fields }; this.events.get(key)?.(event); return event; }
  showModal() { this.open = true; }
  close() { this.open = false; this.fire('close'); }
  focus(options) { this.focusOptions=options;document.activeElement = this; }
  getBoundingClientRect() { return { left: 0, top: 0, right: 320, bottom: 800 }; }
}
function fixture(withContent = false, motionFactory = null) {
  const body=new Element('body');body.style.overflow='auto';
  globalThis.document = { body, createElement: (tag) => new Element(tag) };
  globalThis.window = { addEventListener() {}, removeEventListener() {} };
  const root = new Element('div'); root.dataset.closeLabel = 'Close menu'; if (motionFactory) root.dataset.panelMotion = 'curtain';
  const button = new Element('button'); const nav = new Element('nav'); nav.id = 'menu'; nav.setAttribute('aria-label', 'Main menu');
  root.append(button); root.append(nav);
  if (withContent) {
    root.dataset.panelMode = 'split';
    for (const name of ['content','header']) { const node = new Element('div'); node.className = `rdct-menu-panel-${name}`; node.hidden = true; root.append(node); }
  }
  let count = 0;
  const drawer = createDrawer(root, nav, button, () => { count++; drawer.close(); }, motionFactory);
  return { root, button, nav, drawer, dialog: root.children.find((node) => node.className === 'rdct-mob-drawer'), count: () => count };
}

test('split panel moves its existing header and content blocks and restores hidden state on close',() => {
  const {root,button,drawer,dialog} = fixture(true);
  const content = root.querySelector('.rdct-menu-panel-content');
  const header = root.querySelector('.rdct-menu-panel-header');
  drawer.open();
  assert.equal(dialog.parent,document.body);
  assert.equal(drawer.contains(content),true);
  assert.equal(drawer.contains(button),false);
  assert.equal(dialog.dataset.panelMode,'split');
  assert.equal(content.parent,dialog); assert.equal(header.parent,dialog);
  assert.equal(content.hidden,false); assert.equal(header.hidden,false);
  drawer.close();
  assert.equal(content.parent,root); assert.equal(header.parent,root);
  assert.equal(content.hidden,true); assert.equal(header.hidden,true);
});
test('mobile dialog moves the same navigation and restores body styles and trigger metadata', () => {
  const { root, button, nav, drawer, dialog, count } = fixture();
  drawer.open();
  assert.equal(nav.parent, dialog);
  assert.equal(document.body.style.overflow, 'hidden');
  assert.equal(button.getAttribute('aria-controls'), 'menu-drawer');
  assert.equal(document.activeElement, dialog.children[0]);
  assert.deepEqual(dialog.children[0].focusOptions,{preventScroll:true});
  dialog.children[0].fire('click');
  assert.equal(count(), 1);
  assert.equal(nav.parent, root);
  assert.equal(root.children[1], nav);
  assert.equal(document.body.style.overflow, 'auto');
  assert.equal(document.activeElement, button);
  assert.equal(button.getAttribute('aria-controls'), 'menu');
  drawer.close();
  assert.equal(document.body.style.overflow, 'auto');
});
test('cancel and outside backdrop close while an inside dialog click does not', () => {
  const { drawer, dialog, count } = fixture();
  drawer.open();
  dialog.fire('click', { clientX: 20, clientY: 20 });
  assert.equal(dialog.open, true);
  assert.equal(dialog.fire('cancel').defaultPrevented, true);
  assert.equal(count(), 1);
  drawer.open();
  dialog.fire('click', { clientX: 400, clientY: 20 });
  assert.equal(count(), 2);
  assert.equal(dialog.open, false);
});
test('unsupported or failed native dialog leaves the original menu usable', () => {
  const { root, nav, button, drawer, dialog } = fixture();
  dialog.showModal = () => { throw new Error('Unsupported state'); };
  drawer.open();
  assert.equal(nav.parent, root);
  assert.equal(document.body.style.overflow, 'auto');
  document.createElement = () => ({ showModal: undefined });
  assert.equal(createDrawer(root, nav, button, () => {}), null);
});

test('destroying an open drawer restores the navigation and cannot reopen an orphan dialog', () => {
  const { root, nav, button, drawer, dialog } = fixture();
  drawer.open(); drawer.destroy(); drawer.destroy();
  assert.equal(nav.parent, root);
  assert.equal(root.children.includes(dialog), false);
  assert.equal(document.body.style.overflow, 'auto');
  assert.equal(button.getAttribute('aria-controls'), 'menu');
  drawer.open(); assert.equal(dialog.open, false);
});

test('fullscreen panel covers the viewport beyond the stable scrollbar gutter',()=>{
  const page=fixture();window.innerWidth=1920;page.dialog.dataset.panelMode='fullscreen';
  page.drawer.open();assert.equal(page.dialog.style.inlineSize,'1920px');
  page.drawer.close();window.innerWidth=1280;page.drawer.open();assert.equal(page.dialog.style.inlineSize,'1280px');
  page.drawer.destroy();
});

test('animated dismissal holds the modal and scroll lock until completion, then returns focus',async()=>{
  let finish; const calls=[];
  const page=fixture(false,()=>({play:(open)=>{calls.push(open);return open?Promise.resolve(true):new Promise(resolve=>{finish=resolve;});},cancel(){},destroy(){}}));
  page.drawer.open(); page.dialog.children[0].fire('click');
  assert.equal(page.dialog.open,true); assert.equal(document.body.style.overflow,'hidden');
  finish(true); await new Promise(setImmediate);
  assert.equal(page.dialog.open,false); assert.equal(page.nav.parent,page.root); assert.equal(document.activeElement,page.button);
  assert.deepEqual(calls,[true,false]); page.drawer.destroy();
});

test('reopening or destroying during an animated close invalidates its delayed restoration',async()=>{
  let finish; let disposed=0;
  const page=fixture(false,()=>({play:(open)=>open?Promise.resolve(true):new Promise(resolve=>{finish=resolve;}),cancel(){},destroy(){++disposed;}}));
  page.drawer.open(); page.drawer.close(); page.drawer.open(); finish(true); await new Promise(setImmediate);
  assert.equal(page.dialog.open,true); assert.equal(page.nav.parent,page.dialog);
  page.drawer.close(); page.drawer.destroy(); finish(true); await new Promise(setImmediate);
  assert.equal(page.dialog.open,false); assert.equal(page.nav.parent,page.root); assert.equal(disposed,1); assert.equal(document.body.style.overflow,'auto');
});
