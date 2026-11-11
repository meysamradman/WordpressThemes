import {CustomEase} from 'gsap/CustomEase';
let liquidEase;
export function initializeLiquid(root,{gsap}){
  gsap.registerPlugin(CustomEase);
  liquidEase ??= CustomEase.create('rdct-button-liquid','.23,1,.32,1');
  const label=root.querySelector('.rdct-button__label');if(!label)return()=>{};
  const original=[...label.childNodes];const doc=root.ownerDocument;const view=doc.defaultView;const text=label.textContent;
  const pieces=/[\u0600-\u06ff]/u.test(text)?text.split(/(\s+)/u):view.Intl?.Segmenter?[...new view.Intl.Segmenter(undefined,{granularity:'grapheme'}).segment(text)].map(part=>part.segment):Array.from(text);
  label.replaceChildren(...pieces.map(text=>{if(/^\s*$/u.test(text))return doc.createTextNode(text);const node=doc.createElement('span');node.className='rdct-button__word';node.textContent=text;return node;}));
  const words=[...label.querySelectorAll('.rdct-button__word')];const horizontal=root.dataset.textEffect.includes('-x');const reverse=root.dataset.textEffect.endsWith('-alt');
  const axis=horizontal?'x':'y';const dir=(reverse?-1:1)*(horizontal?1:-1)*(horizontal && root.matches(':dir(rtl)')?-1:1);let timeline;
  const context=gsap.context(()=>{timeline=gsap.timeline({paused:true});const duration=parseFloat(view.getComputedStyle(root).getPropertyValue('--rdct-button-duration')) || .6;const distance=parseFloat(view.getComputedStyle(root).fontSize)*.3;
    words.forEach((word,index)=>{const at=(reverse?words.length-1-index:index)*.03;timeline.to(word,{[axis]:-dir*distance,opacity:0,duration:duration*.5,ease:liquidEase},at).set(word,{[axis]:dir*distance},at+duration*.51).to(word,{[axis]:0,opacity:1,duration:duration*.49,ease:liquidEase},at+duration*.51);});
  },root);
  const enter=()=>timeline.restart();const leave=()=>{if(!root.matches(':hover') && !root.contains(doc.activeElement))timeline.reverse();};
  root.addEventListener('pointerenter',enter);root.addEventListener('pointerleave',leave);root.addEventListener('focusin',enter);root.addEventListener('focusout',leave);
  return()=>{root.removeEventListener('pointerenter',enter);root.removeEventListener('pointerleave',leave);root.removeEventListener('focusin',enter);root.removeEventListener('focusout',leave);context.revert();label.replaceChildren(...original);};
}
