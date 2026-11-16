import {createPageLifecycle} from '../runtime/lifecycle.js';
import {loadGsap} from '../motion.js';
const page=createPageLifecycle(window);
for(const art of document.querySelectorAll('[data-rdc-mini-art]'))page.mountRoot(art,'compact-title-art',()=>import('../runtime/art-orbit.js'),module=>module.initializeOfferingArt(art,loadGsap));
