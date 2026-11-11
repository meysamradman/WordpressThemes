import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeFlipBox} from '../radmanct-components/src/js/components/flipbox.js';

function fixture() {
  const node = () => {
    const target = new EventTarget(); const attributes = new Map();
    Object.assign(target, {hidden: true, inert: false, dataset: {}, getAttribute: (key) => attributes.get(key) ?? null, setAttribute: (key, value) => attributes.set(key, value), removeAttribute: (key) => attributes.delete(key), contains: (child) => child === target});
    return target;
  };
  const root = node(); const button = node(); const front = node(); const back = node();
  front.dataset.rdctFbFace = 'front'; back.dataset.rdctFbFace = 'back';
  const classes = new Set(); const query = new EventTarget(); query.matches = true;
  const doc = {activeElement: null, defaultView: {matchMedia: () => query}};
  Object.assign(root, {ownerDocument: doc, querySelector: () => button, querySelectorAll: () => [front, back], contains: (target) => [root, button, front, back].includes(target), classList: {add: (key) => classes.add(key), remove: (...keys) => keys.forEach((key) => classes.delete(key)), toggle: (key, on) => on ? classes.add(key) : classes.delete(key)}});
  button.focus = () => {doc.activeElement = button;};
  return {root, button, front, back, classes, query, doc};
}

test('late Flip Box blur cannot reapply inaccessible state after teardown', async () => {
  const {root, button, front, back, classes} = fixture();
  const cleanup = initializeFlipBox(root);
  root.dispatchEvent(new Event('focusout'));
  cleanup(); await Promise.resolve();
  assert.equal(button.getAttribute('aria-expanded'), null);
  assert.equal(back.inert, false); assert.equal(front.getAttribute('aria-hidden'), null);
  assert.equal(back.getAttribute('aria-hidden'), null); assert.equal(classes.size, 0);
});

test('Flip Box keeps keyboard access, ignores touch hover and allows clean remount', () => {
  const {root, button, back, query} = fixture();
  const cleanup = initializeFlipBox(root);
  const touch = new Event('pointerenter'); touch.pointerType = 'touch'; root.dispatchEvent(touch);
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  button.dispatchEvent(new Event('click')); assert.equal(back.inert, false);
  const escape = new Event('keydown'); escape.key = 'Escape'; root.dispatchEvent(escape);
  assert.equal(button.getAttribute('aria-expanded'), 'false');
  cleanup(); cleanup(); const remount = initializeFlipBox(root);
  button.dispatchEvent(new Event('click')); assert.equal(button.getAttribute('aria-expanded'), 'true');
  remount(); query.dispatchEvent(new Event('change'));
  assert.equal(button.getAttribute('aria-expanded'), null);
});

test('pointer preference changes close hover state without hiding a focused back face', () => {
  const {root, button, back, query, doc} = fixture(); const cleanup = initializeFlipBox(root);
  const enter = new Event('pointerenter'); enter.pointerType = 'mouse'; root.dispatchEvent(enter);
  assert.equal(button.getAttribute('aria-expanded'), 'true');
  doc.activeElement = back; query.matches = false; query.dispatchEvent(new Event('change'));
  assert.equal(button.getAttribute('aria-expanded'), 'true');
  doc.activeElement = null; query.dispatchEvent(new Event('change'));
  assert.equal(button.getAttribute('aria-expanded'), 'false'); cleanup();
});
