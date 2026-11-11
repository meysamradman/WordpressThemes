import '../../src/js/scroll.js';
import {initializeSticky} from '../../shared/radmanct/src/js/runtime/sticky.js';
import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {mountMotion} from '../../src/js/motion-view.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdl-spl]')){
 page.mountRoot(root,'sticky-cards-layout',()=>({}),()=>initializeSticky(root,{target:root.querySelector('.rdl-spl__hdr'),boundary:root.querySelector('.rdl-spl__list'),minWidth:768,gap:100}));
 mountMotion(root,'sticky-cards-images',(node,{gsap,ScrollTrigger})=>{gsap.registerPlugin(ScrollTrigger);const context=gsap.context(()=>{
  for(const image of node.querySelectorAll('.rdl-spl__card img'))gsap.fromTo(image,{scale:1.5,autoAlpha:0},{scale:1,autoAlpha:1,duration:1.5,delay:.3,ease:'power2.out',scrollTrigger:{trigger:image.parentElement,start:'top 85%',once:true}});
 },node);return()=>context.revert();});
}
