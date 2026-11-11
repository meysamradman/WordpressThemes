import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeScrollProgress} from '../radmanct/src/js/scroll-progress.js';

function fixture(){
  const root={dataset:{},hidden:true};const bar={style:{}};root.querySelector=()=>bar;
  const document=Object.assign(new EventTarget(),{body:{},documentElement:{scrollHeight:3000,clientHeight:1000},getElementById:()=>root});
  const window=Object.assign(new EventTarget(),{scrollY:0});
  const frames=new Map();let frame=0,disconnected=0;
  Object.assign(globalThis,{document,window,requestAnimationFrame:callback=>{frames.set(++frame,callback);return frame;},cancelAnimationFrame:key=>frames.delete(key),ResizeObserver:class{observe(){}disconnect(){disconnected++;}}});
  const flush=()=>{const pending=[...frames.values()];frames.clear();pending.forEach(callback=>callback());};
  const send=(target,type,detail)=>{const event=new Event(type);if(detail!==undefined)event.detail=detail;target.dispatchEvent(event);flush();};
  initializeScrollProgress();flush();
  const release=()=>{const event=new Event('pagehide');event.persisted=false;window.dispatchEvent(event);};
  return {root,bar,document,window,frames,flush,send,release,disconnected:()=>disconnected};
}

test('progress hides the complete track at the top and follows rendered smooth position until native scrolling resumes',()=>{
  const p=fixture();assert.equal(p.root.hidden,true);
  p.window.scrollY=1000;p.send(p.document,'scroll');assert.equal(p.root.hidden,false);assert.equal(p.bar.style.transform,'scaleX(0.5)');
  p.send(p.document,'rdct:smooth-update',{scrollTop:100});assert.equal(p.bar.style.transform,'scaleX(0.05)');
  p.window.scrollY=2000;p.send(p.document,'scroll');assert.equal(p.bar.style.transform,'scaleX(0.05)');
  p.send(p.document,'rdct:smooth-update',{scrollTop:0});assert.equal(p.root.hidden,true);
  p.send(p.document,'rdct:smooth-update',{scrollTop:null});assert.equal(p.root.hidden,false);assert.equal(p.bar.style.transform,'scaleX(1)');
  p.window.scrollY=0;p.send(p.document,'scroll');assert.equal(p.root.hidden,true);p.release();
});

test('progress handles a short or resized document and removes its listeners on departure',()=>{
  const p=fixture();p.window.scrollY=9999;p.send(p.document,'scroll');assert.equal(p.bar.style.transform,'scaleX(1)');
  p.document.documentElement.scrollHeight=500;p.send(p.window,'resize');assert.equal(p.root.hidden,true);assert.equal(p.bar.style.transform,'scaleX(0)');
  p.release();assert.equal(p.disconnected(),1);assert.equal(p.root.dataset.enhanced,undefined);
  p.document.dispatchEvent(new Event('scroll'));assert.equal(p.frames.size,0);
});
