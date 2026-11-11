import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeSticky} from '../runtime/sticky.js';
import {initializeServicesShowcase} from '../ui/services-showcase.js';
import {loadAnimation} from '../motion.js';
import {initializeServicesStrip} from './services-strip.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdc-svc-show]')){
 page.mountRoot(root,'services-showcase',()=>({}),()=>initializeServicesShowcase(root));
 page.mountRoot(root,'services-sticky',()=>({}),()=>initializeSticky(root,{target:root.querySelector('.rdc-svc-show__vis'),boundary:root.querySelector('.rdc-svc-show__ly')}));
}
if(window.matchMedia('(prefers-reduced-motion: no-preference)').matches){
 for(const root of document.querySelectorAll('[data-rdc-svc-strip]')){
  if(root.firstElementChild?.children.length>2)page.mountRoot(root,'services-strip',loadAnimation,vendor=>initializeServicesStrip(root,vendor));
 }
}
