import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
export function initializeMarquee(root,{gsap,ScrollTrigger}){
 gsap.registerPlugin(ScrollTrigger);
 const doc=root.ownerDocument,win=root.querySelector('.rdl-mq__win'),track=root.querySelector('.rdl-mq__track'),group=track.firstElementChild,button=root.querySelector('[data-rdl-mq-play]');
 if(!group.children.length)return()=>{};
 const clone=group.cloneNode(true);clone.setAttribute('aria-hidden','true');clone.setAttribute('inert','');track.append(clone);win.classList.add('is-enh');button.hidden=false;
 let paused=false,visible=false,focused=false,speed,targetScale=1;
 // The reference's 70s cycle contains 15 cards; preserve pixels/second for shorter collections.
 const duration=70*group.children.length/15;
 const tween=gsap.fromTo(track,{xPercent:0},{xPercent:-50,duration,ease:'none',repeat:-1,paused:true});
 // Preserve a wheel burst long enough to reach its requested speed.
 // Smoother tail updates must not cancel the acceleration on every frame.
 const drive=velocity=>{
  const next=gsap.utils.clamp(1,6,Math.abs(velocity/200));
  if(next===targetScale)return;
  targetScale=next;speed?.kill();speed=gsap.to(tween,{timeScale:next,duration:next===1?1.4:.5,ease:'power1.out',overwrite:true});
 };
 const idle=gsap.delayedCall(.7,()=>drive(0)).pause();
 const accelerate=self=>{const velocity=self.getVelocity();if(Math.abs(velocity)/200>targetScale)drive(velocity);idle.restart(true);};
 const sync=()=>{tween.paused(paused||!visible||doc.hidden||focused);button.setAttribute('aria-pressed',String(paused));button.textContent=paused?'ادامهٔ حرکت':'توقف حرکت';};
 const toggle=()=>{paused=!paused;sync();};
 const focus=()=>{focused=true;sync();},blur=()=>{focused=false;sync();};
 const trigger=ScrollTrigger.create({trigger:win,start:'top bottom',end:'bottom top',onToggle:self=>{visible=self.isActive;sync();if(visible)accelerate(self);else{idle.pause();speed?.kill();targetScale=1;tween.timeScale(1);}},onUpdate:self=>{
  if(paused||!visible||doc.hidden||focused)return;
  accelerate(self);
 }});
 visible=trigger.isActive;sync();
 button.addEventListener('click',toggle);win.addEventListener('focusin',focus);win.addEventListener('focusout',blur);doc.addEventListener('visibilitychange',sync);
 return()=>{idle.kill();speed?.kill();trigger.kill();tween.revert();clone.remove();win.classList.remove('is-enh');button.hidden=true;button.removeEventListener('click',toggle);win.removeEventListener('focusin',focus);win.removeEventListener('focusout',blur);doc.removeEventListener('visibilitychange',sync);};
}
for(const root of document.querySelectorAll('[data-rdl-mq]'))mountMotion(root,'marquee',initializeMarquee);
