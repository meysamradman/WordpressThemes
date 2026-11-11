import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';
const source=await readFile(new URL('../radmanct/src/js/preloader.js',import.meta.url),'utf8');
function fixture(reduced=false,readyState='loading',entrance=false,storage){
 const classes=new Set(),timers=new Map(),frames=new Map();let id=0;
 const root={dataset:{exitDuration:'912'},hidden:true,removed:false,classList:{add:value=>classes.add(value)},remove(){this.removed=true;}};
 if(entrance)root.dataset.entrance='1';
 const doc=new EventTarget();doc.readyState=readyState;doc.getElementById=()=>root;
 doc.documentElement={dataset:{}};
 const preference=Object.assign(new EventTarget(),{matches:reduced});
 const win=new EventTarget();
 if(storage)Object.defineProperty(win,'sessionStorage',{get:()=>{if(storage instanceof Error)throw storage;return storage;}});
 vm.runInNewContext(source,{document:doc,window:win,matchMedia:()=>preference,AbortController,Event,
 setTimeout:(fn,delay)=>{timers.set(++id,{fn,delay});return id;},clearTimeout:n=>timers.delete(n),requestAnimationFrame:fn=>{frames.set(++id,fn);return id;},cancelAnimationFrame:n=>frames.delete(n)});
 const frame=()=>{const tasks=[...frames.values()];frames.clear();tasks.forEach(fn=>fn());};
 return {root,doc,win,preference,timers,frames,classes,frame};
}
test('selected dissolve waits for paint, leaves after load and removes itself at the end',()=>{
 const p=fixture();assert.equal(p.root.hidden,false);p.win.dispatchEvent(new Event('load'));assert.equal(p.classes.has('is-out'),false);
 p.frame();p.frame();assert.equal(p.classes.has('is-out'),true);
 [...p.timers.values()].find(t=>t.delay===972).fn();assert.equal(p.root.removed,true);assert.equal(p.timers.size,0);assert.equal(p.frames.size,0);
});

test('curtain runs once per session while subsequent home visits retain the hero gate',()=>{
 const values=new Map(),storage={getItem:key=>values.get(key),setItem:(key,value)=>values.set(key,value)};
 const first=fixture(false,'loading',true,storage);assert.equal(first.root.hidden,false);
 first.doc.dispatchEvent(new Event('pointerdown'));
 const next=fixture(false,'loading',true,storage);
 assert.equal(next.root.removed,true);assert.equal(next.root.hidden,true);
 assert.equal(next.doc.documentElement.dataset.rdcEnt,'pending');
 next.doc.documentElement.dataset.rdcEnt='ready';next.doc.dispatchEvent(new Event('rdc:ent-rdy'));
 assert.equal(next.timers.size,0);
 const internal=fixture(false,'loading',false,storage);assert.equal(internal.root.removed,true);assert.equal(internal.timers.size,0);
});

test('denied session storage retains the original bounded loader',()=>{
 const p=fixture(false,'loading',false,new Error('denied'));
 assert.equal(p.root.hidden,false);p.doc.dispatchEvent(new Event('pointerdown'));assert.equal(p.root.removed,true);assert.equal(p.timers.size,0);
});

test('hero is gated before parsing and loader exit does not reveal text before its entrance is ready',()=>{
 const p=fixture(false,'loading',true),state=p.doc.documentElement.dataset;
 assert.equal(state.rdcEnt,'pending');
 p.doc.dispatchEvent(new Event('pointerdown'));assert.equal(p.root.removed,true);assert.equal(state.rdcEnt,'pending');
 state.rdcEnt='ready';p.doc.dispatchEvent(new Event('rdc:ent-rdy'));
 assert.equal(p.timers.size,0);assert.equal(state.rdcEnt,'ready');
});

test('failed module loading, reduced motion and cached navigation release the initial text gate',()=>{
 for(const method of ['timeout','reduced','pageshow']){
  const p=fixture(false,'loading',true);
  if(method==='timeout')[...p.timers.values()].find(timer=>timer.delay===4000).fn();
  if(method==='reduced'){p.preference.matches=true;p.preference.dispatchEvent(new Event('change'));}
  if(method==='pageshow')p.win.dispatchEvent(Object.assign(new Event('pageshow'),{persisted:true}));
  assert.equal(p.doc.documentElement.dataset.rdcEnt,'fallback');
 }
 const reduced=fixture(true,'loading',true);assert.equal(reduced.doc.documentElement.dataset.rdcEnt,undefined);
});
test('reduced motion and startup after load skip the loader entirely',()=>{
 for(const p of [fixture(true),fixture(false,'complete')]){assert.equal(p.root.removed,true);assert.equal(p.root.hidden,true);assert.equal(p.timers.size,0);}
});
test('safety timeout or user interaction releases the overlay and all pending work',()=>{
 for(const method of ['timeout','pointerdown','pagehide']){
  const p=fixture();if(method==='timeout')[...p.timers.values()].find(t=>t.delay===1800).fn();else(method==='pointerdown'?p.doc:p.win).dispatchEvent(new Event(method));
  assert.equal(p.root.removed,true);assert.equal(p.timers.size,0);assert.equal(p.frames.size,0);
 }
});
