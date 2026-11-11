import '../../src/js/scroll.js';
import {mountTextMarquee} from '../../src/js/ui/text-marquee.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {createRefreshRequest} from '../../shared/radmanct/src/js/runtime/refresh.js';
for(const loop of document.querySelectorAll('[data-rdl-mtx]'))mountTextMarquee(loop);
for(const root of document.querySelectorAll('[data-rdl-tmq]'))mountMotion(root,'story-media',(node,{gsap,ScrollTrigger})=>{
 gsap.registerPlugin(ScrollTrigger);const doc=node.ownerDocument,view=doc.defaultView,scene=node.querySelector('.rdl-tmq__scene'),story=node.querySelector('.rdl-tmq__story'),anchor=node.querySelector('.rdl-tmq__photo-anchor'),photo=node.querySelector('.rdl-tmq__photo'),refresh=createRefreshRequest(view,ScrollTrigger);let alive=true;
 const context=gsap.context(()=>{
  gsap.fromTo(story,{width:'80%',borderRadius:34,y:60},{width:'calc(100% - 24px)',borderRadius:0,y:0,ease:'power1.out',scrollTrigger:{trigger:scene,start:'clamp(top 80%)',end:self=>Math.max(Math.max(0,self.start)+1,Math.min(ScrollTrigger.maxScroll(view),Math.max(0,self.start)+view.innerHeight*.7)),scrub:1,invalidateOnRefresh:true}});
  gsap.fromTo(photo,{width:()=>view.innerWidth<=768?'80%':'60%',scale:.95},{width:'100%',scale:1,ease:'power3.out',scrollTrigger:{trigger:anchor,start:'clamp(top bottom)',end:'clamp(top 20%)',scrub:3.5,invalidateOnRefresh:true}});
 },node);
 refresh.flush();doc.fonts?.ready.then(()=>{if(alive)refresh.request();});
 return()=>{alive=false;context.revert();refresh.dispose();};
});
