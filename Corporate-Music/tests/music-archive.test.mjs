import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';
const source=(await readFile(new URL('../radmanct/src/js/pages/archive.js',import.meta.url),'utf8')).replace(/^import .*;$/gm,'');
const settle=()=>new Promise(resolve=>setImmediate(resolve));
function fixture(){
 const handlers={},windowHandlers={},requests=[],pushes=[],location={href:'http://site.test/music/',origin:'http://site.test'};
 const link={href:'http://site.test/music/page/2/',dataset:{},target:'',hasAttribute:()=>false,matches:()=>true};
 const view={childNodes:[],replaceChildren(...nodes){this.childNodes=nodes;},setAttribute(){},focus(){this.focused=true;},getBoundingClientRect:()=>({top:30})};
 const status={textContent:''};
 const root={dataset:{},classList:{add(){},remove(){}},setAttribute(){},removeAttribute(){},querySelector:s=>s==='[data-rdc-res]'?view:s==='.rdc-arc-sts'?status:null,querySelectorAll:s=>s.includes('.pagination a')?[link]:[],addEventListener:(key,callback)=>{handlers[key]=callback;},dispatchEvent(){}};
 const incoming={childNodes:['page-two-results']};
 const document={title:'First page',head:{querySelector:()=>null,append(){}},querySelector:s=>s==='[data-rdc-arc]'?root:null,querySelectorAll:()=>[],dispatchEvent(){}};
 const context={document,window:{addEventListener:(key,callback)=>{windowHandlers[key]=callback;}},location,URL,AbortController,setTimeout,clearTimeout,CustomEvent:class{},history:{state:{keep:true},pushState(state,unused,url){pushes.push({state,url});location.href=url;}},DOMParser:class{parseFromString(){return {title:'Second page',querySelector:s=>s.includes('[data-rdc-res]')?incoming:null,querySelectorAll:()=>[]};}},fetch:async url=>{requests.push(url);return {ok:true,text:async()=>'<server-results>'};}};
 vm.runInNewContext(source,context);
 const click=(options={})=>{let prevented=false;handlers.click({target:{closest:()=>link},button:0,preventDefault(){prevented=true;},...options});return prevented;};
 return {context,link,view,document,requests,pushes,windowHandlers,location,click};
}
test('pagination replaces results and updates title/history without a document navigation',async()=>{
 const f=fixture();assert.equal(f.click(),true);await settle();
 assert.deepEqual(f.view.childNodes,['page-two-results']);assert.equal(f.document.title,'Second page');
 assert.equal(f.pushes.length,1);assert.equal(f.pushes[0].state.keep,true);assert.equal(f.location.href,f.link.href);
 f.location.href='http://site.test/music/';f.windowHandlers.popstate();await settle();assert.equal(f.pushes.length,1);assert.equal(f.requests.at(-1),'http://site.test/music/');
});
test('modified clicks keep native navigation; failed fetch makes the real link usable',async()=>{
 const f=fixture();assert.equal(f.click({ctrlKey:true}),false);assert.equal(f.requests.length,0);
 f.context.fetch=async()=>{throw new Error('offline');};assert.equal(f.click(),true);await settle();
 assert.equal(f.link.dataset.fallback,'true');assert.equal(f.click(),false);
});
