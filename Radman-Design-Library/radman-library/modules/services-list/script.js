import '../../src/js/scroll.js';
import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {initializeSticky} from '../../shared/radmanct/src/js/runtime/sticky.js';
import {initializeServicesShowcase} from '../../shared/radmanct/src/js/ui/services-showcase.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdc-svc-show]')){
 page.mountRoot(root,'services-showcase',()=>({}),()=>initializeServicesShowcase(root));
 page.mountRoot(root,'services-sticky',()=>({}),()=>initializeSticky(root,{target:root.querySelector('.rdc-svc-show__vis'),boundary:root.querySelector('.rdc-svc-show__ly')}));
}
