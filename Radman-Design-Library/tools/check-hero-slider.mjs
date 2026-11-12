// Bounded interaction checks without a browser. Rendering/real touch need visual QA.
import {readFileSync} from 'node:fs';
import {runInNewContext} from 'node:vm';
import assert from 'node:assert/strict';

const file=new URL('../radman-library/modules/hero-slider/script.js',import.meta.url);
const source=readFileSync(file,'utf8').replace(/^import .*;\r?\n/gm,'').replace('export function','function').split('const page=createPageLifecycle')[0];
const initialize=runInNewContext(`${source}\ninitializeHeroSlider;`,{AbortController});
class Node extends EventTarget{
 constructor(parent){super();this.parent=parent;this.attrs=new Map();this.style={};this.hidden=false;this.inert=false;this.textContent='';this.children=[];this.single={};this.multiple={};parent?.children.push(this);}
 querySelector(key){return this.single[key]??null;}
 querySelectorAll(key){return this.multiple[key]??[];}
 getAttribute(key){return this.attrs.get(key)??null;}
 setAttribute(key,value){this.attrs.set(key,String(value));}
 removeAttribute(key){this.attrs.delete(key);if(key==='style')this.style={};}
 contains(node){return node===this||this.children.some(child=>child.contains(node));}
 closest(selector){return this.interactive&&selector.includes('button')?this:null;}
 focus(){this.ownerDocument.activeElement=this;}
 setPointerCapture(id){this.capture=id;}
 hasPointerCapture(id){return this.capture===id;}
 releasePointerCapture(){this.capture=undefined;}
}
function fire(node,type,values={}){
 const event=new Event(type,{cancelable:true});
 for(const [key,value]of Object.entries(values))Object.defineProperty(event,key,{value});
 node.dispatchEvent(event);return event;
}
function fixture({reduced=false,direction='rtl'}={}){
 const doc=new Node(),view=new Node(),root=new Node(),win=new Node(root),nav=new Node(root),number=new Node(nav),status=new Node(root),prev=new Node(nav),next=new Node(nav);
 doc.defaultView=view;doc.hidden=false;root.ownerDocument=doc;doc.activeElement=root;win.clientWidth=1000;nav.hidden=true;
 const media=new Node();media.matches=reduced;
 view.matchMedia=()=>media;view.getComputedStyle=()=>({direction});view.setTimeout=()=>1;view.clearTimeout=()=>{};
 const slides=Array.from({length:3},(_,index)=>{
  const slide=new Node(win),md=new Node(slide),image=new Node(md),heading=new Node(slide),link=new Node(slide);
  heading.textContent=`Slide ${index+1}`;link.interactive=true;
  slide.single={'[data-rdl-hsl-md]':md,'img':image,'h2':heading};slide.multiple={'[data-rdl-hsl-tx]':[heading,link]};
  return slide;
 });
 root.single={'.rdl-hsl__win':win,'[data-rdl-hsl-nav]':nav,'[data-rdl-hsl-num]':number,'[data-rdl-hsl-status]':status,'[data-rdl-hsl-prev]':prev,'[data-rdl-hsl-next]':next};
 root.multiple={'[data-rdl-hsl-itm]':slides};
 const tweens=[],timelines=[];
 const gsap={set(){},context(initialize){initialize();return{revert(){}};},timeline(){
  const tl={value:0,killed:false,to(){return this;},progress(value){if(value===undefined)return this.value;this.value=value;return this;},duration:()=>1,kill(){this.killed=true;},tweenTo(target,options){
   const tween={killed:false,kill(){this.killed=true;},finish(){if(!this.killed){tl.value=target;options.onComplete();}}};tweens.push(tween);return tween;
  }};timelines.push(tl);return tl;
 }};
 const release=initialize(root,{gsap});
 const flush=()=>tweens.filter(t=>!t.killed).forEach(t=>t.finish());
 const pointer=(type,x,y=10,time=0)=>fire(win,type,{isPrimary:true,button:0,pointerId:1,clientX:x,clientY:y,timeStamp:time});
 return{root,win,nav,number,status,slides,doc,view,media,prev,next,release,flush,pointer,timelines};
}
const active=f=>f.slides.findIndex(slide=>!slide.hidden&&!slide.inert);
let checks=0;
function check(name,fn){fn();checks++;console.log(`PASS ${name}`);}
check('arrows, wrapping, rapid clicks and inactive focus exclusion',()=>{
 const f=fixture();assert.equal(active(f),0);assert.equal(f.nav.hidden,false);
 fire(f.next,'click');f.flush();assert.equal(active(f),1);
 assert.equal(f.slides[0].inert,true);assert.match(f.status.textContent,/2/);
 fire(f.next,'click');fire(f.next,'click');f.flush();assert.equal(active(f),0);
 fire(f.prev,'click');f.flush();assert.equal(active(f),2);f.release();
});
check('RTL drag commits and a short slow drag returns',()=>{
 const f=fixture();f.pointer('pointerdown',100);f.pointer('pointermove',350,10,100);assert.ok(f.timelines.at(-1).progress()>0);
 f.pointer('pointerup',350,10,250);f.flush();assert.equal(active(f),1);
 f.pointer('pointerdown',100);f.pointer('pointermove',120,10,100);f.pointer('pointerup',120,10,250);f.flush();assert.equal(active(f),1);
 f.release();
});
check('drag can reverse direction and pointer cancellation restores current slide',()=>{
 const f=fixture();f.pointer('pointerdown',500);f.pointer('pointermove',700,10,100);f.pointer('pointermove',250,10,200);f.pointer('pointerup',250,10,350);f.flush();assert.equal(active(f),2);
 f.pointer('pointerdown',100);f.pointer('pointermove',350,10,100);f.pointer('pointercancel',350);assert.equal(active(f),2);assert.equal(f.slides.filter(s=>!s.hidden).length,1);f.release();
});
check('vertical touch intent is left to page scrolling',()=>{
 const f=fixture();f.pointer('pointerdown',100,100);const event=f.pointer('pointermove',103,150,100);
 assert.equal(event.defaultPrevented,false);assert.equal(f.timelines.length,0);f.release();
});
check('LTR drag and keyboard direction',()=>{
 const f=fixture({direction:'ltr'});f.pointer('pointerdown',500);f.pointer('pointermove',250,10,100);f.pointer('pointerup',250,10,250);f.flush();assert.equal(active(f),1);
 fire(f.root,'keydown',{key:'ArrowLeft'});f.flush();assert.equal(active(f),0);f.release();
});
check('reduced motion keeps arrows and gestures without reveal timelines',()=>{
 const f=fixture({reduced:true});fire(f.next,'click');assert.equal(active(f),1);assert.equal(f.timelines.length,0);
 f.pointer('pointerdown',100);f.pointer('pointermove',350,10,100);assert.equal(f.slides.filter(s=>!s.hidden).length,1);
 f.pointer('pointerup',350,10,250);assert.equal(active(f),2);assert.equal(f.timelines.length,0);f.release();
});
check('resize cancels a gesture; focus transfers before hiding a focused slide',()=>{
 const f=fixture();f.pointer('pointerdown',100);f.pointer('pointermove',350,10,100);fire(f.view,'resize');assert.equal(active(f),0);
 f.doc.activeElement=f.slides[0].children.at(-1);fire(f.next,'click');f.flush();assert.equal(f.doc.activeElement,f.root);f.release();
});
check('cleanup restores readable slides and removes interaction listeners',()=>{
 const f=fixture();f.pointer('pointerdown',100);f.pointer('pointermove',350,10,100);f.release();
 assert.equal(f.nav.hidden,true);assert.equal(f.root.getAttribute('data-rdl-hsl-on'),null);
 assert.ok(f.slides.every(slide=>!slide.hidden&&!slide.inert&&slide.getAttribute('aria-hidden')===null));
 const count=f.timelines.length;fire(f.next,'click');assert.equal(f.timelines.length,count);
});
console.log(`${checks} interaction checks passed (mock DOM/GSAP; not browser proof).`);
