import '../../src/js/scroll.js';
import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {initializeCarousel} from '../../src/js/ui/carousel.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdl-cr]'))page.mountRoot(root,'carousel',()=>({}),()=>{
 const release=initializeCarousel(root),videos=[...root.querySelectorAll('video')],doc=root.ownerDocument,view=doc.defaultView;
 const pauseHidden=()=>{if(doc.hidden)videos.forEach(video=>video.pause());};
 const observer=videos.length&&view.IntersectionObserver?new view.IntersectionObserver(entries=>{for(const entry of entries)if(!entry.isIntersecting)entry.target.pause();},{threshold:.25}):null;
 videos.forEach(video=>observer?.observe(video));doc.addEventListener('visibilitychange',pauseHidden);
 return()=>{release();observer?.disconnect();videos.forEach(video=>video.pause());doc.removeEventListener('visibilitychange',pauseHidden);};
});
