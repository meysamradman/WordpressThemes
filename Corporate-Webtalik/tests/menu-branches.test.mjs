import test from 'node:test';
import assert from 'node:assert/strict';
import {initializePanelBranches} from '../radmanct/src/js/ui/panel/branches.js';
function fixture(){
 const dialog=new EventTarget(),media=new EventTarget();media.matches=false;
 const attrs=new Set(),nav={},info={},title={},back={focus(){this.focused=true;}};
 const content={append(node){node.position='stage';}};
 const stage={hidden:true,querySelector:s=>({'[data-rdc-pnl-br-cnt]':content,'[data-rdc-pnl-br-ttl]':title,'[data-rdc-pnl-back]':back}[s])};
 const entries=[],menus=new Map(),markers=[];
 const add=(id,label,parentMenu=null)=>{
  const menu={hidden:true,position:'home',before(marker){marker.menu=this;},scrollTop:99};
  const attributes=new Map([['aria-controls',id],['aria-expanded','false']]);
  const button={getAttribute:key=>attributes.get(key),setAttribute:(key,value)=>attributes.set(key,value),parentElement:{querySelector:()=>({textContent:label})},closest:s=>s==='[data-rdc-pnl-tgl]'?button:s==='.rdc-pnl-sub'?parentMenu:null,focus(){this.focused=true;}};
  entries.push(button);menus.set(id,menu);return {button,menu,attributes};
 };
 const first=add('one','Products'),child=add('nested','Nested',first.menu),second=add('two','Services');
 dialog.ownerDocument={defaultView:{matchMedia:()=>media},getElementById:id=>menus.get(id),createComment(){const marker={after(node){node.position='home';},remove(){this.removed=true;}};markers.push(marker);return marker;}};
 dialog.querySelector=s=>({ '[data-rdc-pnl-br]':stage,'.rdc-nav':nav,'.rdc-pnl__info':info }[s]);
 dialog.querySelectorAll=()=>entries;dialog.contains=node=>[...menus.values()].includes(node);dialog.toggleAttribute=(key,on)=>on?attrs.add(key):attrs.delete(key);
 const click=button=>{const event=new Event('click');Object.defineProperty(event,'target',{value:button});dialog.dispatchEvent(event);};
 return {dialog,media,nav,info,title,stage,first,child,second,markers,click,attrs};
}
test('mobile branch reuses native nodes and switching siblings restores the previous branch',()=>{
 const f=fixture(),control=initializePanelBranches(f.dialog);
 f.click(f.first.button);assert.equal(f.first.menu.position,'stage');assert.equal(f.first.menu.hidden,false);assert.equal(f.info.hidden,true);assert.equal(f.nav.inert,true);assert.equal(f.title.textContent,'Products');
 f.click(f.second.button);assert.equal(f.first.menu.position,'home');assert.equal(f.first.menu.hidden,true);assert.equal(f.first.attributes.get('aria-expanded'),'false');assert.equal(f.second.menu.position,'stage');
 control.reset();assert.equal(f.second.menu.position,'home');assert.equal(f.stage.hidden,true);assert.equal(f.info.hidden,false);control.destroy();assert.ok(f.markers.every(marker=>marker.removed));
});
test('nested branches go back one level and then return focus to the root control',()=>{
 const f=fixture(),control=initializePanelBranches(f.dialog);f.click(f.first.button);f.click(f.child.button);
 assert.equal(f.child.menu.position,'stage');assert.equal(f.first.menu.position,'home');
 assert.equal(control.back(),true);assert.equal(f.first.menu.position,'stage');assert.equal(f.child.menu.hidden,true);
 assert.equal(control.back(),true);assert.equal(f.stage.hidden,true);assert.equal(f.first.button.focused,true);assert.equal(control.back(),false);control.destroy();
});
test('cleanup restores mobile navigation and removes branch interaction',()=>{
 const f=fixture(),control=initializePanelBranches(f.dialog);f.click(f.first.button);assert.equal(f.nav.inert,true);
 control.destroy();assert.equal(f.info.inert,false);assert.equal(f.nav.inert,false);assert.equal(f.first.menu.hidden,true);
 f.click(f.second.button);assert.equal(f.second.menu.hidden,true);
});
