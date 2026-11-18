import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';
const source=(await readFile(new URL('../radmanct/src/js/pages/archive.js',import.meta.url),'utf8')).replace(/^import .*;$/gm,'');
const settle=()=>new Promise(resolve=>setImmediate(resolve));
function fixture(){
 const handlers={},windowHandlers={},requests=[],pushes=[],classes=new Set(),viewAttrs=new Map(),location={href:'http://site.test/music/',origin:'http://site.test'};
 const link={href:'http://site.test/music/page/2/',dataset:{},target:'',hasAttribute:()=>false,matches:()=>true};
 const view={childNodes:[],replaceChildren(...nodes){this.childNodes=nodes;},setAttribute(key,value){viewAttrs.set(key,value);},removeAttribute(key){viewAttrs.delete(key);},focus(){this.focused=true;},getBoundingClientRect:()=>({top:30})};
 const status={textContent:''};
 const root={dataset:{},classList:{add:key=>classes.add(key),remove:key=>classes.delete(key)},setAttribute(){},removeAttribute(){},querySelector:s=>s==='[data-rdc-res]'?view:s==='.rdc-arc-sts'?status:null,querySelectorAll:s=>s.includes('.pagination a')?[link]:[],addEventListener:(key,callback)=>{handlers[key]=callback;},dispatchEvent(){}};
 const incoming={childNodes:['page-two-results']};
 let metadata=[];
 const document={title:'First page',head:{querySelectorAll:()=>metadata,append(...nodes){metadata=nodes;}},querySelector:s=>s==='[data-rdc-arc]'?root:null,querySelectorAll:()=>[],dispatchEvent(){}};
 const context={document,window:{addEventListener:(key,callback)=>{windowHandlers[key]=callback;}},location,URL,AbortController,setTimeout,clearTimeout,CustomEvent:class{},history:{state:{keep:true},pushState(state,unused,url){pushes.push({state,url});location.href=url;}},DOMParser:class{parseFromString(){return {title:'Second page',head:{querySelectorAll:()=>[]},querySelector:s=>s.includes('[data-rdc-res]')?incoming:null,querySelectorAll:()=>[]};}},fetch:async url=>{requests.push(url);return {ok:true,text:async()=>'<server-results>'};}};
 vm.runInNewContext(source,context);
 const click=(options={})=>{let prevented=false;handlers.click({target:{closest:()=>link},button:0,preventDefault(){prevented=true;},...options});return prevented;};
 return {context,link,view,document,requests,pushes,windowHandlers,location,click,classes,viewAttrs,status};
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

test('pagination commits the final redirected URL',async()=>{
 const f=fixture(),url='http://site.test/music/?paged=2';
 f.context.fetch=async()=>({ok:true,url,text:async()=>'<server-results>'});
 assert.equal(f.click(),true);await settle();
 assert.equal(f.location.href,url);
});

test('a slow request marks only the result region busy and clears the delayed indicator on pagehide',async()=>{
 const f=fixture(),timers=new Map();
 let signal;
 f.context.setTimeout=(callback,delay)=>{timers.set(delay,callback);return delay;};
 f.context.clearTimeout=id=>timers.delete(id);
 f.context.fetch=(_,options)=>{
  signal=options.signal;
  return new Promise((resolve,reject)=>signal.addEventListener('abort',()=>reject(Object.assign(new Error('aborted'),{name:'AbortError'}))));
 };
 assert.equal(f.click(),true);
 assert.equal(f.viewAttrs.get('aria-busy'),'true');
 assert.equal(f.classes.has('is-load'),false);
 timers.get(160)();
 assert.equal(f.classes.has('is-load'),true);
 f.windowHandlers.pagehide();
 await settle();
 assert.equal(signal.aborted,true);
 assert.equal(f.classes.has('is-load'),false);
 assert.equal(f.viewAttrs.has('aria-busy'),false);
 assert.equal(f.status.textContent,'');
 assert.equal(timers.size,0);
});
