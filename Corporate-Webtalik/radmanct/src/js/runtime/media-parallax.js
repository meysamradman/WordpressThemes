import {reportRuntimeError} from './lifecycle.js';
import {getSmoothScroller} from './scroll-state.js';
import {createRefreshRequest} from './refresh.js';
import {observeSizes} from './observers.js';

const revealed=new WeakSet();
export function initializeMediaParallax(root, {frame, target, amount = 12, minWidth = 768, direction = 'up', motion = 'parallax'}, load) {
 const doc=root.ownerDocument,view=doc.defaultView;
 if(!frame||!target)return()=>{};
 const bounded=(value,fallback,min,max)=>Number.isFinite(Number(value))?Math.min(max,Math.max(min,Number(value))):fallback;
 amount=bounded(amount,12,1,30);
 const width=bounded(minWidth,768,0,1920);
 const media=view.matchMedia(`(min-width: ${width}px) and (prefers-reduced-motion: no-preference)`);
 let active=true,pending=false,context,trigger,refresh;
 const reset=()=>{context?.revert();context=null;trigger=null;refresh?.cancel();};
 const update=async()=>{
  if(!active)return;
  if(!media.matches){reset();return;}
  if(context||pending||motion==='reveal'&&revealed.has(target))return;
  pending=true;
  try{
   const {gsap,ScrollTrigger}=await load();
   if(!active||!root.isConnected||!media.matches)return;
   // A late vendor must never hide a picture the visitor can already see.
   if(motion==='reveal'){
    const rect=frame.getBoundingClientRect?.();
    if(rect&&rect.top<view.innerHeight){revealed.add(target);return;}
   }
   gsap.registerPlugin(ScrollTrigger);
   refresh ||= createRefreshRequest(view,ScrollTrigger);
   const from=direction==='down'?-amount:amount,scale=1+amount*.02+.02;
   context=gsap.context(()=>{},root);
   context.add(()=>{
    if(motion==='reveal'){
     trigger=gsap.fromTo(target,{autoAlpha:0,scale:1.2},{autoAlpha:1,scale:1,duration:2,ease:'power2.out',immediateRender:false,clearProps:'transform,opacity,visibility',onComplete:()=>revealed.add(target),scrollTrigger:{trigger:frame,start:'top 100%',once:true}}).scrollTrigger;
    }else if(motion==='zoom'){
     trigger=gsap.fromTo(target,{scale:1},{scale:1+amount/100,duration:1,ease:'power1.out',scrollTrigger:{trigger:frame,start:'top bottom',end:'bottom center',scrub:1,invalidateOnRefresh:true}}).scrollTrigger;
    }else{
     trigger=gsap.fromTo(target,{yPercent:from,scale},{yPercent:-from,scale,ease:'none',scrollTrigger:{trigger:frame,start:'top bottom',end:'bottom top',scrub:getSmoothScroller(doc)?true:.5,invalidateOnRefresh:true}}).scrollTrigger;
    }
   });
  }catch(error){reset();reportRuntimeError('image-parallax',error,view);}
  finally{pending=false;}
 };
 const request=()=>{if(active&&context)refresh?.request();};
 const mode=()=>{
  if(motion!=='parallax')return;
  if(trigger){const scrub=getSmoothScroller(doc)?true:.5;trigger.vars.scrub=scrub;trigger.scrubDuration(scrub);request();}
 };
 const releaseSize=observeSizes(view,[frame],request);
 media.addEventListener('change',update);doc.addEventListener?.('rdc:scr-mode',mode);
 view.addEventListener('resize',request);root.addEventListener('load',request,true);
 doc.fonts?.ready.then(request).catch(()=>{});
 update();
 return()=>{
  if(!active)return;active=false;releaseSize();refresh?.dispose();
  media.removeEventListener('change',update);doc.removeEventListener?.('rdc:scr-mode',mode);
  view.removeEventListener('resize',request);root.removeEventListener('load',request,true);reset();
 };
}
