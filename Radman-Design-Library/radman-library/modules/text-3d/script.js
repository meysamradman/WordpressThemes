import '../../src/js/scroll.js';
import {mountMotion} from '../../src/js/motion-view.js';
import {loadGsap} from '../../shared/radmanct/src/js/motion.js';

for(const root of document.querySelectorAll('[data-rdl-t3]'))mountMotion(root,'text-3d',(node,{gsap})=>{
 const doc=node.ownerDocument,view=doc.defaultView,rows=[...node.querySelectorAll('.rdl-t3__row')],fine=view.matchMedia('(hover:hover) and (pointer:fine)'),events=new AbortController();
 let alive=true,observer,context;const frames=new Set(),moves=new Map(),visible=new Set();
 const reset=(immediate=false)=>{for(const functions of moves.values())for(const to of functions){to(0);if(immediate)to.tween.progress(1).pause();}};
 const start=()=>{
  if(!alive||context)return;
  context=gsap.context(()=>{
   for(const row of rows){const target=row.querySelector('.rdl-t3__ptr');moves.set(row,['x','y','rotationY','rotationX'].map(key=>gsap.quickTo(target,key,{duration:.7,ease:'power3.out'})));}
  },node);
  if(!('IntersectionObserver' in view))return;
  node.dataset.rdlT3On='1';
  observer=new IntersectionObserver(entries=>{
   for(const entry of entries){
    if(entry.isIntersecting){visible.add(entry.target);const frame=view.requestAnimationFrame(()=>{frames.delete(frame);if(alive&&visible.has(entry.target))entry.target.dataset.rdlT3Play='1';});frames.add(frame);}
    else{visible.delete(entry.target);entry.target.removeAttribute('data-rdl-t3-play');const functions=moves.get(entry.target);for(const to of functions||[]){to(0);to.tween.progress(1).pause();}}
   }
  },{threshold:.2,rootMargin:'0px 0px -8% 0px'});
  rows.forEach(row=>observer.observe(row));
 };
 doc.fonts?.ready?doc.fonts.ready.then(start).catch(start):start();
 const move=event=>{
  if(!fine.matches||node.dataset.rdlT3Ptr!=='1'||event.pointerType==='touch'||doc.hidden)return;
  const x=gsap.utils.clamp(-1,1,event.clientX/view.innerWidth*2-1),y=gsap.utils.clamp(-1,1,event.clientY/view.innerHeight*2-1);
  rows.forEach((row,index)=>{if(!row.hasAttribute('data-rdl-t3-play'))return;const functions=moves.get(row);if(functions){functions[0](x*(index===1?20:12));functions[1](y*(index===1?10:6));functions[2](x*1.5);functions[3](-y*1.5);}});
 };
 const options={passive:true,signal:events.signal};
 node.addEventListener('pointermove',move,options);node.addEventListener('pointerleave',()=>reset(),options);
 view.addEventListener('blur',()=>reset(true),options);doc.addEventListener('visibilitychange',()=>{if(doc.hidden)reset(true);},options);
 fine.addEventListener('change',()=>reset(true),{signal:events.signal});
 return()=>{alive=false;events.abort();observer?.disconnect();frames.forEach(frame=>view.cancelAnimationFrame(frame));context?.revert();node.removeAttribute('data-rdl-t3-on');rows.forEach(row=>row.removeAttribute('data-rdl-t3-play'));};
},{load:loadGsap});
