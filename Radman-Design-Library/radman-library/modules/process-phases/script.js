import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
for(const root of document.querySelectorAll('[data-rdl-phs]'))mountMotion(root,'phases',(node,{gsap,ScrollTrigger})=>{
 gsap.registerPlugin(ScrollTrigger);const doc=node.ownerDocument,button=node.querySelector('[data-rdl-phs-pause]');let paused=false,visible=false;
 const sync=()=>{node.classList.toggle('is-play',visible&&!paused&&!doc.hidden);button.setAttribute('aria-pressed',String(paused));button.textContent=paused?'ادامهٔ حرکت نشان':'توقف حرکت نشان';};
 const context=gsap.context(()=>{
  [...node.querySelectorAll('.rdl-phs__itm')].forEach((item,i)=>gsap.fromTo(item,{x:getComputedStyle(node).direction==='rtl'?24:-24,opacity:0},{x:0,opacity:1,duration:.75,delay:.1+i*.15,ease:'power2.out',scrollTrigger:{trigger:item,start:'top 85%',once:true}}));
  ScrollTrigger.create({trigger:node,start:'top bottom',end:'bottom top',onToggle:self=>{visible=self.isActive;sync();},onRefresh:self=>{visible=self.isActive;sync();}});
 },node);
 const toggle=()=>{paused=!paused;sync();};node.classList.add('is-enh');button.hidden=false;button.addEventListener('click',toggle);doc.addEventListener('visibilitychange',sync);sync();
 return()=>{context.revert();node.classList.remove('is-enh','is-play');button.hidden=true;button.removeEventListener('click',toggle);doc.removeEventListener('visibilitychange',sync);};
});
