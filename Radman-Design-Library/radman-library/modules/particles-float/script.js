// Independent CSS motion: no imports, canvas, RAF loop or resize listener.
const instances=new WeakMap();
export function initializeFloatingParticles(root){
 if(instances.has(root))return instances.get(root);
 const doc=root.ownerDocument,view=doc.defaultView,button=root.querySelector('[data-rdl-pfl-pause]'),motion=view.matchMedia('(prefers-reduced-motion:reduce)'),events=new AbortController();
 let visible=!view.IntersectionObserver,paused=false,alive=true;
 const sync=()=>{
  if(!alive)return;
  const active=visible&&!doc.hidden&&!paused&&!motion.matches;
  root.dataset.rdlPflState=motion.matches?'static':active?'running':'paused';
  button.hidden=motion.matches||!root.querySelector('.rdl-pfl__dot');button.setAttribute('aria-pressed',String(paused));button.textContent=paused?'ادامهٔ حرکت':'توقف حرکت';
 };
 const observer=view.IntersectionObserver?new view.IntersectionObserver(entries=>{visible=entries.some(entry=>entry.isIntersecting);sync();}):null;
 observer?.observe(root);
 const on=(target,name,handler)=>target.addEventListener(name,handler,{signal:events.signal});
 on(button,'click',()=>{paused=!paused;sync();});on(doc,'visibilitychange',sync);on(motion,'change',sync);
 const dispose=()=>{alive=false;events.abort();observer?.disconnect();root.removeAttribute('data-rdl-pfl-state');button.hidden=true;instances.delete(root);};
 on(view,'pagehide',event=>{root.dataset.rdlPflState='paused';if(!event.persisted)dispose();});on(view,'pageshow',sync);
 instances.set(root,dispose);sync();return dispose;
}
for(const root of document.querySelectorAll('[data-rdl-pfl]'))initializeFloatingParticles(root);
