import {lockPanelScroll} from './scroll-lock.js';
export function createContentDialog(doc){
  const dialog=doc.createElement('dialog');dialog.className='rdct-content-dialog';
  const close=doc.createElement('button');close.type='button';close.className='rdct-content-dialog__close';close.textContent='\u00d7';dialog.append(close);doc.body.append(dialog);
  let source;let marker;let hidden;let display;let ariaHidden;let opener;let release;
  const restore=()=>{if(source){source.pause?.();source.querySelectorAll?.('video,audio').forEach(media=>media.pause());}if(source && marker?.parentNode){marker.replaceWith(source);source.hidden=hidden;source.style.display=display;if(ariaHidden===null)source.removeAttribute('aria-hidden');else source.setAttribute('aria-hidden',ariaHidden);}source=marker=null;opener?.setAttribute('aria-expanded','false');opener?.focus({preventScroll:true});release?.();release=null;};
  close.addEventListener('click',()=>dialog.close());dialog.addEventListener('close',restore);
  dialog.addEventListener('click',event=>{const rect=dialog.getBoundingClientRect();if(event.target===dialog && (event.clientX<rect.left || event.clientX>rect.right || event.clientY<rect.top || event.clientY>rect.bottom))dialog.close();});
  return {
    open(target,button,label){
      if(dialog.open || target.contains(button))return false;
      source=target;opener=button;hidden=source.hidden;display=source.style.display;ariaHidden=source.getAttribute('aria-hidden');marker=doc.createComment('rdct-modal-source');source.before(marker);dialog.append(source);source.hidden=false;source.removeAttribute('aria-hidden');if(doc.defaultView.getComputedStyle(source).display==='none')source.style.display='block';
      close.setAttribute('aria-label',label);dialog.setAttribute('aria-label',button.querySelector('.rdct-button__label')?.textContent || button.textContent.trim());release=lockPanelScroll(doc,dialog);
      try{dialog.showModal();}catch{restore();return false;}
      button.setAttribute('aria-expanded','true');close.focus({preventScroll:true});return true;
    },
    close(){if(dialog.open)dialog.close();},
    destroy(){if(dialog.open)dialog.close();restore();dialog.remove();},
  };
}
