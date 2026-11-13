// No sibling design, jQuery, GSAP, scroll runtime or perpetual measurement loop.
export function initializeCurtain(root){
 if(root.hasAttribute('data-rdl-crt-ready'))return()=>{};
 const row=root.querySelector('.rdl-crt__row'),items=[...row.children],view=root.ownerDocument.defaultView,events=new AbortController(),fine=view.matchMedia('(hover:hover) and (pointer:fine)');
 let frame,alive=true;
 const select=item=>{for(const panel of items)panel.open=panel===item;};
 const layout=()=>{frame=undefined;if(!alive)return;const style=view.getComputedStyle(root),width=row.clientWidth-(items.length-1)*(parseFloat(style.getPropertyValue('--rdl-crt-rail'))+parseFloat(style.getPropertyValue('--rdl-crt-gap')));root.style.setProperty('--rdl-crt-width',`${Math.max(1,width)}px`);};
 const queue=()=>{if(alive&&!frame)frame=view.requestAnimationFrame(layout);};
 for(const [index,item] of items.entries()){
  const button=item.querySelector('summary');
  item.addEventListener('pointerenter',()=>{if(fine.matches&&view.innerWidth>=992&&!root.querySelector('summary:focus-visible'))select(item);},{signal:events.signal});
  button.addEventListener('click',event=>{event.preventDefault();select(item);button.focus({preventScroll:true});},{signal:events.signal});
  button.addEventListener('keydown',event=>{
   let next;if(event.key==='Home')next=0;else if(event.key==='End')next=items.length-1;else if(['ArrowRight','ArrowLeft','ArrowDown','ArrowUp'].includes(event.key)){const rtl=view.getComputedStyle(root).direction==='rtl';const forward=event.key==='ArrowDown'||event.key===(rtl?'ArrowLeft':'ArrowRight');next=(index+(forward?1:-1)+items.length)%items.length;}else return;
   event.preventDefault();select(items[next]);items[next].querySelector('summary').focus({preventScroll:true});
  },{signal:events.signal});
 }
 const observer=view.ResizeObserver?new view.ResizeObserver(queue):null;observer?.observe(row);if(!observer)view.addEventListener('resize',queue,{signal:events.signal});
 const dispose=()=>{alive=false;events.abort();observer?.disconnect();if(frame)view.cancelAnimationFrame(frame);root.removeAttribute('data-rdl-crt-ready');root.style.removeProperty('--rdl-crt-width');};
 view.addEventListener('pagehide',event=>{if(!event.persisted)dispose();},{signal:events.signal});
 if(items.length&&!items.some(item=>item.open))select(items[0]);root.setAttribute('data-rdl-crt-ready','');layout();return dispose;
}
for(const root of document.querySelectorAll('[data-rdl-crt]'))initializeCurtain(root);
