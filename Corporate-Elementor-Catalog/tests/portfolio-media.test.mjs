import test from 'node:test';
import assert from 'node:assert/strict';
import {initializePortfolioMedia} from '../radmanct-child/src/js/portfolio/media.js';

function fixture(enabled = true) {
  const view = new EventTarget();
  const media = new EventTarget(); media.matches = enabled;
  view.matchMedia = () => media;
  view.requestAnimationFrame = () => 1;
  view.cancelAnimationFrame = () => {};
  const doc = {defaultView:view};
  const node = () => Object.assign(new EventTarget(), {isConnected:true, ownerDocument:doc, querySelector:() => ({})});
  const archive = Object.assign(new EventTarget(), {ownerDocument:doc, querySelectorAll:() => nodes});
  const nodes = [node()]; const tweens = []; let reverted = 0; let loads = 0;
  const vendor = {gsap:{registerPlugin(){},context:() => ({add:fn => fn(),revert:() => { reverted++; }}),fromTo:(target,from,to) => {
    tweens.push({from,to}); return {scrollTrigger:{refresh(){}}};
  }},ScrollTrigger:{}};
  return {archive,nodes,node,vendor,tweens,load:async() => { loads++; return vendor; },counts:() => ({loads,reverted})};
}
const settle = () => new Promise(setImmediate);

test('portfolio additions mount once, replaced cards release their effects and all motion is bounded', async () => {
  const f = fixture(); const release = initializePortfolioMedia(f.archive,f.load); await settle();
  assert.equal(f.tweens.length,1);
  assert.equal(f.tweens[0].from.yPercent,6);
  assert.equal(f.tweens[0].to.yPercent,-6);
  assert.ok((f.tweens[0].from.scale-1)/2 > .06);
  f.nodes.push(f.node()); f.archive.dispatchEvent(new Event('rdct:archive-updated')); await settle();
  assert.equal(f.tweens.length,2);
  f.archive.dispatchEvent(new Event('rdct:archive-updated')); await settle();
  assert.equal(f.tweens.length,2);
  f.nodes[0].isConnected=false; f.nodes.shift();
  f.archive.dispatchEvent(new Event('rdct:archive-updated')); await settle();
  assert.equal(f.counts().reverted,1);
  release(); release(); assert.equal(f.counts().reverted,2);
});

test('mobile or reduced-motion portfolio keeps native media without requesting the animation vendor', async () => {
  const f=fixture(false); const release=initializePortfolioMedia(f.archive,f.load); await settle();
  assert.deepEqual(f.counts(),{loads:0,reverted:0}); release();
});

test('leaving before a vendor import finishes cannot revive portfolio animation', async () => {
  const f=fixture(); let resolve;
  const release=initializePortfolioMedia(f.archive,() => new Promise(done => { resolve=done; })); await settle();
  release(); resolve(f.vendor); await settle(); assert.equal(f.tweens.length,0);
});
