import test from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import vm from 'node:vm';

const source=readFileSync(new URL('../radmanct/src/js/admin/media-editor.js',import.meta.url),'utf8');
const image=id=>({id,type:'image',title:`Image ${id}`,url:`https://local.test/${id}.jpg`,sizes:{thumbnail:{url:`https://local.test/${id}-150.jpg`,width:150,height:150}}});
function fixture(){
 const dataKey=name=>name.replace(/-([a-z])/g,(_,c)=>c.toUpperCase());
 class Node {
  constructor(tag='div'){this.tag=tag;this.children=[];this.dataset={};this.attributes={};this.events={};this.disabled=false;}
  append(...nodes){for(const n of nodes){n.remove();n.parent=this;this.children.push(n);}}
  remove(){if(this.parent){const list=this.parent.children;list.splice(list.indexOf(this),1);this.parent=null;}}
  replaceChildren(...nodes){for(const child of [...this.children])child.remove();for(const node of nodes)this.append(...(node.tag==='fragment'?[...node.children]:[node]));}
  insertBefore(node,before){node.remove();node.parent=this;this.children.splice(this.children.indexOf(before),0,node);}
  get previousElementSibling(){return this.parent?.children[this.parent.children.indexOf(this)-1];}
  get nextElementSibling(){return this.parent?.children[this.parent.children.indexOf(this)+1];}
  setAttribute(name,value){this.attributes[name]=value;}
  removeAttribute(name){delete this.attributes[name];}
  hasAttribute(name){return name.startsWith('data-')?dataKey(name.slice(5)) in this.dataset:name in this.attributes;}
  matches(selector){if(selector==='button')return this.tag==='button';const m=selector.match(/^\[data-([\w-]+)(?:="([^"]*)")?\](:not\(:disabled\))?$/);return !!m&&this.hasAttribute(`data-${m[1]}`)&&(m[2]===undefined||this.dataset[dataKey(m[1])]===m[2])&&(!m[3]||!this.disabled);}
  closest(selector){for(let node=this;node;node=node.parent)if(node.matches(selector))return node;return null;}
  querySelector(selector){return this.children.flatMap(n=>[n,...n.descendants()]).find(n=>n.matches(selector))||null;}
  descendants(){return this.children.flatMap(n=>[n,...n.descendants()]);}
  addEventListener(name,fn){this.events[name]=fn;}
  dispatchEvent(event){this.changed=(this.changed||0)+1;return true;}
  focus(){this.focused=true;}
  emit(name,target=this){return this.events[name]({target});}
 }
 const root=new Node(),input=new Node('input');input.value='';
 const refs={};for(const name of ['list','choose','remove','error','count','empty']){const node=new Node(['choose','remove'].includes(name)?'button':'div');node.dataset[dataKey(`gallery-${name}`)]='';refs[name]=node;root.append(node);}
 const querySelector=root.querySelector.bind(root);root.querySelector=s=>s.startsWith('[name=')?input:querySelector(s);
 const frames=[],queries=[];let rejectQuery=false;
 const media=options=>{const frame={options,handlers:{},state:()=>({set(name,value){frame[name]=value;}}),on(name,fn){this.handlers[name]=fn;},open(){this.opened=true;},dispose(){this.disposed=true;}};frames.push(frame);return frame;};
 media.model={Selection:class{constructor(models){this.models=models;}}};
 media.query=options=>{queries.push(options);return{models:options.include.map(id=>({id,...image(id)})),more:()=>rejectQuery?Promise.reject(new Error('offline')):Promise.resolve()};};
 const document={querySelectorAll:s=>s==='[data-rdc-gal]'?[root]:[],createElement:tag=>new Node(tag),createDocumentFragment:()=>new Node('fragment')};
 const strings={count:'%d images',view:'View %s',remove:'Remove',earlier:'Earlier',later:'Later',loadError:'Load failed',limit:'Too many images'};
 vm.runInNewContext(source,{document,window:{rdcGal:strings},wp:{media},Event});
 return{root,input,refs,frames,queries,open:()=>refs.choose.emit('click'),update:images=>frames.at(-1).handlers.update({toJSON:()=>images}),click:control=>refs.list.emit('click',control),reject(){rejectQuery=true;}};
}

test('gallery opens on demand and native changes apply only on update; order and per-image removal stay synchronized',async()=>{
 const f=fixture();assert.equal(f.queries.length,0);assert.equal(f.refs.empty.hidden,false);
 await f.open();assert.equal(f.frames[0].options.state,'gallery');assert.equal(f.input.value,'');
 f.update([image(9),image(4),image(7)]);assert.equal(f.input.value,'9,4,7');assert.equal(f.refs.count.textContent,'3 images');
 const first=f.refs.list.children[0];assert.equal(first.querySelector('[data-gallery-move="-1"]').disabled,true);
 assert.equal(first.children[0].href,image(9).url);assert.equal(first.children[0].children[0].src,image(9).sizes.thumbnail.url);
 f.click(first.querySelector('[data-gallery-move="1"]'));assert.equal(f.input.value,'4,9,7');
 f.click(first.querySelector('[data-gallery-delete]'));assert.equal(f.input.value,'4,7');
 await f.open();assert.equal(f.frames[0].disposed,true);assert.equal(f.frames[1].options.state,'gallery-edit');
 assert.deepEqual([...f.queries[0].include],[4,7]);assert.equal(f.queries.length,1);assert.equal(f.frames[1].displaySettings,false);
 // Cancelling a modal has no update event and leaves the stored input intact.
 assert.equal(f.input.value,'4,7');f.refs.remove.emit('click');assert.equal(f.input.value,'');assert.equal(f.refs.remove.disabled,true);assert.equal(f.refs.empty.hidden,false);
});

test('attachment loading failures preserve selection and release the disabled control',async()=>{
 const f=fixture();await f.open();f.update([image(5)]);f.reject();await f.open();
 assert.equal(f.input.value,'5');assert.equal(f.refs.choose.disabled,false);assert.equal(f.refs.error.hidden,false);assert.equal(f.refs.error.textContent,'Load failed');assert.equal(f.frames.length,1);
});

test('oversized gallery updates do not silently truncate or replace the current gallery',async()=>{
 const f=fixture();await f.open();f.update([image(5)]);f.update(Array.from({length:101},(_,i)=>image(i+1)));
 assert.equal(f.input.value,'5');assert.equal(f.refs.error.textContent,'Too many images');
});
