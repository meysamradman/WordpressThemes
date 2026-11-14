import {createPageLifecycle} from '../runtime/lifecycle.js';

export function initializeGallery(dialog) {
  const doc=dialog.ownerDocument;
  const image=dialog.querySelector('img');
  const button=dialog.querySelector('[data-rdc-lb-cls]');
  if(!image||!button||!doc.querySelector('[data-rdc-gal]'))return()=>{};
  let alive=true;
  const lock=locked=>doc.dispatchEvent(new doc.defaultView.CustomEvent('rdc:pnl-lock',{detail:{locked}}));
  const open=event=>{
    const link=event.target.closest?.('a[data-rdc-gal]');
    if(!link||event.defaultPrevented||event.button>0||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey)return;
    event.preventDefault();
    image.src=link.href;image.alt=link.querySelector('img')?.alt||'';
    dialog.showModal();lock(true);
  };
  const close=()=>dialog.close();
  const backdrop=event=>{if(event.target===dialog)close();};
  const closed=()=>{image.removeAttribute('src');lock(false);};
  doc.addEventListener('click',open);
  button.addEventListener('click',close);
  dialog.addEventListener('click',backdrop);
  dialog.addEventListener('close',closed);
  return()=>{
    if(!alive)return;
    alive=false;
    if(dialog.open){dialog.close();closed();}
    doc.removeEventListener('click',open);
    button.removeEventListener('click',close);
    dialog.removeEventListener('click',backdrop);
    dialog.removeEventListener('close',closed);
  };
}

if(typeof document!=='undefined'){
  const page=createPageLifecycle(window);
  const dialog=document.querySelector('.rdc-lb');
  if(dialog)page.mountRoot(dialog,'gallery',()=>({}),()=>initializeGallery(dialog));
}
