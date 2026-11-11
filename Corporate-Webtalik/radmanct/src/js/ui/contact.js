/* Contact behavior adapted from Contact Us Local (Meysam Safari), GPL-2.0-or-later. */
export function initializeContact(root){
 if(!root)return()=>{};
 const button=root.querySelector('button'),panel=root.querySelector('[role="region"]');
 const view=root.ownerDocument.defaultView,controller=new AbortController();
 const on=(node,event,callback)=>node.addEventListener(event,callback,{signal:controller.signal});
 let opened=false,animation;
 const toggle=(open,restoreFocus=false)=>{
  const style=view.getComputedStyle(panel);
  const from={opacity:panel.hidden?'0':style.opacity,transform:panel.hidden?'translateY(12px) scale(.96)':style.transform};
  animation?.cancel();
  opened=open;
  button.setAttribute('aria-expanded',String(open));
  panel.inert=!open;
  panel.hidden=false;
  root.classList.toggle('is-open',open);
  if(restoreFocus)button.focus({preventScroll:true});
  if(view.matchMedia('(prefers-reduced-motion:reduce)').matches||!panel.animate){panel.hidden=!open;return;}
  animation=panel.animate([from,{opacity:open?1:0,transform:open?'translateY(0) scale(1)':'translateY(12px) scale(.96)'}],{duration:220,easing:'cubic-bezier(.22,1,.36,1)'});
  animation.onfinish=()=>{panel.hidden=!opened;animation=null;};
 };
 on(button,'click',()=>{
  toggle(!opened);
  if(opened)panel.querySelector('a')?.focus({preventScroll:true});
 });
 on(root.ownerDocument,'pointerdown',event=>{if(opened&&!root.contains(event.target))toggle(false);});
 on(root,'keydown',event=>{if(opened&&event.key==='Escape'){event.preventDefault();toggle(false,true);}});
 on(root,'focusout',event=>{if(opened&&event.relatedTarget&&!root.contains(event.relatedTarget))toggle(false);});
 on(root.ownerDocument,'visibilitychange',()=>root.classList.toggle('is-hide',root.ownerDocument.hidden));
 return()=>{controller.abort();animation?.cancel();panel.hidden=true;panel.inert=true;root.classList.remove('is-open');button.setAttribute('aria-expanded','false');};
}
