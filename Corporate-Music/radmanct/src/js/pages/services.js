import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeSticky} from '../runtime/sticky.js';
import {initializeServicesShowcase} from '../ui/services-showcase.js';
import {loadAnimation} from '../motion.js';
import {initializeProjectMarquee} from '../ui/project-marquee.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdc-svc-show]')){
 page.mountRoot(root,'services-showcase',()=>({}),()=>initializeServicesShowcase(root));
 page.mountRoot(root,'services-sticky',()=>({}),()=>initializeSticky(root,{target:root.querySelector('.rdc-svc-show__vis'),boundary:root.querySelector('.rdc-svc-show__ly')}));
}
for(const root of document.querySelectorAll('[data-rdc-prj-mq]'))page.mountRoot(root,'project-marquee',loadAnimation,vendor=>initializeProjectMarquee(root,vendor));
