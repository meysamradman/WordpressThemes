import {loadAnimation} from '../motion.js';
import {reportRuntimeError} from './lifecycle.js';
import {getSmoothScroller} from './scroll-state.js';
import {observeSizes} from './observers.js';
import {createRefreshRequest} from './refresh.js';
export function initializeSticky(root,{target,boundary,minWidth=1024,gap=24},load=loadAnimation){
 if(!target||!boundary)return()=>{};
 const doc=root.ownerDocument,view=doc.defaultView;
 const media=view.matchMedia(`(min-width:${minWidth}px) and (hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)`);
 let alive=true,pending=false,context,releaseSize,refresh;
 const stop=()=>{releaseSize?.();releaseSize=null;refresh?.cancel();const hadPin=!!context;context?.revert();context=null;if(hadPin&&alive)refresh?.flush();};
 const sync=async()=>{
  if(!alive)return;
  if(!media.matches){stop();return;}
  if(context||pending)return;
  pending=true;
  try{
   const {gsap,ScrollTrigger}=await load();
   if(!alive||!root.isConnected||!media.matches)return;
   gsap.registerPlugin(ScrollTrigger);
   refresh ||= createRefreshRequest(view,ScrollTrigger);
   const offset=()=>{const bar=doc.getElementById('wpadminbar');const toolbar=bar&&view.getComputedStyle(bar).position==='fixed'?bar.getBoundingClientRect().height:0;return Math.min(toolbar+gap,view.innerHeight-target.offsetHeight-gap);};
   context=gsap.context(()=>{
    gsap.set(target,{position:'relative',top:0});
    ScrollTrigger.create({trigger:target,start:()=>`top ${offset()}`,endTrigger:boundary,end:()=>`bottom ${target.offsetHeight+offset()}`,pin:target,...(getSmoothScroller(doc)?{pinType:'transform'}:{}),pinSpacing:false,invalidateOnRefresh:true});
   },root);
   const request=()=>{if(alive&&context&&root.isConnected)refresh.request();};
   releaseSize=observeSizes(view,[target,boundary],request);
   doc.fonts?.ready.then(request).catch(()=>{});request();
  }catch(error){if(alive)reportRuntimeError('sticky',error,view);stop();}finally{pending=false;}
 };
 const mode=()=>{stop();void sync();};
 doc.addEventListener?.('rdc:scr-mode',mode);media.addEventListener('change',sync);sync();
 return()=>{alive=false;doc.removeEventListener?.('rdc:scr-mode',mode);media.removeEventListener('change',sync);stop();refresh?.dispose();};
}
