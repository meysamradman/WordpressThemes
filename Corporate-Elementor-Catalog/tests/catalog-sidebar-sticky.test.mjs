import test from 'node:test';
import assert from 'node:assert/strict';

globalThis.window={addEventListener(){},removeEventListener(){}};
globalThis.document={querySelectorAll:()=>[]};
const {observeSidebarOffset}=await import('../radmanct-child/src/js/content/sidebar.js');

test('natural sidebar keeps tall content in page flow and updates its sticky boundary when disclosures change height',()=>{
  const properties=new Map(),frames=new Map(),events=new Map();
  let resized,disconnected=false;
  const media={matches:true,addEventListener(){},removeEventListener(){}};
  const root={offsetHeight:1200,hasAttribute:()=>true,style:{setProperty:(key,value)=>properties.set(key,value),removeProperty:key=>properties.delete(key)}};
  const view={innerHeight:800,matchMedia:()=>media,getComputedStyle:()=>({fontSize:'16px'}),requestAnimationFrame:callback=>{frames.set(1,callback);return 1;},cancelAnimationFrame:id=>frames.delete(id),addEventListener:(key,fn)=>events.set(key,fn),removeEventListener:key=>events.delete(key),ResizeObserver:class{constructor(callback){resized=callback;}observe(node){assert.equal(node,root);}disconnect(){disconnected=true;}}};
  root.ownerDocument={defaultView:view,documentElement:{},querySelector:()=>null,getElementById:()=>null,dispatchEvent(){}};
  const release=observeSidebarOffset(root);
  assert.equal(properties.get('--rdct-natural-sticky-top'),'-416px');
  root.offsetHeight=400;resized();
  frames.get(1)();frames.clear();
  assert.equal(properties.get('--rdct-natural-sticky-top'),'16px');
  view.innerHeight=300;events.get('resize')();
  frames.get(1)();frames.clear();
  assert.equal(properties.get('--rdct-natural-sticky-top'),'-116px');
  release();
  assert.equal(disconnected,true);assert.equal(properties.size,0);assert.equal(events.size,0);
});
