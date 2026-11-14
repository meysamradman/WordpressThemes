import {createStickyScene,getStickyItems} from './scene.js';
import {initializeEntrance,addTextFill} from '../../ui/text-motion.js';

export function mount(root,{gsap,ScrollTrigger}) {
  const items=getStickyItems(root);
  if(items.length<2||items.some(item=>!item.querySelector(':scope > .rdc-ss__inr')))return()=>{};
  // Entrance controllers belong to the page, outside the refreshable pin context.
  const entrances=items.map((item,index)=>initializeEntrance(item,{gsap},{viewport:index===0,manual:index>0,targets:[
    item.querySelector('[data-rdc-pnl-ttl]'),
    item.querySelector('[data-rdc-pnl-copy]'),
    ...item.querySelectorAll('[data-rdc-pnl-en]'),
  ]}));
  let nextEntrance=1;
  const releaseScene=createStickyScene(root,gsap,ScrollTrigger,({inners,top,available})=>{
    if(items.some(item=>item.classList.contains('is-tall')))return false;
    const minimum=Math.max(.8,Math.min(.98,Number(root.dataset.minScale) || .9));
    gsap.set(inners,{minHeight:available});
    gsap.set(root,{height:available,overflow:'clip'});
    gsap.set(items,{position:'absolute',inset:0});
    gsap.set(items.slice(1),{yPercent:105});
    const releases=[];
    const firstRead=.35,transition=1,between=.2;
    const span=firstRead+(items.length-1)*transition+Math.max(0,items.length-2)*between;
    const starts=items.slice(1).map((_,index)=>firstRead+index*(transition+between));
    // Refresh can seek with timeline callbacks suppressed (restored scroll/resize).
    // Reconcile from scroll progress instead; each entrance is requested only once.
    const syncEntrances=self=>{
      const time=self.progress*span;
      while(nextEntrance<entrances.length&&time>=starts[nextEntrance-1]+.25){
        entrances[nextEntrance++].play();
      }
    };
    // ScrollSmoother already smooths the scroll. Keep this playhead directly linked.
    const timeline=gsap.timeline({scrollTrigger:{trigger:root,start:()=>`top ${top}`,end:()=>`+=${available*span}`,pin:true,scrub:true,refreshPriority:1,invalidateOnRefresh:true,onUpdate:syncEntrances,onRefresh:syncEntrances}});
    const content=(item,at)=>releases.push(addTextFill(timeline,item.querySelector('[data-rdc-pnl-copy]'),at,{duration:.15,stagger:.2}));
    content(items[0],0);
    items.slice(1).forEach((item,index)=>{
      const at=starts[index];
      const visual=item.querySelector('[data-rdc-pnl-vis]');
      const layers=[inners[index],item,visual].filter(Boolean);
      // Prepare only the moving surfaces, then release their compositing hints.
      timeline.set(layers,{willChange:'transform,opacity'},at-.15);
      timeline.set(layers,{willChange:'auto'},at+transition);
      // Explicit endpoints survive invalidation while the page is mid-scroll.
      timeline.fromTo(inners[index],{scale:1,autoAlpha:1},{scale:minimum,autoAlpha:.45,duration:transition,ease:'none',immediateRender:false},at);
      timeline.fromTo(item,{yPercent:105},{yPercent:0,duration:transition,ease:'none',immediateRender:false},at);
      timeline.set(items[index],{opacity:0,visibility:'hidden'},at+transition);
      if(visual)timeline.fromTo(visual,{yPercent:12,scale:1.06},{yPercent:0,scale:1,duration:.7,ease:'power2.out',immediateRender:false},at+.2);
      content(item,at+.65);
    });
    return()=>releases.forEach(release=>release());
  },()=>entrances.forEach(entrance=>entrance.finish()));
  return()=>{releaseScene();entrances.forEach(entrance=>entrance());};
}
