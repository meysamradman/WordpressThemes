import {observeSizes} from '../runtime/observers.js';

export function initializeCarousel(root){
 const doc=root.ownerDocument,view=doc.defaultView,win=root.querySelector('[data-rdc-rail-track]');
 if(!win)return()=>{};
 const slides=[...win.children],prev=root.querySelectorAll('[data-rdc-rail-prev]'),next=root.querySelectorAll('[data-rdc-rail-next]'),nav=root.querySelectorAll('[data-rdc-rail-nav]'),status=root.querySelector('[data-rdc-rail-status]');
 if(!slides.length||!prev.length||!next.length||!status)return()=>{};
 const reduced=view.matchMedia('(prefers-reduced-motion:reduce)');
 let sign=1,max=0,pages=[0],offsets=[],current=0,raf=0,timer=0,drag=null,motion=null,suppressClick=false;
 const position=()=>Math.max(0,Math.min(max,sign*win.scrollLeft));
 const pageAt=value=>pages.reduce((best,offset,index)=>Math.abs(offset-value)<Math.abs(pages[best]-value)?index:best,0);
 const indicate=()=>{
  raf=0;const value=position();current=pageAt(value);
  for(const button of prev)button.disabled=value<2;
  for(const button of next)button.disabled=value>max-2;
 };
 const announce=()=>{timer=0;indicate();status.textContent=`صفحهٔ ${current+1} از ${pages.length}`;};
 const scroll=()=>{if(!raf)raf=view.requestAnimationFrame(indicate);view.clearTimeout(timer);timer=view.setTimeout(announce,150);};
 const cancelMotion=()=>{if(motion)view.cancelAnimationFrame(motion.raf);motion=null;win.classList.remove('is-moving');};
 const glide=target=>{
  const from=position();target=Math.max(0,Math.min(max,target));cancelMotion();
  if(reduced.matches||Math.abs(target-from)<1){win.scrollLeft=sign*target;return;}
  const state={target,raf:0,start:null},duration=Math.min(650,Math.max(480,Math.abs(target-from)*.5));
  motion=state;win.classList.add('is-moving');
  const frame=time=>{
   if(motion!==state)return;
   state.start??=time;const progress=Math.min(1,(time-state.start)/duration),eased=progress<.5?4*progress**3:1-(-2*progress+2)**3/2;
   win.scrollLeft=sign*(from+(target-from)*eased);
   if(progress<1)state.raf=view.requestAnimationFrame(frame);
   else{cancelMotion();indicate();}
  };
  state.raf=view.requestAnimationFrame(frame);
 };
 const go=index=>glide(pages[Math.max(0,Math.min(pages.length-1,index))]);
 const step=delta=>{
  const value=motion?.target??position();let index=delta>0?pages.findIndex(offset=>offset>value+2):pages.findLastIndex(offset=>offset<value-2);
  if(index<0)index=delta>0?pages.length-1:0;go(index);
 };
 const back=()=>step(-1),forward=()=>step(1);
 const measure=()=>{
  const nextSign=view.getComputedStyle(win).direction==='rtl'?-1:1,nextMax=Math.max(0,win.scrollWidth-win.clientWidth);
  const nextOffsets=slides.map(slide=>Math.abs(slide.offsetLeft-slides[0].offsetLeft));
  if(nextSign!==sign||nextMax!==max||nextOffsets.some((offset,index)=>offset!==offsets[index]))cancelMotion();
  sign=nextSign;max=nextMax;offsets=nextOffsets;
  const nextPages=[0];for(let index=1;index<slides.length;index++){const value=Math.min(max,offsets[index]);if(value>nextPages.at(-1)+2)nextPages.push(value);}
  if(max>nextPages.at(-1)+2)nextPages.push(max);
  pages=nextPages;
  for(const controls of nav)controls.hidden=pages.length<2&&controls.dataset.rdcRailNav==='side';
  win.tabIndex=pages.length<2?-1:0;indicate();
 };
 const key=event=>{
  if(event.target!==win)return;
  if(event.key==='ArrowRight'||event.key==='ArrowLeft'){event.preventDefault();step((event.key==='ArrowRight'?1:-1)*sign);}
  else if(event.key==='Home'||event.key==='End'){event.preventDefault();go(event.key==='Home'?0:pages.length-1);}
 };
 const start=event=>{
  suppressClick=false;
  cancelMotion();
  if(event.pointerType!=='mouse'||!event.isPrimary||event.button!==0||event.target.closest('button,input,select,textarea,video,iframe,[contenteditable]'))return;
  drag={id:event.pointerId,x:event.clientX,start:win.scrollLeft,moved:false};
 };
 const move=event=>{
  if(event.pointerId!==drag?.id)return;
  const delta=event.clientX-drag.x;if(!drag.moved&&Math.abs(delta)<5)return;
  if(!drag.moved){drag.moved=true;win.classList.add('is-drag');win.setPointerCapture(event.pointerId);}
  event.preventDefault();win.scrollLeft=drag.start-delta;
 };
 const finish=event=>{
  if(event.pointerId!==drag?.id)return;
  const moved=drag.moved;drag=null;win.classList.remove('is-drag');
  if(win.hasPointerCapture(event.pointerId))win.releasePointerCapture(event.pointerId);
  if(moved){suppressClick=true;go(pageAt(position()));}
 };
 const preventNativeDrag=event=>{if(drag)event.preventDefault();};
 const preventDragClick=event=>{
  if(!suppressClick||!event.detail)return;
  suppressClick=false;
  event.preventDefault();event.stopImmediatePropagation();
 };
 const focus=event=>{
  if(drag)return;
  const index=slides.findIndex(slide=>slide.contains(event.target));const slide=slides[index];if(index<0)return;
  const value=position(),width=slide.offsetWidth,padding=Number.parseFloat(view.getComputedStyle(win).paddingInlineStart)||0;
  if(offsets[index]<value||offsets[index]+width>value+win.clientWidth-padding){cancelMotion();win.scrollLeft=sign*Math.min(max,offsets[index]);}
 };
 for(const button of prev)button.addEventListener('click',back);
 for(const button of next)button.addEventListener('click',forward);
 win.addEventListener('click',preventDragClick,true);
 const listeners={scroll,keydown:key,pointerdown:start,pointermove:move,pointerup:finish,pointercancel:finish,lostpointercapture:finish,dragstart:preventNativeDrag,focusin:focus,wheel:cancelMotion,touchstart:cancelMotion};
 for(const [name,fn] of Object.entries(listeners))win.addEventListener(name,fn,['scroll','wheel','touchstart'].includes(name)?{passive:true}:undefined);
 const reduce=()=>{if(reduced.matches&&motion){const target=motion.target;cancelMotion();win.scrollLeft=sign*target;}};
 reduced.addEventListener('change',reduce);
 view.addEventListener('pointerup',finish);win.classList.add('is-enh');measure();
 const release=observeSizes(view,[win,slides[0]],measure);
 return()=>{
  release();cancelMotion();reduced.removeEventListener('change',reduce);view.clearTimeout(timer);if(raf)view.cancelAnimationFrame(raf);
  for(const button of prev)button.removeEventListener('click',back);
  for(const button of next)button.removeEventListener('click',forward);
  win.removeEventListener('click',preventDragClick,true);view.removeEventListener('pointerup',finish);
  for(const [name,fn] of Object.entries(listeners))win.removeEventListener(name,fn);
  if(drag&&win.hasPointerCapture(drag.id))win.releasePointerCapture(drag.id);
  win.classList.remove('is-enh','is-drag');
  for(const controls of nav)controls.hidden=true;
  status.textContent='';
 };
}
