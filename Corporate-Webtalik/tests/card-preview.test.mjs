import test from 'node:test';
import assert from 'node:assert/strict';
import { observeCardPreviews } from '../radmanct/src/js/ui/card-preview.js';

function fixture({ mode = 'auto', reduced = false, saveData = false, count = 1, deferred = false, reject = false, external = false, supported = true } = {}) {
  const doc = new EventTarget(), view = new EventTarget(), preference = new EventTarget(), fine = new EventTarget(), connection = new EventTarget();
  preference.matches = reduced; fine.matches = true; connection.saveData = saveData;
  doc.hidden = false; doc.defaultView = view; doc.documentElement = {};
  view.matchMedia = query => query.includes('reduced') ? preference : fine;
  view.navigator = { connection }; view.location = new URL('https://local.test/');
  let intersection, mutation;
  view.IntersectionObserver = class { constructor(fn) { intersection = fn; } observe() {} unobserve() {} disconnect() {} };
  view.MutationObserver = class { constructor(fn) { mutation = fn; } observe() {} disconnect() {} };
  const cards = [];
  const add = (notify = true) => {
    const root = new EventTarget(), video = new EventTarget(), button = new EventTarget(), owner = new EventTarget(), link = new EventTarget();
    const attributes = new Map(); let resolve;
    video.id = `video-${cards.length}`;
    video.dataset = { src: `${external ? 'https://outside.test' : 'https://local.test'}/clip.mp4`, mime: 'video/mp4' };
    video.playCount = video.pauseCount = video.loadCount = 0;
    video.canPlayType = () => supported ? 'probably' : '';
    video.getAttribute = key => attributes.get(key) || null;
    video.removeAttribute = key => attributes.delete(key);
    Object.defineProperty(video, 'src', { set(value) { attributes.set('src', value); } });
    video.pause = () => { ++video.pauseCount; };
    video.load = () => { ++video.loadCount; };
    video.play = () => {
      ++video.playCount;
      return deferred ? new Promise(done => { resolve = done; }) : reject ? Promise.reject(new Error('Autoplay blocked')) : Promise.resolve();
    };
    const play = { hidden: false }, pause = { hidden: true };
    button.hidden = true; button.dataset = { playLabel: 'Play', pauseLabel: 'Pause' };
    button.getAttribute = key => key === 'aria-controls' ? video.id : button[key];
    button.setAttribute = (key, value) => { button[key] = value; };
    button.querySelector = selector => selector.includes('pause') ? pause : play;
    owner.querySelectorAll = () => [button]; owner.contains = node => [root, video, button, link].includes(node);
    root.dataset = { mode }; root.isConnected = true;
    const classes = new Set(); root.classList = { toggle(name, active) { active ? classes.add(name) : classes.delete(name); }, contains: name => classes.has(name) };
    root.closest = selector => selector === 'a' ? link : owner;
    root.querySelector = () => video; root.querySelectorAll = () => []; root.matches = () => true;
    const card = { root, video, button, owner, link, resolve: () => resolve?.() }; cards.push(card);
    if (notify) mutation([{ addedNodes: [root] }]);
    return card;
  };
  for (let i = 0; i < count; ++i) add(false);
  doc.querySelectorAll = () => cards.filter(card => card.root.isConnected).map(card => card.root);
  const cleanup = observeCardPreviews(doc);
  return { doc, view, preference, connection, cards, cleanup, add,
    show(index = 0, ratio = 1) { intersection([{ target: cards[index].root, isIntersecting: ratio > 0, intersectionRatio: ratio }]); },
    remove(index) { cards[index].root.isConnected = false; mutation([{ addedNodes: [] }]); } };
}
const flush = async () => { for (let i = 0; i < 5; ++i) await Promise.resolve(); };

test('images stay untouched until visible; offscreen previews release the video source', async () => {
  const page = fixture(), { root, video, button } = page.cards[0];
  assert.equal(video.getAttribute('src'), null); assert.equal(video.playCount, 0);
  page.show(); await flush();
  assert.equal(video.playCount, 1); assert.equal(video.muted, true); assert.equal(root.classList.contains('is-play'), true); assert.equal(video.defaultMuted,true);assert.equal(video.volume,0);assert.equal(video.loop,true);assert.equal(video.controls,false);assert.equal(button.hidden,true);
  page.show(0, 0); assert.equal(video.getAttribute('src'), null); assert.equal(root.classList.contains('is-play'), false);
  page.show(); await flush(); assert.equal(video.playCount, 2);
  page.show(0, 0); page.show(); await flush(); assert.equal(video.playCount, 3);
  page.cleanup(); page.cleanup(); assert.equal(button.hidden, true); assert.equal(video.getAttribute('src'), null);
});

test('hover mode is selectable per card and returns to the image on pointer or focus exit', async () => {
  const page = fixture({ mode: 'hover' }), { video, link, owner, root } = page.cards[0];
  page.show(); await flush(); assert.equal(video.playCount, 0);
  link.dispatchEvent(new Event('pointerenter')); await flush(); assert.equal(video.playCount, 1);
  link.dispatchEvent(new Event('pointerleave')); assert.equal(root.classList.contains('is-play'), false);
  owner.dispatchEvent(new Event('focusin')); await flush(); assert.equal(video.playCount, 2);
  owner.dispatchEvent(new Event('focusout')); assert.equal(root.classList.contains('is-play'), false);
  page.cleanup();
});

test('visible autoplay is capped at two and hovered cards take priority', async () => {
  const page = fixture({ count: 3 }); for (let i = 0; i < 3; ++i) page.show(i); await flush();
  assert.equal(page.cards.filter(card => card.root.classList.contains('is-play')).length, 2);
  assert.equal(page.cards[2].video.playCount, 0);
  page.cards[2].link.dispatchEvent(new Event('pointerenter')); await flush();
  assert.equal(page.cards[2].root.classList.contains('is-play'), true);
  assert.equal(page.cards.filter(card => card.root.classList.contains('is-play')).length, 2);
  page.doc.hidden = true; page.doc.dispatchEvent(new Event('visibilitychange'));
  assert.ok(page.cards.every(card => !card.root.classList.contains('is-play') && !card.video.getAttribute('src')));
  page.cleanup();
});

test('reduced motion and data saving retain the image without player controls', async () => {
  for (const options of [{ reduced: true }, { saveData: true }]) {
    const page = fixture(options), { video, button } = page.cards[0]; page.show(); await flush();
    assert.equal(video.playCount, 0); assert.equal(video.getAttribute('src'), null);
    button.dispatchEvent(new Event('click')); await flush(); assert.equal(video.playCount, 0);assert.equal(button.hidden,true);
    page.cleanup();
  }
});

test('AJAX additions initialize and late play completion cannot revive detached cards', async () => {
  const page = fixture({ deferred: true }); page.show(); page.remove(0); page.cards[0].resolve(); await flush();
  assert.equal(page.cards[0].root.classList.contains('is-play'), false); assert.equal(page.cards[0].video.getAttribute('src'), null);
  const added = page.add(); page.show(1); added.resolve(); await flush(); assert.equal(added.root.classList.contains('is-play'), true);
  page.cleanup();
});

test('play rejection, unsupported codecs and foreign sources keep the poster', async () => {
  for (const options of [{ reject: true }, { supported: false }, { external: true }]) {
    const page = fixture(options), { video, root } = page.cards[0]; page.show(); await flush();
    assert.equal(root.classList.contains('is-play'), false);
    const attempts = video.playCount; page.show(); await flush(); assert.equal(video.playCount, attempts);
    if (!options.reject) assert.equal(video.getAttribute('src'), null);
    page.cleanup();
  }
});
