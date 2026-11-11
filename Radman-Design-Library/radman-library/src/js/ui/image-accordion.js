import {createPageLifecycle} from '../../../shared/radmanct/src/js/runtime/lifecycle.js';
const page=createPageLifecycle(window);
export function mountImageAccordion(root){
 return page.mountRoot(root,'image-accordion',()=>({}),()=>{
  const items=[...root.querySelectorAll('[data-rdl-iac-itm]')],buttons=items.map(item=>item.querySelector('[data-rdl-iac-tgl]'));
  if(items.length<2||buttons.some(button=>!button))return()=>{};
  const view=root.ownerDocument.defaultView,hover=view.matchMedia('(hover:hover) and (pointer:fine)'),stack=view.matchMedia('(max-width:767px)');let active=0;
  const select=index=>{active=index;items.forEach((item,i)=>{item.classList.toggle('is-on',i===index);buttons[i].setAttribute('aria-pressed',String(i===index));});};
  const enter=event=>{const item=event.target.closest('[data-rdl-iac-itm]');if(hover.matches&&item&&root.contains(item))select(items.indexOf(item));};
  const focus=event=>{const item=event.target.closest('[data-rdl-iac-itm]');if(item)select(items.indexOf(item));};
  const reset=()=>{const focused=root.ownerDocument.activeElement.closest?.('[data-rdl-iac-itm]');select(focused&&root.contains(focused)?items.indexOf(focused):0);};
  const leave=event=>{if(!root.contains(event.relatedTarget))reset();};
  const click=event=>{const button=event.target.closest('[data-rdl-iac-tgl]');if(button)select(buttons.indexOf(button));};
  const keys=event=>{const index=buttons.indexOf(event.target);if(index<0)return;const rtl=getComputedStyle(root.querySelector('.rdl-iac__row')).direction==='rtl';let next;
   if(event.key==='Home')next=0;else if(event.key==='End')next=items.length-1;else if(event.key==='ArrowRight')next=(index+(rtl?-1:1)+items.length)%items.length;else if(event.key==='ArrowLeft')next=(index+(rtl?1:-1)+items.length)%items.length;else return;
   event.preventDefault();select(next);buttons[next].focus({preventScroll:true});
  };
  const responsive=()=>{buttons.forEach(button=>button.hidden=stack.matches);};responsive();stack.addEventListener('change',responsive);root.classList.add('is-enh');select(active);
  root.addEventListener('pointerover',enter);root.addEventListener('pointerleave',reset);root.addEventListener('focusin',focus);root.addEventListener('focusout',leave);root.addEventListener('click',click);root.addEventListener('keydown',keys);
  return()=>{stack.removeEventListener('change',responsive);root.removeEventListener('pointerover',enter);root.removeEventListener('pointerleave',reset);root.removeEventListener('focusin',focus);root.removeEventListener('focusout',leave);root.removeEventListener('click',click);root.removeEventListener('keydown',keys);root.classList.remove('is-enh');items.forEach(item=>item.classList.remove('is-on'));buttons.forEach(button=>{button.hidden=true;button.removeAttribute('aria-pressed');});};
 });
}
