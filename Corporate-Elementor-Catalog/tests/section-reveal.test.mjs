import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { runInNewContext } from 'node:vm';
import { motionOptions, revealClip } from '../radmanct/src/js/components/container-motion-options.js';

test('logical reveal respects inherited direction while explicit directions stay physical', () => {
  assert.equal(revealClip('start', false), revealClip('left', true));
  assert.equal(revealClip('start', true), revealClip('right', false));
  assert.equal(revealClip('end', true), revealClip('left', false));
  const options = motionOptions({ reveal: 'yes', reveal_start: 50, reveal_end: 900, reveal_scrub: 'bad', reveal_direction: 'run()' });
  assert.equal(options.reveal, true); assert.equal(options.reveal_end, 45);
  assert.equal(options.reveal_scrub, 1); assert.equal(options.reveal_direction, 'start');
});

test('scroll reveal restores focus access, cleans listeners, and waits for editor Play', async () => {
  const source = (await readFile(new URL('../radmanct/src/js/components/container-motion.js', import.meta.url), 'utf8')).replace(/^import .*;\r?\n/gm, '').replace(/export /g, '');
  const calls = []; const listeners = new Map(); let killed = 0; let reverted = 0; let reduce = false;
  const root = { isConnected: true, hasAttribute: () => false, matches: () => false,
    ownerDocument: { defaultView: { getComputedStyle: () => ({ direction: 'rtl', clipPath: 'none' }) } },
    addEventListener: (key, fn) => listeners.set(key, fn), removeEventListener: (key) => listeners.delete(key) };
  const context = { motionOptions, revealClip, motionTargets: () => [], ScrollTrigger: {}, gsap: {
    registerPlugin() {}, set: (...args) => calls.push(args),
    fromTo: (...args) => { calls.push(args); return { kill: () => ++killed, scrollTrigger: { kill: () => ++killed } }; },
    matchMedia: () => { let cleanup; return { add: (_, fn) => { if (!reduce) cleanup = fn(); }, revert: () => { cleanup?.(); ++reverted; } }; },
  } };
  runInNewContext(`${source}\nthis.initialize = initializeContainerMotion;`, context);
  const cleanup = context.initialize(root, { reveal: true });
  assert.equal(calls[0][1].clipPath, 'inset(0 0 0 100%)');
  assert.equal(calls[0][2].scrollTrigger.start, 'top 90%');
  assert.equal(calls[0][2].scrollTrigger.end, 'top 50%');
  assert.equal(calls[0][2].scrollTrigger.scrub, 1);
  listeners.get('focusin')(); assert.equal(killed, 2); assert.equal(calls.at(-1)[1].clipPath, 'none');
  cleanup(); assert.equal(listeners.size, 0); assert.equal(reverted, 1);
  const count = calls.length;
  context.initialize(root, { reveal: true }, true); assert.equal(calls.length, count);
  context.initialize(root, { reveal: true }, true, true); assert.equal(calls.at(-1)[2].scrollTrigger, undefined);
  reduce = true; const before = calls.length;
  context.initialize(root, { reveal: true }); assert.equal(calls.length, before); assert.equal(listeners.size, 0);
});
