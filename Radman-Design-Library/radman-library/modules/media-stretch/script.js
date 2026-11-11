import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {getSmoothScroller} from '../../shared/radmanct/src/js/runtime/scroll-state.js';
import {createRefreshRequest} from '../../shared/radmanct/src/js/runtime/refresh.js';
for(const root of document.querySelectorAll('[data-rdl-str]'))mountMotion(root,'stretch',(node,{gsap,ScrollTrigger})=>{
 gsap.registerPlugin(ScrollTrigger);const doc=node.ownerDocument,view=doc.defaultView,stage=node.querySelector('.rdl-str__stage'),frame=node.querySelector('.rdl-str__frame'),refresh=createRefreshRequest(view,ScrollTrigger);let context,alive=true;
 const sync=()=>{context?.revert();context=gsap.context(()=>{
  gsap.fromTo(frame,{width:()=>Math.min(500,stage.clientWidth*.8),borderRadius:250},{width:'100%',borderRadius:0,ease:'power1.out',scrollTrigger:{trigger:stage,start:'top top',end:()=>'+='+Math.max(1,view.innerHeight*.395),pin:stage,pinSpacing:true,pinType:getSmoothScroller(doc)?'transform':undefined,scrub:1,invalidateOnRefresh:true}});
 },node);refresh.flush();};
 sync();doc.addEventListener('rdc:scr-mode',sync);doc.fonts?.ready.then(()=>{if(alive)refresh.request();});
 return()=>{alive=false;doc.removeEventListener('rdc:scr-mode',sync);context?.revert();refresh.dispose();ScrollTrigger.refresh();};
},{query:'(min-width:768px) and (prefers-reduced-motion:no-preference)'});
