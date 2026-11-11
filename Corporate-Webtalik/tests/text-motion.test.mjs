import test from 'node:test';
import assert from 'node:assert/strict';
import {addEntrance,addTextFill,addHeadingEntrance,initializeEntrance} from '../radmanct/src/js/ui/text-motion.js';
function fixture(){
 const doc={createTextNode:text=>make(3,text),createElement:()=>make(1)};
 function make(type,text=''){
  return {nodeType:type,ownerDocument:doc,parentNode:null,childNodes:[],value:text,
   get textContent(){return this.nodeType===3?this.value:this.childNodes.map(n=>n.textContent).join('');},
   set textContent(value){this.childNodes=[make(3,value)];this.childNodes[0].parentNode=this;},
   matches(){return false;},
   replaceWith(...nodes){const p=this.parentNode,i=p.childNodes.indexOf(this);p.childNodes.splice(i,1,...nodes);nodes.forEach(n=>n.parentNode=p);this.parentNode=null;},
   remove(){const p=this.parentNode;if(p)p.childNodes.splice(p.childNodes.indexOf(this),1);this.parentNode=null;}
  };
 }
 const root=make(1),link=make(1),first=make(3,'طراحی وب‌سایت '),last=make(3,'اختصاصی');
 root.childNodes=[first,link];first.parentNode=root;link.parentNode=root;link.childNodes=[last];last.parentNode=link;
 const calls=[],timeline={play(){},fromTo:(...args)=>calls.push(args)};
 return {root,link,first,last,timeline,calls};
}
test('scroll fill preserves Persian shaping, whitespace and inline elements and restores original nodes',()=>{
 const f=fixture(),text=f.root.textContent;
 const release=addTextFill(f.timeline,f.root,.5);
 assert.equal(f.root.textContent,text);assert.equal(f.root.childNodes.at(-1),f.link);
 const [words,from,to,at]=f.calls[0];assert.equal(words.length,3);assert.equal(words[1].textContent,'وب‌سایت');assert.equal(from.opacity,.22);assert.equal(to.opacity,1);assert.equal(to.ease,'none');assert.equal(at,.5);
 release();release();assert.deepEqual(f.root.childNodes,[f.first,f.link]);assert.deepEqual(f.link.childNodes,[f.last]);
});
test('shared entrance adds transform/opacity motion to the owner timeline without a nested scroll trigger',()=>{
 const f=fixture();addEntrance(f.timeline,[null,f.root],1);
 const [nodes,from,to,at]=f.calls[0];assert.deepEqual(nodes,[f.root]);assert.equal(from.y,'2.8125rem');assert.equal(to.y,0);assert.equal(at,1);assert.equal('scrollTrigger' in to,false);
 assert.equal(to.lazy,false);
 addEntrance(f.timeline,[],2);assert.equal(f.calls.length,1);
});

test('pinned text fill fits its allotted scroll interval without changing shared defaults',()=>{
 const f=fixture();const release=addTextFill(f.timeline,f.root,.65,{duration:.15,stagger:.2});
 const [,from,to,at]=f.calls[0];assert.equal(from.opacity,.22);assert.equal(to.duration+to.stagger.amount+at,1);assert.equal(to.ease,'none');
 release();assert.deepEqual(f.root.childNodes,[f.first,f.link]);
});

function entryFixture(reduced=false){
 const doc=new EventTarget(),media=new EventTarget();media.matches=reduced;doc.defaultView={matchMedia:()=>media};let loading=true,plays=0,reverts=0;
 doc.getElementById=()=>loading?{}:null;const root={ownerDocument:doc,isConnected:true,querySelectorAll:()=>[{}]};
 const states=[];doc.documentElement={dataset:{}};
 const gsap={set(nodes,state){states.push(state);},context:fn=>{fn();return {add:fn=>fn(),revert(){reverts++;}};},timeline:options=>{assert.equal(options.paused,true);return {fromTo(nodes,from){states.push(from);},play(){plays++;}};}};
 return {doc,media,root,gsap,states,plays:()=>plays,reverts:()=>reverts,ready(){loading=false;doc.dispatchEvent(new Event('rdc:pre-done'));}};
}
test('initial entrance waits for the preloader, plays once and restores content for reduced motion',()=>{
 const f=entryFixture(),release=initializeEntrance(f.root,{gsap:f.gsap});assert.equal(f.plays(),0);f.ready();f.ready();assert.equal(f.plays(),1);
 f.media.matches=true;f.media.dispatchEvent(new Event('change'));assert.equal(f.reverts(),1);f.media.matches=false;f.media.dispatchEvent(new Event('change'));assert.equal(f.plays(),1);release();f.ready();assert.equal(f.plays(),1);
});
test('reduced motion and early cleanup prevent deferred entrance',()=>{
 const f=entryFixture(true),release=initializeEntrance(f.root,{gsap:f.gsap});f.ready();assert.equal(f.plays(),0);release();
 const g=entryFixture(),stop=initializeEntrance(g.root,{gsap:g.gsap});stop();g.ready();assert.equal(g.plays(),0);
});

test('viewport entrances start independently and never replay on repeated intersection or after disposal',()=>{
 const a=entryFixture(),b=entryFixture();let showA,showB,stops=0;
 a.doc.defaultView.IntersectionObserver=class{constructor(fn){showA=fn;}observe(){}disconnect(){stops++;}};
 b.doc.defaultView.IntersectionObserver=class{constructor(fn){showB=fn;}observe(){}disconnect(){stops++;}};
 const stopA=initializeEntrance(a.root,{gsap:a.gsap},{viewport:true}),stopB=initializeEntrance(b.root,{gsap:b.gsap},{viewport:true});
 a.ready();b.ready();assert.equal(a.plays()+b.plays(),0);
 showA([{isIntersecting:true}]);assert.equal(a.plays(),1);assert.equal(b.plays(),0);
 showB([{isIntersecting:true}]);showB([{isIntersecting:true}]);assert.equal(b.plays(),1);
 stopA();stopB();showB([{isIntersecting:true}]);assert.equal(b.plays(),1);assert.ok(stops>=2);
});

test('rebuilding the same section does not replay its completed entrance',()=>{
 const f=entryFixture();const release=initializeEntrance(f.root,{gsap:f.gsap});f.ready();release();
 const stop=initializeEntrance(f.root,{gsap:f.gsap});assert.equal(f.plays(),1);stop();
});

test('heading preset preserves Persian words and uses the measured word entrance separately from fill',()=>{
 const f=fixture(),original=f.root.textContent;
 const restore=addHeadingEntrance(f.timeline,f.root,.1);
 assert.equal(f.root.textContent,original);assert.equal(f.calls[0][0].length,3);
 const [words,from,to]=f.calls[0];assert.equal(words[1].textContent,f.first.textContent.trim().split(' ')[1]);assert.equal(from.x,'-1.25rem');assert.equal(from.rotationX,10);assert.equal(to.duration,1.6);assert.equal(to.ease,'power4.out');assert.equal(to.stagger,.16);assert.equal('scrollTrigger' in to,false);
 assert.equal(to.lazy,false);
 restore();assert.deepEqual(f.root.childNodes,[f.first,f.link]);
});
test('paragraph preset has its own lower entrance and start delay without splitting the fill nodes',()=>{
 const f=fixture();addEntrance(f.timeline,f.root,0,'paragraph');
 const [nodes,from,to,at]=f.calls[0];assert.deepEqual(nodes,[f.root]);assert.equal(from.rotationX,45);assert.equal(from.y,'2.8125rem');assert.equal(to.duration,1.6);assert.equal(at,.2);assert.deepEqual(f.root.childNodes,[f.first,f.link]);
});

test('changing reduced motion restores split heading nodes during an entrance',()=>{
 const f=fixture(),events=new EventTarget(),preference=new EventTarget();preference.matches=false;
 Object.assign(f.root.ownerDocument,{defaultView:{matchMedia:()=>preference},getElementById:()=>null,addEventListener:events.addEventListener.bind(events),removeEventListener:events.removeEventListener.bind(events)});
 f.root.isConnected=true;f.root.querySelectorAll=()=>[f.root];f.root.matches=selector=>selector.startsWith('h1,');
 const gsap={set(){},context:fn=>{fn();return {add:fn=>fn(),revert(){}};},timeline:()=>f.timeline};
 const release=initializeEntrance(f.root,{gsap});assert.notEqual(f.root.childNodes[0],f.first);
 preference.matches=true;preference.dispatchEvent(new Event('change'));assert.deepEqual(f.root.childNodes,[f.first,f.link]);release();
});

test('entrances prepare autoAlpha zero before the preloader or viewport trigger and release the hero gate only after setup',()=>{
 const hero=entryFixture();hero.doc.documentElement.dataset.rdcEnt='pending';
 const stop=initializeEntrance(hero.root,{gsap:hero.gsap});
 assert.equal(hero.states[0].autoAlpha,0);assert.equal(hero.plays(),0);assert.equal(hero.doc.documentElement.dataset.rdcEnt,'pending');
 hero.ready();assert.equal(hero.plays(),1);assert.equal(hero.doc.documentElement.dataset.rdcEnt,'ready');stop();
 const card=entryFixture();let intersect;
 card.doc.defaultView.IntersectionObserver=class{constructor(fn){intersect=fn;}observe(){}disconnect(){}};
 const release=initializeEntrance(card.root,{gsap:card.gsap},{viewport:true});card.ready();
 assert.equal(card.states[0].autoAlpha,0);assert.equal(card.plays(),0);
 intersect([{isIntersecting:true}]);assert.equal(card.plays(),1);release();
});

test('heading parent stays visible while words enter, with one timeline and no revert between prepare and start',()=>{
 const f=fixture(),events=new EventTarget(),preference=Object.assign(new EventTarget(),{matches:false});
 let loading=true,contexts=0,reverts=0,plays=0;const tweens=[];
 Object.assign(f.root.ownerDocument,{defaultView:{matchMedia:()=>preference},documentElement:{dataset:{rdcEnt:'pending'}},getElementById:()=>loading?{}:null,addEventListener:events.addEventListener.bind(events),removeEventListener:events.removeEventListener.bind(events),dispatchEvent:events.dispatchEvent.bind(events)});
 f.root.isConnected=true;f.root.querySelectorAll=()=>[f.root];f.root.matches=selector=>selector.startsWith('h1,');
 const apply=(nodes,vars)=>{for(const node of Array.isArray(nodes)?nodes:[nodes])if('autoAlpha' in vars){node.style??={};node.style.opacity=vars.autoAlpha;node.style.visibility=vars.autoAlpha?'inherit':'hidden';}};
 const gsap={set:apply,context(fn){contexts++;fn();return{add:fn=>fn(),revert(){reverts++;}};},timeline(options){assert.equal(options.paused,true);return{fromTo(nodes,from,to){apply(nodes,from);tweens.push({nodes,to});},play(){plays++;for(const {nodes,to}of tweens)apply(nodes,to);}};}};
 const release=initializeEntrance(f.root,{gsap});
 assert.equal(f.root.style.opacity,1);assert.equal(contexts,1);assert.equal(reverts,0);assert.equal(plays,0);
 const words=tweens[0].nodes;assert.ok(words.every(word=>word.style.opacity===0));
 loading=false;events.dispatchEvent(new Event('rdc:pre-done'));
 assert.equal(f.root.style.opacity,1);assert.ok(words.every(word=>word.style.opacity===1));assert.equal(reverts,0);assert.equal(plays,1);
 events.dispatchEvent(new Event('rdc:pre-done'));assert.equal(plays,1);
 release();assert.equal(reverts,1);assert.deepEqual(f.root.childNodes,[f.first,f.link]);
});

test('fallback after preparation releases paused entrance styles instead of leaving content hidden',()=>{
 const f=entryFixture();f.doc.documentElement.dataset.rdcEnt='pending';
 const release=initializeEntrance(f.root,{gsap:f.gsap});
 f.doc.documentElement.dataset.rdcEnt='fallback';f.doc.dispatchEvent(new Event('rdc:ent-fb'));
 assert.equal(f.reverts(),1);f.ready();assert.equal(f.plays(),0);release();assert.equal(f.reverts(),1);
});

test('a late vendor never hides text already exposed by the loading fallback',()=>{
 const f=entryFixture();f.doc.documentElement.dataset.rdcEnt='fallback';
 const release=initializeEntrance(f.root,{gsap:f.gsap});f.ready();assert.equal(f.states.length,0);assert.equal(f.plays(),0);release();
});

test('hero opts into shorter timing without changing the shared entrance defaults',()=>{
 for(const [profile,duration,stagger] of [['hero',1.1,.07],['default',1.6,.16]]){
  const f=entryFixture(),calls=[];
  f.gsap.timeline=()=>({fromTo:(...args)=>calls.push(args),play(){}});
  const stop=initializeEntrance(f.root,{gsap:f.gsap},{profile});
  assert.equal(calls[0][2].duration,duration);assert.equal(calls[0][2].stagger,stagger);stop();
 }
});

test('LTR heading mirrors its entry direction; very long texts retain their original nodes',()=>{
 const f=fixture();f.root.ownerDocument.documentElement={dir:'ltr'};
 const restore=addHeadingEntrance(f.timeline,f.root);assert.equal(f.calls[0][1].x,'1.25rem');restore();
 f.first.value=Array(240).fill('word').join(' ');f.calls.length=0;
 const original=[...f.root.childNodes],stop=addTextFill(f.timeline,f.root);
 assert.equal(f.calls[0][0],f.root);assert.deepEqual(f.root.childNodes,original);stop();
 f.calls.length=0;const release=addHeadingEntrance(f.timeline,f.root);
 assert.deepEqual(f.calls[0][0],[f.root]);assert.deepEqual(f.root.childNodes,original);release();
});
