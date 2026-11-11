import {createPageLifecycle,reportRuntimeError} from '../runtime/lifecycle.js';
import {initializeMediaParallax} from '../runtime/media-parallax.js';
import {loadAnimation} from '../motion.js';

export function initializeTitleVideo(root){
 const doc=root.ownerDocument,view=doc.defaultView;
 const video=root.querySelector('video'),button=root.querySelector('[data-rdc-pt-play]');
 if(!video||!button)return()=>{};
 const preference=view.matchMedia('(prefers-reduced-motion:reduce)'),connection=view.navigator?.connection;
 let alive=true,visible=false,suspended=false,paused=false,wanted=false,serial=0;
 const indicate=running=>{
  root.toggleAttribute('data-rdc-pt-run',running);
  button.setAttribute('aria-pressed',String(paused));
  button.setAttribute('aria-label',paused?button.dataset.playLabel:button.dataset.pauseLabel);
 };
 const sync=()=>{
  button.hidden=preference.matches||!!connection?.saveData||!view.IntersectionObserver;
  const next=alive&&root.isConnected&&visible&&!doc.hidden&&!suspended&&!paused&&!button.hidden;
  if(next===wanted)return;
  wanted=next;const token=++serial;
  if(!next){video.pause();indicate(false);return;}
  try{
   if(!video.getAttribute('src')){
    const url=new URL(video.dataset.src,view.location.href);
    if(url.origin!==view.location.origin)throw new Error('Title video must be local');
    video.muted=true;video.defaultMuted=true;video.volume=0;video.loop=true;video.playsInline=true;video.src=url.href;
   }
   Promise.resolve(video.play()).then(()=>{
    if(!alive||!wanted){video.pause();return;}
    if(token===serial)indicate(true);
   }).catch(()=>{if(alive&&token===serial){paused=true;wanted=false;indicate(false);}});
  }catch(error){paused=true;wanted=false;indicate(false);reportRuntimeError('page-title-video',error,view);}
 };
 const toggle=()=>{paused=!paused;sync();};
 const failed=()=>{++serial;paused=true;wanted=false;video.pause();indicate(false);};
 // Without IntersectionObserver, retain the static image rather than run unseen video.
 const observer=view.IntersectionObserver?new view.IntersectionObserver(entries=>{visible=entries.some(entry=>entry.isIntersecting);sync();},{threshold:.05}):null;
 observer?.observe(root);
 const hide=()=>{suspended=true;sync();},show=()=>{suspended=false;sync();};
 video.addEventListener('error',failed);button.addEventListener('click',toggle);preference.addEventListener('change',sync);connection?.addEventListener?.('change',sync);
 doc.addEventListener('visibilitychange',sync);view.addEventListener('pagehide',hide);view.addEventListener('pageshow',show);sync();
 return()=>{
  if(!alive)return;alive=false;wanted=false;++serial;observer?.disconnect();video.pause();indicate(false);button.hidden=true;
  video.removeAttribute('src');video.load();
  video.removeEventListener('error',failed);button.removeEventListener('click',toggle);preference.removeEventListener('change',sync);connection?.removeEventListener?.('change',sync);
  doc.removeEventListener('visibilitychange',sync);view.removeEventListener('pagehide',hide);view.removeEventListener('pageshow',show);
 };
}

if(typeof document!=='undefined'){
 const page=createPageLifecycle(window);
 for(const root of document.querySelectorAll('.rdc-pt--cover')){
  const targets=[...root.querySelectorAll('.rdc-pt__inr>img,.rdc-pt__inr>video')];
  if(targets.length)page.mountRoot(root,'page-title-motion',()=>({}),()=>initializeMediaParallax(root,{frame:root,target:targets,motion:'zoom',amount:15,minWidth:1024},loadAnimation));
 }
 for(const root of document.querySelectorAll('[data-rdc-pt-vid]'))page.mountRoot(root,'page-title-video',()=>({}),()=>initializeTitleVideo(root));
}
