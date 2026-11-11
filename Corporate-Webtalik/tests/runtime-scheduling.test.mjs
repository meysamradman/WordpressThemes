import test from 'node:test';
import assert from 'node:assert/strict';
import {observeSizes,observeMutations} from '../radmanct/src/js/runtime/observers.js';
import {createRefreshRequest} from '../radmanct/src/js/runtime/refresh.js';

test('size subscribers share an observer and release only their own targets',()=>{
 let deliver,instances=0,disconnects=0;const watched=new Set();
 const view={ResizeObserver:class{constructor(fn){instances++;deliver=fn;}observe(n){watched.add(n);}unobserve(n){watched.delete(n);}disconnect(){disconnects++;}}};
 const a={},b={};let first=0,second=0;
 const stopA=observeSizes(view,[a,b],entries=>{first++;assert.equal(entries.length,2);});
 const stopB=observeSizes(view,[b],()=>second++);
 assert.equal(instances,1);deliver([{target:a},{target:b}]);assert.equal(first,1);assert.equal(second,1);
 stopA();deliver([{target:b}]);assert.equal(first,1);assert.equal(second,2);assert.equal(disconnects,0);assert.deepEqual([...watched],[b]);
 stopB();assert.equal(disconnects,1);assert.equal(watched.size,0);
});

test('mutation subscribers share delivery while preserving independent cleanup',()=>{
 let deliver,instances=0,disconnects=0,a=0,b=0;
 const view={document:{documentElement:{}},MutationObserver:class{constructor(fn){instances++;deliver=fn;}observe(){}disconnect(){disconnects++;}}};
 const stopA=observeMutations(view,()=>a++),stopB=observeMutations(view,()=>b++);
 deliver([]);stopA();deliver([]);assert.equal(instances,1);assert.equal(a,1);assert.equal(b,2);assert.equal(disconnects,0);
 stopB();assert.equal(disconnects,1);
});

test('refresh requests coalesce and removing one owner does not cancel another',()=>{
 const timers=new Map();let id=0;const calls=[];
 const view={setTimeout:fn=>{timers.set(++id,fn);return id;},clearTimeout:n=>timers.delete(n)};
 const vendor={refresh:safe=>calls.push(safe)};
 const a=createRefreshRequest(view,vendor),b=createRefreshRequest(view,vendor);
 a.request();b.request();b.request();assert.equal(timers.size,1);a.dispose();assert.equal(timers.size,1);
 [...timers.values()][0]();assert.deepEqual(calls,[true]);
 b.request();b.flush();assert.equal(timers.size,0);assert.deepEqual(calls,[true,undefined]);
 b.request();b.dispose();assert.equal(timers.size,0);
});
