import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeMenuPanel} from '../radmanct/src/js/ui/menu-panel.js';

const deferred=()=>{let resolve;const promise=new Promise(done=>{resolve=done;});return {promise,resolve};};
const flush=async()=>{for(let n=0;n<12;n++)await Promise.resolve();};
function fixture(reduced=false){
 const doc=new EventTarget();doc.defaultView=Object.assign(new EventTarget(),{CustomEvent,matchMedia:()=>({matches:reduced}),location:{href:'http://localhost/',origin:'http://localhost',pathname:'/'}});
 const dialog=new EventTarget();const attributes=new Set();dialog.ownerDocument=doc;dialog.open=false;const styles=new Map();dialog.style={setProperty:(key,value)=>styles.set(key,value),getPropertyValue:key=>styles.get(key),removeProperty:key=>styles.delete(key)};dialog.querySelectorAll=()=>[];
 dialog.setAttribute=name=>attributes.add(name);dialog.removeAttribute=name=>attributes.delete(name);
 dialog.showModal=()=>{dialog.open=true;};dialog.close=()=>{dialog.open=false;dialog.dispatchEvent(new Event('close'));};
 const trigger=new EventTarget();trigger.setAttribute=()=>{};const close=new EventTarget();const calls=[];const finish=deferred();
 const effect={play(open){calls.push(open);return open?Promise.resolve(true):finish.promise;},cancel(){},destroy(){}};
 return {doc,dialog,trigger,close,calls,finish,effect,attributes};
}
test('repeated close clicks wait for one complete closing animation',async()=>{
 const p=fixture();const release=initializeMenuPanel(p.dialog,p.trigger,p.close,async()=>({createPanelMotion:()=>p.effect}));
 p.trigger.dispatchEvent(new Event('click'));await flush();
 p.close.dispatchEvent(new Event('click'));p.close.dispatchEvent(new Event('click'));
 assert.deepEqual(p.calls,[true,false]);assert.equal(p.dialog.open,true);
 p.finish.resolve(true);await flush();assert.equal(p.dialog.open,false);assert.equal(p.attributes.has('data-panel-closing'),false);release();
});
test('closing before the lazy animation import resolves still animates before hiding',async()=>{
 const p=fixture();const loading=deferred();const release=initializeMenuPanel(p.dialog,p.trigger,p.close,()=>loading.promise);
 p.trigger.dispatchEvent(new Event('click'));p.close.dispatchEvent(new Event('click'));assert.equal(p.dialog.open,true);
 loading.resolve({createPanelMotion:()=>p.effect});await flush();assert.deepEqual(p.calls,[false]);assert.equal(p.dialog.open,true);
 p.finish.resolve(true);await flush();assert.equal(p.dialog.open,false);release();
});
test('reduced motion can open and close repeatedly without loading the motion engine',async()=>{
 const p=fixture(true);const release=initializeMenuPanel(p.dialog,p.trigger,p.close,()=>assert.fail('Unexpected motion import'));
 for(let n=0;n<2;n++){p.trigger.dispatchEvent(new Event('click'));assert.equal(p.dialog.open,true);p.close.dispatchEvent(new Event('click'));await flush();assert.equal(p.dialog.open,false);}
 release();
});

test('close control aligns to the opener in RTL/LTR and updates after viewport resize',async()=>{
 for(const direction of ['rtl','ltr']){
  const p=fixture();p.doc.defaultView.getComputedStyle=()=>({direction});
  p.dialog.getBoundingClientRect=()=>({left:0,right:1200});let box={top:57,left:1094,right:1140,width:46,height:46};p.trigger.getBoundingClientRect=()=>box;
  const release=initializeMenuPanel(p.dialog,p.trigger,p.close,async()=>({createPanelMotion:()=>p.effect}));p.trigger.dispatchEvent(new Event('click'));await flush();
  assert.equal(p.dialog.style.getPropertyValue('--rdc-pnl-cls-top'),'57px');assert.equal(p.dialog.style.getPropertyValue('--rdc-pnl-cls-st'),direction==='rtl'?'60px':'1094px');assert.equal(p.dialog.style.getPropertyValue('--rdc-pnl-cls-sz'),'46px');
  box={...box,top:25};p.doc.defaultView.dispatchEvent(new Event('resize'));assert.equal(p.dialog.style.getPropertyValue('--rdc-pnl-cls-top'),'25px');release();assert.equal(p.dialog.style.getPropertyValue('--rdc-pnl-cls-top'),undefined);
 }
});
