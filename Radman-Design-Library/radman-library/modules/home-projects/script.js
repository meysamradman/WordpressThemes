import '../../src/js/scroll.js';
import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {loadAnimation,loadGsap} from '../../shared/radmanct/src/js/motion.js';
import {initializeSelectedWork} from '../../shared/radmanct/src/js/pages/home/projects.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdc-sel-work]'))page.mountRoot(root,'selected-work',()=>({}),()=>initializeSelectedWork(root,loadAnimation,loadGsap));
