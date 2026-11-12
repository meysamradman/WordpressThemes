import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
export function initializeTextImages(root,{gsap,ScrollTrigger}){
 gsap.registerPlugin(ScrollTrigger);
 const heading=root.querySelector('.rdl-tx-im__ttl'),images=[...root.querySelectorAll('.rdl-tx-im__md')];
 if(!heading)return()=>{};
 const timeline=gsap.timeline({scrollTrigger:{trigger:root,start:'top 85%',once:true}});
 timeline.fromTo(heading,{autoAlpha:0},{autoAlpha:1,duration:1,ease:'power1.out',immediateRender:false},0);
 if(images.length)timeline.fromTo(images,{scale:.5,autoAlpha:0},{scale:1,autoAlpha:1,duration:.6,ease:'power2.out',immediateRender:false},.2);
 return()=>{timeline.scrollTrigger?.kill();timeline.revert();};
}
for(const root of document.querySelectorAll('[data-rdl-tx-im]'))mountMotion(root,'text-images',initializeTextImages);
