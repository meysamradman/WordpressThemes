import {loadAnimation} from '../motion.js';
import {reportRuntimeError} from './lifecycle.js';
import {setSmoothScroller} from './scroll-state.js';
import {observeSizes} from './observers.js';
export function mountScrollEffects(smoother,targets){
 if(!targets.length)return()=>{};
 const effects=smoother.effects(targets,{speed:(_,node)=>{
  const value=Number(node.getAttribute('data-rdc-scr-spd'));
  return Number.isFinite(value)&&value>0?Math.min(2,value):1;
 }});
 return()=>effects.forEach(effect=>effect.kill());
}
export function initializeScroll(view=window){
 const nativeTop=()=>view.scrollTo({top:0,behavior:view.matchMedia('(prefers-reduced-motion:reduce)').matches?'auto':'smooth'});
 const doc=view.document,wrapper=doc.getElementById('rdc-scr-wrp'),content=doc.getElementById('rdc-scr-cnt'),progress=doc.querySelector('.rdc-scr-prog');
 if(!wrapper||!content)return{destroy(){},refresh(){},toTop:nativeTop};
 const preference=view.matchMedia('(min-width:48rem) and (hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)');
 const hasArt=!!doc.querySelector('[data-rdc-ofr-art]');let alive=true,smoother,media,pending=false,range=0,measureFrame=0,paintFrame=0,last=-1,lastArt=-1,locked=false,geometry,overflow='',wasPaused=false;
 const paint=value=>{const top=Number.isFinite(value)?value:view.scrollY;const valueNow=range>0?Math.round(Math.min(1,Math.max(0,top/range))*10000)/10000:0;
  if(progress){const hidden=top<=1||range<=0;if(progress.hidden!==hidden)progress.hidden=hidden;if(!hidden&&last!==valueNow){last=valueNow;progress.style.transform=`scaleX(${valueNow})`;}}
  if(hasArt&&lastArt!==top){lastArt=top;doc.dispatchEvent(new view.CustomEvent('rdc:smth-upd',{detail:{scrollTop:top}}));}
 };
 const measure=()=>{measureFrame=0;if(!alive)return;range=Math.max(0,doc.documentElement.scrollHeight-view.innerHeight);paint(smoother?.scrollTop());};
 const queueMeasure=()=>{if(alive&&!measureFrame)measureFrame=view.requestAnimationFrame(measure);};
 const nativeScroll=()=>{if(smoother||paintFrame||!alive)return;paintFrame=view.requestAnimationFrame(()=>{paintFrame=0;paint();});};
 const alignFocus=event=>{
  const {target,top}=event.detail||{};
  if(locked||!target?.isConnected||doc.activeElement!==target||!Number.isFinite(top))return;
  const delta=target.getBoundingClientRect().top-top;
  if(Math.abs(delta)<1)return;
  // Preserve the keyboard target's viewport position when a pin is removed.
  // Let the active scroll owner settle immediately instead of easing a jump.
  if(smoother)smoother.scrollTop(smoother.scrollTop()+delta);
  else view.scrollTo({top:view.scrollY+delta,behavior:'instant'});
 };
 const lock=event=>{const next=!!event.detail?.locked;if(next===locked)return;locked=next;
  if(next){overflow=doc.documentElement.style.overflow;const box=wrapper.getBoundingClientRect();geometry={width:wrapper.style.width,left:wrapper.style.left,right:wrapper.style.right};wasPaused=smoother?.paused()??false;smoother?.paused(true);if(smoother){wrapper.style.width=`${box.width}px`;wrapper.style.left=`${box.left}px`;wrapper.style.right='auto';}doc.documentElement.style.overflow='hidden';}
  else{doc.documentElement.style.overflow=overflow;if(geometry)Object.assign(wrapper.style,geometry);smoother?.paused(wasPaused);queueMeasure();}
 };
 const start=async()=>{if(!alive||!preference.matches||media||pending)return;pending=true;
  try{const [{gsap,ScrollTrigger},{ScrollSmoother}]=await Promise.all([loadAnimation(),import('../smoother-vendor.js')]);if(!alive)return;gsap.registerPlugin(ScrollTrigger,ScrollSmoother);media=gsap.matchMedia();
   media.add('(min-width:48rem) and (hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)',()=>{
    const original=wrapper.getAttribute('style'),padding=content.style.paddingBottom,basePadding=view.getComputedStyle(content).paddingBottom;
    smoother=ScrollSmoother.create({wrapper,content,smooth:1.6,smoothTouch:0,effects:false,wholePixels:true,onFocusIn:(_,event)=>!locked&&!event.target.closest?.('dialog[open],.rdc-menu-tgl'),onUpdate:self=>paint(self.scrollTop())});
    setSmoothScroller(doc,smoother);
    const effectTargets=[...content.querySelectorAll('[data-rdc-scr-spd]')];
    const effectMedia=effectTargets.length?gsap.matchMedia():null;
    effectMedia?.add('(min-width:64rem)',()=>mountScrollEffects(smoother,effectTargets));
    const align=()=>{const bar=doc.getElementById('wpadminbar');const top=bar&&view.getComputedStyle(bar).position==='fixed'?bar.getBoundingClientRect().height:0;wrapper.style.top=`${top}px`;wrapper.style.height=`calc(100% - ${top}px)`;content.style.paddingBottom=`calc(${basePadding} + ${top}px)`;};
    align();if(locked)smoother.paused(true);ScrollTrigger.addEventListener('refreshInit',align);ScrollTrigger.addEventListener('refresh',queueMeasure);ScrollTrigger.refresh();queueMeasure();
    return()=>{effectMedia?.revert();ScrollTrigger.removeEventListener('refreshInit',align);ScrollTrigger.removeEventListener('refresh',queueMeasure);smoother?.kill();smoother=null;content.style.paddingBottom=padding;original===null?wrapper.removeAttribute('style'):wrapper.setAttribute('style',original);setSmoothScroller(doc,null);lastArt=-1;queueMeasure();};
   });
  }catch(error){if(alive)reportRuntimeError('smooth-scroll',error,view);}finally{pending=false;}
 };
 const releaseSize=observeSizes(view,[content],queueMeasure);doc.fonts?.ready.then(queueMeasure).catch(()=>{});
 doc.addEventListener('rdc:fcs-aln',alignFocus);doc.addEventListener('rdc:pnl-lock',lock);doc.addEventListener('rdc:arc-upd',queueMeasure);view.addEventListener('scroll',nativeScroll,{passive:true});view.addEventListener('resize',queueMeasure);preference.addEventListener('change',start);measure();start();
 return{toTop(){if(smoother)smoother.scrollTo(0,true);else nativeTop();},refresh(){smoother?.refresh();queueMeasure();},destroy(){if(!alive)return;alive=false;preference.removeEventListener('change',start);doc.removeEventListener('rdc:fcs-aln',alignFocus);doc.removeEventListener('rdc:pnl-lock',lock);doc.removeEventListener('rdc:arc-upd',queueMeasure);view.removeEventListener('scroll',nativeScroll);view.removeEventListener('resize',queueMeasure);releaseSize();media?.revert();if(measureFrame)view.cancelAnimationFrame(measureFrame);if(paintFrame)view.cancelAnimationFrame(paintFrame);if(locked){doc.documentElement.style.overflow=overflow;locked=false;}if(progress)progress.hidden=true;}};
}
