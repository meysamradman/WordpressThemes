import {observeSizes} from '../../../shared/radmanct/src/js/runtime/observers.js';

export function initializeCarousel(root){
 const doc=root.ownerDocument,view=doc.defaultView,win=root.querySelector('[data-rdl-cr-win]');
 const track=win.firstElementChild,slides=[...track.children],prev=root.querySelector('[data-rdl-cr-prev]'),next=root.querySelector('[data-rdl-cr-next]'),nav=root.querySelector('[data-rdl-cr-nav]'),dots=root.querySelector('[data-rdl-cr-dots]'),status=root.querySelector('[data-rdl-cr-status]');
 if(!slides.length)return()=>{};
 const reduced=view.matchMedia('(prefers-reduced-motion:reduce)');
 let sign=1,max=0,pages=[0],offsets=[],current=0,raf=0,timer=0,drag=null,dotButtons=[],motion=null;
 const position=()=>Math.max(0,Math.min(max,sign*win.scrollLeft));
 const pageAt=value=>pages.reduce((best,offset,index)=>Math.abs(offset-value)<Math.abs(pages[best]-value)?index:best,0);
 const indicate=()=>{
  raf=0;const value=position();current=pageAt(value);prev.disabled=value<2;next.disabled=value>max-2;
  dotButtons.forEach((button,index)=>{if(index===current)button.setAttribute('aria-current','true');else button.removeAttribute('aria-current');});
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
 const choose=event=>{const button=event.target.closest('button[data-page]');if(button&&dots.contains(button))go(Number(button.dataset.page));};
 const measure=()=>{
  const nextSign=view.getComputedStyle(win).direction==='rtl'?-1:1,nextMax=Math.max(0,win.scrollWidth-win.clientWidth);
  const nextOffsets=slides.map(slide=>Math.abs(slide.offsetLeft-slides[0].offsetLeft));
  if(nextSign!==sign||nextMax!==max||nextOffsets.some((offset,index)=>offset!==offsets[index]))cancelMotion();
  sign=nextSign;max=nextMax;offsets=nextOffsets;
  const nextPages=[0];for(let index=1;index<slides.length;index++){const value=Math.min(max,offsets[index]);if(value>nextPages.at(-1)+2)nextPages.push(value);}
  if(max>nextPages.at(-1)+2)nextPages.push(max);
  if(nextPages.length!==pages.length){dots.replaceChildren();dotButtons=nextPages.map((_,index)=>{const button=doc.createElement('button');button.type='button';button.dataset.page=String(index);button.setAttribute('aria-label',`صفحهٔ ${index+1}`);button.setAttribute('aria-controls',win.id);dots.append(button);return button;});}
  pages=nextPages;nav.hidden=dots.hidden=pages.length<2;indicate();
 };
 const key=event=>{
  if(event.target!==win)return;
  if(event.key==='ArrowRight'||event.key==='ArrowLeft'){event.preventDefault();step((event.key==='ArrowRight'?1:-1)*sign);}
  else if(event.key==='Home'||event.key==='End'){event.preventDefault();go(event.key==='Home'?0:pages.length-1);}
 };
 const start=event=>{
  cancelMotion();
  if(event.pointerType!=='mouse'||!event.isPrimary||event.button!==0||event.target.closest('a,button,input,select,textarea,video,[contenteditable]'))return;
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
  if(moved){const value=position(),target=offsets.reduce((best,offset)=>Math.abs(Math.min(max,offset)-value)<Math.abs(best-value)?Math.min(max,offset):best,0);glide(target);}
 };
 const focus=event=>{
  const slide=event.target.closest('.rdl-cr__itm'),index=slides.indexOf(slide);if(index<0)return;
  const value=position(),width=slide.offsetWidth,padding=Number.parseFloat(view.getComputedStyle(win).paddingInlineStart)||0;
  if(offsets[index]<value||offsets[index]+width>value+win.clientWidth-padding){cancelMotion();win.scrollLeft=sign*Math.min(max,offsets[index]);}
 };
 prev.addEventListener('click',back);next.addEventListener('click',forward);dots.addEventListener('click',choose);
 const listeners={scroll,keydown:key,pointerdown:start,pointermove:move,pointerup:finish,pointercancel:finish,lostpointercapture:finish,focusin:focus,wheel:cancelMotion,touchstart:cancelMotion};
 for(const [name,fn] of Object.entries(listeners))win.addEventListener(name,fn,['scroll','wheel','touchstart'].includes(name)?{passive:true}:undefined);
 const reduce=()=>{if(reduced.matches&&motion){const target=motion.target;cancelMotion();win.scrollLeft=sign*target;}};
 reduced.addEventListener('change',reduce);
 view.addEventListener('pointerup',finish);win.classList.add('is-enh');measure();
 const release=observeSizes(view,[win,slides[0]],measure);
 return()=>{
  release();cancelMotion();reduced.removeEventListener('change',reduce);view.clearTimeout(timer);if(raf)view.cancelAnimationFrame(raf);
  prev.removeEventListener('click',back);next.removeEventListener('click',forward);dots.removeEventListener('click',choose);view.removeEventListener('pointerup',finish);
  for(const [name,fn] of Object.entries(listeners))win.removeEventListener(name,fn);
  if(drag&&win.hasPointerCapture(drag.id))win.releasePointerCapture(drag.id);
  win.classList.remove('is-enh','is-drag');nav.hidden=dots.hidden=true;dots.replaceChildren();status.textContent='';
 };
}
