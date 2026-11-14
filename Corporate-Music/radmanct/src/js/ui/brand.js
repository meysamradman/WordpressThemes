export function initializeBrandMotion(doc=document){
 const view=doc.defaultView,preference=view.matchMedia('(prefers-reduced-motion:reduce)');
 const logos=[...doc.querySelectorAll('[data-rdc-logo]')],visible=new Set();
 let observer,alive=true;
 const stop=()=>{observer?.disconnect();observer=undefined;visible.clear();for(const logo of logos)logo.removeAttribute('data-rdc-logo-act');};
 const start=()=>{
  if(!alive||preference.matches||observer||doc.getElementById('rdc-pre')||!view.IntersectionObserver||!logos.length)return;
  observer=new view.IntersectionObserver(entries=>{
   for(const entry of entries){
    const shown=entry.isIntersecting&&entry.intersectionRatio>=.65;
    if(shown)visible.add(entry.target);else visible.delete(entry.target);
    entry.target.toggleAttribute('data-rdc-logo-act',shown);
   }
  },{threshold:.65});
  for(const logo of logos)observer.observe(logo);
 };
 const replay=()=>{
  start();
  if(!alive||preference.matches||doc.getElementById('rdc-pre'))return;
  for(const logo of visible)for(const animation of logo.getAnimations?.({subtree:true})||[]){animation.currentTime=0;animation.play();}
 };
 const changed=()=>{stop();start();};
 doc.addEventListener('rdc:pre-done',start,{once:true});
 view.addEventListener('pageshow',replay);preference.addEventListener('change',changed);start();
 return()=>{alive=false;stop();view.removeEventListener('pageshow',replay);doc.removeEventListener('rdc:pre-done',start);preference.removeEventListener('change',changed);};
}
