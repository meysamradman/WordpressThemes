import '../../src/js/ui/collection-archive.js';
import {initializeExclusiveVideos} from '../../src/js/ui/video-player.js';
import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
const root=document.querySelector('.rdc-collection'),page=createPageLifecycle(window);
if(root)page.mountRoot(root,'videos',()=>({}),()=>initializeExclusiveVideos(root));
