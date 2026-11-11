import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeImageParallax} from '../radmanct-components/src/js/components/image/parallax.js';

function fixture(direction = 'up', enabled = true) {
  const root = new EventTarget(); const view = new EventTarget(); const media = new EventTarget(); media.matches = enabled;
  const target = {}; const frame = {querySelector:()=>target};
  const frames = new Map(); let next = 0; let disconnected = 0; let resized; let reverted = 0; let refreshed = 0; let tween; let downloads = 0;
  Object.assign(view,{matchMedia:()=>media,requestAnimationFrame:(fn)=>{frames.set(++next,fn);return next;},cancelAnimationFrame:(key)=>frames.delete(key),ResizeObserver:class{constructor(fn){resized=fn;}observe(){}disconnect(){disconnected++;}}});
  Object.assign(root,{isConnected:true,dataset:{parallaxAmount:'12',parallaxDirection:direction,parallaxMinWidth:'768'},ownerDocument:{defaultView:view},querySelector:()=>frame});
  const vendor={gsap:{registerPlugin(){},context(fn,scope){assert.equal(scope,root);fn();return{add(fn){fn();},revert(){reverted++;}};},fromTo(node,from,to){assert.equal(node,target);tween={from,to};return{scrollTrigger:{refresh(){refreshed++;}}};}},ScrollTrigger:{}};
  const load=async()=>{downloads++;return vendor;};
  const flush=()=>{const pending=[...frames.values()];frames.clear();pending.forEach((fn)=>fn());};
  return {root,view,media,target,frame,load,vendor,flush,resize:()=>resized(),frames,downloads:()=>downloads,tween:()=>tween,reverted:()=>reverted,refreshed:()=>refreshed,disconnected:()=>disconnected};
}

test('image parallax moves only the inner media, preserves coverage and reverses direction',async()=>{
  for(const direction of ['up','down']) {
    const page=fixture(direction);const cleanup=initializeImageParallax(page.root,page.load);await Promise.resolve();
    const {from,to}=page.tween();assert.equal(from.yPercent,direction==='up'?12:-12);assert.equal(to.yPercent,-from.yPercent);
    assert.ok((from.scale-1)/2 > Math.abs(from.yPercent)/100);
    assert.equal(to.scrollTrigger.trigger,page.frame);assert.equal(to.scrollTrigger.invalidateOnRefresh,true);
    page.resize();page.resize();page.root.dispatchEvent(new Event('load'));assert.equal(page.frames.size,1);page.flush();assert.equal(page.refreshed(),1);
    page.media.matches=false;page.media.dispatchEvent(new Event('change'));assert.equal(page.reverted(),1);
    page.media.matches=true;page.media.dispatchEvent(new Event('change'));await Promise.resolve();
    cleanup();cleanup();assert.equal(page.reverted(),2);assert.equal(page.disconnected(),1);
  }
});

test('mobile and reduced-motion images do not request GSAP until enabled',async()=>{
  const page=fixture('up',false);const cleanup=initializeImageParallax(page.root,page.load);
  assert.equal(page.downloads(),0);page.media.matches=true;page.media.dispatchEvent(new Event('change'));await Promise.resolve();
  assert.equal(page.downloads(),1);assert.ok(page.tween());cleanup();
});

test('a late animation import cannot attach to a removed or released image',async()=>{
  for(const remove of [true,false]) {
    const page=fixture();let resolve;const pending=new Promise((done)=>{resolve=done;});
    const cleanup=initializeImageParallax(page.root,()=>pending);
    if(remove)page.root.isConnected=false;else cleanup();resolve(page.vendor);await pending;await Promise.resolve();
    assert.equal(page.tween(),undefined);cleanup();assert.equal(page.frames.size,0);
  }
});
