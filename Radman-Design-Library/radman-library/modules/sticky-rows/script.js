import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {observeSizes} from '../../shared/radmanct/src/js/runtime/observers.js';
import {createRefreshRequest} from '../../shared/radmanct/src/js/runtime/refresh.js';
import {getSmoothScroller} from '../../shared/radmanct/src/js/runtime/scroll-state.js';

function initializeRows(root,{gsap,ScrollTrigger}){
 const doc=root.ownerDocument,view=doc.defaultView;
 const list=root.querySelector('.rdl-stk-rows__list');
 const rows=[...list.children].slice(0,-1);
 const refresh=createRefreshRequest(view,ScrollTrigger);
 const top=(index)=>{
  const toolbar=doc.getElementById('wpadminbar');
  const adminOffset=toolbar&&view.getComputedStyle(toolbar).position==='fixed'?toolbar.offsetHeight:0;
  const step=parseFloat(view.getComputedStyle(root).getPropertyValue('--rdl-stk-step'));
  return adminOffset+28+index*step;
 };
 let context;
 gsap.registerPlugin(ScrollTrigger);
 const mount=()=>{
  context?.revert();
  context=gsap.context(()=>{
   const pinType=getSmoothScroller(doc)?'transform':undefined;
   rows.forEach((row,index)=>ScrollTrigger.create({
    trigger:row,
    start:()=>`top ${top(index)}`,
    endTrigger:list,
    end:()=>`bottom ${row.offsetHeight+top(index)}`,
    pin:true,
    pinType,
    pinSpacing:false,
    invalidateOnRefresh:true,
   }));
  },root);
  refresh.request();
 };
 mount();
 const unobserve=observeSizes(view,[list,...rows],()=>refresh.request());
 doc.addEventListener('rdc:scr-mode',mount);
 return()=>{
  doc.removeEventListener('rdc:scr-mode',mount);
  unobserve();
  context?.revert();
  refresh.flush();
  refresh.dispose();
 };
}

const query='(min-width:992px) and (min-height:680px) and (hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)';
for(const root of document.querySelectorAll('[data-rdl-stk-rows]')){
 mountMotion(root,'sticky-rows',initializeRows,{query});
}
