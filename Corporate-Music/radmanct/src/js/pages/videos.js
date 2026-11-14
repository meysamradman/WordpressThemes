import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeExclusiveVideos} from '../ui/video-player.js';
const page=createPageLifecycle(window),root=document.querySelector('[data-rdc-video-page]');
if(root)page.mountRoot(root,'videos',()=>({}),()=>initializeExclusiveVideos(root));
