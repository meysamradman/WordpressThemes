import {createPageLifecycle} from '../runtime/lifecycle.js';

export function initializeGallery(dialog) {
 const doc=dialog.ownerDocument,image=dialog.querySelector('img'),close=dialog.querySelector('[data-rdc-lb-cls]');
 if(!image||!close||typeof dialog.showModal!=='function'||!doc.querySelector('[data-rdc-gal]'))return()=>{};
 const previous=dialog.querySelector('[data-rdc-lb-prev]'),next=dialog.querySelector('[data-rdc-lb-next]');
 const counter=dialog.querySelector('[data-rdc-lb-count]'),caption=dialog.querySelector('[data-rdc-lb-caption]'),status=dialog.querySelector('[data-rdc-lb-status]');
 const releases=[];let items=[],index=0,opener,touch;
 const listen=(node,type,handler)=>{if(!node)return;node.addEventListener(type,handler);releases.push(()=>node.removeEventListener(type,handler));};
 const lock=locked=>doc.dispatchEvent(new doc.defaultView.CustomEvent('rdc:pnl-lock',{detail:{locked}}));
 const display=position=>{
  if(!items.length)return;index=Math.max(0,Math.min(items.length-1,position));const link=items[index];
  if(status)status.textContent='در حال دریافت تصویر…';image.alt=link.querySelector('img')?.alt||'';image.src=link.href;
  if(counter)counter.textContent=`${(index+1).toLocaleString('fa-IR')} / ${items.length.toLocaleString('fa-IR')}`;
  if(caption)caption.textContent=link.dataset.rdcCaption||'';
  if(previous)previous.disabled=index===0;if(next)next.disabled=index===items.length-1;
 };
 const open=event=>{
  const link=event.target.closest?.('a[data-rdc-gal]');
  if(!link||event.defaultPrevented||event.button>0||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey)return;
  const group=link.closest('[data-rdc-gallery]')||doc;items=[...group.querySelectorAll('a[data-rdc-gal]')];const position=items.indexOf(link);if(position<0)return;
  event.preventDefault();opener=link;display(position);dialog.showModal();lock(true);
 };
 const closed=()=>{image.removeAttribute('src');touch=undefined;lock(false);opener?.focus({preventScroll:true});};
 listen(doc,'click',open);listen(close,'click',()=>dialog.close());
 listen(previous,'click',()=>display(index-1));listen(next,'click',()=>display(index+1));
 listen(dialog,'click',event=>{if(event.target===dialog)dialog.close();});
 listen(dialog,'close',closed);
 listen(dialog,'keydown',event=>{
  if(!['ArrowLeft','ArrowRight'].includes(event.key)||event.target.matches?.('input,textarea,select'))return;
  event.preventDefault();const rtl=doc.documentElement.dir==='rtl';display(index+(event.key==='ArrowLeft'?(rtl?1:-1):(rtl?-1:1)));
 });
 listen(image,'load',()=>{if(status)status.textContent='';});
 listen(image,'error',()=>{if(status)status.textContent='تصویر دریافت نشد. دوباره تلاش کنید.';});
 listen(image,'pointerdown',event=>{if(event.pointerType==='touch')touch={x:event.clientX,y:event.clientY};});
 listen(image,'pointercancel',()=>{touch=undefined;});
 listen(image,'pointerup',event=>{
  if(!touch)return;const dx=event.clientX-touch.x,dy=event.clientY-touch.y;touch=undefined;
  if(Math.abs(dx)>45&&Math.abs(dx)>Math.abs(dy)){const rtl=doc.documentElement.dir==='rtl';display(index+(dx<0?(rtl?-1:1):(rtl?1:-1)));}
 });
 return()=>{if(dialog.open){dialog.close();closed();}for(const release of releases)release();image.removeAttribute('src');};
}
if(typeof document!=='undefined'){
 const page=createPageLifecycle(window),dialog=document.querySelector('.rdc-lb');
 if(dialog)page.mountRoot(dialog,'gallery',()=>({}),()=>initializeGallery(dialog));
}
