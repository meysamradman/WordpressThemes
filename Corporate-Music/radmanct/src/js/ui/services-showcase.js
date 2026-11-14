export function initializeServicesShowcase(root) {
 const view=root.ownerDocument.defaultView;
 const hover=view.matchMedia('(hover:hover) and (pointer:fine)');
 const desktop=view.matchMedia('(min-width:768px)');
 const items=[...root.querySelectorAll('[data-rdc-svc]')];
 const frames=[...root.querySelectorAll('[data-rdc-svc-im]')];
 const reduced=view.matchMedia('(prefers-reduced-motion:reduce)');
 let alive=true,serial=0,requested=frames.find(frame=>!frame.hidden),current=requested,animation;
 const select=item=>{
  if(!alive)return;
  for(const other of items)if(other!==item)other.open=false;
  const index=item.dataset.rdcSvc,frame=frames.find(node=>node.dataset.rdcSvcIm===index);
  if(!frame||!desktop.matches||frame===requested)return;
  requested=frame;const token=++serial;
  const commit=()=>{
   if(!alive||!root.isConnected||token!==serial||frame===current)return;
   animation?.cancel();animation=undefined;
   for(const node of frames){node.hidden=node!==current&&node!==frame;if(node.style)node.style.zIndex=node===frame?'2':'1';}
   current=frame;
   const finish=()=>{if(!alive||token!==serial)return;for(const node of frames){node.hidden=node!==frame;node.style?.removeProperty('z-index');}animation=undefined;};
   if(reduced.matches||!frame.animate){finish();return;}
   animation=frame.animate([{opacity:0},{opacity:1}],{duration:300,easing:'ease-out'});
   animation.finished.then(finish,()=>{});
  };
  const image=frame.querySelector('img');
  if(!image||image.complete){commit();return;}
  image.loading='eager';
  if(image.decode)image.decode().then(commit,commit);else commit();
 };
 const locate=event=>{const item=event.target.closest('[data-rdc-svc]');return items.includes(item)?item:null;};
 const toggle=event=>{const item=event.target;if(items.includes(item)&&item.open)select(item);};
 const focus=event=>{const item=locate(event);if(item?.open)select(item);};
 const over=event=>{if(!hover.matches||event.pointerType==='touch')return;const item=locate(event);if(item&&!item.contains(event.relatedTarget)){item.open=true;select(item);}};
 const resize=()=>{requested=null;++serial;const item=items.find(node=>node.open);if(item)select(item);};
 desktop.addEventListener('change',resize);
 root.addEventListener('toggle',toggle,true);root.addEventListener('focusin',focus);root.addEventListener('pointerover',over);
 return()=>{alive=false;++serial;animation?.cancel();for(const frame of frames){frame.hidden=frame!==current;frame.style?.removeProperty('z-index');}desktop.removeEventListener('change',resize);root.removeEventListener('toggle',toggle,true);root.removeEventListener('focusin',focus);root.removeEventListener('pointerover',over);};
}
