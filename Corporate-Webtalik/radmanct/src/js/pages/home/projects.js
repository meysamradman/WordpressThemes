import {reportRuntimeError} from '../../runtime/lifecycle.js';
import {initializeEntrance} from '../../ui/text-motion.js';
import {initializeSticky} from '../../runtime/sticky.js';
import {initializeMediaParallax} from '../../runtime/media-parallax.js';
export function initializeSelectedWork(root,load,loadEntry=load){
 const intro=root.querySelector('.rdc-work__intr');
 const releases=[initializeSticky(root,{target:root.querySelector('.rdc-work__intr'),boundary:root.querySelector('.rdc-work__ly')},load)];
 let alive=true;
 if(!root.ownerDocument.defaultView.matchMedia('(prefers-reduced-motion:reduce)').matches)loadEntry().then(vendor=>{
  if(!alive||!root.isConnected)return;
  if(intro)releases.push(initializeEntrance(intro,vendor,{viewport:true,targets:[...intro.children]}));
  for(const meta of root.querySelectorAll('.rdc-work__meta'))releases.push(initializeEntrance(meta,vendor,{viewport:true,targets:[meta.querySelector('h3'),meta.querySelector('p'),meta.querySelector('ul')]}));
 }).catch(error=>{if(alive)reportRuntimeError('selected-work-entrance',error,root.ownerDocument.defaultView);});
 for(const frame of root.querySelectorAll('[data-rdc-work-md]'))releases.push(initializeMediaParallax(frame,{frame,target:frame.querySelector('.rdc-work__md'),minWidth:1024,motion:'reveal'},load));
 return()=>{alive=false;releases.forEach(release=>release());};
}
