export function initializeProjectMarquee(root,{gsap,ScrollTrigger}){
 const media=gsap.matchMedia();
 media.add('(prefers-reduced-motion: no-preference)',()=>{
 gsap.registerPlugin(ScrollTrigger);
 const doc=root.ownerDocument,win=root.querySelector('.rdc-prj-mq__win'),track=root.querySelector('.rdc-prj-mq__trk'),group=track.firstElementChild;
 if(!group.children.length)return()=>{};
 const clone=group.cloneNode(true);clone.setAttribute('aria-hidden','true');clone.setAttribute('inert','');track.append(clone);win.classList.add('is-enh');
 let visible=false,focused=false,speed;
 const duration=70*group.children.length/15;
 const tween=gsap.fromTo(track,{xPercent:0},{xPercent:-50,duration,ease:'none',repeat:-1,paused:true});
 // Reference response only: no held peak, idle timer or extended coast.
 const drive=velocity=>{
  speed=gsap.to(tween,{timeScale:gsap.utils.clamp(1,6,Math.abs(velocity/200)),duration:.5,ease:'power1.out',overwrite:'auto'});
 };
 const settle=()=>{if(visible&&!doc.hidden&&!focused)drive(0);};
 const sync=()=>tween.paused(!visible||doc.hidden||focused);
 const focus=()=>{focused=true;sync();},blur=()=>{focused=false;sync();};
 const trigger=ScrollTrigger.create({trigger:win,start:'top bottom',end:'bottom top',onToggle:self=>{visible=self.isActive;sync();},onUpdate:self=>{
  if(!visible||doc.hidden||focused)return;
  drive(self.getVelocity());
 }});
 visible=trigger.isActive;sync();
 ScrollTrigger.addEventListener('scrollEnd',settle);
 win.addEventListener('focusin',focus);win.addEventListener('focusout',blur);doc.addEventListener('visibilitychange',sync);
 return()=>{ScrollTrigger.removeEventListener('scrollEnd',settle);gsap.killTweensOf(tween);trigger.kill();tween.revert();clone.remove();win.classList.remove('is-enh');win.removeEventListener('focusin',focus);win.removeEventListener('focusout',blur);doc.removeEventListener('visibilitychange',sync);};
 });
 return()=>media.revert();
}
