import './home/offering-art.js';
import {createPageLifecycle,reportRuntimeError} from '../runtime/lifecycle.js';
import {loadAnimation,loadGsap} from '../motion.js';
import {mount} from './home/slides.js';
import {initializeRotator} from '../ui/rotator.js';
import {initializeEntrance} from '../ui/text-motion.js';
const page=createPageLifecycle(window);
export function initializeServices(root,load=loadAnimation){
 const media=root.ownerDocument.defaultView.matchMedia('(min-width:1024px) and (prefers-reduced-motion:no-preference)');let alive=true,pending=false,release;
 const start=async()=>{if(!alive||pending||release||!media.matches)return;pending=true;try{const vendor=await load();if(alive&&root.isConnected&&media.matches)release=mount(root,vendor);}catch(error){if(alive)reportRuntimeError('services',error);}finally{pending=false;}};
 media.addEventListener('change',start);start();return()=>{alive=false;media.removeEventListener('change',start);release?.();};
}
for(const root of document.querySelectorAll('[data-rdc-svcs]'))page.mountRoot(root,'services',()=>({}),()=>initializeServices(root));
for(const root of document.querySelectorAll('[data-rdc-rot]'))page.mountRoot(root,'rotate',loadGsap,(vendor,node)=>initializeRotator(node,vendor));

for(const root of document.querySelectorAll('[data-rdc-sel-work]'))page.mountRoot(root,'selected-work',()=>import('./home/projects.js'),module=>module.initializeSelectedWork(root,loadAnimation,loadGsap));

for(const root of document.querySelectorAll('[data-rdc-ent]'))page.mountRoot(root,'entrance',loadGsap,(vendor,node)=>initializeEntrance(node,vendor,{profile:node.closest('.rdc-hero')?'hero':'default'}));
