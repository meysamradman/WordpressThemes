import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeExclusiveVideos} from '../radmanct/src/js/ui/video-player.js';

function fixture(){
 let observer;
 class Observer{
  observed=new Set();
  constructor(callback){this.callback=callback;observer=this;}
  observe(cover){this.observed.add(cover);}
  unobserve(cover){this.observed.delete(cover);}
  disconnect(){this.observed.clear();}
  enter(cover,ratio=.25){this.callback([{target:cover,isIntersecting:true,intersectionRatio:ratio}]);}
 }
 globalThis.window={IntersectionObserver:Observer};globalThis.IntersectionObserver=Observer;
 const root=new EventTarget(),covers=[];
 root.querySelectorAll=s=>s==='[data-rdc-aparat-visible]'?covers:[];
 const add=()=>{
  const frame=new EventTarget(),player={isConnected:false,querySelector:()=>frame,setAttribute(){this.pending=true;},removeAttribute(){this.pending=false;},remove(){this.isConnected=false;}};
  const cover={hidden:false,parentElement:{querySelector:()=>({content:{firstElementChild:{cloneNode:()=>player}}})},after(){player.isConnected=true;}};
  covers.push(cover);return {cover,player,frame};
 };
 const first=add();const release=initializeExclusiveVideos(root);
 return {root,first,observer,add,release};
}

test('visible Aparat cards prepare real controls before the first click and clean up',()=>{
 const f=fixture(),{cover,player,frame}=f.first;
 assert.equal(player.isConnected,false);
 f.observer.enter(cover,.1);assert.equal(player.isConnected,false);
 f.observer.enter(cover);assert.equal(player.isConnected,true);assert.equal(frame.loading,'eager');assert.equal(cover.hidden,false);
 frame.dispatchEvent(new Event('load'));assert.equal(cover.hidden,true);assert.equal(player.pending,false);
 f.release();assert.equal(cover.hidden,false);assert.equal(player.isConnected,false);assert.equal(f.observer.observed.size,0);
});

test('archive append prepares newly visible cards without a cover click',()=>{
 const f=fixture(),next=f.add();assert.equal(f.observer.observed.has(next.cover),false);
 f.root.dispatchEvent(new Event('rdc:arc-upd'));assert.equal(f.observer.observed.has(next.cover),true);
 f.observer.enter(next.cover);next.frame.dispatchEvent(new Event('load'));assert.equal(next.cover.hidden,true);
 f.release();assert.equal(next.player.isConnected,false);
 f.root.dispatchEvent(new Event('rdc:arc-upd'));assert.equal(f.observer.observed.size,0);
});
