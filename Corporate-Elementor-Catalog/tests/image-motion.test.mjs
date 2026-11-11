import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeImageVisibility} from '../radmanct-components/src/js/components/image/visibility.js';
import {createImageReveal} from '../radmanct-components/src/js/components/image/reveal.js';
import {initializeImageTilt} from '../radmanct-components/src/js/components/image/tilt.js';

test('image motion keeps RTL reveal, reduced motion and cleanup local to its instance', () => {
  const query = new EventTarget(); query.matches = true;
  const pointer = new EventTarget(); pointer.matches = true;
  let intersection; let disconnected = 0; let next = 0;
  const timers = new Map(); const frames = new Map();
  const view = new EventTarget(); Object.assign(view,{
    matchMedia: (key) => key.includes('prefers-reduced') ? query : pointer,
    getComputedStyle: () => ({direction:'rtl',getPropertyValue:() => '0ms'}),
    setTimeout:(fn) => { timers.set(++next,fn); return next; }, clearTimeout:(id) => timers.delete(id),
    requestAnimationFrame:(fn) => { frames.set(++next,fn); return next; }, cancelAnimationFrame:(id) => frames.delete(id),
    IntersectionObserver:class { constructor(fn) { intersection = fn; } observe() {} disconnect() { ++disconnected; } },
  });
  const classes = new Set(); const styles = new Map();
  const root = new EventTarget(); Object.assign(root,{isConnected:true,dataset:{reveal:'start',tilt:'1'},ownerDocument:{defaultView:view},matches:() => false,
    classList:{add:(...names) => names.forEach((n) => classes.add(n)),remove:(...names) => names.forEach((n) => classes.delete(n)),contains:(n) => classes.has(n),toggle:(n,on) => on ? classes.add(n) : classes.delete(n)},
    querySelector:() => ({style:{removeProperty:(key) => styles.delete(key),setProperty:(key,value) => styles.set(key,value)},getBoundingClientRect:() => ({width:300,height:200,left:0,top:0})}),
  });
  const visibilityCleanup = initializeImageVisibility(root, createImageReveal(root)); const tiltCleanup = initializeImageTilt(root);
  let alive = true; const cleanup = () => { if (!alive) return; alive = false; visibilityCleanup(); tiltCleanup(); };
  const countBeforeDuplicate = disconnected;
  assert.equal(disconnected, countBeforeDuplicate);
  assert.equal(root.dataset.reveal,'rl'); intersection([{isIntersecting:true}]);
  assert.equal(classes.has('is-revealing'),true);
  root.dispatchEvent(new Event('focusin')); assert.equal(classes.has('is-revealing'),false); assert.equal(timers.size,0);
  query.matches = false; query.dispatchEvent(new Event('change'));
  assert.equal(classes.has('is-enhanced'),false);
  cleanup(); const count = disconnected; cleanup(); assert.equal(disconnected,count);
  assert.equal(root.dataset.reveal,'start'); assert.equal(frames.size,0);
});
