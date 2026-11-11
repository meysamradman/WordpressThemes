import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { runInNewContext } from 'node:vm';

const preloaderSource = await readFile(new URL('../radmanct/src/js/preloader.js', import.meta.url), 'utf8');
function loader({ state = 'loading', reduced = false, editor = false, exitDuration = '350' } = {}) {
  const document = new EventTarget();
  const window = new EventTarget();
  const timers = new Map();
  const frames = new Map(); let nextFrame = 0;
  const classes = new Set();
  let nextTimer = 0;
  const root = { dataset: { exitDuration }, hidden: true, removed: false, classList: { add: (...values) => values.forEach((value) => classes.add(value)) }, remove() { this.removed = true; } };
  document.readyState = state;
  document.getElementById = () => root;
  document.documentElement = { classList: { contains: () => editor } };
  document.body = { classList: { contains: () => false } };
  runInNewContext(preloaderSource, {
    document, window, AbortController, matchMedia: () => ({ matches: reduced }),
    setTimeout: (callback, delay) => { const id = ++nextTimer; timers.set(id, { callback, delay }); return id; },
    clearTimeout: (id) => timers.delete(id),
    requestAnimationFrame: (callback) => { const id = ++nextFrame; frames.set(id, callback); return id; },
    cancelAnimationFrame: (id) => frames.delete(id),
  });
  const runTimer = (delay) => {
    const entry = [...timers.entries()].find(([, timer]) => timer.delay === delay);
    assert.ok(entry, `Missing ${delay} ms timer`);
    timers.delete(entry[0]); entry[1].callback();
  };
  const paint = () => { const callbacks = [...frames.values()]; frames.clear(); callbacks.forEach((fn) => fn()); };
  return { document, window, root, timers, classes, runTimer, paint, frames };
}

test('preloader skips already completed pages, reduced motion and editor mode', () => {
  for (const options of [{ state: 'complete' }, { reduced: true }, { editor: true }]) {
    const page = loader(options);
    assert.equal(page.root.hidden, true);
    assert.equal(page.root.removed, true);
    assert.equal(page.timers.size, 0);
  }
});

test('interactive DOM keeps the preloader until page load and the first paint', () => {
  const page = loader({state:'interactive',exitDuration:'500'});
  assert.equal(page.root.hidden,false);
  assert.equal(page.root.removed,false);
  page.window.dispatchEvent(new Event('load'));
  assert.equal(page.classes.has('is-leaving'),false);
  page.paint(); page.paint();
  assert.equal(page.classes.has('is-leaving'),true);
  page.runTimer(560);
  assert.equal(page.root.removed,true);
  assert.equal(page.timers.size,0);
});

test('preloader preserves the full staggered dissolve exit while retaining its absolute safety limit', () => {
  const page = loader({ exitDuration: '912' });
  page.paint(); page.paint(); page.window.dispatchEvent(new Event('load'));
  assert.equal(page.root.removed, false);
  assert.equal([...page.timers.values()].some(({ delay }) => delay === 1800), true);
  page.runTimer(972); assert.equal(page.root.removed, true);
  const bad = loader({ exitDuration: '999999' });
  bad.paint(); bad.paint(); bad.window.dispatchEvent(new Event('load'));
  bad.runTimer(410); assert.equal(bad.root.removed, true);
});

test('all Hub styles and the local image reserve their complete exit before a slow-page safety deadline', () => {
  for (const duration of [715, 500, 912, 300]) {
    const page = loader({ exitDuration: String(duration) });
    page.paint(); page.paint();
    const exit = duration + 60;
    page.runTimer(1800 - exit);
    assert.equal(page.classes.has('is-leaving'), true);
    assert.equal(page.root.removed, false);
    page.window.dispatchEvent(new Event('load'));
    assert.equal([...page.timers.values()].filter(({ delay }) => delay === exit).length, 1);
    page.runTimer(exit);
    assert.equal(page.root.removed, true);
    assert.equal(page.timers.size, 0);
  }
});

test('preloader waits for bounded page load and releases on timeout or user interaction', () => {
  const ready = loader();
  assert.equal(ready.root.hidden, false);
  ready.document.dispatchEvent(new Event('DOMContentLoaded'));
  assert.equal(ready.classes.has('is-leaving'), false);
  ready.window.dispatchEvent(new Event('load'));
  assert.equal(ready.classes.has('is-leaving'), false);
  ready.paint(); assert.equal(ready.classes.has('is-leaving'), false);
  ready.paint();
  assert.ok(ready.classes.has('is-leaving'));
  assert.equal([...ready.timers.values()].some(({ delay }) => delay === 1800), true);
  ready.runTimer(410);
  assert.equal(ready.root.removed, true);
  const stalled = loader();
  stalled.runTimer(1800);
  assert.equal(stalled.root.removed, true);
  const keyboard = loader();
  keyboard.document.dispatchEvent(new Event('keydown'));
  assert.equal(keyboard.root.removed, true);
  assert.equal(keyboard.timers.size, 0);
  assert.equal(keyboard.frames.size, 0);
});

test('preloader leaves after its first paint without reloads and cleans up restored pages', () => {
  const page = loader(); page.paint(); page.paint();
  assert.equal(page.classes.has('is-leaving'), false);
  page.window.dispatchEvent(new Event('pageshow'));
  assert.equal(page.root.removed, false);
  page.window.dispatchEvent(new Event('load'));
  assert.equal(page.classes.has('is-leaving'), true);
  page.runTimer(410);
  assert.equal(page.timers.size, 0); assert.equal(page.frames.size, 0);
  const restored = loader();
  const event = new Event('pageshow'); Object.defineProperty(event, 'persisted', { value: true });
  restored.window.dispatchEvent(event);
  assert.equal(restored.root.removed, true); assert.equal(restored.frames.size, 0);
  const leaving = loader(); leaving.window.dispatchEvent(new Event('pagehide'));
  assert.equal(leaving.root.removed, true); assert.equal(leaving.timers.size, 0);
  assert.doesNotMatch(preloaderSource, /location|history\.|fetch\(|XMLHttpRequest/);
});

const progressSource = (await readFile(new URL('../radmanct/src/js/scroll-progress.js', import.meta.url), 'utf8')).replace('export function', 'function');
test('scroll progress clamps values, coalesces events and refreshes geometry after content changes', () => {
  const document = new EventTarget();
  const window = new EventTarget();
  const frames = new Map();
  let nextFrame = 0, height = 1000, heightReads = 0, resize;
  const bar = { style: {} };
  const root = { dataset: {}, hidden: true, querySelector: () => bar };
  document.getElementById = () => root;
  document.documentElement = { get scrollHeight() { heightReads++; return height; }, clientHeight: 500 };
  document.body = {};
  window.scrollY = 250;
  runInNewContext(`${progressSource}\ninitializeScrollProgress();`, {
    document, window, AbortController,
    requestAnimationFrame: (callback) => { const id = ++nextFrame; frames.set(id, callback); return id; },
    cancelAnimationFrame: (id) => frames.delete(id),
    ResizeObserver: class { constructor(callback) { resize = callback; } observe() {} disconnect() {} },
  });
  const paint = () => { const callbacks = [...frames.values()]; frames.clear(); callbacks.forEach((callback) => callback()); };
  paint(); assert.equal(bar.style.transform, 'scaleX(0.5)');
  const reads = heightReads;
  window.scrollY = 9999;
  document.dispatchEvent(new Event('scroll')); document.dispatchEvent(new Event('scroll'));
  assert.equal(frames.size, 1);
  paint(); assert.equal(bar.style.transform, 'scaleX(1)'); assert.equal(heightReads, reads);
  window.scrollY = -40;
  document.dispatchEvent(new Event('scroll')); paint(); assert.equal(bar.style.transform, 'scaleX(0)');
  height = 500; resize(); paint(); assert.equal(root.hidden, true);
  height = 1500; window.scrollY = 500; resize(); paint();
  assert.equal(root.hidden, false); assert.equal(bar.style.transform, 'scaleX(0.5)');
  document.dispatchEvent(new Event('scroll'));
  window.dispatchEvent(new Event('pagehide'));
  assert.equal(frames.size, 0);
});
