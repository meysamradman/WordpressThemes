import test from 'node:test';
import assert from 'node:assert/strict';
import { orbitPoint, initializeOfferingArt } from '../radmanct/src/js/pages/home/offering-art.js';

test('pipe particles interpolate the actual pipe and disappear at both endpoints', () => {
  const track = { points: [{ x: 10, y: 20 }, { x: 10, y: 40 }, { x: 30, y: 40 }] };
  assert.deepEqual(orbitPoint(.25, track), { x: 10, y: 30, scale: 1, opacity: 1, front: true });
  assert.equal(orbitPoint(0, track).opacity, 0);
  assert.ok(orbitPoint(.999, track).opacity < .02);
  assert.deepEqual(orbitPoint(.25, track), orbitPoint(1.25, track));
});

test('network spheres follow their cubic connection and fade at loop boundaries', () => {
  const route = [375,290,330,240,300,240,285,183];
  const point = orbitPoint(.5,{route});
  assert.ok(point.x > 285 && point.x < 375 && point.y > 183 && point.y < 290);
  assert.equal(point.front,false);
  assert.equal(orbitPoint(0,{route}).opacity,0);
  assert.deepEqual(point,orbitPoint(1.5,{route}));
});

test('projected orbits loop seamlessly with matching depth on both sides of the artwork', () => {
  const track = { cx: 500, cy: 305, rx: 470, ry: 200, tilt: -17 };
  for (const turn of [0, .08, .44, .76]) {
    const a = orbitPoint(turn, track), b = orbitPoint(turn + 1, track);
    for (const key of ['x', 'y', 'scale', 'opacity']) assert.ok(Math.abs(a[key] - b[key]) < 1e-9);
    assert.ok(a.x > 0 && a.x < 1000 && a.y > 0 && a.y < 625);
  }
  const front = orbitPoint(.25, track), back = orbitPoint(.75, track);
  assert.equal(front.front, true); assert.equal(back.front, false);
  assert.ok(front.scale > back.scale && front.opacity > back.opacity);
});

function fixture({ reduced = false, deferred = false, labels = false } = {}) {
  let intersect; let loads = 0; let mounts = 0; let reverts = 0; let resolve;
  const tweens = []; const query = new EventTarget(); query.matches = reduced;
  const doc = new EventTarget(); doc.hidden = false;
  const view = new EventTarget(); doc.defaultView = view;
  view.matchMedia = () => query;
  view.IntersectionObserver = class { constructor(fn) { intersect = fn; } observe() {} disconnect() {} };
  view.ResizeObserver = class { observe() {} disconnect() {} };
  const track = { hasAttribute: () => false, dataset: { orbitTilt: '-17', orbitCx: '500', orbitCy: '305', orbitRx: '430', orbitRy: '200' } };
  const orb = { dataset: { orbitTrack: '0', orbitPhase: '.08' }, style: { zIndex: '3' } };
  const parent = { style: {} };
  const services=Object.assign(new EventTarget(),{dataset:{}});
  const root = { ownerDocument: doc, isConnected: true, clientWidth: 800, clientHeight: 500, closest: selector => selector==='[data-rdc-svcs]'?services:parent, querySelector: () => null, querySelectorAll: selector => selector.includes('tilt') ? [track] : selector.includes('label') ? (labels ? [{ dataset: { artLabelDelay: '0' } }, { dataset: { artLabelDelay: '.22' } }] : []) : [orb] };
  const gsap = {
    quickSetter: (node, key) => value => { node[key] = value; },
    set() {},
    context(fn) { ++mounts; fn(); return { revert() { ++reverts; } }; },
    to(state, options) { const tween = { options, stopped: true, paused(value) { this.stopped = value; }, play() { this.stopped = false; } }; tweens.push(tween); return tween; },
    fromTo(node, from, options) { const tween = this.to(node, options); tween.options = options; tween.from = from; return tween; },
  };
  const pending = deferred ? new Promise(done => { resolve = done; }) : Promise.resolve({ gsap });
  const stop = initializeOfferingArt(root, () => { ++loads; return pending; });
  return { root, parent, services, doc, query, tweens, orb, stop, show(value = true) { intersect([{ isIntersecting: value }]); }, resolve: () => resolve({ gsap }), counts: () => ({ loads, mounts, reverts }) };
}
const flush = async () => { for (let i = 0; i < 5; ++i) await Promise.resolve(); };

test('visible particles keep running during panel motion without restarting their engine',async()=>{
 const page=fixture();page.show();await flush();assert.equal(page.tweens[0].stopped,false);
 page.services.dataset.rdcSvcsMoving='1';page.services.dispatchEvent(new Event('rdc:svcs-mot'));
 assert.equal(page.tweens[0].stopped,false);assert.equal(page.orb.style.willChange,'transform');
 page.services.dataset.rdcSvcsMoving='0';page.services.dispatchEvent(new Event('rdc:svcs-mot'));
 assert.equal(page.tweens[0].stopped,false);assert.equal(page.counts().mounts,1);
 page.services.dataset.rdcSvcsMoving='1';page.services.dispatchEvent(new Event('rdc:svcs-mot'));
 assert.equal(page.tweens[0].stopped,false);page.stop();
 page.services.dataset.rdcSvcsMoving='0';page.services.dispatchEvent(new Event('rdc:svcs-mot'));
 assert.equal(page.counts().mounts,1);assert.equal(page.counts().reverts,1);
});

test('engine loads on demand, pauses outside view or behind the next panel and respects reduced motion', async () => {
  const page = fixture(); assert.equal(page.counts().loads, 0);
  page.show(); await flush(); assert.equal(page.counts().mounts, 1); assert.equal(page.tweens[0].stopped, false);
  page.show(false); assert.equal(page.tweens[0].stopped, true);
  page.show(); page.doc.hidden = true; page.doc.dispatchEvent(new Event('visibilitychange')); assert.equal(page.tweens[0].stopped, true);
  page.doc.hidden = false; page.parent.style.opacity = '.45'; page.doc.dispatchEvent(new Event('rdc:smth-upd')); assert.equal(page.tweens[0].stopped, true);
  page.parent.style.opacity = '1'; page.doc.dispatchEvent(new Event('rdc:smth-upd')); assert.equal(page.tweens[0].stopped, false);
  page.query.matches = true; page.query.dispatchEvent(new Event('change')); assert.equal(page.counts().reverts, 1);
  page.query.matches = false; page.query.dispatchEvent(new Event('change')); await flush(); assert.equal(page.counts().loads, 1); assert.equal(page.counts().mounts, 2);
  page.stop(); page.stop(); assert.equal(page.counts().reverts, 2);
});

test('reduced motion skips the engine and a late import cannot animate an outgoing scene', async () => {
  const reduced = fixture({ reduced: true }); reduced.show(); await flush(); assert.equal(reduced.counts().loads, 0); reduced.stop();
  const page = fixture({ deferred: true }); page.show(); page.stop(); page.resolve(); await flush(); assert.equal(page.counts().mounts, 0);
});

test('isolated hero words move independently and share particle visibility and cleanup', async () => {
  const page = fixture({ labels: true }); page.show(); await flush();
  assert.equal(page.tweens.length, 3);
  assert.equal(page.tweens[1].options.delay, 0);
  assert.equal(page.tweens[2].options.delay, .22);
  for (const tween of page.tweens.slice(1)) {
    assert.equal(tween.options.opacity, 1);
    assert.equal(tween.from.y, 3);
    assert.equal(tween.options.y, 0);
    assert.equal(tween.options.yoyo, true);
    assert.equal(tween.stopped, false);
  }
  page.show(false); assert.ok(page.tweens.every(tween => tween.stopped));
  page.query.matches = true; page.query.dispatchEvent(new Event('change'));
  assert.equal(page.counts().reverts, 1); page.stop();
});

test('orb rendering writes one combined transform and restores the static artwork after cleanup',async()=>{
 const page=fixture();let writes=0,value='';Object.defineProperty(page.orb.style,'transform',{get:()=>value,set:next=>{value=next;writes++;},configurable:true});
 page.show();await flush();const before=writes;page.tweens[0].options.onUpdate();assert.equal(writes,before+1);
 const point=orbitPoint(.08,{cx:500,cy:305,rx:430,ry:200,tilt:-17});
 const x=Math.round(point.x/1000*800*1000)/1000,y=Math.round(point.y/625*500*1000)/1000;
 assert.equal(value,`translate(${x}px,${y}px) translate(-50%,-50%) scale(${point.scale})`);
 page.stop();assert.equal(value,'');assert.equal(page.orb.style.zIndex,'3');assert.equal(page.orb.style.opacity,'');
});


test('only running particles request a transform layer and release it on pause and cleanup',async()=>{
 const page=fixture();assert.equal(page.orb.style.willChange,undefined);
 page.show();await flush();assert.equal(page.orb.style.willChange,'transform');
 page.show(false);assert.equal(page.orb.style.willChange,'');
 page.show();assert.equal(page.orb.style.willChange,'transform');
 page.parent.style.opacity='.45';page.doc.dispatchEvent(new Event('rdc:smth-upd'));
 assert.equal(page.orb.style.willChange,'');
 page.parent.style.opacity='1';page.doc.dispatchEvent(new Event('rdc:smth-upd'));
 assert.equal(page.orb.style.willChange,'transform');page.stop();assert.equal(page.orb.style.willChange,'');
});
