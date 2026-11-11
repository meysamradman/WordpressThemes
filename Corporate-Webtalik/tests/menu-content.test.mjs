import test from 'node:test';
import assert from 'node:assert/strict';
import {initializePanelContent} from '../radmanct/src/js/ui/panel/content.js';
function fixture(desktop=false){
 const media=new EventTarget();media.matches=desktop;let clones=0,appends=0,resets=0;
 const mobile={},host={append(){appends++;},replaceChildren(){appends=0;}},template={dataset:{label:'Start a conversation'},content:{cloneNode(){clones++;return {};}}};
 const attrs=new Map([['aria-label','Menu']]),triggerAttrs=new Map([['aria-label','Open menu']]);
 const dialog={open:false,ownerDocument:{defaultView:{matchMedia:()=>media}},querySelector:key=>({'[data-rdc-pnl-mob]':mobile,'[data-rdc-pnl-desk-host]':host,'[data-rdc-pnl-desk]':template}[key]),getAttribute:key=>attrs.get(key),setAttribute:(key,value)=>attrs.set(key,value)};
 const trigger={getAttribute:key=>triggerAttrs.get(key),setAttribute:(key,value)=>triggerAttrs.set(key,value)};
 const content=initializePanelContent(dialog,trigger,()=>resets++);
 return {media,mobile,host,dialog,attrs,content,clones:()=>clones,appends:()=>appends,resets:()=>resets};
}
test('mobile never mounts desktop project media; desktop mounts only on first opening',()=>{
 const f=fixture();assert.equal(f.clones(),0);assert.equal(f.mobile.hidden,false);f.dialog.open=true;f.content.prepare();assert.equal(f.clones(),0);
 f.media.matches=true;f.media.dispatchEvent(new Event('change'));assert.equal(f.clones(),1);assert.equal(f.mobile.inert,true);assert.equal(f.host.hidden,false);assert.equal(f.attrs.get('aria-label'),'Start a conversation');
 f.content.prepare();assert.equal(f.clones(),1);f.media.matches=false;f.media.dispatchEvent(new Event('change'));assert.equal(f.mobile.hidden,false);assert.equal(f.host.inert,true);assert.equal(f.attrs.get('aria-label'),'Menu');f.content.destroy();
});
test('closed desktop defers its image and cleanup removes the breakpoint listener',()=>{
 const f=fixture(true);assert.equal(f.clones(),0);f.dialog.open=true;f.content.prepare();assert.equal(f.clones(),1);
 f.content.destroy();const count=f.resets();f.media.dispatchEvent(new Event('change'));assert.equal(f.resets(),count);assert.equal(f.appends(),0);assert.equal(f.mobile.hidden,false);
});
