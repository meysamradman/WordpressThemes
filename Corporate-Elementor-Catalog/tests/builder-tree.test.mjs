import test from 'node:test';
import assert from 'node:assert/strict';
import {containers,findContainer,updateContainer,withinLimits} from '../radmanct/src/js/admin/builder-tree.js';
import {moveItem,duplicateRow} from '../radmanct/src/js/admin/builder-mutations.js';
import {resolveSelection} from '../radmanct/src/js/admin/builder-selection.js';
import {findPreviewDrop} from '../radmanct/src/js/admin/builder-drag.js';
import {commit,undo,redo} from '../radmanct/src/js/admin/builder-history.js';

const fixture = () => ({rows:[{id:'header',appearance:{rowLayout:'flex'},items:[
  {id:'brand',type:'container',appearance:{width:'custom',customWidth:30},layout:{rowLayout:'flex',direction:'row'},items:[{id:'logo',type:'logo'}]},
  {id:'actions',type:'container',layout:{rowLayout:'flex'},items:[{id:'panel',type:'panel'}]},
]}]});

test('moving between groups preserves IDs and content, while undo/redo restore the whole tree',()=>{
  const original=fixture();
  const moved=moveItem(original,'actions','panel','brand',1);
  assert.deepEqual(findContainer(moved,'brand').items.map((item)=>item.id),['logo','panel']);
  assert.equal(findContainer(moved,'actions').items.length,0);
  assert.equal(findContainer(original,'actions').items.length,1);
  const history=commit({past:[],present:original,future:[]},moved);
  assert.deepEqual(undo(history).present,original);
  assert.deepEqual(redo(undo(history)).present,moved);
  assert.equal(resolveSelection(moved,{itemId:'panel',rowId:'actions'}).row.id,'brand');
  assert.equal(resolveSelection(moved,{rowId:'brand'}).item.id,'brand');
});

test('groups reject self/descendant drops and nesting beyond two inner levels',()=>{
  const original=fixture();
  assert.equal(moveItem(original,'header','brand','brand',0),original);
  const nested=moveItem(original,'header','actions','brand',0);
  assert.equal(moveItem(nested,'header','brand','actions',0),nested);
  const deeper=updateContainer(nested,'actions',(row)=>({...row,items:[{id:'third',type:'container',items:[]}]}));
  assert.equal(withinLimits(deeper),false);
  const full=updateContainer(original,'brand',(row)=>({...row,items:Array.from({length:6},(_,id)=>({id:`child${id}`,type:'text'}))}));
  assert.equal(moveItem(full,'actions','panel','brand',0),full);
});

test('duplicating a row creates fresh identities for every nested node',()=>{
  const duplicated=duplicateRow(fixture(),'header');
  const ids=containers(duplicated).flatMap((row)=>[row.id,...row.items.filter((item)=>item.type!=='container').map((item)=>item.id)]);
  assert.equal(new Set(ids).size,ids.length);
  assert.equal(duplicated.rows[1].items[0].appearance.customWidth,30);
});

test('canvas drops choose the innermost group and allow empty groups',()=>{
  const layout=fixture();
  const rect=(left,top,width,height)=>({left,top,width,height,right:left+width,bottom:top+height});
  const targets=[{id:'header',rowId:'header',item:false,rect:rect(0,0,1000,100)},
    {id:'brand',rowId:'header',item:true,rect:rect(0,0,300,100)},
    {id:'brand',rowId:'brand',item:false,rect:rect(0,0,300,100)},
    {id:'logo',rowId:'brand',item:true,rect:rect(0,0,100,100)},
    {id:'actions',rowId:'actions',item:false,rect:rect(600,0,400,100)}];
  assert.equal(findPreviewDrop(targets,80,50,layout,false,true).rowId,'brand');
  assert.equal(findPreviewDrop(targets,80,50,layout,false,true).index,0);
  assert.deepEqual(findPreviewDrop(targets,700,50,layout),{rowId:'actions',index:1,token:'actions'});
});

test('resize commits once on release, respects scaled RTL movement and preserves sibling device widths',async()=>{
  globalThis.window={wp:{element:{createElement:(type,props,...children)=>({type,props,children}),useRef:()=>({current:null}),useState:()=>[null,()=>{}]}}};
  const {ResizeHandle}=await import('../radmanct/src/js/admin/builder-resize.js');
  for (const rtl of [false,true]) {
    const commits=[];
    const tree=ResizeHandle({target:{rect:{width:100}},parent:{rect:{width:200}},item:{appearance:{customWidth:40,tabletCustomWidth:60}},device:'mobile',rtl,label:'Resize',scale:.5,onCommit:(value)=>commits.push(value)});
    const handle=tree.children.find((node)=>node?.type==='button');
    const event=(x)=>({clientX:x,pointerId:1,preventDefault(){},stopPropagation(){},currentTarget:{setPointerCapture(){}}});
    handle.props.onPointerDown(event(100)); handle.props.onPointerMove(event(125));
    assert.equal(commits.length,0);
    handle.props.onPointerUp();
    assert.equal(commits.length,1);
    assert.equal(commits[0].mobileCustomWidth,rtl?25:75);
    assert.equal(commits[0].mobileWidth,'custom');
    assert.equal(commits[0].tabletCustomWidth,60);
    assert.equal(commits[0].customWidth,40);
    handle.props.onPointerDown(event(100));handle.props.onPointerCancel();handle.props.onPointerUp();
    assert.equal(commits.length,1);
  }
});

test('panel template choices show the empty value once even when the server includes it',async()=>{
  const {MenuPanelFields}=await import('../radmanct/src/js/admin/builder-menu-panel-fields.js');
  const tree=MenuPanelFields({item:{panelTemplateId:0},update(){},labels:{none:'None'},config:{navigationPanels:{panelTemplateId:{label:'Template',default:0,choices:'templates'}},panelTemplates:[{id:0,title:'None'},{id:7,title:'Custom'}]}});
  const fieldset=tree.children.flat().find((node)=>node?.type==='fieldset');
  const select=fieldset.children[1];
  assert.deepEqual(select.props.options,[[0,'None'],[7,'Custom']]);
});
