import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
export function initializeMarquee(root,{gsap,ScrollTrigger}){
 gsap.registerPlugin(ScrollTrigger);
 const doc=root.ownerDocument,win=root.querySelector('.rdl-mq__win'),track=root.querySelector('.rdl-mq__track'),group=track.firstElementChild,button=root.querySelector('[data-rdl-mq-play]');
 if(!group.children.length)return()=>{};
 const clone=group.cloneNode(true);clone.setAttribute('aria-hidden','true');clone.setAttribute('inert','');track.append(clone);win.classList.add('is-enh');button.hidden=false;
 let paused=false,visible=false,focused=false,speed;
 const tween=gsap.fromTo(track,{xPercent:0},{xPercent:-50,duration:70,ease:'none',repeat:-1,paused:true});
 // Match the reference velocity curve; settle even when no final scroll update arrives.
 const drive=velocity=>{
  speed?.kill();speed=gsap.to(tween,{timeScale:gsap.utils.clamp(1,6,Math.abs(velocity/200)),duration:.5,ease:'power1.out',overwrite:true});
 };
 const idle=gsap.delayedCall(.2,()=>drive(0)).pause();
 const accelerate=self=>{drive(self.getVelocity());idle.restart(true);};
 const sync=()=>{tween.paused(paused||!visible||doc.hidden||focused);button.setAttribute('aria-pressed',String(paused));button.textContent=paused?'ادامهٔ حرکت':'توقف حرکت';};
 const toggle=()=>{paused=!paused;sync();};
 const focus=()=>{focused=true;sync();},blur=()=>{focused=false;sync();};
 const trigger=ScrollTrigger.create({trigger:win,start:'top bottom',end:'bottom top',onToggle:self=>{visible=self.isActive;sync();if(visible)accelerate(self);else{idle.pause();speed?.kill();tween.timeScale(1);}},onUpdate:self=>{
  if(paused||!visible||doc.hidden||focused)return;
  accelerate(self);
 }});
 visible=trigger.isActive;sync();
 button.addEventListener('click',toggle);win.addEventListener('focusin',focus);win.addEventListener('focusout',blur);doc.addEventListener('visibilitychange',sync);
 return()=>{idle.kill();speed?.kill();trigger.kill();tween.revert();clone.remove();win.classList.remove('is-enh');button.hidden=true;button.removeEventListener('click',toggle);win.removeEventListener('focusin',focus);win.removeEventListener('focusout',blur);doc.removeEventListener('visibilitychange',sync);};
}
for(const root of document.querySelectorAll('[data-rdl-mq]'))mountMotion(root,'marquee',initializeMarquee);
