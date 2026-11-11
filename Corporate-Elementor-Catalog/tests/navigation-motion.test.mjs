import test from 'node:test';
import assert from 'node:assert/strict';
import {waveFrame} from '../radmanct/src/js/navigation/motion/wave.js';
import {createPanelTimeline} from '../radmanct/src/js/navigation/motion/timeline.js';

test('wave cover and reveal exchange a full shape at the handoff and finish empty in both directions',()=>{
  const duration=800; const half=(duration+300)/(duration*2+600);
  assert.equal(waveFrame(0,true,duration).path,'M0 100 V100 Q50 100 100 100 V100 Z');
  assert.equal(waveFrame(half,true,duration).path,'M0 100 V0 Q50 0 100 0 V100 Z');
  assert.equal(waveFrame(1,true,duration).path,'M0 0 V0 Q50 0 100 0 V0 Z');
  assert.equal(waveFrame(0,false,duration).path,'M0 0 V0 Q50 0 100 0 V0 Z');
  assert.equal(waveFrame(half,false,duration).path,'M0 0 V100 Q50 100 100 100 V0 Z');
  assert.equal(waveFrame(1,false,duration).path,'M0 100 V100 Q50 100 100 100 V100 Z');
  assert.equal(waveFrame(.6,true,duration).covered,false);
  assert.equal(waveFrame(.6,false,duration).covered,true);
});

function fixture(reduce=false) {
  const reduced=new EventTarget(); reduced.matches=reduce; const pending=new Map(); let canceled=0; let activeTimeline;
  const nodes=[{inert:false},{inert:true}]; const item={};
  const view={matchMedia:()=>reduced};
  const selector=':scope > .rdct-nav, :scope > [data-rdct-panel-body], :scope > .rdct-menu-panel-header, :scope > .rdct-menu-panel-content';
  const dialog={ownerDocument:{defaultView:view},style:{overflow:'auto'},querySelectorAll:(value)=>value===selector?nodes:[item]};
  const vendor={
    context(fn){fn();return {add:fn=>fn(),revert(){++canceled;activeTimeline?.kill();}};},
    timeline(config){let state,tween;const timeline={kill(){pending.clear();if(state){state.progress=0;tween.onUpdate();}},to(value,options){state=value;tween=options;pending.set(1,config.onComplete);return this;},fromTo(){return this;},play(){return this;}};activeTimeline=timeline;return timeline;},
  };
  return {reduced,pending,nodes,dialog,timeline:createPanelTimeline(dialog,vendor),canceled:()=>canceled};
}

test('reduced-motion changes finish a running panel, restore existing inert state and release scoped GSAP work',async()=>{
  const page=fixture(); const progress=[];
  const result=page.timeline.run(800,(value)=>progress.push(value)); page.timeline.items(true);
  assert.equal(page.nodes[0].inert,true); assert.equal(page.dialog.style.overflow,'hidden');
  page.reduced.matches=true; page.reduced.dispatchEvent(new Event('change'));
  assert.equal(await result,true); assert.equal(progress.at(-1),1); assert.equal(page.pending.size,0);
  assert.deepEqual(page.nodes.map(node=>node.inert),[false,true]); assert.equal(page.dialog.style.overflow,'auto'); assert.ok(page.canceled()>=1);
  page.timeline.destroy();
});

test('cancel/destroy settle pending work without running a late update or leaving content inert',async()=>{
  const page=fixture(); const result=page.timeline.run(800,()=>{});
  page.timeline.cancel(); assert.equal(await result,false); assert.equal(page.pending.size,0);
  assert.deepEqual(page.nodes.map(node=>node.inert),[false,true]);
  const second=page.timeline.run(800,()=>{}); page.timeline.destroy(); page.timeline.destroy();
  assert.equal(await second,false); assert.equal(await page.timeline.run(800,()=>assert.fail('Destroyed timeline drew an update')),false);
  const reduced=fixture(true); const values=[]; assert.equal(await reduced.timeline.run(800,value=>values.push(value)),true); assert.deepEqual(values,[1]); assert.equal(reduced.pending.size,0); reduced.timeline.destroy();
});

test('GSAP revert cannot redraw the covered first frame after successful completion',async()=>{
  const page=fixture(); const values=[];
  const result=page.timeline.run(800,value=>values.push(value));
  const complete=page.pending.get(1); complete();
  assert.equal(await result,true); assert.equal(values.at(-1),1); assert.deepEqual(values,[0,1]);
  assert.equal(page.dialog.style.overflow,'auto'); page.timeline.destroy(); assert.equal(values.at(-1),1);
});
