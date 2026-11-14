import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeMusicPlayer} from '../ui/music-player.js';
const page=createPageLifecycle(window),root=document.querySelector('[data-rdc-music-home]');
if(root)page.mountRoot(root,'music-home',()=>({}),()=>{
 const releasePlayer=initializeMusicPlayer(root),reduced=matchMedia('(prefers-reduced-motion:reduce)');
 const click=event=>{
  const feature=root.querySelector('[data-rdc-feature-track]');
  if(event.target.closest('[data-rdc-feature-prev],[data-rdc-feature-next]')&&feature){const width=feature.clientWidth,rtl=getComputedStyle(feature).direction==='rtl',current=Math.round(Math.abs(feature.scrollLeft)/width),last=feature.children.length-1,step=event.target.closest('[data-rdc-feature-next]')?1:-1,index=(current+step+last+1)%(last+1);feature.scrollTo({left:(rtl?-1:1)*index*width,behavior:reduced.matches?'instant':'smooth'});}
  const button=event.target.closest('[data-rdc-rail-prev],[data-rdc-rail-next]');if(button){const rail=button.closest('[data-rdc-rail]').querySelector('[data-rdc-rail-track]'),rtl=getComputedStyle(rail).direction==='rtl',gap=parseFloat(getComputedStyle(rail).columnGap)||0,step=button.hasAttribute('data-rdc-rail-next')?1:-1;rail.scrollBy({left:(rtl?-1:1)*step*(rail.firstElementChild.getBoundingClientRect().width+gap),behavior:reduced.matches?'instant':'smooth'});}
 };
 const videos=[...root.querySelectorAll('video')],pauseOthers=event=>{for(const video of videos)if(video!==event.target)video.pause();};for(const video of videos)video.addEventListener('play',pauseOthers);
 root.addEventListener('click',click);return()=>{releasePlayer();root.removeEventListener('click',click);for(const video of videos){video.pause();video.removeEventListener('play',pauseOthers);}};
});
