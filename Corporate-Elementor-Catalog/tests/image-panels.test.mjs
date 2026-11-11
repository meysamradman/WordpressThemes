import test from 'node:test';
import assert from 'node:assert/strict';
import { createImagePanelMotion } from '../radmanct-components/src/js/components/curtain/images.js';

function fixture() {
  const preference = new EventTarget(); preference.matches = true;
  const root = { isConnected: true, dataset: { ratio: '3', duration: '400' }, ownerDocument: { defaultView: { matchMedia: () => preference } } };
  const items = [{}, {}, {}]; let loads = 0; let reverts = 0; let initial; const moves = [];
  const gsap = { context: () => ({ add: (fn) => fn(), revert: () => ++reverts }), set: (nodes, vars) => { initial = nodes.map((_, i) => vars.flexGrow(i)); }, quickTo: (item, key, options) => (value) => moves.push({ index: items.indexOf(item), key, options, value }) };
  return { root, items, preference, gsap, load: async () => { ++loads; return { gsap }; }, loads: () => loads, reverts: () => reverts, initial: () => initial, moves };
}

test('image panels load motion only in wide view, reuse setters and reset for reduced motion', async () => {
  const f = fixture(); const motion = createImagePanelMotion(f.root, f.items, f.load);
  motion.update(0, false); assert.equal(f.loads(), 0);
  motion.update(0, true); await Promise.resolve(); assert.deepEqual(f.initial(), [3, 1, 1]);
  motion.update(2, true); assert.deepEqual(f.moves.map(m => m.value), [1, 1, 3]); assert.equal(f.loads(), 1);
  assert.equal(f.moves[0].options.duration, .4);
  f.preference.matches = false; f.preference.dispatchEvent(new Event('change')); assert.equal(f.reverts(), 1);
  motion.update(1, true); assert.equal(f.loads(), 1);
  motion.destroy(); motion.destroy(); assert.equal(f.reverts(), 1);
});

test('late GSAP loading cannot mount into released or detached panels', async () => {
  for (const detach of [false, true]) {
    const f = fixture(); let resolve; const load = () => new Promise(done => { resolve = done; });
    const motion = createImagePanelMotion(f.root, f.items, load);
    motion.update(0, true);
    if (detach) f.root.isConnected = false; else motion.destroy();
    resolve({ gsap: f.gsap }); await Promise.resolve();
    assert.equal(f.initial(), undefined); motion.destroy();
  }
});
