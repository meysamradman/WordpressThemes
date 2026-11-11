import '../../src/js/scroll.js';
import '../../shared/radmanct/src/js/pages/home/offering-art.js';
import {createPageLifecycle,reportRuntimeError} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {loadAnimation} from '../../shared/radmanct/src/js/motion.js';
import {mount} from '../../shared/radmanct/src/js/pages/home/slides.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdc-svcs]'))page.mountRoot(root,'services',()=>({}),()=>{
 const media=matchMedia('(min-width:1024px) and (prefers-reduced-motion:no-preference)');let alive=true,pending=false,release;
 const start=async()=>{if(!alive||pending||release||!media.matches)return;pending=true;try{const vendor=await loadAnimation();if(alive&&root.isConnected&&media.matches)release=mount(root,vendor);}catch(error){if(alive)reportRuntimeError('services',error);}finally{pending=false;}};
 media.addEventListener('change',start);start();return()=>{alive=false;media.removeEventListener('change',start);release?.();};
});
