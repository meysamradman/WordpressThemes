import {initializeMenuPanel} from './menu-panel.js';
import {loadGsap} from '../motion.js';
import {reportRuntimeError} from '../runtime/lifecycle.js';
export function initializeHeader(doc=document){
 const view=doc.defaultView,nav=doc.querySelector('[data-rdc-menu]'),dialog=doc.getElementById('rdc-menu-dlg');const desktop=view.matchMedia('(min-width:1024px)');const preference=view.matchMedia('(min-width:1024px) and (hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)');let alive=true,pending=false,releases=[],generation=0,panelRelease;
 const stop=()=>{generation++;for(const release of releases)release();releases=[];};
 const sync=async()=>{stop();if(!nav||!preference.matches||pending||!alive)return;const token=generation;pending=true;try{const [vendor,effect]=await Promise.all([loadGsap(),nav?import('./menu-hover.js'):null]);if(alive&&preference.matches&&generation===token){if(effect)releases.push(effect.mount(nav,vendor));}}catch(error){if(alive)reportRuntimeError('header-effects',error,view);}finally{pending=false;if(alive&&preference.matches&&generation!==token)sync();}};
 if(dialog)panelRelease=initializeMenuPanel(dialog,doc.querySelector('[data-rdc-menu-open]'),dialog.querySelector('[data-rdc-menu-cls]'),desktop);
 preference.addEventListener('change',sync);sync();return()=>{alive=false;stop();panelRelease?.();preference.removeEventListener('change',sync);};
}
