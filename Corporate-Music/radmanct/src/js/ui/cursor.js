function controlLabel(control,doc){
 if(!control||control.closest?.('[data-rdc-cur-tip="off"]'))return '';
 const explicit=control.getAttribute('data-rdc-cur-lbl');
 const named=control.getAttribute('aria-labelledby')?.split(/\s+/).map(id=>doc.getElementById?.(id)?.textContent||'').join(' ');
 let text=explicit||named||control.getAttribute('aria-label')||control.getAttribute('title');
 if(!text){
  const copy=control.cloneNode?.(true);
  copy?.querySelectorAll('svg,[aria-hidden="true"],[hidden],.sub-menu,.rdc-pnl-sub').forEach(child=>child.remove());
  text=copy?.textContent||control.textContent||control.querySelector?.('img[alt]')?.getAttribute('alt')||'';
 }
 return text.replace(/\s+/g,' ').trim().slice(0,80);
}
const interactive='a[href],button:not(:disabled),summary,[role="button"]';
export function initializeCursor(node,view=window){
 if(!node)return()=>{};
 const doc=node.ownerDocument,media=view.matchMedia('(hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)');
 const label=node.querySelector?.('[data-rdc-cur-lbl]'),parent=node.parentNode,next=node.nextSibling,original=node.getAttribute('style');
 let frame=0,x=-100,y=-100,active=false,target=null,suppressed=false;
 const state=element=>{
  if(target===element)return;target=element;
  const disabled=element?.closest?.('[data-rdc-cur="off"],input,textarea,select,[contenteditable="true"],#wpadminbar');
  suppressed=Boolean(disabled);
  const control=disabled?null:element?.closest?.(interactive);
  node.toggleAttribute?.('data-interactive',Boolean(control));
  const text=controlLabel(control,doc);
  if(label)label.textContent=text;
  node.toggleAttribute?.('data-label',Boolean(text));
  const host=element?.closest?.('dialog[open]')||parent;
  if(host&&node.parentNode!==host)host.append(node);
  if(suppressed)hide();
 };
 const paint=()=>{
  frame=0;if(!active||doc.hidden||suppressed)return;
  node.style.transform=`translate3d(${x-17.5}px,${y-17.5}px,0)`;
  // The label fits within half the viewport; point it toward the larger free side.
  node.toggleAttribute?.('data-label-left',x>view.innerWidth/2);
  node.toggleAttribute?.('data-label-above',y>view.innerHeight-65);
  if(node.hidden)node.hidden=false;
 };
 const move=event=>{
  if(!active||doc.hidden||event.pointerType==='touch')return;
  state(event.target);
  if(x===event.clientX&&y===event.clientY)return;
  x=event.clientX;y=event.clientY;if(!frame)frame=view.requestAnimationFrame(paint);
 };
 const hide=()=>{if(frame)view.cancelAnimationFrame(frame);frame=0;node.hidden=true;};
 const visibility=()=>{if(doc.hidden)hide();};
 const sync=()=>{active=media.matches;if(!active)hide();node.style.willChange=active?'transform':'';};
 const out=event=>{if(!event.relatedTarget)hide();};
 media.addEventListener('change',sync);doc.addEventListener('visibilitychange',visibility);doc.addEventListener('pointerleave',hide);doc.addEventListener('pointerout',out);view.addEventListener('pointermove',move,{passive:true});sync();
 return()=>{
  hide();media.removeEventListener('change',sync);doc.removeEventListener('visibilitychange',visibility);doc.removeEventListener('pointerleave',hide);doc.removeEventListener('pointerout',out);view.removeEventListener('pointermove',move);
  if(parent&&node.parentNode!==parent)parent.insertBefore(node,next?.parentNode===parent?next:null);
  for(const name of ['data-interactive','data-label','data-label-left','data-label-above'])node.removeAttribute(name);
  if(label)label.textContent='';original===null?node.removeAttribute('style'):node.setAttribute('style',original);
 };
}
