import '../../src/js/scroll.js';
import {loadGsap} from '../../shared/radmanct/src/js/motion.js';
import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';

/** One instance owns its gesture, reveal timeline and DOM state; no slider engine. */
export function initializeHeroSlider(root,{gsap}){
 const doc=root.ownerDocument,view=doc.defaultView,win=root.querySelector('.rdl-hsl__win');
 const slides=[...root.querySelectorAll('[data-rdl-hsl-itm]')],nav=root.querySelector('[data-rdl-hsl-nav]');
 const number=root.querySelector('[data-rdl-hsl-num]'),status=root.querySelector('[data-rdl-hsl-status]');
 if(slides.length<2)return()=>{};
 const events=new AbortController(),motion=view.matchMedia('(prefers-reduced-motion:reduce)');
 const warmed=new WeakSet();
 const saved=new Map();
 const remember=node=>{if(!saved.has(node))saved.set(node,{style:node.getAttribute('style'),hidden:node.hidden,inert:node.inert,aria:node.getAttribute('aria-hidden')});return node;};
 const parts=slides.map(slide=>({slide:remember(slide),media:remember(slide.querySelector('[data-rdl-hsl-md]')),image:slide.querySelector('img'),text:[...slide.querySelectorAll('[data-rdl-hsl-tx]')].map(remember)}));
 parts.forEach(part=>{if(part.image)remember(part.image);});
 let index=0,pending=-1,direction=1,timeline,context,settle,gesture,suppressClick=false,suppressTimer,alive=true;
 const warmNext=()=>{
  const image=parts[index].image,next=parts[(index+1)%parts.length].image;
  if(!next)return;
  if(!image||image.complete){next.loading='eager';return;}
  if(warmed.has(image))return;
  warmed.add(image);image.addEventListener('load',()=>{if(alive)next.loading='eager';},{once:true,signal:events.signal});
 };
 const isRtl=view.getComputedStyle(root).direction==='rtl',rtl=()=>isRtl;
 const restoreStyle=node=>{const value=saved.get(node).style;if(value===null)node.removeAttribute('style');else node.setAttribute('style',value);};
 const normalize=()=>{
  for(const [i,part] of parts.entries()){
   [part.slide,part.media,part.image,...part.text].filter(Boolean).forEach(restoreStyle);
   part.slide.hidden=i!==index;part.slide.inert=i!==index;part.slide.setAttribute('aria-hidden',String(i!==index));
  }
  number.textContent=String(index+1).padStart(2,'0');
  root.removeAttribute('data-rdl-hsl-drag');
 };
 // Revert through GSAP so its transform cache and the DOM return together.
 // Removing inline styles alone leaves the outgoing x/scale cached for reuse.
 const kill=()=>{settle?.kill();context?.revert();timeline?.kill();settle=timeline=context=undefined;pending=-1;};
 const announce=()=>{status.textContent=`اسلاید ${index+1} از ${slides.length}: ${slides[index].querySelector('h2').textContent}`;};
 const complete=commit=>{
  if(commit&&pending>=0){
   if(slides[index].contains(doc.activeElement))root.focus({preventScroll:true});
   index=pending;
  }
  kill();normalize();if(commit){announce();warmNext();}
 };
 const prepare=step=>{
  kill();normalize();direction=step;pending=(index+step+slides.length)%slides.length;
  const previous=parts[index],next=parts[pending],sign=(rtl()?-1:1)*step;
  // Preload only the neighbour being revealed; the first slide is eager in PHP.
  if(next.image)next.image.loading='eager';
  if(motion.matches)return;
  next.slide.hidden=false;next.slide.style.zIndex='2';
  context=gsap.context(()=>{
   gsap.set(next.media,{xPercent:sign*100});
   if(next.image)gsap.set(next.image,{xPercent:-sign*100,scale:1.08});
   gsap.set(next.text,{autoAlpha:0,x:0,y:32});
   timeline=gsap.timeline({paused:true,defaults:{ease:'none'}})
    .to(next.media,{xPercent:0,duration:1},0)
    .to(previous.text,{autoAlpha:0,x:-sign*24,duration:.32,stagger:.025},0);
   if(next.image)timeline.to(next.image,{xPercent:0,scale:1,duration:1},0);
   if(previous.image)timeline.to(previous.image,{xPercent:-sign*3,scale:1.08,duration:1},0);
   timeline.to(next.text,{autoAlpha:1,y:0,duration:.55,stagger:.075},.26);
  },root);
 };
 const finish=commit=>{
  if(pending<0)return;
  if(motion.matches||!timeline){complete(commit);return;}
  const progress=timeline.progress(),target=commit?1:0;
  if(Math.abs(target-progress)<.001){complete(commit);return;}
  settle=timeline.tweenTo(target*timeline.duration(),{duration:commit?Math.max(.25,.95*(1-progress)):Math.max(.18,.4*progress),ease:commit?'power3.inOut':'power2.out',onComplete:()=>complete(commit)});
 };
 const navigate=step=>{
  if(gesture)return;
  // Rapid arrows finish the accepted transition before starting another one.
  if(pending>=0)complete(true);
  prepare(step);finish(true);
 };
 const releaseCapture=()=>{if(gesture&&win.hasPointerCapture?.(gesture.id))win.releasePointerCapture(gesture.id);};
 const cancel=()=>{releaseCapture();gesture=undefined;if(pending>=0)complete(false);root.removeAttribute('data-rdl-hsl-drag');};
 const down=event=>{
  if(!event.isPrimary||event.button!==0||gesture||settle||doc.hidden||event.target.closest('a,button,input,textarea,select'))return;
  gesture={id:event.pointerId,x:event.clientX,y:event.clientY,lastX:event.clientX,lastTime:event.timeStamp,velocity:0,width:win.clientWidth,dragging:false,dx:0};
 };
 const move=event=>{
  if(!gesture||event.pointerId!==gesture.id)return;
  const dx=event.clientX-gesture.x,dy=event.clientY-gesture.y;
  if(!gesture.dragging){
   if(Math.abs(dy)>Math.abs(dx)&&Math.abs(dy)>8){gesture=undefined;return;}
   if(Math.abs(dx)<8)return;
   gesture.dragging=true;win.setPointerCapture(event.pointerId);root.setAttribute('data-rdl-hsl-drag','');
  }
  if(event.cancelable)event.preventDefault();
  const step=(dx>0?1:-1)*(rtl()?1:-1);
  if(pending<0||step!==direction){prepare(step);root.setAttribute('data-rdl-hsl-drag','');}
  const elapsed=event.timeStamp-gesture.lastTime;
  if(elapsed>0)gesture.velocity=(event.clientX-gesture.lastX)/elapsed;
  gesture.lastX=event.clientX;gesture.lastTime=event.timeStamp;gesture.dx=dx;
  timeline?.progress(Math.min(.88,Math.abs(dx)/Math.max(1,gesture.width*.65)));
 };
 const up=event=>{
  if(!gesture||event.pointerId!==gesture.id)return;
  const current=gesture;
  releaseCapture();gesture=undefined;root.removeAttribute('data-rdl-hsl-drag');
  if(!current.dragging)return;
  suppressClick=true;view.clearTimeout(suppressTimer);suppressTimer=view.setTimeout(()=>{suppressClick=false;},350);
  const distance=Math.abs(current.dx),fast=event.timeStamp-current.lastTime<90&&Math.abs(current.velocity)>.5&&Math.sign(current.velocity)===Math.sign(current.dx);
  finish(distance>Math.min(110,current.width*.18)||(distance>28&&fast));
 };
 const on=(target,name,callback,options={})=>target.addEventListener(name,callback,{...options,signal:events.signal});
 on(root.querySelector('[data-rdl-hsl-prev]'),'click',()=>navigate(-1));
 on(root.querySelector('[data-rdl-hsl-next]'),'click',()=>navigate(1));
 on(root,'keydown',event=>{
  if(event.altKey||event.ctrlKey||event.metaKey||event.target.closest('input,textarea,select'))return;
  if(event.key==='Escape'&&gesture){event.preventDefault();cancel();return;}
  if(event.key!=='ArrowLeft'&&event.key!=='ArrowRight')return;
  event.preventDefault();navigate((event.key==='ArrowRight'?1:-1)*(rtl()?-1:1));
 });
 on(win,'pointerdown',down);on(win,'pointermove',move,{passive:false});on(win,'pointerup',up);
 on(win,'pointercancel',cancel);on(win,'lostpointercapture',()=>{if(gesture)cancel();});
 on(win,'dragstart',event=>event.preventDefault());
 on(win,'click',event=>{if(suppressClick){event.preventDefault();event.stopPropagation();suppressClick=false;}},{capture:true});
 on(view,'blur',cancel);on(view,'resize',cancel);
 on(doc,'visibilitychange',()=>{if(doc.hidden){if(settle)complete(true);else cancel();}});
 on(motion,'change',()=>{cancel();normalize();});
 root.setAttribute('data-rdl-hsl-on','');nav.hidden=false;normalize();warmNext();
 return()=>{
  if(!alive)return;alive=false;events.abort();releaseCapture();gesture=undefined;kill();view.clearTimeout(suppressTimer);
  for(const [node,attributes]of saved){restoreStyle(node);node.hidden=attributes.hidden;node.inert=attributes.inert;if(attributes.aria===null)node.removeAttribute('aria-hidden');else node.setAttribute('aria-hidden',attributes.aria);}
  nav.hidden=true;status.textContent='';number.textContent='01';root.removeAttribute('data-rdl-hsl-on');root.removeAttribute('data-rdl-hsl-drag');
 };
}

const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdl-hsl]'))page.mountRoot(root,'hero-slider',loadGsap,vendor=>initializeHeroSlider(root,vendor));
