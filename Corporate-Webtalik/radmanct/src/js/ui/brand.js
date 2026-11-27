export function initializeBrandMotion(doc=document){
 const view=doc.defaultView,preference=view.matchMedia('(prefers-reduced-motion:reduce)');
 const logos=[...doc.querySelectorAll('[data-rdc-logo]')],played=new Set();
 let observer,alive=true;
 const stop=()=>{observer?.disconnect();observer=undefined;};
 const start=()=>{
  if(!alive||doc.hidden||preference.matches||observer||doc.querySelector('#rdc-pre.is-in')||!view.IntersectionObserver)return;
  const waiting=logos.filter(logo=>!played.has(logo));
  if(!waiting.length)return;
  observer=new view.IntersectionObserver(entries=>{
   for(const entry of entries){
    if(!entry.isIntersecting||entry.intersectionRatio<.65||played.has(entry.target))continue;
    played.add(entry.target);
    entry.target.toggleAttribute('data-rdc-logo-act',true);
    observer.unobserve(entry.target);
   }
  },{threshold:.65});
  for(const logo of waiting)observer.observe(logo);
 };
 const changed=()=>{
  stop();
  if(preference.matches)for(const logo of played)logo.removeAttribute('data-rdc-logo-act');
  start();
 };
 doc.addEventListener('rdc:pre-done',start,{once:true});
 doc.addEventListener('visibilitychange',changed);
 view.addEventListener('pageshow',start);
 preference.addEventListener('change',changed);
 start();
 return()=>{
  alive=false;
  stop();
  for(const logo of logos)logo.removeAttribute('data-rdc-logo-act');
  doc.removeEventListener('visibilitychange',changed);
  view.removeEventListener('pageshow',start);
  doc.removeEventListener('rdc:pre-done',start);
  preference.removeEventListener('change',changed);
 };
}
