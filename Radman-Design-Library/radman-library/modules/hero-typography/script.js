import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {loadGsap} from '../../shared/radmanct/src/js/motion.js';

// A single page-entry reveal: no carousel, splitting, scroll scene or idle loop.
export function initializeHeroTypography(root,{gsap}){
 const rows=[...root.querySelectorAll('[data-rdl-htx-row]')];
 if(!rows.length||root.getBoundingClientRect().bottom<=0)return()=>{};
 const context=gsap.context(()=>{
  gsap.fromTo(rows,{autoAlpha:0,y:40},{autoAlpha:1,y:0,duration:1,stagger:.08,ease:'power2.out',clearProps:'transform,opacity,visibility'});
 },root);
 return()=>context.revert();
}
for(const root of document.querySelectorAll('[data-rdl-htx]'))mountMotion(root,'hero-typography',initializeHeroTypography,{load:loadGsap});
