import {initializePanelContent} from './panel/content.js';
import {initializePanelBranches} from './panel/branches.js';
import {reportRuntimeError} from '../runtime/lifecycle.js';
export function initializeMenuPanel(dialog,trigger,close,load=()=>import('./panel/wave.js')){
 const doc=dialog.ownerDocument,view=doc.defaultView;let alive=true,desired=false,generation=0,motion,pending,closing,locked=false;
 const lock=value=>{if(locked===value)return;locked=value;doc.dispatchEvent(new view.CustomEvent('rdc:pnl-lock',{detail:{locked:value}}));};
 const update=()=>trigger?.setAttribute('aria-expanded',String(dialog.open));
 const positionKeys=['--rdc-pnl-cls-top','--rdc-pnl-cls-st','--rdc-pnl-cls-sz'];
 const alignClose=()=>{
  const box=trigger?.getBoundingClientRect?.();if(!box||box.width<=0||box.height<=0)return;
  const frame=dialog.getBoundingClientRect(),rtl=view.getComputedStyle(dialog).direction==='rtl';
  dialog.style.setProperty(positionKeys[0],`${Math.max(0,box.top)}px`);
  dialog.style.setProperty(positionKeys[1],`${Math.max(0,rtl?frame.right-box.right:box.left-frame.left)}px`);
  dialog.style.setProperty(positionKeys[2],`${box.width}px`);
 };
 const resized=()=>{if(dialog.open&&desired)alignClose();};
 const animate=()=>pending??=load().then(module=>{if(alive)motion=module.createPanelMotion(dialog,1100);return motion;}).catch(error=>{pending=undefined;throw error;});
 const show=async()=>{if(!alive||desired)return;desired=true;const token=++generation;dialog.setAttribute('data-motion-covered','');dialog.showModal();content.prepare();dialog.scrollTop=0;lock(true);alignClose();update();if(view.matchMedia('(prefers-reduced-motion:reduce)').matches){dialog.removeAttribute('data-motion-covered');return;}try{const effect=await animate();if(alive&&desired&&dialog.open&&token===generation)await effect.play(true);}catch(error){if(alive&&token===generation){dialog.removeAttribute('data-motion-covered');reportRuntimeError('menu-panel',error,view);}}};
 const hide=()=>{
  if(!alive||!dialog.open)return Promise.resolve();
  if(closing)return closing;
  desired=false;const token=++generation;dialog.setAttribute('data-panel-closing','');
  closing=(async()=>{
   try{
    if(!view.matchMedia('(prefers-reduced-motion:reduce)').matches){
     const effect=motion||await animate();
     if(alive&&!desired&&dialog.open&&token===generation)await effect.play(false);
    }
   }catch(error){if(alive)reportRuntimeError('menu-panel',error,view);}
   finally{
    if(alive&&!desired&&token===generation){dialog.close();dialog.removeAttribute('data-motion-covered');}
    dialog.removeAttribute('data-panel-closing');
   }
  })().finally(()=>{closing=undefined;});return closing;
 };
 const branches=initializePanelBranches(dialog);
 const content=initializePanelContent(dialog,trigger,()=>branches.reset());
 const cancel=event=>{event.preventDefault();if(!branches.back())hide();};
 const closed=()=>{branches.reset();desired=false;++generation;motion?.cancel();dialog.removeAttribute('data-motion-covered');dialog.removeAttribute('data-panel-closing');lock(false);update();};
 const anchor=event=>{const link=event.target.closest('a');if(!link||event.ctrlKey||event.metaKey||event.shiftKey)return;const target=new URL(link.href,view.location.href);if(target.origin===view.location.origin&&target.pathname===view.location.pathname&&target.hash){event.preventDefault();hide().then(()=>{if(alive)view.location.assign(target.href);});}};
 view.addEventListener('resize',resized);
 trigger?.addEventListener('click',show);close?.addEventListener('click',hide);dialog.addEventListener('cancel',cancel);dialog.addEventListener('close',closed);dialog.addEventListener('click',anchor);
 return()=>{if(!alive)return;alive=false;++generation;view.removeEventListener('resize',resized);for(const key of positionKeys)dialog.style.removeProperty(key);trigger?.removeEventListener('click',show);close?.removeEventListener('click',hide);dialog.removeEventListener('cancel',cancel);dialog.removeEventListener('close',closed);dialog.removeEventListener('click',anchor);branches.destroy();content.destroy();motion?.destroy();if(dialog.open)dialog.close();lock(false);update();};
}
