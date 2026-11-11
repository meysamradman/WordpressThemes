import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeCursor } from '../radmanct/src/js/cursor.js';

test('magnetic cursor caches target geometry, stops at rest and cleans up on preference changes', () => {
  const previous = Object.fromEntries(['document','window','Element','matchMedia','requestAnimationFrame','cancelAnimationFrame'].map(key => [key,globalThis[key]]));
  const doc = new EventTarget(), view = new EventTarget(), media = new EventTarget(); media.matches = true;
  const frames = new Map(); let frameId = 0, reads = 0, time = 0;
  const dot = {style:{}}, ring = {style:{}}, label = {textContent:''};
  const classes = new Set();
  const root = {dataset:{hoverEffect:'magnetic',followDuration:'.18'},hidden:true,classList:{toggle(name,enabled){if(enabled)classes.add(name);else classes.delete(name);}},querySelector: selector => selector.endsWith('dot') ? dot : selector.endsWith('ring') ? ring : label};
  const link = {getAttribute:()=>null,getBoundingClientRect:()=>{reads++;return {left:50,top:30,width:100,height:40};}};
  class Target { closest(selector) { return selector.startsWith('a[href]') ? link : null; } }
  Object.assign(globalThis,{document:doc,window:view,Element:Target,matchMedia:()=>media,requestAnimationFrame:fn=>{frames.set(++frameId,fn);return frameId;},cancelAnimationFrame:id=>frames.delete(id)});
  doc.getElementById = () => root;
  const move = () => { const event = new Event('pointermove'); Object.defineProperties(event,{target:{value:new Target()},pointerType:{value:'mouse'},clientX:{value:100},clientY:{value:80}}); doc.dispatchEvent(event); };
  try {
    const cleanup = initializeCursor(); move(); move();
    assert.equal(reads,1); assert.ok(classes.has('is-interactive'));
    for(let i=0;i<150 && frames.size;i++){const batch=[...frames.values()];frames.clear();time+=16.67;batch.forEach(fn=>fn(time));}
    assert.equal(frames.size,0); assert.equal(reads,1);
    doc.dispatchEvent(new Event('scroll')); assert.equal(root.hidden,true);
    move(); assert.equal(reads,2);
    media.matches=false; media.dispatchEvent(new Event('change')); assert.equal(root.hidden,true);
    cleanup(); assert.equal(root.dataset.enhanced,undefined); assert.equal(frames.size,0);
  } finally { for(const [key,value] of Object.entries(previous)){if(value===undefined)delete globalThis[key];else globalThis[key]=value;} }
});
