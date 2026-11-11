import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeHeaderText } from '../radmanct-components/src/js/components/text/header-state.js';

test('header text shares sampling, resolves sticky/tone colors and releases the last subscriber', () => {
  let top = 0; let tone = 'dark'; let inserted = 0; let removed = 0; const frames = new Map(); let id = 0;
  const view = new EventTarget();
  const header = Object.assign(new EventTarget(), { classList: {contains: () => false}, contains: () => false, before: () => ++inserted, getBoundingClientRect: () => ({ top: 0, bottom: 80, left: 0, width: 1000 }) });
  const section = { dataset: {}, closest: () => section };
  Object.assign(view, { innerWidth: 1000, innerHeight: 800, requestAnimationFrame: fn => { frames.set(++id, fn); return id; }, cancelAnimationFrame: key => frames.delete(key), getComputedStyle: node => ({ position: 'sticky', getPropertyValue: key => node.colors?.[key] || '' }) });
  const doc = Object.assign(new EventTarget(), { defaultView: view, documentElement: {dataset:{rdctScheme:'dark'}}, createElement: () => ({ style: {}, setAttribute() {}, getBoundingClientRect: () => ({ top }), remove: () => ++removed }), elementsFromPoint: () => { section.dataset.rdctHeaderTone = tone; return [section]; } });
  header.ownerDocument = doc;
  const root = () => ({ ownerDocument: doc, dataset: {}, colors: { '--rdct-text-header-sticky': '#111', '--rdct-text-header-dark': '#fff' }, closest: () => header, removeAttribute: function () { delete this.dataset.headerCurrent; } });
  const flush = () => { const pending = [...frames.values()]; frames.clear(); pending.forEach(fn => fn()); };
  const first = root(); const second = root();
  const release = initializeHeaderText(first); const releaseSecond = initializeHeaderText(second);
  assert.equal(inserted, 1); assert.equal(frames.size, 1); flush(); assert.equal(first.dataset.headerCurrent, 'dark');
  top = -20; view.dispatchEvent(new Event('scroll')); view.dispatchEvent(new Event('resize')); assert.equal(frames.size, 1); flush(); assert.equal(second.dataset.headerCurrent, 'sticky');
  release(); release(); assert.equal(removed, 0); assert.equal(first.dataset.headerCurrent, undefined);
  top = 0; tone = 'light'; view.dispatchEvent(new Event('scroll')); flush(); assert.equal(second.dataset.headerCurrent, undefined);
  tone = 'auto'; doc.dispatchEvent(new Event('rdct:color-scheme')); flush(); assert.equal(second.dataset.headerCurrent, 'dark');
  doc.documentElement.dataset.rdctScheme = 'light'; doc.dispatchEvent(new Event('rdct:color-scheme')); flush(); assert.equal(second.dataset.headerCurrent, undefined);
  tone = 'dark'; doc.dispatchEvent(new Event('rdct:smooth-update')); flush(); assert.equal(second.dataset.headerCurrent, 'dark');
  releaseSecond(); assert.equal(removed, 1); view.dispatchEvent(new Event('scroll')); assert.equal(frames.size, 0);
});

test('ordinary content does not create header observers', () => {
  const release = initializeHeaderText({ closest: () => null }); assert.doesNotThrow(release);
});
