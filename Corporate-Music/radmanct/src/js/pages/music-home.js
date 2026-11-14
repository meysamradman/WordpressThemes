import {initializeCarousel} from '../ui/carousel.js';
import {initializeExclusiveVideos} from '../ui/video-player.js';
import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeMusicPlayer} from '../ui/music-player.js';
const page=createPageLifecycle(window),root=document.querySelector('[data-rdc-music-home]');
if(root)page.mountRoot(root,'music-home',()=>({}),()=>{
 const releaseRails=[...root.querySelectorAll('[data-rdc-rail]')].map(initializeCarousel);
 const releasePlayer=initializeMusicPlayer(root),reduced=matchMedia('(prefers-reduced-motion:reduce)');
 const click=event=>{
  const feature=root.querySelector('[data-rdc-feature-track]');
  if(event.target.closest('[data-rdc-feature-prev],[data-rdc-feature-next]')&&feature){const width=feature.clientWidth,rtl=getComputedStyle(feature).direction==='rtl',current=Math.round(Math.abs(feature.scrollLeft)/width),last=feature.children.length-1,step=event.target.closest('[data-rdc-feature-next]')?1:-1,index=(current+step+last+1)%(last+1);feature.scrollTo({left:(rtl?-1:1)*index*width,behavior:reduced.matches?'instant':'smooth'});}

 };
 const releaseVideos=initializeExclusiveVideos(root);
 root.addEventListener('click',click);return()=>{for(const release of releaseRails)release();releasePlayer();root.removeEventListener('click',click);releaseVideos();};
});
