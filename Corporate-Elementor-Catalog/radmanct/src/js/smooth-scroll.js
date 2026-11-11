import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ScrollSmoother } from 'gsap/ScrollSmoother';
import { reportRuntimeError } from '../../../shared/frontend/lifecycle.js';

export function initializeSmoothScroll() {
  const wrapper = document.getElementById('rdct-scroll-wrapper');
  const content = document.getElementById('rdct-scroll-content');
  if (!wrapper || !content || ScrollSmoother.get()) return;
  gsap.registerPlugin(ScrollTrigger, ScrollSmoother);
  const media = gsap.matchMedia();
  media.add('(min-width:48rem) and (hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)', () => {
    let wasPaused=false;let locked=false;let unlockedGeometry;
    const duration=Number(wrapper.dataset.smoothDuration);
    const hasHeader=!!content.querySelector('header.rdct-bld--sticky-all, header.rdct-bld--sticky-desktop');
    const hasContrast=!!content.querySelector('[data-rdct-header-colors], [data-rdct-text-header]');
    const hasProgress=!!document.getElementById('rdct-scroll-progress');
    const notify=scrollTop=>{if(hasHeader || hasContrast || hasProgress)document.dispatchEvent(new CustomEvent('rdct:smooth-update',{detail:{scrollTop}}));};
    const offset=()=>document.getElementById('wpadminbar')?.getBoundingClientRect().height || 0;
    const smoother = ScrollSmoother.create({ wrapper, content, smooth: Number.isFinite(duration) && duration>=.1 && duration<=3 ? duration : .8, smoothTouch: 0, effects: false, wholePixels: true,
      onFocusIn:(_,event)=>!locked && !event.target.closest?.('dialog[open], .rdct-menu-toggle'),
      onUpdate:self=>notify(self.scrollTop()) });
    const bottomPadding=getComputedStyle(content).paddingBottom;
    const align=()=>{const top=offset();wrapper.style.top=`${top}px`;wrapper.style.height=`calc(100% - ${top}px)`;content.style.paddingBottom=`calc(${bottomPadding} + ${top}px)`;};
    align();ScrollTrigger.addEventListener('refreshInit',align);
    let alive=true;let unpin=()=>{};let unpinSidebars=()=>{};
    if(hasHeader) {
      import('./smooth-header.js').then(({pinSmoothHeaders})=>{if(alive)unpin=pinSmoothHeaders(content,ScrollTrigger,offset);}).catch(error=>{if(alive)reportRuntimeError('smooth-header',error);});
    }
    if(content.querySelector('[data-rdct-sticky-sidebar]')) {
      import('./smooth-sidebar.js').then(({pinSmoothSidebars})=>{if(alive)unpinSidebars=pinSmoothSidebars(content,gsap,ScrollTrigger);}).catch(error=>{if(alive)reportRuntimeError('smooth-sidebar',error);});
    }
    const lock=(event)=>{
      if(event.detail.locked && !locked){
        const bounds=wrapper.getBoundingClientRect();
        wasPaused=smoother.paused();unlockedGeometry={width:wrapper.style.width,left:wrapper.style.left,right:wrapper.style.right};locked=true;
        gsap.killTweensOf(smoother,'scrollTop');smoother.paused(true);
        wrapper.style.width=`${bounds.width}px`;wrapper.style.left=`${bounds.left}px`;wrapper.style.right='auto';
      }
      else if(!event.detail.locked && locked){locked=false;Object.assign(wrapper.style,unlockedGeometry);smoother.paused(wasPaused);}
    };
    const scrollTo=event=>{if(!event.detail?.target?.isConnected)return;event.preventDefault();const tween=gsap.to(smoother,{scrollTop:Math.max(0,smoother.offset(event.detail.target,'top top')-offset()),duration:Math.max(0,Math.min(5,Number(event.detail.duration) || 0)),ease:'power2.inOut',overwrite:'auto'});event.detail.cancel=()=>tween.kill();};
    document.addEventListener('rdct:scroll-to',scrollTo);
    document.addEventListener('rdct:panel-scroll-lock',lock);
    return () => {alive=false;document.removeEventListener('rdct:scroll-to',scrollTo);document.removeEventListener('rdct:panel-scroll-lock',lock);ScrollTrigger.removeEventListener('refreshInit',align);gsap.killTweensOf(smoother,'scrollTop');unpinSidebars();unpin();smoother.kill();notify(null);};
  });
  return () => media.revert();
}
