import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { runInNewContext } from 'node:vm';
import { revealTween } from '../radmanct-components/src/js/components/text/reveal-settings.js';

const source = (await readFile(new URL('../radmanct-components/src/js/components/text/lines.js', import.meta.url), 'utf8')).replace(/^import .*;\r?\n/gm, '').replace(/export /g, '');
const tick = () => new Promise((resolve) => setImmediate(resolve));

function fixture({ reduced = false, pending = false, fail = false, mask = true } = {}) {
  class Node {
    constructor(text = '') { this.value = text; this.children = []; this.attributes = {}; this.isConnected = true; }
    get textContent() { return this.children.length ? this.children.map((node) => node.textContent).join('') : this.value; }
    set textContent(value) { this.value = value; this.children = []; }
    setAttribute(key, value) { this.attributes[key] = value; }
    replaceChildren(...nodes) { this.value = ''; this.children = nodes; }
  }
  let ready; let split; let cleanup; let reverts = 0; let mediaReverts = 0;
  const animations = []; const errors = [];
  const doc = { createElement: () => new Node(), fonts: { ready: pending ? new Promise((resolve) => { ready = resolve; }) : Promise.resolve() } };
  const text = '\u0645\u06cc\u200c\u062e\u0648\u0627\u0647\u0645 English 123\n  Next';
  const root = new Node(text); root.ownerDocument = doc;
  root.dataset = { rdctTextReveal: 'scroll', revealSplit: 'lines', revealMask: mask ? 'true' : 'false', revealStagger: '0', revealFrom: 'end' };
  const gsap = {
    registerPlugin() {}, fromTo(lines, from, to) { const animation = { lines, from, to, totalTime() {}, revert() {} }; animations.push(animation); return animation; },
    matchMedia: () => ({ add(query, callback) { if (reduced) return; const context = { add(key, callback) { context[key] = callback; } }; cleanup = callback(context); }, revert() { ++mediaReverts; cleanup?.(); } }),
  };
  const SplitText = { create(target, options) {
    if (fail) throw new Error('Split failed');
    split = { target, options, lines: [new Node(text)], revert() { ++reverts; } };
    split.animation = options.onSplit(split); return split;
  } };
  const context = { gsap, ScrollTrigger: {}, SplitText, revealTween, reportRuntimeError: (...args) => errors.push(args) };
  runInNewContext(source, context);
  const initialize = () => context.initializeTextLines(root, context);
  return { root, text, initialize, animations, errors, ready, get split() { return split; }, get reverts() { return reverts; }, get mediaReverts() { return mediaReverts; } };
}

test('line reveal uses native responsive splitting, masks whole lines, preserves Persian joins and one accessible text', async () => {
  const f = fixture(); const destroy = f.initialize(); assert.equal(f.initialize(), destroy); await tick();
  assert.equal(f.split.options.type, 'lines'); assert.equal(f.split.options.tag, 'span'); assert.equal(f.split.options.autoSplit, true); assert.equal(f.split.options.reduceWhiteSpace, false);
  assert.equal(f.split.options.mask, 'lines'); assert.equal(f.split.options.aria, 'none');
  assert.equal(f.split.options.linesClass, 'rdct-tr__line');
  assert.equal(f.root.children[0].textContent, f.text); assert.equal(f.root.children[1].attributes['aria-hidden'], 'true');
  assert.equal(f.animations[0].from.yPercent, 100); assert.equal(f.animations[0].to.yPercent, 0);
  assert.equal(f.animations[0].to.stagger.each, 0); assert.equal(f.animations[0].to.stagger.from, 'end');
  const rebuilt = { lines: [{ textContent: 'New line' }] };
  assert.equal(f.split.options.onSplit(rebuilt), f.animations[1]);
  assert.equal(f.animations[1].lines, rebuilt.lines);
  destroy(); destroy(); assert.equal(f.reverts, 1); assert.equal(f.mediaReverts, 1); assert.equal(f.root.textContent, f.text);
  f.split.options.onSplit(rebuilt); assert.equal(f.animations.length, 2);
});

test('reduced motion, excessive content, wrong modes and canceled font waits retain readable plain text', async () => {
  const reduced = fixture({ reduced: true }); reduced.initialize(); await tick(); assert.equal(reduced.split, undefined); assert.equal(reduced.root.textContent, reduced.text);
  const pending = fixture({ pending: true }); pending.initialize()(); pending.ready(); await tick(); assert.equal(pending.split, undefined); assert.equal(pending.root.textContent, pending.text);
  const oversized = fixture(); oversized.root.textContent = 'word '.repeat(401); oversized.initialize(); await tick(); assert.equal(oversized.split, undefined);
  const fill = fixture(); fill.root.dataset.rdctTextReveal = 'fill'; fill.initialize(); await tick(); assert.equal(fill.split, undefined);
});

test('unmasked lines retain opacity-only motion and a split failure restores the original content', async () => {
  const plain = fixture({ mask: false }); plain.initialize(); await tick(); assert.equal(plain.split.options.mask, undefined); assert.equal(plain.animations[0].from.yPercent, undefined);
  const failure = fixture({ fail: true }); const destroy = failure.initialize(); await tick(); assert.equal(failure.root.textContent, failure.text); assert.equal(failure.errors.length, 1); destroy();
});
