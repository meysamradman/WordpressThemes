import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';

test('pending menu hover import cannot mount a removed or released root', async () => {
  const source = await readFile(new URL('../radmanct/src/js/navigation/hover.js', import.meta.url), 'utf8');
  for (const mode of ['removed', 'released', 'mounted']) {
    let resolve; let mounted = 0; let destroyed = 0;
    const pending = new Promise((done) => {resolve = done;});
    const context = vm.createContext({load: () => pending, reportRuntimeError: () => assert.fail('Unexpected load failure')});
    vm.runInContext(source.replace(/^import .*;\r?\n/gm, '').replace(/^const effects = .*;$/m, 'const effects = {roll: load};').replace('export function mountMenuHover', 'function mountMenuHover') + '\nglobalThis.mountMenuHover = mountMenuHover;', context);
    const root = {dataset: {menuHover: 'roll'}, isConnected: true};
    const cleanup = context.mountMenuHover(root);
    if (mode === 'removed') root.isConnected = false;
    if (mode === 'released') cleanup();
    resolve({mount: () => {mounted++; return () => {destroyed++;};}});
    await pending; await Promise.resolve();
    assert.equal(mounted, mode === 'mounted' ? 1 : 0);
    cleanup(); cleanup(); assert.equal(destroyed, mode === 'mounted' ? 1 : 0);
  }
});

test('rolling menu text handles pointer, keyboard, reduced motion and teardown', async () => {
  const source = await readFile(new URL('../radmanct/src/js/navigation/hover/roll.js', import.meta.url), 'utf8');
  const reduced = new EventTarget(); reduced.matches = false;
  const fine = new EventTarget(); fine.matches = true;
  const link = new EventTarget(); link.contains = (node) => node === link;
  const text = {}; const copy = {}; const states = []; let reverted = 0;
  const root = {querySelectorAll: () => [label]};
  const label = {closest: (selector) => selector === 'a' ? link : root, querySelector: (selector) => selector.endsWith('__text') ? text : copy};
  const timeline = {to(target, options) {assert.equal(target, text); assert.equal(options.y, -4); assert.equal(options.duration, .45); return this;}, fromTo(target, start, end) {assert.equal(target, copy); assert.equal(start.y, 4); assert.equal(end.y, 0); assert.equal(start.yPercent, 100); assert.equal(end.yPercent, 0); return this;}, play() {states.push('play');}, reverse() {states.push('reverse');}, pause(value) {assert.equal(value, 0); states.push('reset');}};
  const context = vm.createContext({gsap: {registerPlugin() {}, context(fn) {fn(); return {revert() {reverted++;}};}, timeline: () => timeline}, CustomEase: {create(name, curve) {assert.equal(curve, '.5,1.5,.35,1'); return name;}}, document: {activeElement: null}, matchMedia: (query) => query.includes('reduced') ? reduced : fine});
  vm.runInContext(source.replace(/^import .*;\r?\n/gm, '').replace('export function mount', 'function mount') + '\nglobalThis.mount = mount;', context);
  const cleanup = context.mount(root);
  const fire = (type, extra = {}) => {const event = new Event(type); Object.assign(event, extra); link.dispatchEvent(event);};
  fire('pointerenter', {pointerType: 'mouse'}); assert.equal(states.at(-1), 'play');
  fire('pointerleave'); assert.equal(states.at(-1), 'reverse');
  fire('focusin'); fire('pointerleave'); assert.equal(states.at(-1), 'play');
  fire('focusout', {relatedTarget: null}); assert.equal(states.at(-1), 'reverse');
  fire('pointerenter', {pointerType: 'touch'}); assert.equal(states.at(-1), 'reverse');
  reduced.matches = true; reduced.dispatchEvent(new Event('change')); assert.equal(states.at(-1), 'reset');
  fire('focusin'); assert.equal(states.at(-1), 'reset');
  cleanup(); assert.equal(reverted, 1); const length = states.length;
  fire('focusin'); reduced.dispatchEvent(new Event('change')); assert.equal(states.length, length);
});
