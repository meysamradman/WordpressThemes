import test from 'node:test';
import assert from 'node:assert/strict';
import {createPointerDrag} from '../radmanct/src/js/admin/builder-pointer-drag.js';
import {findPreviewDrop,previewDropLine,dropIndex} from '../radmanct/src/js/admin/builder-drag.js';
import {moveItem} from '../radmanct/src/js/admin/builder-mutations.js';

function event(type,x,y,id=1) {
  const capture=new Set();
  return {pointerType:type,pointerId:id,button:0,isPrimary:true,clientX:x,clientY:y,
    preventDefault(){},stopPropagation(){},
    currentTarget:{focus(){},setPointerCapture(id){capture.add(id);},hasPointerCapture(id){return capture.has(id);},releasePointerCapture(id){capture.delete(id);}},
  };
}

test('mouse, touch and pen move a panel before the first logo through pointer events without native dragstart',()=>{
  for (const type of ['mouse','touch','pen']) {
    const original={rows:[{id:'header',items:[{id:'logo'},{id:'menu'},{id:'panel'}]}]};
    let layout=original;let source;let moves=0;
    const rect=(left,width)=>({left,top:0,width,height:80,right:left+width,bottom:80});
    const targets=[{id:'header',rowId:'header',item:false,rect:rect(0,900)},
      {id:'logo',rowId:'header',item:true,rect:rect(600,300)},
      {id:'menu',rowId:'header',item:true,rect:rect(300,300)},
      {id:'panel',rowId:'header',item:true,rect:rect(0,300)}];
    const controller=createPointerDrag({start(data){source=data;},move(){moves++;},cancel(){},end(e){
      const target=findPreviewDrop(targets,e.clientX,e.clientY,layout,false,true);
      layout=moveItem(layout,source.rowId,source.itemId,target.rowId,dropIndex(layout,source,target.rowId,target.index));
    }});
    const handlers=controller.bind({rowId:'header',itemId:'panel'});
    assert.equal(handlers.draggable,false);
    handlers.onPointerDown(event(type,150,40));
    handlers.onPointerMove(event(type,152,40));assert.equal(moves,0);
    handlers.onPointerMove(event(type,850,40));assert.equal(moves,1);
    assert.equal(layout,original);
    handlers.onPointerUp(event(type,850,40));
    assert.deepEqual(layout.rows[0].items.map((item)=>item.id),['panel','logo','menu']);
    handlers.onPointerUp(event(type,850,40));
    assert.deepEqual(layout.rows[0].items.map((item)=>item.id),['panel','logo','menu']);
  }
});

test('capture loss, Escape and disposal cancel while clicks and secondary pointers cannot commit a move',()=>{
  let ended=0;let cancelled=0;let started=0;
  const controller=createPointerDrag({start(){started++;},move(){},end(){ended++;},cancel(){cancelled++;}});
  const handlers=controller.bind({itemId:'panel'});
  handlers.onPointerDown({...event('mouse',0,0),button:2});
  handlers.onPointerDown({...event('touch',0,0),isPrimary:false});
  assert.equal(started,0);
  handlers.onPointerDown(event('mouse',0,0));handlers.onPointerUp(event('mouse',0,0));
  assert.equal(ended,0);assert.equal(cancelled,1);
  for (const finish of [()=>handlers.onLostPointerCapture(event('mouse',20,0)),()=>handlers.onKeyDown({key:'Escape'}),()=>controller.cancel()]) {
    handlers.onPointerDown(event('mouse',0,0));handlers.onPointerMove(event('mouse',20,0));
    handlers.onLostPointerCapture(event('mouse',20,0,9));
    finish();handlers.onPointerUp(event('mouse',20,0));
  }
  assert.equal(ended,0);assert.equal(cancelled,4);
});

test('insertion line marks the beginning on the correct side in RTL and LTR',()=>{
  const layout={rows:[{id:'row',items:[{id:'logo'}]}]};
  const rect={left:100,top:20,width:200,height:80,right:300,bottom:100};
  const targets=[{id:'row',item:false,rect},{id:'logo',item:true,rect}];
  assert.equal(previewDropLine({rowId:'row',index:0},targets,layout,false,true).left,298.5);
  assert.equal(previewDropLine({rowId:'row',index:0},targets,layout,false,false).left,98.5);
  assert.equal(previewDropLine({rowId:'row',index:1},targets,layout,false,true).left,98.5);
});
