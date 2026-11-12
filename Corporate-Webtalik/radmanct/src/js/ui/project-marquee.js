export function initializeProjectMarquee(root,{gsap,ScrollTrigger}){
 const media=gsap.matchMedia();
 media.add('(prefers-reduced-motion: no-preference)',()=>{
 gsap.registerPlugin(ScrollTrigger);
 const doc=root.ownerDocument,win=root.querySelector('.rdc-prj-mq__win'),track=root.querySelector('.rdc-prj-mq__trk'),group=track.firstElementChild;
 if(!group.children.length)return()=>{};
 const clone=group.cloneNode(true);clone.setAttribute('aria-hidden','true');clone.setAttribute('inert','');track.append(clone);win.classList.add('is-enh');
 let visible=false,focused=false,speed,targetScale=1;
 const tween=gsap.fromTo(track,{xPercent:0},{xPercent:-50,duration:70,ease:'none',repeat:-1,paused:true});
 // Preserve a wheel burst long enough to reach its requested speed.
 // Smoother tail updates must not cancel the acceleration on every frame.
 const drive=velocity=>{
  const next=gsap.utils.clamp(1,6,Math.abs(velocity/200));
  if(next===targetScale)return;
  targetScale=next;speed?.kill();speed=gsap.to(tween,{timeScale:next,duration:.5,ease:'power1.out',overwrite:true});
 };
 const idle=gsap.delayedCall(.5,()=>drive(0)).pause();
 const accelerate=self=>{const velocity=self.getVelocity();if(Math.abs(velocity)/200>targetScale)drive(velocity);idle.restart(true);};
 const sync=()=>tween.paused(!visible||doc.hidden||focused);
 const focus=()=>{focused=true;sync();},blur=()=>{focused=false;sync();};
 const trigger=ScrollTrigger.create({trigger:win,start:'top bottom',end:'bottom top',onToggle:self=>{visible=self.isActive;sync();if(visible)accelerate(self);else{idle.pause();speed?.kill();targetScale=1;tween.timeScale(1);}},onUpdate:self=>{
  if(!visible||doc.hidden||focused)return;
  accelerate(self);
 }});
 visible=trigger.isActive;sync();
 win.addEventListener('focusin',focus);win.addEventListener('focusout',blur);doc.addEventListener('visibilitychange',sync);
 return()=>{idle.kill();speed?.kill();trigger.kill();tween.revert();clone.remove();win.classList.remove('is-enh');win.removeEventListener('focusin',focus);win.removeEventListener('focusout',blur);doc.removeEventListener('visibilitychange',sync);};
 });
 return()=>media.revert();
}
