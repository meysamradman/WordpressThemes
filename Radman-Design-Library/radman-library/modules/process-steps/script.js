import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
export function initializeSteps(root,{gsap,ScrollTrigger}){
 gsap.registerPlugin(ScrollTrigger);
 const context=gsap.context(()=>{
  const rtl=getComputedStyle(root).direction==='rtl';
  for(const [index,node] of [...root.querySelectorAll('.rdl-steps__itm')].entries()){
   gsap.fromTo(node,{autoAlpha:0,x:rtl?20:-20},{autoAlpha:1,x:0,duration:.75,delay:.1*(index+1),ease:'power2.out',scrollTrigger:{trigger:node,start:'top 85%',once:true}});
  }
  for(const node of root.querySelectorAll('.rdl-steps__hdr>div'))gsap.fromTo(node,{autoAlpha:0,y:20},{autoAlpha:1,y:0,duration:.75,ease:'power2.out',scrollTrigger:{trigger:node,start:'top 85%',once:true}});
 },root);
 return()=>context.revert();
}
for(const root of document.querySelectorAll('[data-rdl-steps]'))mountMotion(root,'steps',initializeSteps);
