import test from 'node:test';
import assert from 'node:assert/strict';
import { createPageLifecycle, reportRuntimeError } from '../shared/frontend/lifecycle.js';

const flush = () => new Promise((resolve) => setImmediate(resolve));
function fixture(debug = false) {
  const view = new EventTarget();
  const observers = []; const errors = [];
  Object.assign(view, {
    document: { documentElement: {}, getElementById: () => null },
    rdctComponents: { debug }, console: { error: (...args) => errors.push(args) },
    MutationObserver: class {
      constructor(callback) { this.callback = callback; this.connected = false; observers.push(this); }
      observe() { this.connected = true; }
      disconnect() { this.connected = false; }
    },
  });
  const hide = (persisted = false) => view.dispatchEvent(Object.assign(new Event('pagehide'), { persisted }));
  return { view, observers, errors, hide, page: createPageLifecycle(view) };
}

test('PHP bootstrap and Elementor share one root instance with independent cleanup ownership', async () => {
  const { page, observers } = fixture();
  const root = { isConnected: true }; let imports = 0; let initialized = 0; let destroyed = 0;
  const load = async () => { ++imports; return {}; };
  const initialize = () => { ++initialized; return () => ++destroyed; };
  const php = page.mountRoot(root, 'image', load, initialize);
  const elementor = page.mountRoot(root, 'image', load, initialize);
  await flush();
  assert.equal(imports, 1); assert.equal(initialized, 1); assert.equal(observers.length, 1);
  elementor(); elementor(); assert.equal(destroyed, 0);
  php(); assert.equal(destroyed, 1); assert.equal(observers[0].connected, false);
  const next = page.mountRoot(root, 'image', load, initialize);
  await flush(); assert.equal(initialized, 2);
  php(); assert.equal(destroyed, 1);
  next(); page.dispose(); assert.equal(destroyed, 2);
});

test('detached roots release all owners and a later replacement gets its own instance', async () => {
  const { page, observers } = fixture(); let destroyed = 0;
  const root = { isConnected: true };
  const old = page.mountRoot(root, 'carousel', async () => ({}), () => () => ++destroyed);
  page.mountRoot(root, 'carousel', async () => ({}), () => assert.fail('duplicate'));
  await flush(); root.isConnected = false; observers[0].callback();
  assert.equal(destroyed, 1); assert.equal(observers[0].connected, false);
  root.isConnected = true;
  const next = page.mountRoot(root, 'carousel', async () => ({}), () => () => ++destroyed);
  await flush(); old(); assert.equal(destroyed, 1);
  next(); page.dispose(); assert.equal(destroyed, 2);
});

test('destroy while import is pending cannot initialize a removed widget or outgoing page', async () => {
  for (const reason of ['owner', 'detach', 'page']) {
    const { page, observers, hide } = fixture(); const root = { isConnected: true };
    let resolve; let initialized = 0;
    const cancel = page.mountRoot(root, 'text', () => new Promise((done) => { resolve = done; }), () => ++initialized);
    await flush();
    if (reason === 'owner') cancel();
    if (reason === 'detach') { root.isConnected = false; observers[0].callback(); }
    if (reason === 'page') hide();
    resolve({}); await flush(); assert.equal(initialized, 0, reason);
    page.dispose();
  }
});

test('bfcache entry preserves instances and a subsequent real exit still cleans once', async () => {
  const { page, hide } = fixture(); let destroyed = 0;
  page.mount(async () => ({}), () => () => ++destroyed, 'menus'); await flush();
  hide(true); assert.equal(destroyed, 0);
  hide(false); hide(false); assert.equal(destroyed, 1);
  let loaded = false;
  page.mount(() => { loaded = true; }, () => {}, 'late');
  await flush(); assert.equal(loaded, false);
});

test('import and initializer errors report only with explicit local debug configuration', async () => {
  for (const debug of [false, true]) {
    const { page, errors, view } = fixture(debug);
    await page.mount(() => Promise.reject(new Error('chunk failed')), () => assert.fail(), 'carousel').ready;
    await page.mount(async () => ({}), () => { throw new Error('init failed'); }, 'image').ready;
    assert.equal(errors.length, debug ? 2 : 0);
    if (debug) assert.match(errors[0][0], /carousel/);
    view.rdctComponents.debug = false;
    view.document.getElementById = () => ({ dataset: { rdctDebug: '1' } });
    reportRuntimeError('menus', new Error('theme'), view);
    assert.equal(errors.length, debug ? 3 : 1);
    page.dispose();
  }
});

test('cleanup failure does not prevent cleanup of other page features', async () => {
  const { page, errors } = fixture(true); let cleaned = 0;
  await page.mount(async () => ({}), () => () => { throw new Error('cleanup'); }, 'first').ready;
  await page.mount(async () => ({}), () => () => ++cleaned, 'second').ready;
  page.dispose(); assert.equal(cleaned, 1); assert.equal(errors.length, 1);
});
