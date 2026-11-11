import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {loadGsap} from '../../shared/radmanct/src/js/motion.js';
import {initializeEntrance} from '../../shared/radmanct/src/js/ui/text-motion.js';
for(const root of document.querySelectorAll('[data-rdl-tx-demo]'))mountMotion(root,'text-scroll',(node,vendor)=>initializeEntrance(node,vendor,{viewport:true,profile:'default'}),{load:loadGsap});
