import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeCursor} from '../radmanct/src/js/ui/cursor.js';
import {initializeScroll,mountScrollEffects} from '../radmanct/src/js/runtime/scroll.js';
function frames(view){let number=0;const pending=new Map();view.requestAnimationFrame=fn=>{pending.set(++number,fn);return number;};view.cancelAnimationFrame=id=>pending.delete(id);return {pending,flush(){const tasks=[...pending.values()];pending.clear();tasks.forEach(fn=>fn());}};}
function event(type,values={}){return Object.assign(new Event(type),values);}

test('native focus alignment retains viewport position and removes its listener on teardown',()=>{
 const view=new EventTarget(),doc=new EventTarget(),media=new EventTarget();
 media.matches=false;view.document=doc;view.matchMedia=()=>media;view.innerHeight=900;view.scrollY=1000;frames(view);
 const positions=[];view.scrollTo=options=>positions.push(options);
 doc.documentElement={style:{},scrollHeight:5000};doc.querySelector=()=>null;
 doc.getElementById=id=>id==='rdc-scr-wrp'?{style:{}}:id==='rdc-scr-cnt'?{}:null;
 const target={isConnected:true,getBoundingClientRect:()=>({top:450})};doc.activeElement=target;
 const state=initializeScroll(view);
 const align=()=>doc.dispatchEvent(event('rdc:fcs-aln',{detail:{target,top:600}}));
 align();assert.deepEqual(positions,[{top:850,behavior:'instant'}]);
 doc.activeElement=null;align();assert.equal(positions.length,1);
 state.destroy();doc.activeElement=target;align();assert.equal(positions.length,1);
});
test('cursor coalesces pointer input into one frame and releases work when hidden or removed',()=>{
 const view=new EventTarget(),doc=new EventTarget(),media=new EventTarget();media.matches=true;doc.hidden=false;view.matchMedia=()=>media;const queue=frames(view);
 let writes=0,transform='';const style={};Object.defineProperty(style,'transform',{get:()=>transform,set:value=>{transform=value;writes++;}});
 const node={ownerDocument:doc,hidden:true,style,getAttribute:()=>null,removeAttribute(){},setAttribute(){}};
 const stop=initializeCursor(node,view);
 for(let i=0;i<30;i++)view.dispatchEvent(event('pointermove',{clientX:i,clientY:40,pointerType:'mouse'}));
 assert.equal(queue.pending.size,1);assert.equal(writes,0);queue.flush();assert.equal(writes,1);assert.equal(transform,'translate3d(11.5px,22.5px,0)');
 doc.hidden=true;doc.dispatchEvent(event('visibilitychange'));assert.equal(node.hidden,true);
 stop();view.dispatchEvent(event('pointermove',{clientX:60,clientY:70,pointerType:'mouse'}));assert.equal(queue.pending.size,0);
});
test('native scroll painting does not remeasure document height on every scroll event',()=>{
 const view=new EventTarget(),doc=new EventTarget(),media=new EventTarget();media.matches=false;view.document=doc;view.matchMedia=()=>media;view.innerHeight=600;view.scrollY=0;const queue=frames(view);let reads=0;
 const html={style:{}};Object.defineProperty(html,'scrollHeight',{get(){reads++;return 1600;}});doc.documentElement=html;
 const wrapper={style:{}},content={},progress={hidden:true,style:{}};doc.getElementById=id=>id==='rdc-scr-wrp'?wrapper:id==='rdc-scr-cnt'?content:null;doc.querySelector=selector=>selector==='.rdc-scr-prog'?progress:null;
 const state=initializeScroll(view);assert.equal(reads,1);view.scrollY=500;
 for(let i=0;i<30;i++)view.dispatchEvent(event('scroll'));assert.equal(queue.pending.size,1);queue.flush();assert.equal(reads,1);assert.equal(progress.style.transform,'scaleX(0.5)');
 view.dispatchEvent(event('resize'));queue.flush();assert.equal(reads,2);state.destroy();view.dispatchEvent(event('scroll'));assert.equal(queue.pending.size,0);
});

test('cursor shrinks only on real controls and labels links through explicit labels or their accessible text',()=>{
 const view=new EventTarget(),doc=new EventTarget(),media=new EventTarget();media.matches=true;doc.hidden=false;view.matchMedia=()=>media;view.innerWidth=1200;view.innerHeight=800;const queue=frames(view);
 const attributes=new Set(),label={textContent:''};
 const node={ownerDocument:doc,hidden:true,style:{},querySelector:()=>label,getAttribute:()=>null,removeAttribute:key=>attributes.delete(key),toggleAttribute:(key,on)=>on?attributes.add(key):attributes.delete(key)};
 const stop=initializeCursor(node,view);
 const move=(target,x)=>{const e=event('pointermove',{clientX:x,clientY:50,pointerType:'mouse'});Object.defineProperty(e,'target',{value:target});view.dispatchEvent(e);queue.flush();};
 const link={getAttribute:()=> 'View details'},plain={closest:()=>null};
 move(plain,100);assert.equal(attributes.has('data-interactive'),false);assert.equal(label.textContent,'');
 move({closest:selector=>selector.startsWith('a[href]')?link:null},200);assert.equal(attributes.has('data-interactive'),true);assert.equal(label.textContent,'View details');
 const ordinary={getAttribute:()=>null,textContent:'Contact us'};
 move({closest:selector=>selector.startsWith('a[href]')?ordinary:null},250);assert.equal(label.textContent,'Contact us');
 const iconButton={getAttribute:key=>key==='aria-label'?'Open menu':null};
 move({closest:selector=>selector.startsWith('a[href]')?iconButton:null},275);assert.equal(label.textContent,'Open menu');
 const menuLink={getAttribute:()=> 'Services',closest:()=>({})};
 move({closest:selector=>selector.startsWith('a[href]')?menuLink:null},285);assert.equal(attributes.has('data-interactive'),true);assert.equal(attributes.has('data-label'),false);assert.equal(label.textContent,'');
 move({closest:selector=>selector.startsWith('[data-rdc-cur=')?{}:null},300);assert.equal(node.hidden,true);assert.equal(label.textContent,'');
 move(plain,400);assert.equal(node.hidden,false);assert.equal(attributes.has('data-label'),false);
 media.matches=false;media.dispatchEvent(new Event('change'));move(plain,500);assert.equal(node.hidden,true);stop();
});

test('image speed delegates to the existing smoother and cleans up only its effects',()=>{
 let config,stops=0,calls=0;const target={getAttribute:()=>'.8'};
 const smoother={effects:(nodes,options)=>{calls++;assert.deepEqual(nodes,[target]);config=options;return [{kill(){stops++;}}];}};
 const release=mountScrollEffects(smoother,[target]);assert.equal(config.speed(0,target),.8);assert.equal(config.speed(0,{getAttribute:()=> 'bad'}),1);release();assert.equal(stops,1);
 mountScrollEffects(smoother,[])();assert.equal(calls,1);
});
