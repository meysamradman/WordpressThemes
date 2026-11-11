import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeColorScheme } from '../radmanct/src/js/color-scheme.js';
import { cursorFollowAlpha } from '../radmanct/src/js/cursor.js';

function fixture({scheme = 'light', mode = 'system', blocked = false} = {}) {
  const media = new EventTarget(); media.matches = false;
  const view = new EventTarget(); view.matchMedia = () => media;
  view.CustomEvent = class extends Event { constructor(type, options) { super(type); this.detail = options.detail; } };
  const storage = new Map();
  view.localStorage = { getItem: key => { if (blocked) throw Error('Blocked'); return storage.get(key); }, setItem: (key, value) => { if (blocked) throw Error('Blocked'); storage.set(key, value); } };
  const buttons = [0, 1].map(() => ({setAttribute(key, value) { this[key] = value; }}));
  const doc = new EventTarget(); doc.documentElement = {dataset: {rdctScheme: scheme, rdctSchemeMode: mode}};
  doc.querySelectorAll = () => buttons;
  const click = () => { const event = new Event('click'); Object.defineProperty(event, 'target', {value: {closest: () => buttons[0]}}); doc.dispatchEvent(event); };
  return {doc, view, media, buttons, storage, click};
}

test('one color scheme synchronizes every toggle, persists choice and cleans up', () => {
  const f = fixture(); const cleanup = initializeColorScheme(f.doc, f.view);
  f.click(); assert.equal(f.doc.documentElement.dataset.rdctScheme, 'dark');
  assert.equal(f.storage.get('rdct-color-scheme'), 'dark');
  assert.ok(f.buttons.every(button => button['aria-pressed'] === 'true'));
  f.media.dispatchEvent(new Event('change'));
  assert.equal(f.doc.documentElement.dataset.rdctScheme, 'dark');
  cleanup(); f.click(); assert.equal(f.doc.documentElement.dataset.rdctScheme, 'dark');
});

test('system preference stays live and blocked storage does not prevent manual switching', () => {
  const f = fixture({blocked:true}); const cleanup = initializeColorScheme(f.doc, f.view);
  f.media.matches = true; f.media.dispatchEvent(new Event('change'));
  assert.equal(f.doc.documentElement.dataset.rdctScheme, 'dark');
  f.click(); assert.equal(f.doc.documentElement.dataset.rdctScheme, 'light');
  cleanup();
});

test('cursor following has the same decay at 60 and 120 FPS', () => {
  const remaining60 = (1 - cursorFollowAlpha(1000/60, .18)) ** 60;
  const remaining120 = (1 - cursorFollowAlpha(1000/120, .18)) ** 120;
  assert.ok(Math.abs(remaining60 - remaining120) < 1e-10);
  assert.ok(cursorFollowAlpha(16, .1) > cursorFollowAlpha(16, .4));
});
