import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import {runInNewContext} from 'node:vm';

const source=(await readFile(new URL('../radmanct-components/src/js/components/button/actions.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'').replace(/import\(/g,'load(').replace('export function initializeActions','function initializeActions');
const tick=()=>new Promise(setImmediate);
function fixture(){
  let resolve;let loads=0;let created=0;let opened=0;let destroyed=0;let listener;
  const context={reportRuntimeError:error=>{throw error;},load:()=>{loads++;return new Promise(done=>{resolve=done;});}};
  runInNewContext(source,context);
  const root={isConnected:true,dataset:{action:'modal',anchor:'content'},ownerDocument:{defaultView:{},getElementById:()=>({})},addEventListener:(_,callback)=>{listener=callback;},removeEventListener:()=>{listener=null;}};
  const release=context.initializeActions(root);
  return {release,click:()=>listener({preventDefault(){}}),finish:()=>resolve({createContentDialog:()=>{created++;return{open:()=>{opened++;},destroy:()=>{destroyed++;}};}}),counts:()=>[loads,created,opened,destroyed]};
}
test('content dialog loads on click and is destroyed with its button',async()=>{
  const page=fixture();assert.deepEqual(page.counts(),[0,0,0,0]);
  page.click();page.finish();await tick();assert.deepEqual(page.counts(),[1,1,1,0]);
  page.release();assert.deepEqual(page.counts(),[1,1,1,1]);
});
test('late dialog import cannot create or open an overlay after button cleanup',async()=>{
  const page=fixture();page.click();page.release();page.finish();await tick();
  assert.deepEqual(page.counts(),[1,0,0,0]);
});
