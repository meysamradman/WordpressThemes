import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {getSmoothScroller} from '../../shared/radmanct/src/js/runtime/scroll-state.js';
import {createRefreshRequest} from '../../shared/radmanct/src/js/runtime/refresh.js';
import {observeSizes} from '../../shared/radmanct/src/js/runtime/observers.js';
export function initializeColumns(root,{gsap,ScrollTrigger}){
 gsap.registerPlugin(ScrollTrigger);
 const doc=root.ownerDocument,view=doc.defaultView,columns=[...root.querySelectorAll('[data-rdl-cgal-col]')],win=root.querySelector('.rdl-cgal__win');
 const refresh=createRefreshRequest(view,ScrollTrigger);let alive=true,effects=[],context;
 const clear=()=>{effects.forEach(effect=>effect.kill());effects=[];context?.revert();context=undefined;};
 const sync=()=>{
  clear();root.classList.add('is-enh');
  context=gsap.context(()=>{
   const smoother=getSmoothScroller(doc);
   if(smoother)effects=smoother.effects(columns,{speed:(_,node)=>Number(node.dataset.rdlCgalSpd),lag:0});
   else for(const column of columns){
    const reverse=Number(column.dataset.rdlCgalSpd)<0,travel=()=>Math.max(0,column.offsetHeight-win.clientHeight)*(reverse?.8:.25);
    gsap.fromTo(column,{y:()=>reverse?-travel():0},{y:()=>reverse?0:-travel(),ease:'none',scrollTrigger:{trigger:win,start:'top bottom',end:'bottom top',scrub:true,invalidateOnRefresh:true}});
   }
  },root);refresh.flush();
 };
 sync();doc.addEventListener('rdc:scr-mode',sync);
 const releaseSize=observeSizes(view,[win,...columns],()=>refresh.request());
 doc.fonts?.ready.then(()=>{if(alive)refresh.request();});
 return()=>{alive=false;doc.removeEventListener('rdc:scr-mode',sync);releaseSize();clear();root.classList.remove('is-enh');refresh.dispose();ScrollTrigger.refresh();};
}
for(const root of document.querySelectorAll('[data-rdl-cgal]'))mountMotion(root,'column-gallery',initializeColumns,{query:'(min-width:768px) and (prefers-reduced-motion:no-preference)'});
