export function initializeProjectMarquee(root,{gsap,ScrollTrigger}){
 const media=gsap.matchMedia();
 media.add('(prefers-reduced-motion: no-preference)',()=>{
 gsap.registerPlugin(ScrollTrigger);
 const doc=root.ownerDocument,win=root.querySelector('.rdc-prj-mq__win'),track=root.querySelector('.rdc-prj-mq__trk'),group=track.firstElementChild;
 if(!group.children.length)return()=>{};
 const clone=group.cloneNode(true);clone.setAttribute('aria-hidden','true');clone.setAttribute('inert','');track.append(clone);win.classList.add('is-enh');
 let visible=false,focused=false,speed;
 const tween=gsap.fromTo(track,{xPercent:0},{xPercent:-50,duration:70,ease:'none',repeat:-1,paused:true});
 // Match the reference velocity curve; settle even when no final scroll update arrives.
 const drive=velocity=>{
  root.dataset.rdcMqVelocity=String(velocity);root.dataset.rdcMqPeak=String(Math.max(Number(root.dataset.rdcMqPeak)||0,Math.abs(velocity)));
  speed?.kill();speed=gsap.to(tween,{timeScale:gsap.utils.clamp(1,6,Math.abs(velocity/200)),duration:.5,ease:'power1.out',overwrite:true});
 };
 const idle=gsap.delayedCall(.2,()=>drive(0)).pause();
 const accelerate=self=>{drive(self.getVelocity());idle.restart(true);};
 const sync=()=>tween.paused(!visible||doc.hidden||focused);
 const focus=()=>{focused=true;sync();},blur=()=>{focused=false;sync();};
 const trigger=ScrollTrigger.create({trigger:win,start:'top bottom',end:'bottom top',onToggle:self=>{visible=self.isActive;sync();if(visible)accelerate(self);else{idle.pause();speed?.kill();tween.timeScale(1);}},onUpdate:self=>{
  if(!visible||doc.hidden||focused)return;
  accelerate(self);
 }});
 visible=trigger.isActive;sync();
 win.addEventListener('focusin',focus);win.addEventListener('focusout',blur);doc.addEventListener('visibilitychange',sync);
 return()=>{idle.kill();speed?.kill();trigger.kill();tween.revert();clone.remove();win.classList.remove('is-enh');win.removeEventListener('focusin',focus);win.removeEventListener('focusout',blur);doc.removeEventListener('visibilitychange',sync);};
 });
 return()=>media.revert();
}
