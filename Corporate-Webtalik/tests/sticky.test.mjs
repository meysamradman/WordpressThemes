import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeSticky} from '../radmanct/src/js/runtime/sticky.js';
import {setSmoothScroller} from '../radmanct/src/js/runtime/scroll-state.js';
const flush=async()=>{for(let n=0;n<6;n++)await Promise.resolve();};
function fixture(matches=true){
 const media=new EventTarget();media.matches=matches;const timers=new Map();let id=0,config,reverted=0,disconnected=0,resize,loads=0,refreshes=0;
 const view={innerHeight:900,matchMedia:()=>media,getComputedStyle:()=>({position:'fixed'}),clearTimeout:n=>timers.delete(n),setTimeout:fn=>{timers.set(++id,fn);return id;},ResizeObserver:class{constructor(fn){resize=fn;}observe(){}disconnect(){disconnected++;}}};
 const doc=Object.assign(new EventTarget(),{defaultView:view,getElementById:()=>({getBoundingClientRect:()=>({height:32})}),fonts:{ready:Promise.resolve()}});
 view.CustomEvent=class extends Event{constructor(type){super(type);}};
 const root={ownerDocument:doc,isConnected:true},target={offsetHeight:400},boundary={};
 const vendor={gsap:{registerPlugin(){},set(){},context(fn){fn();return {revert(){reverted++;}};}},ScrollTrigger:{create(value){config=value;return {};},refresh(){refreshes++;}}};
 return {root,target,boundary,media,timers,view,vendor,load:async()=>{loads++;return vendor;},resize:()=>resize([{target}]),config:()=>config,reverted:()=>reverted,disconnected:()=>disconnected,loads:()=>loads,refreshes:()=>refreshes};
}
test('native sticky leaves pin selection to ScrollTrigger and resets on breakpoint exit',async()=>{
 const p=fixture();const release=initializeSticky(p.root,{target:p.target,boundary:p.boundary},p.load);await flush();const c=p.config();
 assert.equal(c.pin,p.target);assert.equal(c.pinType,undefined);assert.equal(c.pinSpacing,false);assert.equal(c.endTrigger,p.boundary);assert.equal(c.start(),'top 56');assert.equal(c.end(),'bottom 456');
 p.media.matches=false;p.media.dispatchEvent(new Event('change'));assert.equal(p.reverted(),1);assert.equal(p.disconnected(),1);assert.equal(p.timers.size,0);release();
});

test('switching the actual smooth owner rebuilds only the sticky pin selection',async()=>{
 const p=fixture();setSmoothScroller(p.root.ownerDocument,{});
 const stop=initializeSticky(p.root,{target:p.target,boundary:p.boundary},p.load);await flush();
 assert.equal(p.config().pinType,'transform');
 setSmoothScroller(p.root.ownerDocument,null);await flush();
 assert.equal(p.config().pinType,undefined);assert.equal(p.reverted(),1);stop();
});
test('resize/font changes coalesce into one refresh instead of refreshing every animation frame',async()=>{
 const p=fixture();const release=initializeSticky(p.root,{target:p.target,boundary:p.boundary,gap:16},p.load);await flush();p.resize();p.resize();p.resize();assert.equal(p.timers.size,1);
 [...p.timers.values()][0]();assert.equal(p.refreshes(),1);release();assert.equal(p.timers.size,0);
});
test('mobile/reduced mode skips the vendor and late imports cannot revive a disposed sticky target',async()=>{
 const p=fixture(false);const release=initializeSticky(p.root,{target:p.target,boundary:p.boundary},p.load);await flush();assert.equal(p.loads(),0);release();
 const q=fixture();let done;const loading=new Promise(resolve=>{done=resolve;});const stop=initializeSticky(q.root,{target:q.target,boundary:q.boundary},()=>loading);stop();done(q.vendor);await flush();assert.equal(q.config(),undefined);
});
