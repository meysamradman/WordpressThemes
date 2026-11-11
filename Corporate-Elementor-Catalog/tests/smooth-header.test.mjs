import test from 'node:test';
import assert from 'node:assert/strict';
import {pinSmoothHeaders} from '../radmanct/src/js/smooth-header.js';

test('immediate smooth header preserves its own pin and cleanup',()=>{
  const section={matches:()=>true};const classes=new Set();let event;
  const doc={querySelector:selector=>selector.includes('wp-page')?null:{children:[section]},defaultView:{Event:class{constructor(type){this.type=type;}}}};
  const root={style:{},dataset:{rdctStickyStart:'immediate'},classList:{contains:name=>classes.has(name),remove:name=>classes.delete(name),toggle:(name,value)=>value?classes.add(name):classes.delete(name)},setAttribute:(name,value)=>root[name]=value,removeAttribute:name=>delete root[name],dispatchEvent:value=>event=value.type};
  let settings;let killed=false;
  const release=pinSmoothHeaders({ownerDocument:doc,querySelectorAll:()=>[root]},{create:options=>{settings=options;return{kill:revert=>{killed=revert;}};}},()=>32);
  assert.equal(settings.trigger,root);assert.equal(settings.pin,root);assert.equal(settings.pinReparent,true);assert.equal(settings.pinSpacing,false);assert.equal(settings.start(),'top top+=32');
  settings.onToggle({isActive:true});assert.equal(classes.has('is-sticky-active'),true);
  assert.equal(settings.pinType,'fixed');settings.onUpdate({isActive:true});assert.equal(root.style.top,'32px');
  settings.onToggle({isActive:false,progress:1});assert.equal(classes.has('is-sticky-active'),true);
  release();assert.equal(killed,true);assert.equal(classes.has('is-sticky-active'),false);assert.equal(root['data-rdct-smooth-sticky'],undefined);assert.equal(event,'rdct:sticky-refresh');
});
