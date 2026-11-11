import test from 'node:test';
import assert from 'node:assert/strict';
import { observePreviewMarkers, previewStyle } from '../radmanct-components/src/js/integrations/atomic-preview-observer.js';

function fixture() {
  const frames = new Map(); const roots = new Map(); const links = [];
  let callback; let observers = 0; let disconnects = 0; let next = 0;
  const doc = {
    documentElement: {},
    defaultView: {
      MutationObserver: class { constructor(fn) { callback = fn; ++observers; } observe() {} disconnect() { ++disconnects; } },
      requestAnimationFrame(fn) { const id = ++next; frames.set(id, fn); return id; },
      cancelAnimationFrame(id) { frames.delete(id); },
    },
    querySelectorAll: selector => selector.startsWith('link') ? links : roots.get(selector) || [],
    createElement: () => ({ remove() { links.splice(links.indexOf(this), 1); } }),
    head: { append(link) { links.push(link); } },
  };
  return { doc, roots, links, frames, mutate: () => callback(), counts: () => ({ observers, disconnects }), flush() { for (const [id, fn] of frames) { frames.delete(id); fn(); } } };
}

test('Atomic families share one observer/frame and release independently', () => {
  const page = fixture(); let renders = 0; let stops = 0;
  const a = { isConnected: true, dataset: { options: '{}' } };
  const b = { isConnected: true, dataset: { options: '{}' } };
  page.roots.set('a', [a]); page.roots.set('b', [b]);
  const render = () => { ++renders; return () => ++stops; };
  const stopA = observePreviewMarkers(page.doc, 'a', render);
  const stopB = observePreviewMarkers(page.doc, 'b', render);
  assert.equal(page.counts().observers, 1); assert.equal(renders, 2);
  page.mutate(); page.mutate(); assert.equal(page.frames.size, 1);
  page.flush(); assert.equal(renders, 2);
  a.dataset.options = '{"changed":true}'; page.mutate(); page.flush();
  assert.equal(renders, 3); assert.equal(stops, 1);
  stopA(); stopA(); assert.equal(page.counts().disconnects, 0);
  page.roots.set('b', []); page.mutate(); page.flush(); assert.equal(stops, 3);
  page.mutate(); stopB(); assert.equal(page.frames.size, 0); assert.equal(page.counts().disconnects, 1);
  observePreviewMarkers(page.doc, 'a', render)(); assert.equal(page.counts().observers, 2);
});

test('one preview cannot remove another preview stylesheet by releasing twice', () => {
  const page = fixture();
  const first = previewStyle(page.doc, '/button.css'); const second = previewStyle(page.doc, '/button.css');
  assert.equal(page.links.length, 1);
  first(); first(); assert.equal(page.links.length, 1);
  second(); assert.equal(page.links.length, 0);
  const existing = { href: '/native.css', remove() { assert.fail('Native stylesheet must survive'); } };
  page.links.push(existing); const release = previewStyle(page.doc, '/native.css'); release(); release();
  assert.equal(page.links[0], existing);
});
