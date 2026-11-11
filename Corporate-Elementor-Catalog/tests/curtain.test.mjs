import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeCurtain } from '../radmanct-components/src/js/components/curtain.js';

function fixture(trigger = 'click', rtl = false) {
  const doc = { activeElement: null };
  class Node extends EventTarget {
    constructor() { super(); this.attrs = new Map(); const classes = new Set(); this.classList = { contains: (v) => classes.has(v), add: (v) => classes.add(v), remove: (v) => classes.delete(v), toggle: (v, on) => on ? classes.add(v) : classes.delete(v) }; this.ownerDocument = doc; }
    setAttribute(k, v) { this.attrs.set(k, v); }
    getAttribute(k) { return this.attrs.get(k); }
    removeAttribute(k) { this.attrs.delete(k); }
    contains(node) { return node === this || (this.children || []).some((child) => child.contains(node)); }
    focus() { doc.activeElement = this; }
    getBoundingClientRect() { return { width: 460 }; }
  }
  const desktop = new EventTarget(); desktop.matches = true;
  const timers = new Map(); let timerId = 0;
  const frames = new Map(); let frameId = 0; let disconnected = 0;
  const view = new EventTarget(); Object.assign(view, { matchMedia: () => desktop, setTimeout: (fn) => { timers.set(++timerId, fn); return timerId; }, clearTimeout: (id) => timers.delete(id), getComputedStyle: () => ({ direction: rtl ? 'rtl' : 'ltr' }), requestAnimationFrame: (callback) => { frames.set(++frameId, callback); return frameId; }, cancelAnimationFrame: (id) => frames.delete(id), ResizeObserver: class { observe() {} disconnect() { ++disconnected; } } });
  doc.defaultView = view;
  const root = new Node(); root.getBoundingClientRect = () => ({ width: 1200 }); root.dataset = { trigger, activeItem: '2' }; const styles = new Map(); root.style = { setProperty: (k, v) => styles.set(k, v), removeProperty: (k) => styles.delete(k) };
  const entries = Array.from({ length: 3 }, () => { const item = new Node(); item.classList.add('rdct-curtain__item'); const button = new Node(); const panel = new Node(); item.children = [button, panel]; item.querySelector = (selector) => selector.includes('trigger') ? button : panel; return { item, button, panel }; });
  root.children = entries.map(({ item }) => item);
  const fire = (node, type, properties = {}) => { const event = new Event(type, { cancelable: true }); Object.assign(event, properties); node.dispatchEvent(event); return event; };
  const flush = () => { const callbacks = [...frames.values()]; frames.clear(); callbacks.forEach((callback) => callback()); };
  const hover = (index, x = index * 100) => { const event = new Event('pointermove'); Object.assign(event, { pointerType: 'mouse', clientX: x, clientY: 20 }); Object.defineProperty(event, 'target', { value: entries[index].item }); root.dispatchEvent(event); };
  const settle = () => { const pending = [...timers.values()]; timers.clear(); pending.forEach((fn) => fn()); };
  return { root, entries, doc, desktop, styles, frames, timers, hover, settle, fire, flush, disconnected: () => disconnected };
}

test('curtain switches one panel, protects inactive focus and restores the no-JS presentation on cleanup', () => {
  const page = fixture(); const cleanup = initializeCurtain(page.root); assert.equal(initializeCurtain(page.root), cleanup);
  assert.equal(page.entries[1].button.getAttribute('aria-expanded'), 'true'); assert.equal(page.entries[0].panel.inert, true);
  page.doc.activeElement = page.entries[1].panel;
  page.fire(page.entries[2].button, 'click');
  assert.equal(page.entries[2].panel.inert, false); assert.equal(page.entries[1].panel.inert, true); assert.equal(page.doc.activeElement, page.entries[1].button);
  page.flush(); assert.equal(page.styles.get('--rdct-curtain-content-width'), '1024px');
  cleanup(); cleanup(); assert.equal(page.disconnected(), 1); assert.equal(page.frames.size, 0);
  for (const entry of page.entries) { assert.equal(entry.panel.inert, false); assert.equal(entry.button.getAttribute('aria-expanded'), 'true'); }
  assert.equal(page.root.classList.contains('is-enhanced'), false);
});

test('curtain supports RTL keyboard movement, touch click and hover without stealing a focused panel', () => {
  const page = fixture('hover', true); const cleanup = initializeCurtain(page.root); page.flush();
  assert.equal(page.fire(page.entries[1].button, 'keydown', { key: 'ArrowLeft' }).defaultPrevented, true);
  assert.equal(page.doc.activeElement, page.entries[2].button);
  page.hover(0); page.settle(); assert.equal(page.entries[2].panel.inert, false);
  page.doc.activeElement = null; page.hover(0); page.settle(); assert.equal(page.entries[0].panel.inert, false);
  page.desktop.matches = false; page.fire(page.entries[1].item, 'pointerenter', { pointerType: 'touch' }); assert.equal(page.entries[0].panel.inert, false);
  page.fire(page.entries[1].button, 'click'); assert.equal(page.entries[1].panel.inert, false);
  page.fire(page.desktop, 'change'); page.flush(); assert.equal(page.styles.has('--rdct-curtain-content-width'), false);
  cleanup();
});

test('hover ignores stationary layout changes, cancels on leave and keeps final content width stable', () => {
  const page = fixture('hover'); const cleanup = initializeCurtain(page.root); page.flush();
  page.hover(0, 50); assert.equal(page.entries[1].panel.inert, false); page.settle(); page.flush();
  assert.equal(page.entries[0].panel.inert, false); assert.equal(page.styles.get('--rdct-curtain-content-width'), '1024px');
  page.hover(2, 50); page.settle(); assert.equal(page.entries[0].panel.inert, false);
  page.hover(2, 60); page.fire(page.root, 'pointerleave'); page.settle(); assert.equal(page.entries[0].panel.inert, false);
  page.hover(2, 80); cleanup(); assert.equal(page.timers.size, 0);
});
