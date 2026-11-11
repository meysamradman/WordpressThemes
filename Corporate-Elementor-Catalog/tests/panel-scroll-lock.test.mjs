import test from 'node:test';
import assert from 'node:assert/strict';
import {lockPanelScroll} from '../radmanct/src/js/navigation/scroll-lock.js';

test('native stable gutter preserves layout without double compensation and restores nested locks',()=>{
  const doc={body:{style:{overflow:'auto'}},documentElement:{clientWidth:1190,style:{overflow:'',scrollbarGutter:'stable',paddingRight:'4px'}},defaultView:{innerWidth:1200,CSS:{supports:()=>true}}};
  const first=lockPanelScroll(doc,{});const second=lockPanelScroll(doc,{});
  assert.equal(doc.documentElement.style.scrollbarGutter,'stable');
  assert.equal(doc.documentElement.style.paddingRight,'4px');
  first();assert.equal(doc.documentElement.style.overflow,'hidden');
  second();assert.deepEqual(doc.documentElement.style,{overflow:'',scrollbarGutter:'stable',paddingRight:'4px'});
});

test('smooth-scroll receives the pause before root overflow or gutter changes',()=>{
  const doc={body:{style:{overflow:'auto'}},documentElement:{clientWidth:1185,style:{overflow:'visible',scrollbarGutter:'stable',paddingRight:''}},defaultView:{innerWidth:1200,CustomEvent:class{constructor(type,options){this.detail=options.detail;}}}};
  const values=[];
  doc.dispatchEvent=event=>{if(event.detail.locked)values.push([doc.documentElement.style.overflow,doc.documentElement.style.scrollbarGutter,doc.body.style.overflow]);};
  const release=lockPanelScroll(doc,{});
  assert.deepEqual(values,[['visible','stable','auto']]);
  release();
});

test('nested panels release the shared document lock in either order and restore the initial value once',()=>{
  for(const reversed of [false,true]) {
    const doc={body:{style:{overflow:'auto'}}}; const first=lockPanelScroll(doc,{}); const second=lockPanelScroll(doc,{});
    const releases=reversed?[second,first]:[first,second];
    releases[0](); releases[0](); assert.equal(doc.body.style.overflow,'hidden');
    releases[1](); releases[1](); assert.equal(doc.body.style.overflow,'auto');
    const next=lockPanelScroll(doc,{}); next(); assert.equal(doc.body.style.overflow,'auto');
  }
});

test('page root lock and smoother notifications last until the last panel releases',()=>{
  const events=[];
  const doc={body:{style:{overflow:'auto'}},documentElement:{clientWidth:1185,style:{overflow:'clip',scrollbarGutter:'auto',paddingRight:''}},defaultView:{innerWidth:1200,CustomEvent:class{constructor(type,options){this.type=type;this.detail=options.detail;}}},dispatchEvent:event=>events.push(event.detail.locked)};
  const first=lockPanelScroll(doc,{});const second=lockPanelScroll(doc,{});
  assert.equal(doc.documentElement.style.overflow,'hidden');assert.equal(doc.documentElement.style.scrollbarGutter,'auto');assert.equal(doc.documentElement.style.paddingRight,'calc(0px + 15px)');
  first();assert.deepEqual(events,[true]);
  second();assert.deepEqual(events,[true,false]);
  assert.deepEqual(doc.documentElement.style,{overflow:'clip',scrollbarGutter:'auto',paddingRight:''});
});

test('short pages do not gain a gutter and nested panels share the original scrollbar gap',()=>{
  for(const gap of [0,15]){
    const doc={body:{style:{overflow:''}},documentElement:{clientWidth:1200-gap,style:{overflow:'',scrollbarGutter:'auto'}},defaultView:{innerWidth:1200}};
    const values=[];const owner=()=>({style:{setProperty:(key,value)=>values.push([key,value])}});
    const first=lockPanelScroll(doc,owner());doc.documentElement.clientWidth=1200;
    const second=lockPanelScroll(doc,owner());
    assert.equal(doc.documentElement.style.scrollbarGutter,'auto');
    assert.equal(doc.documentElement.style.paddingRight || '',gap?'calc(0px + 15px)':'');
    assert.deepEqual(values,[]);
    first();assert.equal(doc.body.style.overflow,'hidden');second();
    assert.equal(doc.documentElement.style.scrollbarGutter,'auto');assert.equal(doc.body.style.overflow,'');
  }
});
