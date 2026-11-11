import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeGallery} from '../radmanct/src/js/ui/gallery.js';

function fixture(){
  const doc=new EventTarget();doc.defaultView={CustomEvent};
  const button=new EventTarget(),image={removeAttribute(name){delete this[name];}};
  const link={href:'https://local.test/photo.webp',querySelector:()=>({alt:'Gallery image'})};
  link.closest=()=>link;doc.querySelector=()=>link;
  const dialog=new EventTarget();dialog.ownerDocument=doc;dialog.open=false;
  dialog.querySelector=selector=>selector==='img'?image:button;
  dialog.showModal=()=>{dialog.open=true;};
  dialog.close=()=>{dialog.open=false;queueMicrotask(()=>dialog.dispatchEvent(new Event('close')));};
  const locks=[];doc.addEventListener('rdc:pnl-lock',event=>locks.push(event.detail.locked));
  const release=initializeGallery(dialog);
  const click=(options={})=>{const event=new Event('click',{cancelable:true});Object.defineProperty(event,'target',{value:link});Object.assign(event,options);doc.dispatchEvent(event);return event;};
  return{dialog,button,image,locks,release,click};
}

test('gallery opens an attachment, preserves its alt and unlocks on native close',async()=>{
  const f=fixture();assert.equal(f.click().defaultPrevented,true);
  assert.equal(f.dialog.open,true);assert.equal(f.image.src,'https://local.test/photo.webp');
  assert.equal(f.image.alt,'Gallery image');assert.deepEqual(f.locks,[true]);
  f.button.dispatchEvent(new Event('click'));await Promise.resolve();
  assert.equal(f.dialog.open,false);assert.equal(f.image.src,undefined);assert.deepEqual(f.locks,[true,false]);f.release();
});

test('gallery retains native navigation for modified, secondary and handled clicks',()=>{
  const f=fixture();for(const options of [{ctrlKey:true},{metaKey:true},{shiftKey:true},{altKey:true},{button:1}]){
    assert.equal(f.click(options).defaultPrevented,false);assert.equal(f.dialog.open,false);
  }
  assert.deepEqual(f.locks,[]);f.release();
});

test('disposing an open gallery restores scrolling once and removes delegated handlers',async()=>{
  const f=fixture();f.click();f.release();f.release();await Promise.resolve();
  assert.deepEqual(f.locks,[true,false]);assert.equal(f.image.src,undefined);
  assert.equal(f.click().defaultPrevented,false);assert.equal(f.dialog.open,false);
});
