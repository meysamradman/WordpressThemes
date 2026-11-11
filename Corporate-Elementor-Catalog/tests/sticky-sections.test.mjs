import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';
import {mount as stack} from '../radmanct-components/src/js/components/sticky-sections/stack.js';
import {mount as panels} from '../radmanct-components/src/js/components/sticky-sections/panels.js';
import {mount as split} from '../radmanct-components/src/js/components/sticky-sections/split.js';

function fixture(mode, count = 3) {
  const node = () => {
    const target = new EventTarget(); const classes = new Set(); const styles = new Map();
    Object.assign(target,{offsetHeight:300,classList:{contains:(key)=>classes.has(key),add:(key)=>classes.add(key),remove:(key)=>classes.delete(key),toggle:(key,on)=>on?classes.add(key):classes.delete(key)},style:{setProperty:(key,value)=>styles.set(key,value),removeProperty:(key)=>styles.delete(key)}});
    return target;
  };
  const view = new EventTarget(); const media = new EventTarget(); media.matches = true;
  let next = 0; const frames = new Map(); const animations = []; const pins = []; let reverted = 0;
  Object.assign(view,{innerWidth:1440,innerHeight:900,matchMedia:()=>media,requestAnimationFrame:(fn)=>{frames.set(++next,fn);return next;},cancelAnimationFrame:(key)=>frames.delete(key)});
  const root = node(); const inners = Array.from({length:count},node); const items = inners.map((inner)=>{const item=node();item.classList.add('rdct-ss__item');item.querySelector=()=>inner;return item;});
  const fixed = node(); fixed.offsetHeight=200; const list={children:items,offsetHeight:1000};
  Object.assign(root,{dataset:{mode,offset:'30',spacer:'30',minScale:'.8',minWidth:'992'},isConnected:true,children:items,ownerDocument:{defaultView:view,querySelectorAll:()=>[]},querySelector:(selector)=>selector.includes('__list')?mode==='split'?list:null:selector.includes('__fixed')?mode==='split'?fixed:null:null});
  const gsap = {registerPlugin(){}, context(fn){fn();return{revert(){reverted++;}};},to(target,options){animations.push({target,options});}};
  const ScrollTrigger = {create:(options)=>pins.push(options)};
  const flush = () => {const work=[...frames.values()];frames.clear();work.forEach((fn)=>fn());};
  return {root,items,inners,fixed,media,view,animations,pins,flush,vendor:{gsap,ScrollTrigger},reverted:()=>reverted};
}

test('Hub stack and zoom share GSAP timing while keeping their distinct depth and spacing',()=>{
  for(const mode of ['stack','zoom']) {
    const page=fixture(mode);const cleanup=stack(page.root,page.vendor);
    assert.equal(page.animations.length,2);assert.equal(page.animations[0].options.scale,mode==='zoom'?.93:.8);
    assert.equal(page.animations[0].options.scrollTrigger.scrub,.25);
    page.media.matches=false;page.media.dispatchEvent(new Event('change'));page.flush();
    assert.equal(page.root.classList.contains('is-enhanced'),false);assert.equal(page.reverted(),1);
    cleanup();cleanup();
  }
});

test('service panels pin existing content and skip cards taller than the viewport',()=>{
  const page=fixture('services');page.inners[0].offsetHeight=1200;
  const cleanup=panels(page.root,page.vendor);
  assert.equal(page.pins.length,1);assert.equal(page.pins[0].pin,page.inners[1]);assert.equal(page.pins[0].pinSpacing,false);
  page.root.dispatchEvent(new Event('focusin'));page.flush();
  assert.equal(page.root.classList.contains('is-enhanced'),false);assert.equal(page.reverted(),1);
  cleanup();
});

test('split pins only its arbitrary fixed column and preserves tall-content fallback',()=>{
  for(const tall of [false,true]) {
    const page=fixture('split');if(tall)page.fixed.offsetHeight=1400;
    const cleanup=split(page.root,page.vendor);
    assert.equal(page.pins.length,tall?0:1);
    if(!tall)assert.equal(page.pins[0].pin,page.fixed);
    assert.equal(page.root.classList.contains('is-enhanced'),!tall);cleanup();
  }
});

test('a fixed column also supports one long arbitrary scrolling template',()=>{
  const page = fixture('split',1); const cleanup = split(page.root,page.vendor);
  assert.equal(page.pins.length,1);cleanup();
});

test('sticky dispatcher skips animation downloads on mobile and cannot mount after teardown',async()=>{
  const source=await readFile(new URL('../radmanct-components/src/js/components/sticky-sections.js',import.meta.url),'utf8');
  for(const released of [false,true]) {
    const page=fixture('services');page.media.matches=false;
    let resolve;let downloads=0;let initialized=0;let destroyed=0;
    const pending=new Promise((done)=>{resolve=done;});
    const context=vm.createContext({loadOptional:()=>pending,loadAnimation:async()=>{downloads++;return{};},reportRuntimeError:(_key,error)=>{throw error;}});
    vm.runInContext(source.replace(/^import .*;\r?\n/gm,'').replaceAll('export function','function').replaceAll('import(','loadOptional('),context);
    const cleanup=context.initializeStickySections(page.root);assert.equal(downloads,0);
    page.media.matches=true;page.media.dispatchEvent(new Event('change'));assert.equal(downloads,1);
    if(released)cleanup();
    resolve({mount:()=>{initialized++;return()=>destroyed++;}});await new Promise((done)=>setImmediate(done));
    assert.equal(initialized,released?0:1);cleanup();cleanup();assert.equal(destroyed,released?0:1);
  }
});
