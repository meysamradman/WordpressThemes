import {observeSizes} from '../runtime/observers.js';

export function initializeServicesStrip(root,{gsap,ScrollTrigger}){
 const track=root.firstElementChild;
 const slides=[...track.children];
 const view=root.ownerDocument.defaultView;
 const media=gsap.matchMedia();
 gsap.registerPlugin(ScrollTrigger);
 media.add('(prefers-reduced-motion: no-preference)',()=>{
  const clones=slides.map(slide=>{
   const clone=slide.cloneNode(true);
   clone.setAttribute('aria-hidden','true');
   return clone;
  });
  track.append(...clones);
  root.scrollLeft=0;
  root.classList.add('is-enh');
  const state={progress:0,offset:0};
  let cycle=0,step=0,width=0,drag;
  const paint=()=>{
   if(!cycle)return;
   const position=(.35-2.35*state.progress)*width+state.offset;
   track.style.transform=`translate3d(${((position%cycle+cycle)%cycle)-cycle}px,0,0)`;
  };
  const measure=()=>{
   const first=slides[0].getBoundingClientRect();
   width=first.width;
   step=slides[1].getBoundingClientRect().left-first.left;
   cycle=clones[0].getBoundingClientRect().left-first.left;
   paint();
  };
  const snap=offset=>gsap.to(state,{offset,duration:.25,ease:'power2.out',overwrite:'auto',onUpdate:paint});
  const start=event=>{
   if(!event.isPrimary||event.button!==0)return;
   gsap.killTweensOf(state,'offset');
   drag={id:event.pointerId,x:event.clientX,offset:state.offset};
   root.setPointerCapture(event.pointerId);
  };
  const move=event=>{
   if(event.pointerId!==drag?.id)return;
   state.offset=drag.offset+event.clientX-drag.x;
   paint();
  };
  const end=event=>{
   if(event.pointerId!==drag?.id)return;
   if(event.type==='pointerup'&&step&&Math.abs(event.clientX-drag.x)>8){
    const position=(.35-2.35*state.progress)*width+state.offset;
    snap(state.offset+Math.round(position/step)*step-position);
   }
   drag=null;
  };
  const key=event=>{
   if(event.key!=='ArrowLeft'&&event.key!=='ArrowRight')return;
   event.preventDefault();
   snap(state.offset+(event.key==='ArrowLeft'?step:-step));
  };
  const listeners={pointerdown:start,pointermove:move,pointerup:end,pointercancel:end,lostpointercapture:end,keydown:key};
  for(const [name,callback] of Object.entries(listeners))root.addEventListener(name,callback);
  const releaseSize=observeSizes(view,[root],measure);
  measure();
  gsap.to(state,{
   progress:1,ease:'power1.out',onUpdate:paint,
   scrollTrigger:{trigger:root,start:'top-=1000 top',end:'bottom top',scrub:true,onRefresh:measure},
  });
  return()=>{
   releaseSize();
   gsap.killTweensOf(state,'offset');
   for(const [name,callback] of Object.entries(listeners))root.removeEventListener(name,callback);
   if(drag&&root.hasPointerCapture(drag.id))root.releasePointerCapture(drag.id);
   track.style.removeProperty('transform');
   root.classList.remove('is-enh');
   for(const clone of clones)clone.remove();
  };
 });
 return()=>media.revert();
}
