import {readFileSync} from 'node:fs';
import {test} from 'node:test';
import assert from 'node:assert/strict';
const source=readFileSync(new URL('../radmanct/src/js/ui/carousel.js',import.meta.url),'utf8').replace(/^import .*;\r?\n/,'').replace('export function','function');
class Target{
 listeners=new Map();hidden=false;disabled=false;scrollLeft=0;captured=false;
 classList={values:new Set(),add(...names){names.forEach(n=>this.values.add(n));},remove(...names){names.forEach(n=>this.values.delete(n));}};
 addEventListener(name,fn){if(!this.listeners.has(name))this.listeners.set(name,new Set());this.listeners.get(name).add(fn);}
 removeEventListener(name,fn){this.listeners.get(name)?.delete(fn);}
 emit(name,event={}){for(const fn of [...this.listeners.get(name)||[]])fn({target:this,preventDefault(){},...event});}
 setPointerCapture(){this.captured=true;}hasPointerCapture(){return this.captured;}releasePointerCapture(){this.captured=false;}
}
function fixture({rtl=true,reduced=false,fit=false}={}){
 const win=new Target(),prev=new Target(),next=new Target(),nav=new Target(),status=new Target(),view=new Target(),frames=new Map();let id=0,time=0,measure,released=false;
 const media=new Target();media.matches=reduced;view.matchMedia=()=>media;view.getComputedStyle=()=>({direction:rtl?'rtl':'ltr',paddingInlineStart:'0'});view.setTimeout=()=>++id;view.clearTimeout=()=>{};view.requestAnimationFrame=fn=>{frames.set(++id,fn);return id;};view.cancelAnimationFrame=key=>frames.delete(key);
 win.clientWidth=300;win.scrollWidth=fit?300:900;win.children=Array.from({length:6},(_,i)=>({offsetLeft:(rtl?-1:1)*150*i,offsetWidth:150,contains:()=>false}));
 const nodes={'[data-rdc-rail-track]':win,'[data-rdc-rail-prev]':prev,'[data-rdc-rail-next]':next,'[data-rdc-rail-nav]':nav,'[data-rdc-rail-status]':status};
 const root={ownerDocument:{defaultView:view},querySelector:key=>nodes[key]};
 const initialize=new Function('observeSizes',source+';return initializeCarousel;')((v,n,fn)=>{measure=fn;return()=>{released=true;};});const dispose=initialize(root);
 const flush=()=>{for(let i=0;frames.size&&i<30;i++){time+=100;const pending=[...frames.values()];frames.clear();pending.forEach(fn=>fn(time));}assert.equal(frames.size,0);};
 return {win,prev,next,nav,view,flush,measure:()=>measure(),dispose,released:()=>released};
}
test('RTL and LTR arrows advance exactly one card; rapid clicks retain their destination',()=>{for(const rtl of [true,false]){const f=fixture({rtl});assert.equal(f.prev.disabled,true);f.next.emit('click');f.flush();assert.equal(f.win.scrollLeft,rtl?-150:150);f.next.emit('click');f.next.emit('click');f.flush();assert.equal(f.win.scrollLeft,rtl?-450:450);f.prev.emit('click');f.flush();assert.equal(f.win.scrollLeft,rtl?-300:300);f.dispose();}});
test('fitting cards retain disabled navigation; resize notifications preserve active motion',()=>{const fit=fixture({fit:true});assert.equal(fit.nav.hidden,false);assert.equal(fit.prev.disabled,true);assert.equal(fit.next.disabled,true);fit.dispose();const f=fixture();f.next.emit('click');f.measure();f.flush();assert.equal(f.win.scrollLeft,-150);f.dispose();});
test('keyboard boundaries, reduced motion and cleanup retain native scrolling',()=>{const f=fixture({reduced:true});f.win.emit('keydown',{key:'End'});assert.equal(f.win.scrollLeft,-600);f.win.emit('scroll');f.flush();assert.equal(f.next.disabled,true);f.win.emit('keydown',{key:'Home'});assert.equal(Math.abs(f.win.scrollLeft),0);f.next.emit('click');assert.equal(f.win.scrollLeft,-150);f.dispose();assert.equal(f.released(),true);assert.equal([...f.win.listeners.values()].every(set=>set.size===0),true);assert.equal(f.nav.hidden,true);});
test('mouse drag snaps to a card and preserves interactive links',()=>{const f=fixture({reduced:true});const target={closest:()=>null};f.win.emit('pointerdown',{target,pointerType:'mouse',isPrimary:true,button:0,pointerId:1,clientX:0});f.win.emit('pointermove',{pointerId:1,clientX:160});f.win.emit('pointerup',{pointerId:1});assert.equal(f.win.scrollLeft,-150);f.win.emit('pointerdown',{target:{closest:()=>({})},pointerType:'mouse',isPrimary:true,button:0,pointerId:2,clientX:0});f.win.emit('pointermove',{pointerId:2,clientX:160});assert.equal(f.win.scrollLeft,-150);f.dispose();});

