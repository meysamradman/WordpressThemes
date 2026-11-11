import test from 'node:test';
import assert from 'node:assert/strict';
import {pinSmoothSidebars} from '../radmanct/src/js/smooth-sidebar.js';

test('smooth sidebar uses the existing pin engine, respects container bounds and fully releases on breakpoint teardown',()=>{
  const listeners=new Map(),frames=new Map(),attributes=new Set(),classes=new Set();
  let disconnected=false,killed=0,refreshes=0,cleanup;
  const parent={};
  const view={getComputedStyle:()=>({getPropertyValue:()=> '100px',fontSize:'16px'}),requestAnimationFrame:fn=>{frames.set(1,fn);return 1;},cancelAnimationFrame:id=>frames.delete(id),ResizeObserver:class{observe(){}disconnect(){disconnected=true;}}};
  const doc={defaultView:view,documentElement:{},addEventListener:(key,fn)=>listeners.set(key,fn),removeEventListener:key=>listeners.delete(key)};
  const root={parentElement:parent,offsetHeight:500,setAttribute:key=>attributes.add(key),removeAttribute:key=>attributes.delete(key),classList:{add:key=>classes.add(key),remove:key=>classes.delete(key)}};
  const configs=[];
  const ScrollTrigger={create:config=>{configs.push(config);return {kill(){killed++;}};},refresh(){refreshes++;}};
  const gsap={matchMedia:()=>({add:(query,fn)=>{assert.equal(query,'(min-width:64.01rem)');cleanup=fn();},revert:()=>cleanup()})};
  const release=pinSmoothSidebars({ownerDocument:doc,querySelectorAll:()=>[root]},gsap,ScrollTrigger);
  assert.equal(configs.length,1);
  assert.equal(configs[0].pin,root);
  assert.equal(configs[0].endTrigger,parent);
  assert.equal(configs[0].start(),'top top+=116');
  assert.equal(configs[0].end(),'bottom top+=616');
  assert.equal(configs[0].pinType,'transform');
  assert.equal(configs[0].pinReparent,undefined);
  listeners.get('rdct:archive-updated')();listeners.get('rdct:sidebar-offset')();
  assert.equal(frames.size,1);
  release();
  assert.equal(killed,1);assert.equal(disconnected,true);assert.equal(listeners.size,0);assert.equal(frames.size,0);assert.equal(attributes.size,0);assert.equal(classes.size,0);assert.equal(refreshes,1);
});

test('a tall natural sidebar pins only after its bottom is reachable and recalculates after resizing',()=>{
  let config,cleanup,top=-416;
  const view={getComputedStyle:()=>({getPropertyValue:()=>String(top),fontSize:'16px'}),ResizeObserver:class{observe(){}disconnect(){}}};
  const doc={defaultView:view,documentElement:{},addEventListener(){},removeEventListener(){}};
  const root={parentElement:{},offsetHeight:1200,hasAttribute:key=>key==='data-rdct-natural-sidebar',setAttribute(){},removeAttribute(){}};
  const gsap={matchMedia:()=>({add:(_,fn)=>{cleanup=fn();},revert:()=>cleanup()})};
  const release=pinSmoothSidebars({ownerDocument:doc,querySelectorAll:()=>[root]},gsap,{create:value=>{config=value;return {kill(){}};},refresh(){}});
  assert.equal(config.start(),'top top-=416');
  assert.equal(config.end(),'bottom top+=784');
  top=116;root.offsetHeight=400;
  assert.equal(config.start(),'top top+=116');
  assert.equal(config.end(),'bottom top+=516');
  release();
});
