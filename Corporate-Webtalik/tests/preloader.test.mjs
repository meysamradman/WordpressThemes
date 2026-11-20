import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';

const source = await readFile(new URL('../radmanct/src/js/preloader.js', import.meta.url), 'utf8');

function fixture({type = 'navigate', reduced = false, readyState = 'loading', entrance = false} = {}) {
  const classes = new Set();
  const timers = new Map();
  const assigned = [];
  let timerId = 0;
  const root = Object.assign(new EventTarget(), {
    dataset: {entrance: entrance ? '1' : undefined},
    hidden: true,
    classList: {
      add: value => classes.add(value),
      contains: value => classes.has(value),
      remove: (...values) => values.forEach(value => classes.delete(value)),
    },
  });
  const doc = Object.assign(new EventTarget(), {
    readyState,
    getElementById: () => root,
    querySelector: () => null,
    documentElement: {dataset: {}},
  });
  const preference = Object.assign(new EventTarget(), {matches: reduced});
  const location = new URL('https://example.test/current/');
  location.assign = url => assigned.push(url);
  const win = Object.assign(new EventTarget(), {location});
  vm.runInNewContext(source, {
    document: doc, window: win, URL, matchMedia: () => preference,
    performance: {getEntriesByType: () => [{type}]}, AbortController, Event,
    setTimeout: (fn, delay) => {timers.set(++timerId, {fn, delay}); return timerId;},
    clearTimeout: id => timers.delete(id),
  });
  function click(href, {target = null, excluded = false, claimed = false, ...options} = {}) {
    const link = {
      href: new URL(href, location).href,
      getAttribute: name => name === 'href' ? href : target,
      matches: () => excluded,
    };
    const event = Object.assign(new Event('click', {cancelable: true}), {button: 0, ...options});
    Object.defineProperty(event, 'target', {value: {closest: () => link}});
    if (claimed) event.preventDefault();
    win.dispatchEvent(event);
    return event.defaultPrevented;
  }
  const end = phase => root.dispatchEvent(Object.assign(new Event('animationend'), {animationName: 'rdc-pre-' + phase}));
  const runTimer = delay => [...timers.values()].find(timer => timer.delay === delay).fn();
  return {root, doc, win, preference, timers, classes, assigned, click, end, runTimer};
}

test('first visits, repeat visits and reload open the curtain immediately and finish without window.load', () => {
  for (const type of ['navigate', 'navigate', 'reload']) {
    const page = fixture({type});
    let finished = 0;
    page.doc.addEventListener('rdc:pre-done', () => finished++);
    assert.equal(page.root.hidden, false);
    assert.equal(page.classes.has('is-in'), true);
    page.end('in');
    assert.equal(page.root.hidden, true);
    assert.equal(page.timers.size, 0);
    assert.equal(finished, 1);
  }
});

test('normal navigation covers the source before assigning once and resets on restore', () => {
  const page = fixture();
  page.end('in');
  assert.equal(page.click('/target/'), true);
  assert.equal(page.click('/ignored/'), true);
  assert.equal(page.classes.has('is-out'), true);
  assert.deepEqual(page.assigned, []);
  page.end('out');
  page.end('out');
  assert.deepEqual(page.assigned, ['https://example.test/target/']);
  page.win.dispatchEvent(new Event('pagehide'));
  page.win.dispatchEvent(Object.assign(new Event('pageshow'), {persisted: true}));
  assert.equal(page.root.hidden, true);
  assert.equal(page.timers.size, 0);
  assert.equal(page.click('/can-leave-again/'), true);
});

test('preserves special links and previously handled interactions', () => {
  const page = fixture();
  page.end('in');
  for (const [url, options] of [
    ['#content'], ['/target/#content'], ['/current/'], ['https://elsewhere.test/'],
    ['/wp-admin/'], ['/wp-json/'], ['/?rest_route=/wp/v2/pages'], ['/upload.webp'],
    ['/target/', {ctrlKey: true}], ['/target/', {button: 1}], ['/target/', {target: '_blank'}],
    ['/target/', {excluded: true}], ['/target/', {claimed: true}],
  ]) {
    assert.equal(page.click(url, options), options?.claimed ?? false);
    assert.equal(page.root.hidden, true);
  }
  assert.deepEqual(page.assigned, []);
});

test('safety timers recover missing animation events and canceled navigation', () => {
  const page = fixture();
  page.runTimer(1400);
  assert.equal(page.root.hidden, true);
  page.click('/target/');
  page.runTimer(850);
  assert.deepEqual(page.assigned, ['https://example.test/target/']);
  page.runTimer(1400);
  assert.equal(page.root.hidden, true);
  assert.equal(page.timers.size, 0);
});

test('input can dismiss entry without prematurely showing gated hero text', () => {
  const page = fixture({entrance: true});
  const state = page.doc.documentElement.dataset;
  assert.equal(state.rdcEnt, 'pending');
  page.doc.dispatchEvent(new Event('pointerdown'));
  assert.equal(page.root.hidden, true);
  assert.equal(state.rdcEnt, 'pending');
  state.rdcEnt = 'ready';
  page.doc.dispatchEvent(new Event('rdc:ent-rdy'));
  assert.equal(page.timers.size, 0);
});

test('hero gate fails open after timeout, on reduced motion and when leaving the document', () => {
  for (const method of ['timeout', 'reduced', 'pagehide']) {
    const page = fixture({entrance: true});
    if (method === 'timeout') page.runTimer(4000);
    if (method === 'reduced') {
      page.preference.matches = true;
      page.preference.dispatchEvent(new Event('change'));
    }
    if (method === 'pagehide') page.win.dispatchEvent(new Event('pagehide'));
    assert.equal(page.doc.documentElement.dataset.rdcEnt, 'fallback');
  }
});

test('reduced motion, history and late startup skip the entry overlay', () => {
  for (const params of [{reduced: true}, {type: 'back_forward'}, {readyState: 'complete'}]) {
    const page = fixture({...params, entrance: true});
    assert.equal(page.root.hidden, true);
    assert.equal(page.doc.documentElement.dataset.rdcEnt, undefined);
    assert.equal(page.timers.size, 0);
  }
  assert.equal(fixture({reduced: true}).click('/target/'), false);
});
