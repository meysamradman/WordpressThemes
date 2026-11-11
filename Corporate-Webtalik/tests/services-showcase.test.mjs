import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeServicesShowcase} from '../radmanct/src/js/ui/services-showcase.js';
const deferred=()=>{let resolve;const promise=new Promise(done=>{resolve=done;});return {promise,resolve};};
function fixture(){
 const handlers=new Map();const desktop=new EventTarget();desktop.matches=true;
 const items=[0,1,2].map(n=>({dataset:{rdcSvc:String(n)},open:n===0,contains:()=>false,closest(){return this;}}));
 const waiting=[deferred(),deferred(),deferred()];let decodes=0;
 const frames=items.map((_,n)=>({dataset:{rdcSvcIm:String(n)},hidden:n!==0,querySelector:()=>({complete:n===0,loading:'lazy',decode(){decodes++;return waiting[n].promise;}})}));
 const root={isConnected:true,ownerDocument:{defaultView:{matchMedia:q=>q.includes('768')?desktop:{matches:!q.includes('reduced-motion')}}},querySelectorAll:q=>q==='[data-rdc-svc]'?items:frames,addEventListener:(key,fn)=>handlers.set(key,fn),removeEventListener:key=>handlers.delete(key)};
 const release=initializeServicesShowcase(root);
 return {root,items,frames,waiting,handlers,desktop,release,decodes:()=>decodes};
}
test('focus alone keeps native details closed; keyboard toggle selects one item',async()=>{
 const p=fixture();p.handlers.get('focusin')({target:p.items[1]});assert.equal(p.items[1].open,false);
 p.items[1].open=true;p.handlers.get('toggle')({target:p.items[1]});assert.equal(p.items[0].open,false);
 p.waiting[1].resolve();await Promise.resolve();assert.equal(p.frames[1].hidden,false);assert.equal(p.frames[0].hidden,true);p.release();assert.equal(p.handlers.size,0);
});
test('hover keeps the latest preview when image decoding completes out of order',async()=>{
 const p=fixture();const over=p.handlers.get('pointerover');over({target:p.items[1],relatedTarget:null,pointerType:'mouse'});over({target:p.items[2],relatedTarget:null,pointerType:'mouse'});
 p.waiting[2].resolve();await Promise.resolve();p.waiting[1].resolve();await Promise.resolve();assert.equal(p.frames[2].hidden,false);assert.equal(p.frames[1].hidden,true);assert.equal(p.items[1].open,false);p.release();
});
test('mobile skips large preview decoding; teardown rejects a late preview',async()=>{
 const p=fixture();p.desktop.matches=false;p.items[1].open=true;p.handlers.get('toggle')({target:p.items[1]});assert.equal(p.decodes(),0);
 p.desktop.matches=true;p.desktop.dispatchEvent(new Event('change'));assert.equal(p.decodes(),1);p.release();p.waiting[1].resolve();await Promise.resolve();assert.equal(p.frames[1].hidden,true);
});

test('image handoff keeps the previous preview underneath and duplicate toggle does not replay it',async()=>{
 const p=fixture(),finished=deferred();let animations=0;
 for(const frame of p.frames)frame.style={removeProperty(){}};
 p.frames[1].animate=()=>{animations++;return {finished:finished.promise,cancel(){}};};
 p.items[1].open=true;p.handlers.get('toggle')({target:p.items[1]});p.waiting[1].resolve();await Promise.resolve();
 assert.equal(p.frames[0].hidden,false);assert.equal(p.frames[1].hidden,false);
 p.handlers.get('focusin')({target:p.items[1]});p.handlers.get('toggle')({target:p.items[1]});assert.equal(animations,1);
 finished.resolve();await Promise.resolve();assert.equal(p.frames[0].hidden,true);assert.equal(p.frames[1].hidden,false);p.release();
});
