import test from 'node:test';
import assert from 'node:assert/strict';

let state, cursor, effects, onLoaded;
globalThis.window={wp:{element:{
  createElement:(type,props,...children)=>({type,props:props || {},children}),
  useState(initial){const index=cursor++;if(!(index in state))state[index]=initial;return [state[index],next=>{state[index]=typeof next==='function'?next(state[index]):next;if(index===1 && Object.keys(state[index]).length)onLoaded?.();}];},
  useEffect(effect){effects.push(effect);},
}}};
const {Inspector}=await import('../radmanct/src/js/admin/builder-inspector.js');
const labels=new Proxy({},{get:(_,key)=>key});
const render=type=>{cursor=0;effects=[];return Inspector({item:{type},row:{appearance:{}},update(){},labels,config:{},actions:[]});};
const reset=()=>{state=[];onLoaded=null;};

test('selected menu controls load once into local state and are reusable after selection changes',async()=>{
  reset();render('text');assert.equal(effects[0](),undefined);assert.deepEqual(state[1],{});
  render('menu');
  const loaded=new Promise(resolve=>{onLoaded=resolve;});
  effects[0]();await loaded;
  assert.equal(state[1].menu.name,'MenuFields');
  render('text');effects[0]();render('menu');assert.equal(effects[0](),undefined);
  assert.equal(state[1].menu.name,'MenuFields');
});

test('a late field import cannot update a disposed inspector',async()=>{
  reset();render('panel');const cleanup=effects[0]();cleanup();
  await import('../radmanct/src/js/admin/builder-menu-panel-fields.js');
  await new Promise(setImmediate);
  assert.deepEqual(state[1],{});
});
