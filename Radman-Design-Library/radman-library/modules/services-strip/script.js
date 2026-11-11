import '../../src/js/scroll.js';
import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {loadAnimation} from '../../shared/radmanct/src/js/motion.js';
import {initializeServicesStrip} from '../../shared/radmanct/src/js/pages/services-strip.js';
const page=createPageLifecycle(window);
if(matchMedia('(prefers-reduced-motion:no-preference)').matches){
 for(const root of document.querySelectorAll('[data-rdc-svc-strip]')){
  if(root.firstElementChild?.children.length>2)page.mountRoot(root,'services-strip',loadAnimation,vendor=>initializeServicesStrip(root,vendor));
 }
}
