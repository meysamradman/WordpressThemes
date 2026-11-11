import test from 'node:test';
import assert from 'node:assert/strict';
import {initializePointerTilt} from '../shared/frontend/pointer-tilt.js';

test('shared tilt batches pointer updates and resets on viewport, preference and teardown', () => {
  const root = new EventTarget(); const view = new EventTarget(); const query = new EventTarget(); query.matches = true;
  const frames = new Map(); const styles = new Map(); let next = 0; let reads = 0;
  Object.assign(view, {matchMedia: () => query, requestAnimationFrame: (fn) => {frames.set(++next, fn); return next;}, cancelAnimationFrame: (id) => frames.delete(id)});
  root.ownerDocument = {defaultView: view};
  const target = {style: {setProperty: (key, value) => styles.set(key, value), removeProperty: (key) => styles.delete(key)}, getBoundingClientRect: () => {reads++; return {left: 0, top: 0, width: 100, height: 100};}};
  const cleanup = initializePointerTilt(root, target, {amount: () => 5, xProperty: '--x', yProperty: '--y'});
  const move = (x, y, pointerType = 'mouse') => {const event = new Event('pointermove'); Object.assign(event, {clientX: x, clientY: y, pointerType}); root.dispatchEvent(event);};
  const flush = () => {const pending = [...frames.values()]; frames.clear(); pending.forEach((fn) => fn());};
  move(0, 0, 'touch'); assert.equal(frames.size, 0);
  move(10, 10); move(100, 0); assert.equal(frames.size, 1); flush();
  assert.equal(styles.get('--x'), '5deg'); assert.equal(styles.get('--y'), '5deg'); assert.equal(reads, 1);
  move(300, -300); flush(); assert.equal(styles.get('--x'), '5deg'); assert.equal(reads, 1);
  view.dispatchEvent(new Event('scroll')); assert.equal(styles.size, 0);
  move(50, 50); flush(); assert.equal(reads, 2); assert.equal(styles.get('--x'), '0deg');
  move(0, 0); query.matches = false; query.dispatchEvent(new Event('change')); assert.equal(frames.size, 0); assert.equal(styles.size, 0);
  move(0, 0); assert.equal(frames.size, 0);
  query.matches = true; move(0, 0); cleanup(); cleanup(); assert.equal(frames.size, 0);
  move(0, 0); assert.equal(frames.size, 0);
});
