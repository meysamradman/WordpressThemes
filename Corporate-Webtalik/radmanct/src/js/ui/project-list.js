import {loadAnimation,loadGsap} from '../motion.js';
import {reportRuntimeError} from '../runtime/lifecycle.js';

export function initializeProjectList(root) {
 const doc=root.ownerDocument;
 const view=doc.defaultView;
 const preference=view.matchMedia('(min-width:768px) and (hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)');
 let alive=true,loading=false,failed=false,release;

 async function mount(){
  if(!alive||loading||failed||release||!preference.matches)return;
  loading=true;
  try {
   const {gsap}=await loadGsap();
   if(alive&&root.isConnected&&preference.matches)release=attachPreview(root,gsap);
  } catch(error) {
   failed=true;
   if(alive)reportRuntimeError('project-list-preview',error,view);
  } finally { loading=false; }
 }

 function sync(){
  if(preference.matches)void mount();
  else { release?.(); release=undefined; }
 }

 root.addEventListener('pointerenter',mount);
 preference.addEventListener('change',sync);
 const releaseScroll=mountRowScroll(root);
 return()=>{
  alive=false;
  release?.();
  releaseScroll();
  preference.removeEventListener('change',sync);
  root.removeEventListener('pointerenter',mount);
 };
}

function mountRowScroll(root) {
 const view=root.ownerDocument.defaultView;
 const rows=[...root.querySelectorAll('.rdc-prj-list__rows > li')];
 const query='(min-width:992px) and (prefers-reduced-motion:no-preference)';
 const preference=view.matchMedia(query);
 let near=!view.IntersectionObserver,alive=true,loading=false,failed=false,media;

 async function mount(){
  if(!alive||!near||!preference.matches||loading||failed||media)return;
  loading=true;
  try {
   const {gsap,ScrollTrigger}=await loadAnimation();
   if(!alive||!root.isConnected||!preference.matches)return;
   gsap.registerPlugin(ScrollTrigger);
   media=gsap.matchMedia();
   media.add(query,()=>{
    for(const row of rows){
     gsap.to(row,{
      y:-72,duration:1.5,
      scrollTrigger:{trigger:row,start:'top 70%',scrub:1},
     });
    }
   },root);
   observer?.disconnect();
  } catch(error) {
   failed=true;
   if(alive)reportRuntimeError('project-list-scroll',error,view);
  } finally { loading=false; }
 }

 const observer=view.IntersectionObserver?new view.IntersectionObserver(([entry])=>{
  near=entry.isIntersecting;
  if(near)void mount();
 },{rootMargin:'360px 0px'}):null;
 observer?.observe(root);
 preference.addEventListener('change',mount);
 void mount();
 return()=>{
  alive=false;
  observer?.disconnect();
  preference.removeEventListener('change',mount);
  media?.revert();
 };
}

function attachPreview(root,gsap) {
 const doc=root.ownerDocument;
 const view=doc.defaultView;
 const image=doc.createElement('img');
 image.className='rdc-prj-list__preview';
 image.alt='';
 image.setAttribute('aria-hidden','true');
 doc.body.append(image);
 const moveX=gsap.quickTo(image,'x',{duration:.35,ease:'power3.out'});
 const moveY=gsap.quickTo(image,'y',{duration:.35,ease:'power3.out'});
 const hoverTween=gsap.fromTo(image,{autoAlpha:0,scale:.88,rotation:-8},{autoAlpha:1,scale:1,rotation:-14,duration:.28,ease:'power2.out',paused:true});
 let activeRow,rendered=false;

 function hide(){
  activeRow=undefined;
  rendered=false;
  hoverTween.reverse();
 }

 function move(event){
  const row=event.target.closest('[data-rdc-prj-preview]');
  if(event.pointerType!=='mouse'||!row||!root.contains(row)){
   if(activeRow)hide();
   return;
  }
  const width=Math.min(272,view.innerWidth*.25), height=width*2/3;
  const x=Math.max(16,Math.min(view.innerWidth-width-16,event.clientX-width/2));
  const y=Math.max(16,Math.min(view.innerHeight-height-16,event.clientY-height+30));
  if(!rendered)gsap.set(image,{x,y});
  else { moveX(x); moveY(y); }
  if(activeRow===row)return;
  activeRow=row;
  image.src=row.dataset.rdcPrjPreview;
  image.decode().then(()=>{
   if(activeRow!==row)return;
   rendered=true;
   hoverTween.play();
  },()=>{if(activeRow===row)hide();});
 }

 root.addEventListener('pointermove',move,{passive:true});
 root.addEventListener('pointerleave',hide);
 view.addEventListener('scroll',hide,{passive:true});
 return()=>{
  activeRow=undefined;
  root.removeEventListener('pointermove',move);
  root.removeEventListener('pointerleave',hide);
  view.removeEventListener('scroll',hide);
  hoverTween.kill();moveX.tween.kill();moveY.tween.kill();
  image.remove();
 };
}
