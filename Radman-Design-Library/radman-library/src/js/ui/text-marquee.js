import {mountMotion} from '../motion-view.js';
import {createRefreshRequest} from '../../../shared/radmanct/src/js/runtime/refresh.js';
export function mountTextMarquee(root){return mountMotion(root,'text-marquee-loop',(node,{gsap,ScrollTrigger})=>{
 gsap.registerPlugin(ScrollTrigger);const doc=node.ownerDocument,view=doc.defaultView,win=node.querySelector('.rdl-mtx__win'),track=node.querySelector('.rdl-mtx__track'),group=track?.firstElementChild,button=node.querySelector('[data-rdl-mtx-pause]');if(!group||!button)return()=>{};
 const refresh=createRefreshRequest(view,ScrollTrigger),clone=group.cloneNode(true);clone.setAttribute('aria-hidden','true');clone.setAttribute('inert','');track.append(clone);node.classList.add('is-enh');let paused=false,visible=false,focused=false,alive=true,loop;
 const sync=()=>{loop?.paused(paused||!visible||doc.hidden||focused);button.setAttribute('aria-pressed',String(paused));button.textContent=paused?'ادامهٔ حرکت':'توقف حرکت';};
 const context=gsap.context(()=>{
  loop=gsap.fromTo(track,{xPercent:0},{xPercent:-50,duration:20,ease:'none',repeat:-1,paused:true});loop.totalTime(20*100);loop.eventCallback('onReverseComplete',()=>loop.totalTime(20*100));
  gsap.fromTo([group,clone],{y:300,autoAlpha:0},{y:0,autoAlpha:1,duration:1.2,ease:'power3.out',scrollTrigger:{trigger:win,start:'top bottom',toggleActions:'play none none reverse'}});
  ScrollTrigger.create({trigger:win,start:'top bottom',end:'bottom top',onToggle:self=>{visible=self.isActive;sync();},onRefresh:self=>{visible=self.isActive;sync();},onUpdate:self=>loop.timeScale(self.direction===1?-1:1)});
 },node);
 const toggle=()=>{paused=!paused;sync();},focus=()=>{focused=true;sync();},blur=event=>{if(!node.contains(event.relatedTarget)){focused=false;sync();}};
 button.hidden=false;button.addEventListener('click',toggle);node.addEventListener('focusin',focus);node.addEventListener('focusout',blur);doc.addEventListener('visibilitychange',sync);sync();refresh.flush();doc.fonts?.ready.then(()=>{if(alive)refresh.request();});
 return()=>{alive=false;context.revert();clone.remove();node.classList.remove('is-enh');button.hidden=true;button.removeEventListener('click',toggle);node.removeEventListener('focusin',focus);node.removeEventListener('focusout',blur);doc.removeEventListener('visibilitychange',sync);refresh.dispose();};
});}
