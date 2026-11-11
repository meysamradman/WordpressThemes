import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { runInNewContext } from 'node:vm';
import { revealTween } from '../radmanct-components/src/js/components/text/reveal-settings.js';

const source = (await readFile(new URL('../radmanct-components/src/js/components/text/reveal.js', import.meta.url), 'utf8')).replace(/^import .*;\r?\n/gm, '').replace(/export /g, '');

test('animation vendor imports share one pending load and a failure permits retry', async (t) => {
  const previous = globalThis.window;
  t.after(() => { if (previous === undefined) delete globalThis.window; else globalThis.window = previous; });
  globalThis.window = { rdctComponents: { animationUrl: 'data:text/javascript,export const gsap={};export const ScrollTrigger={};' } };
  const shared = await import('../radmanct-components/src/js/animation.js?shared-contract');
  const first = shared.loadAnimation();
  assert.equal(shared.loadAnimation(), first);
  assert.ok((await first).gsap);
  window.rdctComponents.animationUrl = 'data:text/javascript,throw new Error("vendor failed");';
  const retry = await import('../radmanct-components/src/js/animation.js?retry-contract');
  await assert.rejects(retry.loadAnimation(), /vendor failed/);
  window.rdctComponents.animationUrl = 'data:text/javascript,export const gsap={};export const ScrollTrigger={};';
  assert.ok((await retry.loadAnimation()).ScrollTrigger);
});

function fixture({ reduced = false, deferred = false } = {}) {
  class Node {
    constructor(text = '') { this.value = text; this.children = []; this.attributes = {}; this.dataset = {}; this.isConnected = true; }
    get textContent() { return this.children.length ? this.children.map((node) => node.textContent).join('') : this.value; }
    set textContent(value) { this.value = value; this.children = []; }
    setAttribute(key, value) { this.attributes[key] = value; }
    append(node) { this.children.push(node); }
    replaceChildren(...nodes) { this.value = ''; this.children = nodes; }
  }
  let ready;
  const fonts = deferred ? new Promise((resolve) => { ready = resolve; }) : Promise.resolve();
  const document = { fonts: { ready: fonts }, createElement: () => new Node(), createTextNode: (text) => new Node(text) };
  const animations = []; const media = [];
  const gsap = { registerPlugin() {}, fromTo: (words, from, to) => animations.push({ words, from, to }), matchMedia() {
    const entry = { add(query, callback) { if (reduced) return; const context = { add: (key, fn) => { context[key] = fn; } }; entry.cleanup = callback(context); }, revert() { entry.cleanup?.(); } };
    media.push(entry); return entry;
  } };
  const context = { revealTween, gsap, ScrollTrigger: {}, document, WeakMap, Promise, Number, Array };
  runInNewContext(source, context);
  const initialize = context.initializeTextReveal;
  context.initializeTextReveal = (root) => initialize(root, { gsap, ScrollTrigger: context.ScrollTrigger });
  const text = '\u0645\u06cc\u200c\u062e\u0648\u0627\u0647\u0645 English 123\nNext';
  const root = new Node(text); root.dataset = { rdctTextReveal: 'scroll', initialOpacity: '0.25' };
  return { ...context, root, text, animations, media, ready };
}

test('scroll reveal preserves Persian joins and whitespace, initializes once and restores text', async () => {
  const f = fixture(); const destroy = f.initializeTextReveal(f.root);
  assert.equal(f.initializeTextReveal(f.root), destroy);
  await Promise.resolve();
  assert.equal(f.media.length, 1); assert.equal(f.animations.length, 1);
  assert.equal(f.animations[0].words[0].textContent, f.text.split(' ')[0]);
  assert.equal(f.root.children[0].textContent, f.text);
  assert.equal(f.root.children[1].textContent, f.text);
  assert.equal(f.root.children[1].attributes['aria-hidden'], 'true');
  destroy(); destroy(); assert.equal(f.root.textContent, f.text);
  assert.equal(f.root.children.length, 0);
});

test('reduced motion, hover and destroyed pending font loads never hide text', async () => {
  const reduced = fixture({ reduced: true }); reduced.initializeTextReveal(reduced.root);
  await Promise.resolve(); assert.equal(reduced.animations.length, 0); assert.equal(reduced.root.textContent, reduced.text);
  const hover = fixture(); hover.root.dataset.rdctTextReveal = 'hover'; hover.initializeTextReveal(hover.root);
  assert.equal(hover.media.length, 0);
  const oversized = fixture(); oversized.root.textContent = 'word '.repeat(401); oversized.initializeTextReveal(oversized.root);
  assert.equal(oversized.media.length, 0);
  const pending = fixture({ deferred: true }); const destroy = pending.initializeTextReveal(pending.root); destroy(); pending.ready();
  await Promise.resolve(); assert.equal(pending.animations.length, 0); assert.equal(pending.root.children.length, 0);
});

test('each reveal uses its own animation controls and rejects invalid values', async () => {
  const custom = fixture();
  Object.assign(custom.root.dataset, { finalOpacity: '0.85', revealStart: 'top 70%', revealEnd: 'bottom bottom', revealFrom: 'end', revealScrub: '3', revealStagger: '0' });
  custom.initializeTextReveal(custom.root); await Promise.resolve();
  const { to } = custom.animations[0];
  assert.equal(to.opacity, 0.85); assert.equal(to.stagger.from, 'end'); assert.equal(to.stagger.each, 0);
  assert.equal(to.scrollTrigger.start, 'top 70%'); assert.equal(to.scrollTrigger.end, 'bottom bottom'); assert.equal(to.scrollTrigger.scrub, 3);
  const invalid = fixture();
  Object.assign(invalid.root.dataset, { finalOpacity: 'NaN', initialOpacity: '', revealStart: 'javascript:bad()', revealEnd: '<script>', revealFrom: '__proto__', revealScrub: 'Infinity', revealStagger: '-1' });
  invalid.initializeTextReveal(invalid.root); await Promise.resolve();
  const fallback = invalid.animations[0];
  assert.equal(fallback.from.opacity, 0.25); assert.equal(fallback.to.opacity, 1);
  assert.equal(fallback.to.stagger.from, 'start'); assert.equal(fallback.to.stagger.each, 0);
  assert.equal(fallback.to.scrollTrigger.start, 'top 80%'); assert.equal(fallback.to.scrollTrigger.end, 'bottom 35%'); assert.equal(fallback.to.scrollTrigger.scrub, 0.5);
});
