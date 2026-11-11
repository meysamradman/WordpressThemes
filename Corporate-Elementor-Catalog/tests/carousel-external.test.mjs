import test from 'node:test';
import assert from 'node:assert/strict';
import {registerCarouselExternal} from '../radmanct-components/src/js/components/carousel/external.js';

function page() {
  const nodes=[]; const listeners=new Map(); let notify; let disconnected=false;
  const doc={documentElement:{},querySelectorAll:(selector) => nodes.filter((node) => node.classes.has(selector.slice(1))),
    addEventListener:(type,fn) => listeners.set(type,fn),removeEventListener:(type) => listeners.delete(type),
    defaultView:{MutationObserver:class {constructor(fn){notify=fn;} observe(){} disconnect(){disconnected=true;}}}};
  function node(tag,classes='',parent=null) {
    const attributes=new Map();
    const self={tagName:tag,classes:new Set(classes.split(' ')),parent,disabled:false,
      matches:() => ['BUTTON','A'].includes(tag) || attributes.get('role') === 'button',
      querySelectorAll:() => nodes.filter((candidate) => candidate.parent === self && candidate.matches()),
      getAttribute:(key) => attributes.get(key) ?? null,setAttribute:(key,value) => attributes.set(key,String(value)),removeAttribute:(key) => attributes.delete(key),
      closest(selector){for(let current=self;current;current=current.parent){if(selector.startsWith('.') ? current.classes.has(selector.slice(1)) : current.matches())return current;}return null;}};
    nodes.push(self); return self;
  }
  const fire=(type,target,extra={}) => {const event={type,target,button:0,defaultPrevented:false,preventDefault(){this.defaultPrevented=true;},...extra};listeners.get(type)?.(event);return event;};
  return {doc,node,nodes,listeners,fire,notify:(records=[{type:'childList'}])=>notify?.(records),disconnected:()=>disconnected};
}
function carousel(p,options={}) {
  const handlers=new Set(); const calls=[]; let interacted=0;
  const slider={activeIndex:0,enabled:true,isLocked:false,allowSlidePrev:true,allowSlideNext:true,params:{rewind:false},
    get isBeginning(){return this.activeIndex===0;},get isEnd(){return this.activeIndex===2;},
    on:(_,fn)=>handlers.add(fn),off:(_,fn)=>handlers.delete(fn),
    slideNext(){calls.push('next');this.activeIndex=this.isEnd&&this.params.rewind?0:Math.min(2,this.activeIndex+1);for(const fn of handlers)fn();},
    slidePrev(){calls.push('previous');this.activeIndex=this.isBeginning&&this.params.rewind?2:Math.max(0,this.activeIndex-1);for(const fn of handlers)fn();}};
  const release=registerCarouselExternal({ownerDocument:p.doc},slider,options,()=>++interacted);
  return {slider,calls,release,handlers,refresh:()=>{for(const fn of handlers)fn();},interacted:()=>interacted};
}

test('external controls accept button widgets, respect boundaries and leave unrelated clicks alone',()=>{
  const p=page(); const wrapper=p.node('DIV','next-widget'); const next=p.node('A','',wrapper); const span=p.node('SPAN','',next); const prev=p.node('BUTTON','previous');
  const c=carousel(p,{external_next_class:'next-widget',external_previous_class:'previous'});
  assert.equal(prev.getAttribute('aria-disabled'),'true'); assert.equal(next.getAttribute('aria-disabled'),'false');
  assert.equal(p.fire('click',span).defaultPrevented,true); assert.equal(c.slider.activeIndex,1); assert.equal(prev.getAttribute('aria-disabled'),'false');
  p.fire('click',next); p.fire('click',next); assert.equal(c.slider.activeIndex,2); assert.equal(c.interacted(),2);
  p.fire('click',prev); assert.equal(c.slider.activeIndex,1);
  assert.equal(p.fire('click',p.node('BUTTON','unrelated')).defaultPrevented,false);
  c.release(); assert.equal(next.getAttribute('aria-disabled'),null); assert.equal(p.listeners.size,0); assert.equal(p.disconnected(),true);
});

test('rewind, lock and original disabled state use the existing slider behavior',()=>{
  const p=page(); const prev=p.node('BUTTON','previous'); const next=p.node('A','next');
  const c=carousel(p,{external_previous_class:'previous',external_next_class:'next'});
  c.slider.params.rewind=true; c.refresh(); p.fire('click',prev); assert.equal(c.slider.activeIndex,2);
  p.fire('click',next); assert.equal(c.slider.activeIndex,0);
  c.slider.isLocked=true; c.refresh(); p.fire('click',next); assert.equal(c.calls.length,2);
  c.release(); next.setAttribute('aria-disabled','true');
  const d=carousel(p,{external_next_class:'next'}); p.fire('click',next); assert.equal(d.calls.length,0); d.release();
  assert.equal(next.getAttribute('aria-disabled'),'true');
});

test('keyboard supports role buttons and Space without doubling native button or anchor Enter activation',()=>{
  const p=page(); const anchor=p.node('A','next'); const button=p.node('BUTTON','next'); const role=p.node('DIV','next'); role.setAttribute('role','button');
  const c=carousel(p,{external_next_class:'next'}); c.slider.params.rewind=true;
  p.fire('keydown',anchor,{key:' '}); assert.equal(c.calls.length,1);
  p.fire('keydown',anchor,{key:'Enter'}); assert.equal(c.calls.length,1);
  p.fire('keydown',button,{key:' '}); assert.equal(c.calls.length,1);
  p.fire('keydown',role,{key:'Enter'}); assert.equal(c.calls.length,2);
  p.fire('keydown',role,{key:'Enter',repeat:true}); assert.equal(c.calls.length,2); c.release();
});

test('late controls, duplicate targets, removal and iframe documents remain isolated',()=>{
  const p=page(); const a=carousel(p,{external_next_class:'next'}); const next=p.node('A','next'); p.notify();
  assert.equal(next.getAttribute('aria-disabled'),'false');
  const b=carousel(p,{external_next_class:'next'}); p.fire('click',next); assert.equal(a.calls.length+b.calls.length,0);
  b.release(); p.fire('click',next); assert.equal(a.calls.length,1);
  const other=page(); const own=other.node('BUTTON','next'); const c=carousel(other,{external_next_class:'next'});
  other.fire('click',own); assert.equal(a.calls.length,1); assert.equal(c.calls.length,1);
  p.nodes.splice(p.nodes.indexOf(next),1); p.notify(); assert.equal(next.getAttribute('aria-disabled'),null);
  a.release(); a.release(); assert.equal(a.handlers.size,0); c.release();
});

test('unused, invalid and marquee controls do not install document listeners',()=>{
  for(const options of [{},{external_next_class:'body > a'},{external_next_class:'.next'},{external_next_class:'next',mode:'marquee'}]){
    const p=page(); const c=carousel(p,options); assert.equal(p.listeners.size,0); c.release();
  }
});

test('removing a control class restores its original accessibility state immediately',()=>{
  const p=page(); const next=p.node('BUTTON','next'); const c=carousel(p,{external_next_class:'next'});
  assert.equal(next.getAttribute('aria-disabled'),'false');
  next.classes.delete('next'); p.notify([{type:'attributes',target:next,oldValue:'next'}]);
  assert.equal(next.getAttribute('aria-disabled'),null);
  assert.equal(p.fire('click',next).defaultPrevented,false); c.release();
});
