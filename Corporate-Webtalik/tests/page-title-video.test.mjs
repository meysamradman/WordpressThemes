import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeTitleVideo} from '../radmanct/src/js/pages/page-title.js';

function fixture({reduced=false,saveData=false,deferred=false}={}){
 const doc=new EventTarget(),view=new EventTarget(),preference=new EventTarget(),connection=new EventTarget();
 doc.defaultView=view;doc.hidden=false;preference.matches=reduced;connection.saveData=saveData;
 view.navigator={connection};view.location=new URL('http://local.test/');view.matchMedia=()=>preference;
 let show,plays=0,pauses=0,loads=0,src='';const promises=[];
 view.IntersectionObserver=class{constructor(fn){show=fn;}observe(){}disconnect(){}};
 const video=new EventTarget();video.dataset={src:'http://local.test/title.mp4'};
 video.getAttribute=key=>key==='src'?src:null;
 Object.defineProperty(video,'src',{get:()=>src,set:value=>{src=value;}});
 video.play=()=>{plays++;return deferred?new Promise(resolve=>promises.push(resolve)):Promise.resolve();};
 video.pause=()=>pauses++;video.load=()=>loads++;video.removeAttribute=()=>{src='';};
 const button=new EventTarget();button.dataset={playLabel:'Play',pauseLabel:'Pause'};const attrs=new Map();button.setAttribute=(key,value)=>attrs.set(key,value);
 const root={ownerDocument:doc,isConnected:true,querySelector:selector=>selector==='video'?video:button,toggleAttribute:(name,value)=>value?attrs.set(name,''):attrs.delete(name)};
 const stop=initializeTitleVideo(root);
 return{root,video,button,attrs,doc,view,preference,connection,stop,show:value=>show([{isIntersecting:value}]),resolve:index=>promises[index](),counts:()=>({plays,pauses,loads,src})};
}
const settle=async()=>{for(let i=0;i<6;i++)await Promise.resolve();};

test('title video loads only in view, pauses on user action/hidden tabs and cleans up',async()=>{
 const f=fixture();assert.equal(f.counts().src,'');f.show(true);await settle();
 assert.equal(f.counts().plays,1);assert.equal(f.video.muted,true);assert.equal(f.attrs.has('data-rdc-pt-run'),true);
 f.show(true);assert.equal(f.counts().plays,1);
 f.button.dispatchEvent(new Event('click'));assert.equal(f.attrs.get('aria-label'),'Play');assert.equal(f.attrs.has('data-rdc-pt-run'),false);
 f.button.dispatchEvent(new Event('click'));await settle();assert.equal(f.counts().plays,2);
 f.doc.hidden=true;f.doc.dispatchEvent(new Event('visibilitychange'));assert.equal(f.attrs.has('data-rdc-pt-run'),false);
 f.doc.hidden=false;f.doc.dispatchEvent(new Event('visibilitychange'));await settle();assert.equal(f.counts().plays,3);
 f.stop();f.stop();assert.equal(f.counts().loads,1);assert.equal(f.counts().src,'');
 f.show(true);assert.equal(f.counts().plays,3);
});

test('reduced motion and Save-Data retain the static fallback without downloading video',async()=>{
 for(const options of [{reduced:true},{saveData:true}]){
  const f=fixture(options);f.show(true);await settle();assert.equal(f.counts().src,'');assert.equal(f.counts().plays,0);assert.equal(f.button.hidden,true);f.stop();
 }
});

test('stale playback cannot stop a newer request and errors/late completion preserve fallback',async()=>{
 const f=fixture({deferred:true});f.show(true);f.show(false);f.show(true);
 const paused=f.counts().pauses;f.resolve(0);await settle();assert.equal(f.counts().pauses,paused);
 f.resolve(1);await settle();assert.equal(f.attrs.has('data-rdc-pt-run'),true);
 f.video.dispatchEvent(new Event('error'));assert.equal(f.attrs.has('data-rdc-pt-run'),false);assert.equal(f.attrs.get('aria-label'),'Play');f.stop();
 const late=fixture({deferred:true});late.show(true);late.stop();late.resolve(0);await settle();assert.equal(late.attrs.has('data-rdc-pt-run'),false);
});
