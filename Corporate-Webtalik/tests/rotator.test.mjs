import {test} from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import {runInNewContext} from 'node:vm';

test('rotating text pauses offscreen, on focus and reduced motion, then cleans up', async () => {
  const source = (await readFile(new URL('../radmanct/src/js/ui/rotator.js', import.meta.url), 'utf8')).replace(/^import .*;\r?\n/gm, '').replace(/export /g, '');
  const events = () => {
    const handlers = new Map();
    return {addEventListener: (key, fn) => handlers.set(key, fn), removeEventListener: (key) => handlers.delete(key), emit: (key, value = {}) => handlers.get(key)?.(value), handlers};
  };
  const node = (textContent = '') => {
    const attrs = new Map(); const styles = new Map();
    return {...events(), textContent, dataset: {}, getAttribute: (key) => attrs.get(key) ?? null, setAttribute: (key, value) => attrs.set(key, value), removeAttribute: (key) => attrs.delete(key), style: {setProperty: (key, value) => styles.set(key, value)}};
  };
  const words = [node('Design'), node('Build'), node('Deliver')];
  const play = node(); const rotator = node(); rotator.querySelectorAll = () => words;
  const classes = new Set(); const reduced = {...events(), matches: false};
  let intersect; let disconnected = false; let tick; let pending = false;
  const doc = {...events(), hidden: false, defaultView: {matchMedia: () => reduced, IntersectionObserver: class {constructor(callback) {intersect = callback;} observe() {} disconnect() {disconnected = true;}}}};
  const root = {...node(), ownerDocument: doc, dataset: {rdcTxRvl: 'rotate', wordDelay: 2}, querySelector: (selector) => selector === '.rdc-tx__rtr' ? rotator : play, contains: () => false, classList: {add:key=>classes.add(key),toggle: (key, enabled) => enabled ? classes.add(key) : classes.delete(key), remove: (...keys) => keys.forEach(key=>classes.delete(key))}};
  const context = {WeakMap, Number, Math, setTimeout: (fn) => {tick = fn; pending = true; return 1;}, clearTimeout: () => {pending = false;}};
  runInNewContext(source, context);
  const transitions = []; let killed = 0;
  const gsap = {set:()=>({kill(){}}),timeline:({defaults})=>({kill(){killed++;},fromTo(target,from,to){transitions.push({target,from,to,defaults});return this;}})};
  const cleanup = context.initializeRotator(root,{gsap});
  assert.equal(context.initializeRotator(root), cleanup);
  assert.equal(pending, false);
  intersect([{isIntersecting: true}]); assert.equal(pending, true);
  tick(); assert.equal(words[1].dataset.current, 'true');
  assert.equal(transitions[0].to.rotationX,95); assert.equal(transitions[1].from.rotationX,-95);
  assert.equal(transitions[0].defaults.ease,'power4.inOut'); assert.equal(transitions[0].defaults.duration,.8);
  root.emit('focusin'); assert.equal(pending, false);
  root.emit('focusout', {relatedTarget: null}); assert.equal(pending, true);
  play.emit('click'); assert.equal(pending, false);
  play.emit('click'); assert.equal(pending, true);
  reduced.matches = true; reduced.emit('change'); assert.equal(pending, false); assert.equal(classes.size, 0);
  reduced.matches = false; reduced.emit('change'); assert.equal(pending, true);
  doc.hidden = true; doc.emit('visibilitychange'); assert.equal(pending, false);
  cleanup(); cleanup();
  assert.equal(disconnected, true); assert.equal(root.handlers.size, 0); assert.equal(doc.handlers.size, 0); assert.equal(play.handlers.size, 0);
  assert.equal(rotator.getAttribute('role'), null); assert.equal(words[1].getAttribute('aria-hidden'), null);
  assert.ok(killed > 0);
});
