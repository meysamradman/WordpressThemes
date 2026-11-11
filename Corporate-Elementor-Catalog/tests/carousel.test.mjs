import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';
import { responsiveBreakpoints } from '../radmanct-components/src/js/components/responsive.js';
import { registerCarouselSync } from '../radmanct-components/src/js/components/carousel/sync.js';
import { registerCarouselExternal } from '../radmanct-components/src/js/components/carousel/external.js';

const source = (await readFile(new URL('../radmanct-components/src/js/components/carousel/engine.js', import.meta.url), 'utf8')).replace(/^import .*;$/gm, '').replaceAll('export function', 'function').replace(/\bimport\(/g, 'loadOptional(');
function fixture(direction = 'ltr', reduce = false, overrides = {}, loadOptional = () => Promise.reject(new Error('Unexpected optional feature'))) {
  class Node extends EventTarget {
    constructor() { super(); this.attributes = {}; this.classes = new Set(); this.classList = {add: (v) => this.classes.add(v), remove: (v) => this.classes.delete(v), toggle: (v, enabled) => enabled ? this.classes.add(v) : this.classes.delete(v)}; }
    setAttribute(key, value) { this.attributes[key] = value; }
    getAttribute(key) { return this.attributes[key] ?? null; }
    removeAttribute(key) { delete this.attributes[key]; }
    contains(node) { return node === this || (this.children ?? []).some((child) => child.contains(node)); }
    focus() { doc.activeElement = this; }
  }
  const previous = new Node(); const next = new Node(); const play = new Node(); const pagination = new Node();
  const controls = new Node(); controls.hidden = true;
  controls.querySelector = (key) => ({'[data-previous]': previous, '[data-next]': next, '[data-play]': play, '.rdct-crs__pagination': pagination})[key];
  const viewport = new Node(); const root = new Node();
  root.dataset = {options: JSON.stringify({autoplay: true, delay: 3000, duration: 500, slides_mobile: 1, slides_tablet: 2, slides: 3.2, gap: 24, arrows: true, pagination: 'numbers', ...overrides})};
  const reduced = new EventTarget(); reduced.matches = reduce;
  const doc = new EventTarget(); doc.defaultView = {matchMedia: () => reduced, getComputedStyle: () => ({direction})}; root.ownerDocument = doc;
  root.querySelectorAll = () => [controls];
  root.querySelector = (key) => key.endsWith('__viewport') ? viewport : controls;
  let created = 0; let destroyed = 0; let slider;
  class Swiper {
    constructor(element, options) { ++created; this.params = options; this.slides = [new Node(), new Node()]; this.activeIndex = 0; this.autoplay = {running: Boolean(options.autoplay), start: () => {this.autoplay.running = true;}, stop: () => {this.autoplay.running = false;}}; slider = this; }
    on(event, callback) { (this.events ??= {})[event] = callback; }
    update() {}
    destroy() { ++destroyed; }
  }
  const context = vm.createContext({loadOptional, reportRuntimeError: (_scope, error) => { throw error; }, Swiper, responsiveBreakpoints, registerCarouselSync, registerCarouselExternal, Navigation: {}, Pagination: {}, Autoplay: {}, A11y: {}});
  vm.runInContext(source, context);
  const focus = (target) => { const event = new Event('focusin'); Object.defineProperty(event, 'target', {value: target}); root.dispatchEvent(event); };
  return {root, viewport, controls, play, previous, reduced, doc, focus, initialize: (modules,state) => context.initializeCarousel(root, modules,state), slider: () => slider, created: () => created, destroyed: () => destroyed};
}

test('carousel scopes direction and controls to its own instance and cleans up once', () => {
  for (const direction of ['rtl', 'ltr']) {
    const page = fixture(direction); const cleanup = page.initialize();
    assert.equal(page.initialize(), cleanup); assert.equal(page.created(), 1); assert.equal(page.viewport.dir, direction);
    assert.equal(page.slider().params.loop, false); assert.equal(page.slider().params.navigation.prevEl, page.previous);
    assert.equal(page.slider().params.breakpoints[1024].slidesPerView, 3.2); assert.equal(page.controls.hidden, false);
    cleanup(); cleanup(); assert.equal(page.destroyed(), 1); assert.equal(page.controls.hidden, true);
  }
});

test('carousel uses server-resolved device boundaries and rejects malformed boundaries', () => {
  for (const [breakpoints, expected] of [[{tablet:901,desktop:1201},[901,1201]], [{tablet:1200,desktop:900},[768,1024]], [{tablet:'900',desktop:1200},[768,1024]]]) {
    const page = fixture('rtl', false, {breakpoints}); const cleanup = page.initialize();
    assert.deepEqual(Object.keys(page.slider().params.breakpoints).map(Number),expected);
    cleanup();
  }
});

test('autoplay respects focus, its own pause button and a live reduced-motion preference', () => {
  const page = fixture(); const cleanup = page.initialize();
  page.focus(page.play); assert.equal(page.slider().autoplay.running, true);
  page.play.dispatchEvent(new Event('click')); assert.equal(page.slider().autoplay.running, false);
  page.play.dispatchEvent(new Event('click')); assert.equal(page.slider().autoplay.running, true);
  page.focus(page.previous); assert.equal(page.slider().autoplay.running, false);
  page.play.dispatchEvent(new Event('click')); page.reduced.matches = true; page.reduced.dispatchEvent(new Event('change'));
  assert.equal(page.slider().params.speed, 0); assert.equal(page.slider().autoplay.running, false); assert.equal(page.play.disabled, true);
  cleanup(); const reduced = fixture('rtl', true); reduced.initialize(); assert.equal(reduced.slider().autoplay.running, false);
});

test('center, group navigation and autoplay policies remain independent', () => {
  const page = fixture('rtl', false, {alignment:'center',group_slides:true,draggable:false,rewind:false,pause_hover:false,resume_interaction:true,stop_last:true,autoplay_direction:'reverse',slide_count:8});
  const cleanup = page.initialize(); const slider = page.slider();
  assert.equal(slider.params.centeredSlides,true); assert.equal(slider.params.breakpoints[1024].slidesPerGroup,3);
  assert.equal(slider.params.allowTouchMove,false); assert.equal(slider.params.rewind,false);
  assert.equal(slider.params.initialSlide,7); assert.equal(slider.params.autoplay.pauseOnMouseEnter,false);
  assert.equal(slider.params.autoplay.disableOnInteraction,false); assert.equal(slider.params.autoplay.reverseDirection,true);
  slider.isBeginning = true; slider.events.slideChange(); assert.equal(slider.autoplay.running,false); cleanup();
});

test('offscreen and hidden pages stop playback without resuming a manual, touch or focus stop', () => {
  const page = fixture(); let notify; let disconnected = 0;
  page.doc.defaultView.IntersectionObserver = class { constructor(callback) { notify = callback; } observe() {} disconnect() { ++disconnected; } };
  const cleanup = page.initialize(); const slider = page.slider();
  assert.equal(slider.autoplay.running, false);
  notify([{ isIntersecting: true }]); assert.equal(slider.autoplay.running, true);
  page.doc.hidden = true; page.doc.dispatchEvent(new Event('visibilitychange')); assert.equal(slider.autoplay.running, false);
  page.doc.hidden = false; page.doc.dispatchEvent(new Event('visibilitychange')); assert.equal(slider.autoplay.running, true);
  notify([{ isIntersecting: false }]); notify([{ isIntersecting: true }]); assert.equal(slider.autoplay.running, true);
  slider.events.sliderFirstMove(); notify([{ isIntersecting: false }]); notify([{ isIntersecting: true }]); assert.equal(slider.autoplay.running, false);
  page.play.dispatchEvent(new Event('click')); assert.equal(slider.autoplay.running, true);
  slider.autoplay.running = false; slider.events.autoplayStop();
  notify([{ isIntersecting: false }]); notify([{ isIntersecting: true }]); assert.equal(slider.autoplay.running, false);
  page.play.dispatchEvent(new Event('click')); assert.equal(slider.autoplay.running, true);
  page.focus(page.previous); page.doc.dispatchEvent(new Event('visibilitychange')); assert.equal(slider.autoplay.running, false);
  cleanup(); assert.equal(disconnected, 1);
});

test('fade keeps forms in inactive slides out of focus and restores the original attributes on cleanup', () => {
  for (const direction of ['rtl', 'ltr']) {
    const page = fixture(direction, false, { mode: 'slider', transition: 'fade', autoplay: false });
    page.viewport.setAttribute('tabindex', '2');
    const effect = {}; const cleanup = page.initialize([effect]); const slider = page.slider();
    assert.equal(slider.params.effect, 'fade'); assert.equal(slider.params.fadeEffect.crossFade, true);
    assert.ok(slider.params.modules.includes(effect));
    assert.equal(slider.params.breakpoints[1024].slidesPerView, 1);
    assert.equal(slider.slides[1].getAttribute('aria-hidden'), 'true');
    assert.equal(slider.slides[1].getAttribute('inert'), '');
    page.doc.activeElement = slider.slides[0]; slider.activeIndex = 1; slider.events.slideChange();
    assert.equal(page.doc.activeElement, page.viewport);
    assert.equal(slider.slides[1].getAttribute('aria-hidden'), null);
    cleanup(); assert.equal(slider.slides[0].getAttribute('inert'), null);
    assert.equal(slider.slides[1].getAttribute('aria-hidden'), null); assert.equal(page.viewport.getAttribute('tabindex'), '2');
  }
});

test('stack uses the same engine, mirrors exit direction, isolates inactive content and restores state', () => {
  for (const direction of ['rtl','ltr']) {
    const page = fixture(direction,false,{mode:'stack',slide_count:3});
    const cleanup = page.initialize([{}],{index:1,wanted:false});
    const slider = page.slider();
    assert.equal(slider.params.effect,'creative');
    assert.equal(slider.params.slidesPerView,1);
    assert.equal(slider.params.breakpoints[1024].slidesPerView,1);
    assert.equal(slider.params.initialSlide,1);
    assert.equal(slider.params.creativeEffect.prev.translate[0],direction === 'rtl' ? '-120%' : '120%');
    assert.equal(slider.params.creativeEffect.next.translate[2],-80);
    assert.equal(slider.slides[1].getAttribute('inert'),'');
    assert.equal(slider.autoplay.running,false);
    assert.equal(cleanup.snapshot().wanted,false);
    assert.ok(page.root.classes.has('is-stack'));
    cleanup();
    assert.equal(page.root.classes.has('is-stack'),false);
    assert.equal(slider.slides[1].getAttribute('inert'),null);
    assert.equal(page.viewport.getAttribute('tabindex'),null);
  }
});

test('mobile stack fallback stays a single regular slide without importing an effect module', () => {
  const page = fixture('rtl',true,{mode:'stack',slide_count:3});
  const cleanup = page.initialize([],{index:2,wanted:false});
  assert.equal(page.slider().params.effect,'slide');
  assert.equal(page.slider().params.slidesPerView,1);
  assert.equal(page.slider().params.speed,0);
  assert.equal(page.root.classes.has('is-stack'),false);
  assert.equal(page.slider().params.initialSlide,2);
  cleanup();
});

test('stack adapter rebuilds only on preference/breakpoint change and preserves pause/index', async () => {
  const stackSource = (await readFile(new URL('../radmanct-components/src/js/components/carousel/stack.js',import.meta.url),'utf8')).replace(/^import .*;$/gm,'').replaceAll('export function','function');
  const media = new EventTarget(); media.matches = true;
  const calls = []; let destroyed = 0;
  const root = {dataset:{options:'{}'},ownerDocument:{defaultView:{matchMedia:() => media}}};
  const context = vm.createContext({responsiveBreakpoints,reportRuntimeError:assert.fail,initialize:(node,modules,state) => {
    calls.push({modules,state});
    const cleanup = () => destroyed++;
    cleanup.snapshot = () => ({index:2,wanted:false});
    return cleanup;
  }});
  vm.runInContext(stackSource,context);
  const cleanup = context.initializeCarousel(root,async () => ({EffectCreative:{}}));
  await new Promise((resolve) => setImmediate(resolve));
  assert.equal(calls[0].modules.length,1);
  media.matches = false; media.dispatchEvent(new Event('change'));
  assert.equal(calls[1].modules.length,0); assert.equal(calls[1].state.index,2); assert.equal(calls[1].state.wanted,false);
  cleanup(); cleanup(); assert.equal(destroyed,2);
  media.dispatchEvent(new Event('change')); assert.equal(calls.length,2);
});

test('mobile stack skips effect download and removal during import prevents late initialization', async () => {
  const source = (await readFile(new URL('../radmanct-components/src/js/components/carousel/stack.js',import.meta.url),'utf8')).replace(/^import .*;$/gm,'').replaceAll('export function','function');
  const media = new EventTarget(); media.matches = false;
  let loads = 0, starts = 0, stops = 0, resolve;
  const root = {isConnected:true,dataset:{options:'{}'},ownerDocument:{defaultView:{matchMedia:() => media}}};
  const context = vm.createContext({responsiveBreakpoints,reportRuntimeError:assert.fail,initialize:() => {starts++; return () => stops++;}});
  vm.runInContext(source,context);
  const cleanup = context.initializeCarousel(root,() => { loads++; return new Promise((done) => {resolve = done;}); });
  assert.equal(loads,0); assert.equal(starts,1);
  media.matches = true; media.dispatchEvent(new Event('change'));
  assert.equal(loads,1); assert.equal(stops,1);
  cleanup(); root.isConnected = false; resolve({EffectCreative:{}});
  await new Promise((done) => setImmediate(done)); assert.equal(starts,1);
});

test('optional carousel connections load only when selected and cannot register after destruction', async () => {
  const requested = []; let registered = 0; let released = 0;
  const module = {registerCarouselSync: () => { ++registered; return () => ++released; }, registerCarouselExternal: () => { ++registered; return () => ++released; }};
  const load = (key) => { requested.push(key); return Promise.resolve(module); };
  const plain = fixture('ltr', false, {}, load); plain.initialize()();
  assert.deepEqual(requested, []);
  const selected = fixture('ltr', false, {controller_id:'hero',external_previous_class:'previous'}, load);
  const cleanup = selected.initialize(); await new Promise(setImmediate);
  assert.deepEqual(requested.sort(), ['./external.js','./sync.js']); assert.equal(registered,2);
  cleanup(); assert.equal(released,2);
  requested.length = 0;
  const removed = fixture('ltr', false, {controller_id:'hero',external_next_class:'next'}, load);
  removed.initialize()(); await new Promise(setImmediate);
  assert.equal(registered,2); assert.equal(released,2);
});
