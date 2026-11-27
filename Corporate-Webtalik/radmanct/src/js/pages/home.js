import './home/offering-art.js';
import {createPageLifecycle} from '../runtime/lifecycle.js';
import {loadAnimation,loadGsap} from '../motion.js';
import {initializeServices} from './home/slides.js';
import {initializeRotator} from '../ui/rotator.js';
import {initializeEntrance} from '../ui/text-motion.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdc-svcs]'))page.mountRoot(root,'services',()=>({}),()=>initializeServices(root));
for(const root of document.querySelectorAll('[data-rdc-rot]'))page.mountRoot(root,'rotate',loadGsap,(vendor,node)=>initializeRotator(node,vendor));

for(const root of document.querySelectorAll('[data-rdc-sel-work]'))page.mountRoot(root,'selected-work',()=>import('./home/projects.js'),module=>module.initializeSelectedWork(root,loadAnimation,loadGsap));

for(const root of document.querySelectorAll('[data-rdc-ent]'))page.mountRoot(root,'entrance',loadGsap,(vendor,node)=>initializeEntrance(node,vendor,{profile:node.closest('.rdc-hero')?'hero':'default'}));
