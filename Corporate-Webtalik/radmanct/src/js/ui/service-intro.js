import {loadAnimation} from '../motion.js';
import {reportRuntimeError} from '../runtime/lifecycle.js';
import {initializeMediaParallax} from '../runtime/media-parallax.js';
import {initializeEntrance} from './text-motion.js';

export function initializeServiceIntro(root) {
 const view=root.ownerDocument.defaultView;
 const preference=view.matchMedia('(prefers-reduced-motion:no-preference)');
 const media=[...root.querySelectorAll('[data-rdc-intro-media]')];
 const releases=media.map(frame=>initializeMediaParallax(frame,{
  frame,target:frame.firstElementChild,minWidth:0,motion:'reveal',
 },loadAnimation));
 let active=true,pending=false,initialized=false;

 async function start(){
  if(!active||pending||initialized||!preference.matches)return;
  pending=true;
  try {
   const vendor=await loadAnimation();
   if(!active||!root.isConnected||!preference.matches)return;
   const {gsap,ScrollTrigger}=vendor;
   for(const copy of root.querySelectorAll('[data-rdc-intro-copy]'))releases.push(initializeEntrance(copy,vendor,{viewport:true}));
   const art=root.querySelector('[data-rdc-intro-art]');
   if(art){
    gsap.registerPlugin(ScrollTrigger);
    const motion=gsap.matchMedia();
    releases.push(()=>motion.revert());
    motion.add('(min-width:1024px) and (prefers-reduced-motion:no-preference)',()=>{
     const [left,top,right]=art.children;
     const timeline=gsap.timeline({defaults:{ease:'power2.out'},scrollTrigger:{trigger:art,start:'top 90%',end:'bottom center',scrub:1}});
     timeline.from(left,{x:-100},0).from(top,{y:-100},0).from(right,{x:100},0);
    },root);
   }
   initialized=true;
  } catch(error) {
   if(active)reportRuntimeError('service-intro',error,view);
  } finally { pending=false; }
 }

 preference.addEventListener('change',start);
 void start();
 return()=>{
  active=false;
  preference.removeEventListener('change',start);
  releases.forEach(release=>release());
 };
}
