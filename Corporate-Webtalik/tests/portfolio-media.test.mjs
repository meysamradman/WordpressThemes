import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeMediaParallax} from '../radmanct/src/js/runtime/media-parallax.js';
import {initializePortfolioMedia} from '../radmanct/src/js/pages/portfolio.js';
import {setSmoothScroller} from '../radmanct/src/js/runtime/scroll-state.js';

function fixture(enabled = true) {
  const view = new EventTarget();
  const media = new EventTarget(); media.matches = enabled;
  view.matchMedia = () => media;
  view.requestAnimationFrame = () => 1;
  view.cancelAnimationFrame = () => {};
  const doc = Object.assign(new EventTarget(),{defaultView:view});
  view.setTimeout=setTimeout;view.clearTimeout=clearTimeout;
  view.CustomEvent=class extends Event{constructor(type,options){super(type);this.detail=options?.detail;}};
  const node = () => Object.assign(new EventTarget(), {isConnected:true, ownerDocument:doc, querySelector:() => ({})});
  const archive = Object.assign(new EventTarget(), {ownerDocument:doc, querySelectorAll:() => nodes});
  const nodes = [node()]; const tweens = []; let reverted = 0; let loads = 0;
  const vendor = {gsap:{registerPlugin(){},context:() => ({add:fn => fn(),revert:() => { reverted++; }}),fromTo:(target,from,to) => {
    const trigger={vars:to.scrollTrigger,scrubDuration(value){this.scrub=value;},refresh(){}};
    tweens.push({from,to,trigger}); return {scrollTrigger:trigger};
  }},ScrollTrigger:{}};
  return {archive,nodes,node,vendor,tweens,load:async() => { loads++; return vendor; },counts:() => ({loads,reverted})};
}
const settle = () => new Promise(setImmediate);

test('cover zoom keeps its reference scrub when the scroll owner changes and reverts for reduced motion',async()=>{
 const f=fixture(),root=f.nodes[0],media=new EventTarget();media.matches=true;
 root.ownerDocument.defaultView.matchMedia=()=>media;
 const release=initializeMediaParallax(root,{frame:root,target:[{},{}],motion:'zoom',amount:15,minWidth:1024},f.load);
 await settle();
 const zoom=f.tweens[0];
 assert.equal(zoom.from.scale,1);assert.equal(zoom.to.scale,1.15);
 assert.equal(zoom.to.scrollTrigger.start,'top bottom');assert.equal(zoom.to.scrollTrigger.end,'bottom center');
 assert.equal(zoom.to.scrollTrigger.scrub,1);
 setSmoothScroller(root.ownerDocument,{});
 assert.equal(zoom.to.scrollTrigger.scrub,1);assert.equal(f.tweens.length,1);
 media.matches=false;media.dispatchEvent(new Event('change'));
 assert.equal(f.counts().reverted,1);
 release();assert.equal(f.counts().reverted,1);
});

test('portfolio additions mount once, replaced cards release their effects and all motion is bounded', async () => {
  const f = fixture(); const release = initializePortfolioMedia(f.archive,f.load); await settle();
  assert.equal(f.tweens.length,1);
  assert.equal(f.tweens[0].from.yPercent,6);
  assert.equal(f.tweens[0].to.yPercent,-6);
  assert.ok((f.tweens[0].from.scale-1)/2 > .06);
  f.nodes.push(f.node()); f.archive.dispatchEvent(new Event('rdc:arc-upd')); await settle();
  assert.equal(f.tweens.length,2);
  f.archive.dispatchEvent(new Event('rdc:arc-upd')); await settle();
  assert.equal(f.tweens.length,2);
  f.nodes[0].isConnected=false; f.nodes.shift();
  f.archive.dispatchEvent(new Event('rdc:arc-upd')); await settle();
  assert.equal(f.counts().reverted,1);
  release(); release(); assert.equal(f.counts().reverted,2);
});

test('parallax follows the actual scroll owner without recreating its tween',async()=>{
 const f=fixture(),root=f.nodes[0],target={};
 const stop=initializeMediaParallax(root,{frame:root,target},f.load);await settle();
 assert.equal(f.tweens[0].to.scrollTrigger.scrub,.5);
 setSmoothScroller(root.ownerDocument,{});
 assert.equal(f.tweens[0].trigger.scrub,true);assert.equal(f.tweens.length,1);
 setSmoothScroller(root.ownerDocument,null);
 assert.equal(f.tweens[0].trigger.scrub,.5);assert.equal(f.tweens.length,1);stop();
});

test('late reveal never hides visible media; completed reveal is not replayed across breakpoints',async()=>{
 const f=fixture(),root=f.nodes[0],media=new EventTarget();media.matches=true;
 f.nodes[0].ownerDocument.defaultView.innerHeight=900;root.getBoundingClientRect=()=>({top:500});
 const stop=initializeMediaParallax(root,{frame:root,target:{},motion:'reveal'},f.load);await settle();
 assert.equal(f.tweens.length,0);stop();
 const target={};root.getBoundingClientRect=()=>({top:1500});root.ownerDocument.defaultView.matchMedia=()=>media;
 const release=initializeMediaParallax(root,{frame:root,target,motion:'reveal'},f.load);await settle();
 const tween=f.tweens[0];assert.equal(tween.to.immediateRender,false);assert.equal(tween.to.scrollTrigger.once,true);
 tween.to.onComplete();media.matches=false;media.dispatchEvent(new Event('change'));
 media.matches=true;media.dispatchEvent(new Event('change'));await settle();assert.equal(f.tweens.length,1);release();
});

test('bounded parallax endpoints retain overscan at small and large amplitudes',async()=>{
 for(const amount of [1,6,12,30]){
  const f=fixture(),root=f.nodes[0];const stop=initializeMediaParallax(root,{frame:root,target:{},amount},f.load);await settle();
  const {from,to}=f.tweens[0];assert.ok((from.scale-1)/2>Math.abs(from.yPercent)/100);assert.equal(from.scale,to.scale);stop();
 }
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

test('selected work entry uses the reference fade/zoom without a duplicate manual scroll tween',async()=>{
 const f=fixture(),root=f.nodes[0],media=new EventTarget();media.matches=true;root.ownerDocument.defaultView.matchMedia=()=>media;
 const release=initializeMediaParallax(root,{frame:root,target:{},minWidth:1024,motion:'reveal'},f.load);await settle();
 assert.equal(f.tweens.length,1);
 const zoom=f.tweens[0];
 assert.equal(zoom.from.scale,1.2);assert.equal(zoom.from.autoAlpha,0);assert.equal(zoom.to.autoAlpha,1);assert.equal(zoom.to.scale,1);assert.equal(zoom.to.duration,2);assert.equal(zoom.to.scrollTrigger.scrub,undefined);
 assert.equal('y' in zoom.to,false);assert.equal('yPercent' in zoom.to,false);
 media.matches=false;media.dispatchEvent(new Event('change'));assert.equal(f.counts().reverted,1);release();assert.equal(f.counts().reverted,1);
});
