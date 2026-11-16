import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {observeSizes} from '../../shared/radmanct/src/js/runtime/observers.js';
import {initializeCarousel} from '../../src/js/ui/carousel.js';
function initializeVenture(root){
 const carousel=root.querySelector('[data-rdl-cr]'),win=carousel.querySelector('[data-rdl-cr-win]'),slides=[...win.firstElementChild.children],cards=[...root.querySelectorAll('[data-rdl-venture-card]')];
 const view=root.ownerDocument.defaultView,release=initializeCarousel(carousel);let offsets=[],sign=1,frame=0,active=-1;
 const sync=()=>{
  frame=0;const x=Math.max(0,sign*win.scrollLeft),index=offsets.reduce((best,n,i)=>Math.abs(n-x)<Math.abs(offsets[best]-x)?i:best,0);
  if(index===active)return;active=index;
  cards.forEach((card,i)=>{const selected=i===index;card.dataset.rdlVentureActive=String(selected);card.querySelector('[data-rdl-venture-pick]').setAttribute('aria-expanded',String(selected));card.querySelector('[data-rdl-venture-detail]').inert=!selected;});
 };
 const measure=()=>{sign=view.getComputedStyle(win).direction==='rtl'?-1:1;offsets=slides.map(slide=>Math.abs(slide.offsetLeft-slides[0].offsetLeft));sync();};
 const scroll=()=>{if(!frame)frame=view.requestAnimationFrame(sync);};
 const choose=event=>{if(event.target.closest('a'))return;const card=event.target.closest('[data-rdl-venture-card]');if(!card)return;carousel.querySelector(`[data-page="${card.dataset.rdlVentureCard}"]`)?.click();};
 root.dataset.rdlVentureReady='true';
 win.addEventListener('scroll',scroll,{passive:true});root.addEventListener('click',choose);measure();const releaseSize=observeSizes(view,[win],measure);
 return()=>{if(frame)view.cancelAnimationFrame(frame);win.removeEventListener('scroll',scroll);root.removeEventListener('click',choose);releaseSize();release();delete root.dataset.rdlVentureReady;cards.forEach(card=>{delete card.dataset.rdlVentureActive;card.querySelector('[data-rdl-venture-pick]').setAttribute('aria-expanded','true');card.querySelector('[data-rdl-venture-detail]').inert=false;});};
}
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdl-venture]'))page.mountRoot(root,'venture-carousel',()=>({}),()=>initializeVenture(root));
