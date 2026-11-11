import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeContentTables } from '../radmanct/src/js/components/content-tables.js';

class Element {
  constructor(tag, classes = []) {
    this.tagName = tag; this.attributes = new Map(); this.classes = new Set(classes); this.children = []; this.clientWidth = 320; this.scrollWidth = 320;
    this.classList = { contains: (name) => this.classes.has(name), add: (name) => this.classes.add(name) };
  }
  get parentElement() { return this.parent; }
  getAttribute(key) { return this.attributes.get(key) ?? null; }
  setAttribute(key, value) { this.attributes.set(key, value); }
  removeAttribute(key) { this.attributes.delete(key); }
  append(node) { if (node.parent) node.parent.children.splice(node.parent.children.indexOf(node), 1); this.children.push(node); node.parent = this; }
  before(node) { node.parent = this.parent; this.parent.children.splice(this.parent.children.indexOf(this), 0, node); }
  closest(selector) { return selector.split(',').some((part) => part.trim().startsWith('.') ? this.classes.has(part.trim().slice(1)) : this.tagName === part.trim()) ? this : this.parent?.closest(selector); }
  querySelector() { return null; }
}
function fixture(existing = false) {
  const content = new Element('div');
  const table = new Element('table', ['alignwide']);
  if (existing) { const figure = new Element('figure', ['wp-block-table']); content.append(figure); figure.append(table); }
  else content.append(table);
  const tasks = new Map(), listeners = new Map(); let serial = 0; let observer;
  globalThis.document = { body: { dataset: { tableLabel: 'Scrollable table' } }, querySelectorAll: () => [table], createElement: (tag) => new Element(tag) };
  globalThis.window = { addEventListener: (name, fn) => listeners.set(name, fn), removeEventListener: (name) => listeners.delete(name) };
  globalThis.requestAnimationFrame = (fn) => { tasks.set(++serial, fn); return serial; };
  globalThis.cancelAnimationFrame = (id) => tasks.delete(id);
  globalThis.ResizeObserver = class { constructor(fn) { this.update = fn; observer = this; } observe() {} disconnect() { this.disconnected = true; } };
  const flush = () => { const pending = [...tasks.values()]; tasks.clear(); pending.forEach((fn) => fn()); };
  return { content, table, flush, listeners, observer: () => observer };
}

test('legacy tables get one local scroll region and keyboard focus only while overflowing', () => {
  const { content, table, flush, observer, listeners } = fixture();
  const cleanup = initializeContentTables();
  const region = table.parent;
  assert.equal(content.children.length, 1);
  assert.equal(region.classList.contains('alignwide'), true);
  assert.equal(region.classList.contains('rdct-tbl-scroll'), true);
  region.scrollWidth = 640; flush();
  assert.equal(region.getAttribute('tabindex'), '0');
  assert.equal(region.getAttribute('role'), 'region');
  assert.equal(region.getAttribute('aria-label'), 'Scrollable table');
  initializeContentTables(); assert.equal(table.parent, region);
  region.clientWidth = 700; observer().update(); flush();
  assert.equal(region.getAttribute('tabindex'), null);
  assert.equal(region.getAttribute('role'), null);
  cleanup(); assert.equal(observer().disconnected, true); assert.equal(listeners.size, 0);
});

test('Gutenberg table containers and existing accessibility attributes are preserved', () => {
  const { table, flush } = fixture(true);
  const region = table.parent; region.setAttribute('tabindex', '-1'); region.setAttribute('aria-label', 'Original caption');
  region.scrollWidth = 900;
  const cleanup = initializeContentTables(); flush();
  assert.equal(table.parent, region);
  assert.equal(region.getAttribute('tabindex'), '-1');
  assert.equal(region.getAttribute('aria-label'), 'Original caption');
  assert.equal(region.getAttribute('role'), null);
  cleanup(); assert.equal(region.getAttribute('aria-label'), 'Original caption');
});

test('the shared table component keeps its own scroll wrapper and accessibility', () => {
  const {content,table,listeners,observer} = fixture();
  const wrapper = new Element('div',['rdct-tb']);
  content.append(wrapper); wrapper.append(table);
  wrapper.setAttribute('role','region'); wrapper.setAttribute('tabindex','0'); wrapper.setAttribute('aria-label','Specifications');
  const cleanup = initializeContentTables();
  assert.equal(table.parent,wrapper);
  assert.equal(wrapper.classList.contains('rdct-tbl-scroll'),false);
  assert.equal(wrapper.getAttribute('aria-label'),'Specifications');
  assert.equal(observer(),undefined); assert.equal(listeners.size,0);
  cleanup();
});
