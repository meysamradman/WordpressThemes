import '../../src/js/scroll.js';
import '../../shared/radmanct/src/js/pages/home/offering-art.js';
import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {loadGsap} from '../../shared/radmanct/src/js/motion.js';
import {initializeRotator} from '../../shared/radmanct/src/js/ui/rotator.js';
import {initializeEntrance} from '../../shared/radmanct/src/js/ui/text-motion.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdc-rot]'))page.mountRoot(root,'rotate',loadGsap,(vendor,node)=>initializeRotator(node,vendor));
for(const root of document.querySelectorAll('[data-rdc-ent]'))page.mountRoot(root,'entrance',loadGsap,(vendor,node)=>initializeEntrance(node,vendor,{profile:'hero'}));
