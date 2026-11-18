import {mediaWaitEvents,updateMediaWait} from './media-wait.js';

export function initializeExclusiveVideos(root){
 const buttons=[...root.querySelectorAll('[data-rdc-video-toggle]')];
 const sync=video=>{const button=video.closest('.rdc-video-card__media')?.querySelector('[data-rdc-video-toggle]');if(button)button.hidden=!video.paused&&!video.ended;};
 const mediaState=event=>{
  const video=event.target;
  if(video.tagName!=='VIDEO')return;
  updateMediaWait(video,video.closest('.rdc-video-card__media,.rdc-clip-player'),event.type);
  if(event.type==='play')for(const other of root.querySelectorAll('video'))if(other!==video)other.pause();
  sync(video);
 };
 const click=event=>{const button=event.target.closest('[data-rdc-video-toggle]');if(!button||!root.contains(button))return;const video=button.parentElement.querySelector('video');if(video)Promise.resolve(video.play()).catch(()=>sync(video));};
 for(const button of buttons){const video=button.parentElement.querySelector('video');if(video)sync(video);}
 for(const event of mediaWaitEvents)root.addEventListener(event,mediaState,true);root.addEventListener('click',click);
 return()=>{for(const event of mediaWaitEvents)root.removeEventListener(event,mediaState,true);root.removeEventListener('click',click);for(const video of root.querySelectorAll('video')){video.pause();updateMediaWait(video,video.closest('.rdc-video-card__media,.rdc-clip-player'),'pause');}for(const button of buttons)button.hidden=true;};
}