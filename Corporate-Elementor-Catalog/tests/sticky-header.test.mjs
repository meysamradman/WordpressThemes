import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeStickyHeader,firstSection} from '../radmanct/src/js/components/sticky-header.js';

function fixture(portal=false) {
  const frames=new Map();const listeners=new Map();const classes=new Set();const tweens=[];let next=0;let bottom=900;
  const events={addEventListener:(key,fn)=>listeners.set(key,fn),removeEventListener:key=>listeners.delete(key)};
  let geometryReads=0;let geometryWrites=0;let sets=0;
  const placeholder={style:{},setAttribute(){},hidden:true,getBoundingClientRect:()=>{++geometryReads;return {width:1440,left:0};},before:root=>root.parent='content',remove(){}};
  const section={matches:()=>true,getBoundingClientRect:()=>({bottom,height:900})};
  const main={children:[section],querySelector:()=>null,querySelectorAll:()=>[section]};
  const desktop={...events,matches:false};const reduced={...events,matches:false};
  const view={...events,matchMedia:query=>query.includes('reduce')?reduced:desktop,getComputedStyle:()=>({getPropertyValue:()=>32}),requestAnimationFrame:fn=>{const id=++next;frames.set(id,fn);return id;},cancelAnimationFrame:id=>frames.delete(id)};
  const doc={...events,defaultView:view,querySelector:()=>main,createElement:()=>placeholder,body:{append:root=>root.parent='body'}};
  const style=new Proxy({getPropertyValue:()=>'',getPropertyPriority:()=>'',setProperty(){},removeProperty(name){delete this[name];}}, {set(target,key,value){++geometryWrites;target[key]=value;return true;}});
  const root={ownerDocument:doc,isConnected:true,parent:'content',style,dataset:{stickyMode:'all'},classList:{contains:name=>classes.has(name),add:name=>classes.add(name),remove:name=>classes.delete(name)},after(){},addEventListener(){},removeEventListener(){},getBoundingClientRect:()=>({height:70,width:1440,left:0})};
  const vendor={context:()=>({add:fn=>fn(),revert(){}}),set(){++sets;},to:(node,options)=>{const tween={options,kill(){this.killed=true;}};tweens.push(tween);return tween;}};
  const cleanup=initializeStickyHeader(root,{portal,vendor});
  const move=value=>{bottom=value;listeners.get('rdct:smooth-update')();for(const [id,fn]of frames){frames.delete(id);fn();}};
  return{root,classes,placeholder,tweens,move,cleanup,desktop,reduced,counts:()=>({geometryReads,geometryWrites,sets})};
}

for(const portal of [false,true])test(`after-first-section waits, animates, reverses and restores (smooth=${portal})`,()=>{
  const page=fixture(portal);
  assert.equal(page.classes.has('is-sticky-active'),false);assert.equal(page.tweens.length,0);
  page.move(33);assert.equal(page.tweens.length,0);
  page.move(31);assert.equal(page.classes.has('is-sticky-active'),true);assert.equal(page.tweens.at(-1).options.duration,.32);assert.equal(page.placeholder.hidden,false);
  assert.equal(page.root.parent,portal?'body':'content');
  page.move(80);const exit=page.tweens.at(-1);assert.equal(exit.options.duration,.22);assert.equal(page.classes.has('is-sticky-active'),true);
  page.move(20);exit.options.onComplete();assert.equal(page.classes.has('is-sticky-active'),true);
  page.move(80);page.tweens.at(-1).options.onComplete();assert.equal(page.classes.has('is-sticky-active'),false);assert.equal(page.root.parent,'content');assert.equal(page.placeholder.hidden,true);
  page.cleanup();page.cleanup();
});

test('missing first section never falls back to immediate sticky',()=>{
  const root={ownerDocument:{querySelector:()=>null},removeAttribute(){throw Error('Must retain delayed-start marker');}};
  initializeStickyHeader(root)();assert.equal(firstSection(root.ownerDocument),null);
});

test('desktop-only mode follows the breakpoint and reduced motion restores without an exit tween',()=>{
  const page=fixture();page.root.dataset.stickyMode='desktop';
  page.move(20);assert.equal(page.classes.has('is-sticky-active'),false);
  page.desktop.matches=true;page.move(20);assert.equal(page.classes.has('is-sticky-active'),true);
  page.reduced.matches=true;page.desktop.matches=false;page.move(20);
  assert.equal(page.classes.has('is-sticky-active'),false);assert.equal(page.placeholder.hidden,true);page.cleanup();
});

test('steady smooth scrolling reads portal bounds once and avoids unchanged style writes',()=>{
  const page=fixture(true);page.move(20);
  const before=page.counts();
  page.move(19);page.move(18);
  assert.equal(page.counts().geometryReads-before.geometryReads,2);
  assert.equal(page.counts().geometryWrites,before.geometryWrites);
  page.reduced.matches=true;page.move(17);
  const settled=page.counts().sets;
  page.move(16);page.move(15);
  assert.equal(page.counts().sets,settled);
  page.cleanup();
});
