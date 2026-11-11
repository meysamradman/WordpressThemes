import test from 'node:test';
import assert from 'node:assert/strict';
import {createStickyScene} from '../radmanct/src/js/pages/home/scene.js';

function fixture(){
 const doc=new EventTarget(),view=new EventTarget(),media=new EventTarget(),classes=new Set();
 const frames=new Map();let id=0,mounts=0,reverts=0,refreshes=0;
 doc.defaultView=view;doc.querySelectorAll=()=>[];doc.activeElement=null;
 Object.assign(view,{innerWidth:1440,innerHeight:900,matchMedia:()=>media,queueMicrotask,CustomEvent:class extends Event{constructor(type,options){super(type);this.detail=options.detail;}},
  requestAnimationFrame:fn=>{frames.set(++id,fn);return id;},cancelAnimationFrame:n=>frames.delete(n)});
 media.matches=true;
 const items=[0,1].map(()=>({classList:{contains:()=>true,remove(){},toggle(){}},querySelector:()=>({offsetHeight:500})}));
 const root=Object.assign(new EventTarget(),{ownerDocument:doc,dataset:{},children:items,
  classList:{add:n=>classes.add(n),remove:n=>classes.delete(n)},style:{removeProperty(){},setProperty(){}},
  parentElement:{closest:()=>null},querySelector:()=>null,contains:n=>n===link||n===other});
 const makeLink=()=>({visible:true,getBoundingClientRect:()=>({top:600}),matches(){return this.visible;},focus(options){assert.equal(options.preventScroll,true);doc.activeElement=this;root.dispatchEvent(new Event('focusin'));}});
 const link=makeLink(),other=makeLink();
 const gsap={registerPlugin(){},context(fn){mounts++;fn();return {revert(){reverts++;doc.activeElement=null;}};}};
 const stop=createStickyScene(root,gsap,{refresh(){refreshes++;}},()=>{});
 const flush=()=>{const work=[...frames.values()];frames.clear();work.forEach(fn=>fn());};
 return {doc,view,root,link,other,stop,flush,frames,classes,counts:()=>({mounts,reverts,refreshes})};
}

test('pointer focus retains the pin; keyboard focus survives reparenting and restores the pin on exit',async()=>{
 const p=fixture();p.link.visible=false;p.link.focus({preventScroll:true});p.flush();
 assert.equal(p.classes.has('is-enh'),true);assert.equal(p.counts().mounts,1);
 p.link.visible=true;p.link.focus({preventScroll:true});p.flush();
 assert.equal(p.classes.has('is-enh'),false);assert.equal(p.doc.activeElement,p.link);
 p.doc.activeElement=p.other;p.root.dispatchEvent(new Event('focusout'));await Promise.resolve();p.flush();
 assert.equal(p.classes.has('is-enh'),false);assert.equal(p.doc.activeElement,p.other);
 p.doc.activeElement=null;p.root.dispatchEvent(new Event('focusout'));await Promise.resolve();p.flush();
 assert.equal(p.classes.has('is-enh'),true);assert.equal(p.counts().mounts,2);
 p.stop();p.root.dispatchEvent(new Event('focusin'));assert.equal(p.frames.size,0);
});

test('unchanged resize events share one frame and never recreate an identical scene',()=>{
 const p=fixture();for(let i=0;i<20;i++)p.view.dispatchEvent(new Event('resize'));
 assert.equal(p.frames.size,1);p.flush();assert.equal(p.counts().mounts,1);
 p.view.innerHeight=768;p.view.dispatchEvent(new Event('resize'));p.flush();assert.equal(p.counts().mounts,2);
 p.stop();
});
