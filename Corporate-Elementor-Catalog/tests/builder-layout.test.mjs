import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { createItem } from '../radmanct/src/js/admin/builder-elements.js';
import { initializeTransparentHeader } from '../radmanct/src/js/components/transparent-header.js';
import { findPreviewDrop } from '../radmanct/src/js/admin/builder-drag.js';

function fixture(mode = 'all', observer = true) {
  const window = new EventTarget(); window.scrollY = 0; globalThis.window = window;
  const desktop = new EventTarget(); desktop.matches = false; globalThis.matchMedia = () => desktop;
  const frames = new Map(); let id = 0; let height = 80; let reads = 0; let resized;
  globalThis.requestAnimationFrame = (fn) => { frames.set(++id, fn); return id; };
  globalThis.cancelAnimationFrame = (key) => frames.delete(key);
  let disconnected = 0;
  globalThis.ResizeObserver = observer ? class { constructor(fn) { resized = fn; } observe() {} disconnect() { ++disconnected; } } : undefined;
  const classes = new Set(); const styles = new Map([['--rdct-header-height', '12px']]);
  const root = Object.assign(new EventTarget(), { isConnected: true, dataset: { stickyMode: mode }, getBoundingClientRect: () => { ++reads; return { height }; },
    style: { getPropertyValue: (key) => styles.get(key) || '', setProperty: (key, value) => styles.set(key, value), removeProperty: (key) => styles.delete(key) },
    classList: { contains: key => classes.has(key), add: (key) => classes.add(key), remove: (...keys) => keys.forEach((key) => classes.delete(key)), toggle: (key, value) => value ? classes.add(key) : classes.delete(key) } });
  const flush = () => { const callbacks = [...frames.values()]; frames.clear(); callbacks.forEach((fn) => fn()); };
  return { root, window, desktop, classes, styles, frames, flush, resize: (value) => { height = value; if (resized) resized(); else window.dispatchEvent(new Event('resize')); }, reads: () => reads, disconnected: () => disconnected };
}

test('transparent header measures once, coalesces scroll and restores its state on cleanup', () => {
  const page = fixture(); const cleanup = initializeTransparentHeader(page.root);
  assert.equal(page.styles.get('--rdct-header-height'), '80px'); assert.equal(page.classes.has('is-ready'), true);
  page.window.scrollY = 30; for (let i = 0; i < 10; ++i) page.window.dispatchEvent(new Event('scroll'));
  assert.equal(page.frames.size, 1); page.flush(); assert.equal(page.classes.has('is-scrolled'), true); assert.equal(page.reads(), 1);
  page.resize(120); page.flush(); assert.equal(page.styles.get('--rdct-header-height'), '120px');
  page.resize(0); page.flush(); assert.equal(page.styles.get('--rdct-header-height'), '0px');
  cleanup(); cleanup(); assert.equal(page.disconnected(), 1); assert.equal(page.styles.get('--rdct-header-height'), '12px'); assert.equal(page.classes.size, 0);
  page.window.dispatchEvent(new Event('scroll')); assert.equal(page.frames.size, 0);
});

test('transparent sticky respects device selection and works without ResizeObserver', () => {
  const page = fixture('desktop', false); const cleanup = initializeTransparentHeader(page.root);
  page.window.scrollY = 100; page.window.dispatchEvent(new Event('scroll')); page.flush(); assert.equal(page.classes.has('is-scrolled'), false);
  page.desktop.matches = true; page.desktop.dispatchEvent(new Event('change')); page.flush(); assert.equal(page.classes.has('is-scrolled'), true);
  page.resize(90); page.flush(); assert.equal(page.styles.get('--rdct-header-height'), '90px'); cleanup();
  const plain = fixture('off'); const stop = initializeTransparentHeader(plain.root);
  plain.window.scrollY = 100; plain.window.dispatchEvent(new Event('scroll')); assert.equal(plain.frames.size, 0); plain.flush(); assert.equal(plain.classes.has('is-scrolled'), false); stop();
});

test('desktop-only overlay leaves mobile flow untouched and follows breakpoint changes', () => {
  const page = fixture('all'); page.root.dataset.rdctTransparentDevices = 'desktop';
  const cleanup = initializeTransparentHeader(page.root);
  assert.equal(page.classes.has('is-ready'), false); assert.equal(page.reads(), 0);
  page.desktop.matches = true; page.desktop.dispatchEvent(new Event('change')); page.flush();
  assert.equal(page.classes.has('is-ready'), true); assert.equal(page.reads(), 1);
  page.desktop.matches = false; page.desktop.dispatchEvent(new Event('change')); page.flush();
  assert.equal(page.classes.has('is-ready'), false); assert.equal(page.classes.has('is-scrolled'), false);
  cleanup(); assert.equal(page.frames.size, 0);
});

test('new footer lists default to vertical expanded menus; grid keeps explicit responsive tracks', async () => {
  const footer = createItem('menu', { kind: 'footer' });
  assert.equal(footer.menuLayout, 'vertical'); assert.equal(footer.mobileDisplay, 'expanded');
  assert.equal(createItem('menu', { kind: 'header' }).mobileDisplay, undefined);
  const css = await readFile(new URL('../radmanct/src/css/builder.css', import.meta.url), 'utf8');
  assert.match(css, /repeat\(var\(--builder-columns, 3\), minmax\(0, 1fr\)\)/);
  assert.match(css, /repeat\(var\(--builder-mobile-columns, 1\), minmax\(0, 1fr\)\)/);
  assert.match(css, /--builder-grid-column: 1 \/ -1/);
});

test('grid drops follow the selected column count, full spans and RTL reading direction', () => {
  const row = { id: 'row', appearance: { rowLayout: 'grid', columns: 1, mobileColumns: 2 }, items: [{ id: 'item' }] };
  const targets = [{ id: 'item', rowId: 'row', item: true, rect: { left: 0, top: 0, right: 100, bottom: 60, width: 100, height: 60 } }];
  const layout = { rows: [row] };
  assert.equal(findPreviewDrop(targets, 75, 5, layout, false, false).index, 0);
  assert.equal(findPreviewDrop(targets, 75, 5, layout, true, false).index, 1);
  assert.equal(findPreviewDrop(targets, 25, 5, layout, true, true).index, 1);
  row.items[0].appearance = { width: 'full' };
  assert.equal(findPreviewDrop(targets, 75, 5, layout, true, false).index, 0);
  assert.equal(findPreviewDrop(targets, 75, 50, layout, true, false).index, 1);
});


test('transparent header reserves only its actual overlap in the adjacent title and restores on teardown',()=>{
  const page=fixture('off');const offsets=new Map([['--rdct-title-header-offset','10px']]);
  page.root.nextElementSibling={matches:selector=>selector==='[data-rdct-title-area]',style:{getPropertyValue:key=>offsets.get(key)||'',setProperty:(key,value)=>offsets.set(key,value),removeProperty:key=>offsets.delete(key)}};
  const release=initializeTransparentHeader(page.root);
  assert.equal(offsets.get('--rdct-title-header-offset'),'80px');
  page.resize(110);page.flush();assert.equal(offsets.get('--rdct-title-header-offset'),'110px');
  page.root.dataset.rdctTransparentDevices='desktop';page.desktop.dispatchEvent(new Event('change'));page.flush();
  assert.equal(offsets.get('--rdct-title-header-offset'),'0px');
  page.desktop.matches=true;page.desktop.dispatchEvent(new Event('change'));page.flush();
  assert.equal(offsets.get('--rdct-title-header-offset'),'110px');
  release();assert.equal(offsets.get('--rdct-title-header-offset'),'10px');
});
