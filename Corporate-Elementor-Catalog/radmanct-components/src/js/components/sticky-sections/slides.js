import {createStickyScene} from './scene.js';

export function mount(root,{gsap,ScrollTrigger}) {
  return createStickyScene(root,gsap,ScrollTrigger,({items,inners,top,available})=>{
    if(items.some(item=>item.classList.contains('is-tall')))return false;
    const direction=getComputedStyle(root).direction==='rtl'?-1:1;
    const minimum=Math.max(.8,Math.min(.98,Number(root.dataset.minScale) || .9));
    gsap.set(inners,{minHeight:available});
    gsap.set(root,{height:available,overflow:'clip'});
    gsap.set(items,{position:'absolute',inset:0});
    gsap.set(items.slice(1),{yPercent:105});
    const timeline=gsap.timeline({scrollTrigger:{trigger:root,start:()=>`top ${top}`,end:()=>`+=${available * (items.length - 1) * 1.35}`,pin:true,scrub:.45,invalidateOnRefresh:true}});
    timeline.to({}, {duration:.35});
    items.slice(1).forEach((item,index)=>{
      const at=timeline.duration();
      timeline.to(inners[index],{scale:minimum,autoAlpha:.45,duration:1,ease:'power1.inOut'},at);
      timeline.to(item,{yPercent:0,duration:1,ease:'power2.inOut'},at);
      const title=item.querySelector('[data-rdct-panel-title]');
      const visual=item.querySelector('[data-rdct-panel-visual]');
      const copy=item.querySelector('[data-rdct-panel-copy]');
      if(title)timeline.from(title,{xPercent:direction*7,y:34,scale:.95,autoAlpha:.3,duration:.8,ease:'power2.out'},at+.2);
      if(visual)timeline.from(visual,{yPercent:12,scale:1.06,clipPath:'inset(12% 0 0 0 round 20px)',duration:.9,ease:'power2.out'},at+.15);
      if(copy)timeline.from(copy,{y:24,autoAlpha:.25,duration:.6,ease:'power1.out'},at+.4);
      timeline.to({}, {duration:.4});
    });
  });
}
