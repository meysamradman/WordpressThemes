import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { runInNewContext } from 'node:vm';

const source = (await readFile(new URL('../radmanct/src/js/container-motion-manager.js', import.meta.url), 'utf8'))
  .replace(/^import .*;\r?\n/gm, '').replace('export function', 'function').replace("import('./components/container-motion.js')", 'loadMotion()');
const flush = async () => { for (let i = 0; i < 8; ++i) await Promise.resolve(); };

test('preview Reset reverts only its selected element until Play or a settings edit, including a pending import', async () => {
  const view = fixture(); const cleanup = view.start(true); await flush();
  const reset = view.listeners.get('rdct:motion:reset');
  reset({detail:{root:view.root}}); await flush();
  assert.equal(view.counts().stops,1); assert.equal(view.counts().starts,1);
  view.mutate(); await flush(); assert.equal(view.counts().starts,1);
  view.listeners.get('rdct:motion:play')({detail:{root:view.root}}); await flush(); assert.equal(view.counts().starts,2);
  reset({detail:{root:view.root}}); view.edit({animate:true,duration:2}); await flush(); assert.equal(view.counts().starts,3);
  cleanup(); assert.equal(view.listeners.size,0);
  const pending = fixture({deferred:true}); const stop = pending.start(true);
  pending.listeners.get('rdct:motion:reset')({detail:{root:pending.root}}); pending.resolve(); await flush();
  assert.equal(pending.counts().starts,0); stop();
});

function fixture({ reduced = false, deferred = false, options = {animate:true}, width } = {}) {
  let key = JSON.stringify(options);
  let callback; let resolveLoad; let loads = 0; let starts = 0; let stops = 0; let disconnected = 0;
  const history = [];
  const root = { isConnected: true, getAttribute: () => key, closest: () => root };
  const listeners = new Map(); const mediaListeners = new Set();
  const scope = {
    documentElement: {}, querySelectorAll: () => root.isConnected && key ? [root] : [],
    addEventListener: (event, fn) => listeners.set(event, fn),
    removeEventListener: (event, fn) => { if (listeners.get(event) === fn) listeners.delete(event); },
  };
  if (width !== undefined) { scope.defaultView = new EventTarget(); scope.defaultView.innerWidth = width; }
  const query = { matches: !reduced, addEventListener: (_, fn) => mediaListeners.add(fn), removeEventListener: (_, fn) => mediaListeners.delete(fn) };
  const module = { initializeContainerMotion: (root, options, preview, play) => { ++starts; history.push({root,preview,play}); return () => { ++stops; }; } };
  const pending = deferred ? new Promise((resolve) => { resolveLoad = resolve; }) : Promise.resolve(module);
  const context = {
    matchMedia: () => query, queueMicrotask,
    loadMotion: () => { ++loads; return pending; },
    MutationObserver: class {
      constructor(fn) { callback = fn; }
      observe() {}
      disconnect() { ++disconnected; }
    },
  };
  runInNewContext(`${source}\nthis.observe = observeContainerMotion;`, context);
  return {
    start: (preview = false) => context.observe(scope,preview), root, scope,
    counts: () => ({ loads, starts, stops, disconnected }),
    mutate: (records = []) => callback(records),
    edit: (options) => { key = JSON.stringify(options); callback([{ type: 'attributes', target: root }]); },
    reduce: (value) => { query.matches = !value; for (const fn of mediaListeners) fn(); },
    resolve: () => resolveLoad(module),
    listeners, mediaListeners, history,
  };
}

test('reduced motion avoids the engine; changed settings and children rebuild only the live owner', async () => {
  const view = fixture({ reduced: true }); const cleanup = view.start(); await flush();
  assert.equal(view.counts().loads, 0);
  view.reduce(false); await flush(); assert.equal(view.counts().starts, 1);
  view.edit({ animate: true, duration: 2 }); await flush();
  assert.equal(view.counts().starts, 2); assert.equal(view.counts().stops, 1);
  view.mutate([{ type: 'childList', target: view.root }]); await flush();
  assert.equal(view.counts().starts, 3); assert.equal(view.counts().stops, 2);
  view.reduce(true); await flush(); assert.equal(view.counts().stops, 3);
  cleanup(); cleanup();
  assert.equal(view.counts().disconnected, 1); assert.equal(view.listeners.size, 0); assert.equal(view.mediaListeners.size, 0);
});

test('horizontal-only pages skip GSAP in the editor and below their selected breakpoint', async () => {
  const page = fixture({options:{horizontal:true},width:600});
  const cleanup = page.start(); await flush(); assert.equal(page.counts().loads,0);
  page.scope.defaultView.innerWidth = 1200; page.scope.defaultView.dispatchEvent(new Event('resize')); await flush();
  assert.equal(page.counts().starts,1);
  page.scope.defaultView.innerWidth = 600; page.scope.defaultView.dispatchEvent(new Event('resize')); await flush();
  assert.equal(page.counts().stops,1); cleanup();
  const editor = fixture({options:{horizontal:true},width:1200}); const stop = editor.start(true);
  await flush(); assert.equal(editor.counts().loads,0); stop();
});

test('late engine imports cannot start after teardown or after their root is removed', async () => {
  for (const remove of [false, true]) {
    const view = fixture({ deferred: true }); const cleanup = view.start();
    if (remove) { view.root.isConnected = false; view.mutate(); await flush(); } else cleanup();
    view.resolve(); await flush();
    assert.equal(view.counts().starts, 0);
    cleanup();
  }
});

test('one manager owns a document; stale cleanup leaves the replacement observer active', async () => {
  const view = fixture(); const old = view.start(); await flush();
  const current = view.start(); await flush(); old();
  assert.equal(view.counts().stops, 1); assert.equal(view.listeners.size, 1);
  view.listeners.get('rdct:motion:play')(); await flush();
  assert.equal(view.counts().starts, 3); assert.equal(view.counts().stops, 2);
  current(); assert.equal(view.counts().stops, 3); assert.equal(view.listeners.size, 0);
});


test('adding a child motion owner invalidates the already animated parent targets', async () => {
  const view = fixture(); const cleanup = view.start(); await flush();
  const child = {parentElement:view.root};
  view.mutate([{type:'attributes',target:child,attributeName:'data-rdct-motion'}]); await flush();
  assert.equal(view.counts().starts,2); assert.equal(view.counts().stops,1);
  cleanup();
});

test('selected replay isolates parent, independent child and sibling owners while global replay remains explicit', async () => {
  const view = fixture();
  const child = {isConnected:true,getAttribute:() => JSON.stringify({animate:true})};
  const sibling = {isConnected:true,getAttribute:child.getAttribute};
  view.scope.querySelectorAll = () => [view.root,child,sibling].filter((root) => root.isConnected);
  const cleanup = view.start(true); await flush(); assert.equal(view.counts().starts,3);
  const replay = view.listeners.get('rdct:motion:play');
  replay({detail:{root:view.root}}); replay({detail:{root:view.root}}); await flush();
  assert.equal(view.counts().stops,1); assert.equal(view.counts().starts,4);
  assert.equal(view.history.at(-1).root,view.root); assert.equal(view.history.at(-1).play,true);
  replay({detail:{root:child}}); await flush();
  assert.equal(view.counts().stops,2); assert.equal(view.history.at(-1).root,child);
  replay({detail:{}}); replay({detail:{root:{isConnected:true}}});
  child.isConnected = false; replay({detail:{root:child}}); await flush(); assert.equal(view.counts().starts,5);
  replay(); await flush(); assert.equal(view.counts().starts,7);
  assert.ok(view.history.slice(-2).every((entry) => entry.play));
  cleanup(); replay({detail:{root:view.root}}); await flush(); assert.equal(view.counts().starts,7);
});

test('a replay queued during a delayed engine import replaces only its selected pending initialization', async () => {
  const view = fixture({deferred:true});
  const cleanup = view.start(true);
  view.listeners.get('rdct:motion:play')({detail:{root:view.root}}); await flush();
  view.resolve(); await flush();
  assert.equal(view.counts().starts,1); assert.equal(view.history[0].play,true);
  cleanup();
});
