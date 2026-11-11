import test from 'node:test';
import assert from 'node:assert/strict';
import { normalizeAtomicComponent, renderAtomicComponent, observeAtomicComponents } from '../radmanct-components/src/js/integrations/atomic-component-preview.js';

const config = { icons: {}, fields: {
  icon: { size: { default: 24, min: 8, max: 128 }, mirror: { default: true }, name: { default: 'phone', options: { phone: 'Phone' } } },
  'text-reveal': { text: { default: 'Text' }, tag: { default: 'p', options: { p: 'p', h2: 'h2' } }, mode: { default: 'scroll', options: { scroll: 'Scroll', hover: 'Hover' } }, direction: { default: 'inherit', options: { inherit: 'Inherit', rtl: 'RTL' } }, idle: { default: '#667085' }, active: { default: '#172b3a' } },
} };

function fixture(input = {}) {
  let notify; let disconnected = 0;
  const elements = [];
  const doc = { createElement: (tag) => {
    const node = { tag, dataset: {}, attributes: {}, style: { values: {}, setProperty(key, value) { this.values[key] = value; } }, setAttribute(key, value) { this.attributes[key] = value; } };
    elements.push(node); return node;
  }, documentElement: {}, defaultView: { MutationObserver: class { constructor(fn) { notify = fn; } observe() {} disconnect() { ++disconnected; } } } };
  const marker = { dataset: { rdctAtomicComponent: 'text-reveal', text: '<b>Plain text</b>', ...input }, ownerDocument: doc, isConnected: true, children: [], replaceChildren() { this.children = []; }, append(node) { this.children.push(node); } };
  doc.querySelectorAll = () => marker.isConnected ? [marker] : [];
  return { doc, marker, elements, notify: () => notify(), disconnected: () => disconnected };
}

test('Atomic values reject unknown enums, nonnumeric envelopes and unsafe tags', () => {
  assert.deepEqual(normalizeAtomicComponent('icon', { name: 'remote.svg', size: [], mirror: 'false' }, config.fields), { name: 'phone', size: 24, mirror: false });
  assert.equal(normalizeAtomicComponent('icon', { size: '900' }, config.fields).size, 128);
  assert.equal(normalizeAtomicComponent('icon', { size: true }, config.fields).size, 24);
  assert.equal(normalizeAtomicComponent('text-reveal', { tag: 'script' }, config.fields).tag, 'p');
});

test('Atomic text preview preserves plain text, RTL and safe color with delayed animation cleanup', async () => {
  const page = fixture({ tag: 'h2', direction: 'rtl', idle: 'red;background:url(bad)' });
  let resolve; let starts = 0;
  const pending = new Promise((done) => { resolve = done; });
  const cleanup = renderAtomicComponent(page.marker, config, () => pending);
  const root = page.marker.children[0];
  assert.equal(root.tag, 'h2'); assert.equal(root.textContent, '<b>Plain text</b>'); assert.equal(root.attributes.dir, 'rtl');
  assert.equal(root.style.values['--rdct-text-idle'], '#667085');
  cleanup(); resolve({ initializeTextReveal() { ++starts; } }); await pending; await Promise.resolve();
  assert.equal(starts, 0);
  const hover = fixture({ mode: 'hover' });
  renderAtomicComponent(hover.marker, config, () => { throw new Error('Hover must not load GSAP'); });
  assert.equal(hover.marker.children[0].tabIndex, 0);
});

test('Atomic plain Text preview preserves native alpha and short hex colors', () => {
  for (const [input, expected] of [['#1234','#11223344'],['#12345680','#12345680'],['rgba(12,34,56,0)','#0c223800'],['transparent','#00000000']]) {
    const page = fixture({mode:'hover',idle:input,active:input});
    renderAtomicComponent(page.marker,config,assert.fail);
    assert.equal(page.marker.children[0].style.values['--rdct-text-idle'],expected);
    assert.equal(page.marker.children[0].style.values['--rdct-text-active'],expected);
  }
});

test('Atomic observation coalesces edits, ignores its own rendering and cleans removed roots once', async () => {
  const page = fixture(); let stops = 0; let starts = 0; const frames = new Map(); let id = 0;
  const flush = () => { const work = [...frames.values()]; frames.clear(); work.forEach((fn) => fn()); };
  const cleanup = observeAtomicComponents(page.doc, config, async () => ({ initializeTextReveal() { ++starts; return () => { ++stops; }; } }), (fn) => { frames.set(++id, fn); return id; }, (key) => frames.delete(key));
  await Promise.resolve(); assert.equal(starts, 1);
  page.notify(); page.notify(); assert.equal(frames.size, 1); flush(); assert.equal(page.elements.length, 1);
  page.marker.dataset.text = 'Changed'; page.notify(); flush(); await Promise.resolve();
  assert.equal(stops, 1); assert.equal(starts, 2);
  page.marker.isConnected = false; page.notify(); flush(); assert.equal(stops, 2);
  cleanup(); cleanup(); assert.equal(page.disconnected(), 1); assert.equal(stops, 2);
});

test('overlong text and large word sets avoid unnecessary animation imports', () => {
  const empty = fixture({ text: 'x'.repeat(16001) });
  const large = fixture({ text: Array(401).fill('word').join(' ') });
  const fail = () => { throw new Error('Must not animate over-limit text'); };
  renderAtomicComponent(empty.marker, config, fail); assert.equal(empty.marker.children.length, 0);
  renderAtomicComponent(large.marker, config, fail); assert.equal(large.marker.children[0].dataset.rdctTextReveal, 'static');
});

test('Atomic icon preview uses local SVG, clamps size and retains accessible labels', () => {
  const page = fixture({ rdctAtomicComponent: 'icon', name: 'phone', size: '999', mirror: 'false', label: '<b>Phone</b>' });
  const svg = { localName: 'svg', attributes: { 'aria-hidden': 'true' }, classList: { remove(name) { svg.removed = name; } }, setAttribute(key, value) { this.attributes[key] = value; }, removeAttribute(key) { delete this.attributes[key]; } };
  page.doc.importNode = (node) => node;
  page.doc.defaultView.DOMParser = class { parseFromString(source, type) { assert.equal(source, '<svg/>'); assert.equal(type, 'image/svg+xml'); return { documentElement: svg }; } };
  const iconConfig = { ...config, icons: { phone: '<svg/>' }, fields: { ...config.fields, icon: { ...config.fields.icon, stroke: { default: 2, min: 1, max: 3 }, label: { default: '' } } } };
  renderAtomicComponent(page.marker, iconConfig, () => { throw new Error('Icon must not load animation'); });
  assert.equal(svg.attributes.width, 128); assert.equal(svg.attributes['aria-label'], 'Phone'); assert.equal(svg.attributes.role, 'img');
  assert.equal(svg.attributes['aria-hidden'], undefined); assert.equal(svg.removed, 'rdct-ico--directional');
});
