import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeGallery} from '../radmanct/src/js/ui/gallery.js';
class Element extends EventTarget{constructor(){super();this.hidden=false;this.disabled=false;this.dataset={};}removeAttribute(key){delete this[key];}}
function fixture(){
 const doc=new Element();doc.documentElement={dir:'rtl'};doc.defaultView={CustomEvent};
 const image=new Element(),close=new Element(),previous=new Element(),next=new Element(),counter=new Element(),caption=new Element(),status=new Element();
 const nodes={'img':image,'[data-rdc-lb-cls]':close,'[data-rdc-lb-prev]':previous,'[data-rdc-lb-next]':next,'[data-rdc-lb-count]':counter,'[data-rdc-lb-caption]':caption,'[data-rdc-lb-status]':status};
 const dialog=new Element();dialog.ownerDocument=doc;dialog.querySelector=key=>nodes[key];dialog.showModal=()=>{dialog.open=true;};dialog.close=()=>{dialog.open=false;dialog.dispatchEvent(new Event('close'));};
 const group={querySelectorAll:()=>links};
 const links=[0,1,2].map(index=>({href:`http://local.test/${index}.webp`,dataset:{rdcCaption:`Caption ${index}`},querySelector:()=>({alt:`Photo ${index}`}),closest:()=>group,focus(){this.focused=true;}}));
 doc.querySelector=()=>links[0];
 function open(index=0){const event=new Event('click',{cancelable:true});Object.defineProperty(event,'target',{value:{closest:()=>links[index]}});doc.dispatchEvent(event);return event;}
 const release=initializeGallery(dialog);return {doc,image,close,previous,next,counter,caption,status,dialog,links,open,release};
}
test('gallery opens one image, moves within its album and restores focus',()=>{
 const f=fixture();assert.equal(f.open().defaultPrevented,true);assert.equal(f.dialog.open,true);assert.equal(f.image.src,f.links[0].href);assert.equal(f.previous.disabled,true);
 f.next.dispatchEvent(new Event('click'));assert.equal(f.image.src,f.links[1].href);assert.equal(f.caption.textContent,'Caption 1');
 f.close.dispatchEvent(new Event('click'));assert.equal(f.dialog.open,false);assert.equal(f.links[0].focused,true);assert.equal(f.image.src,undefined);f.release();
});
test('RTL keyboard and touch navigation respect gallery boundaries',()=>{
 const f=fixture();f.open();const key=new Event('keydown',{cancelable:true});key.key='ArrowLeft';f.dialog.dispatchEvent(key);assert.equal(f.image.src,f.links[1].href);
 const down=new Event('pointerdown');Object.assign(down,{pointerType:'touch',clientX:0,clientY:0});f.image.dispatchEvent(down);
 const up=new Event('pointerup');Object.assign(up,{clientX:70,clientY:2});f.image.dispatchEvent(up);assert.equal(f.image.src,f.links[2].href);assert.equal(f.next.disabled,true);
 f.next.dispatchEvent(new Event('click'));assert.equal(f.image.src,f.links[2].href);f.release();
});
test('unsupported modal leaves the original image link usable',()=>{
 const f=fixture();f.release();delete f.dialog.showModal;const release=initializeGallery(f.dialog);assert.equal(f.open().defaultPrevented,false);release();
});
