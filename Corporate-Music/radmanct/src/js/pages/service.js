import {createPageLifecycle} from '../runtime/lifecycle.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('.rdc-mosaic'))page.mountRoot(root,'mosaic-border',()=>({}),()=>{
 const doc=root.ownerDocument,view=doc.defaultView,preference=view.matchMedia('(prefers-reduced-motion:reduce)');
 const boxes=[...root.querySelectorAll('.rdc-mosaic__copy,.rdc-mosaic__md')];
 let observer;
 const visibility=()=>root.toggleAttribute('data-rdc-rim-hidden',doc.hidden);
 const sync=()=>{
  observer?.disconnect();observer=undefined;root.removeAttribute('data-rdc-rim-ready');
  for(const box of boxes)box.removeAttribute('data-rdc-rim-run');
  if(preference.matches||!view.IntersectionObserver)return;
  observer=new view.IntersectionObserver(entries=>{for(const entry of entries)entry.target.toggleAttribute('data-rdc-rim-run',entry.isIntersecting);},{threshold:0});
  for(const box of boxes)observer.observe(box);
  root.setAttribute('data-rdc-rim-ready','');visibility();
 };
 preference.addEventListener('change',sync);doc.addEventListener('visibilitychange',visibility);sync();
 return()=>{observer?.disconnect();preference.removeEventListener('change',sync);doc.removeEventListener('visibilitychange',visibility);root.removeAttribute('data-rdc-rim-ready');root.removeAttribute('data-rdc-rim-hidden');for(const box of boxes)box.removeAttribute('data-rdc-rim-run');};
});
