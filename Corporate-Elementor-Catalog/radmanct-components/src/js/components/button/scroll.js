import {loadGsap} from '../../animation.js';
export async function scrollToTarget(root,target){
  const doc=root.ownerDocument;const view=doc.defaultView;
  const speed=parseFloat(view.getComputedStyle(root).getPropertyValue('--rdct-button-scroll-speed'));
  const duration=view.matchMedia('(prefers-reduced-motion:reduce)').matches?0:Number.isFinite(speed)?speed:.8;
  const detail={target,duration};const event=new view.CustomEvent('rdct:scroll-to',{detail,cancelable:true});doc.dispatchEvent(event);
  if(event.defaultPrevented)return()=>detail.cancel?.();
  const [{gsap},{ScrollToPlugin}]=await Promise.all([loadGsap(),import('gsap/ScrollToPlugin')]);
  if(!root.isConnected || !target.isConnected)return()=>{};
  gsap.registerPlugin(ScrollToPlugin);const tween=gsap.to(view,{scrollTo:{y:target,offsetY:doc.getElementById('wpadminbar')?.getBoundingClientRect().height || 0,autoKill:true},duration,ease:'power2.inOut',overwrite:'auto'});
  return()=>tween.kill();
}
