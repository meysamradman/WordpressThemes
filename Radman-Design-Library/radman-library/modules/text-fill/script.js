import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {addTextFill} from '../../shared/radmanct/src/js/ui/text-motion.js';
import {createRefreshRequest} from '../../shared/radmanct/src/js/runtime/refresh.js';
for(const root of document.querySelectorAll('[data-rdl-tx-demo]'))mountMotion(root,'text-fill',(node,{gsap,ScrollTrigger})=>{
 gsap.registerPlugin(ScrollTrigger);
 const text=node.querySelector('.rdl-tx-demo__fill');if(!text)return()=>{};
 const refresh=createRefreshRequest(node.ownerDocument.defaultView,ScrollTrigger);let restore=()=>{},alive=true;
 const context=gsap.context(()=>{
  const timeline=gsap.timeline({paused:true});restore=addTextFill(timeline,text);
  ScrollTrigger.create({trigger:text,animation:timeline,start:'clamp(top 85%)',end:'clamp(bottom 60%)',scrub:true});
 },node);
 refresh.flush();node.ownerDocument.fonts?.ready.then(()=>{if(alive)refresh.request();});
 return()=>{alive=false;refresh.dispose();context.revert();restore();};
});
