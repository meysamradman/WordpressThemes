import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {observeSizes} from '../../shared/radmanct/src/js/runtime/observers.js';
import {createRefreshRequest} from '../../shared/radmanct/src/js/runtime/refresh.js';
import {getSmoothScroller} from '../../shared/radmanct/src/js/runtime/scroll-state.js';
export function initializeWellness(root,{gsap,ScrollTrigger}){
 gsap.registerPlugin(ScrollTrigger);
 const view=root.ownerDocument.defaultView,win=root.querySelector('.rdl-well__win'),track=root.querySelector('.rdl-well__track');
 const refresh=createRefreshRequest(view,ScrollTrigger);
 const distance=()=>Math.max(0,track.scrollWidth-win.clientWidth+parseFloat(getComputedStyle(win).paddingLeft)+parseFloat(getComputedStyle(win).paddingRight));
 const context=gsap.context(()=>{
  win.scrollLeft=0;win.classList.add('is-enh');
  gsap.fromTo(track,{x:0},{x:()=>-distance(),ease:'none',scrollTrigger:{trigger:root,start:'top top',end:()=>'+='+Math.max(1,distance()),pin:true,pinType:getSmoothScroller(root.ownerDocument)?'transform':undefined,scrub:true,anticipatePin:1,invalidateOnRefresh:true}});
 },root);
 const unobserve=observeSizes(view,[win],()=>refresh.request());ScrollTrigger.refresh();
 return()=>{unobserve();refresh.dispose();context.revert();win.classList.remove('is-enh');ScrollTrigger.refresh();};
}
for(const root of document.querySelectorAll('[data-rdl-well]'))mountMotion(root,'wellness',initializeWellness,{query:'(min-width:960px) and (prefers-reduced-motion:no-preference)'});
