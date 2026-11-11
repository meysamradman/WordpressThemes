import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdl-bot]'))page.mountRoot(root,'robot',()=>({}),()=>{
 const cfg=JSON.parse(root.dataset.rdlBotCfg||'{}'),reduce=matchMedia('(prefers-reduced-motion:reduce)'),coarse=matchMedia('(pointer:coarse)');
 let alive=true,pending=false,release,visible=false;
 const sync=async()=>{
  if(reduce.matches||(cfg.mobileFallback&&coarse.matches)){release?.();release=undefined;return;}
  if(!alive||pending||release||!visible)return;
  pending=true;
  try{const {mountRobot}=await import('./viewer.js');if(alive&&!reduce.matches&&!(cfg.mobileFallback&&coarse.matches)){const dispose=await mountRobot(root,cfg,()=>alive);if(alive&&!reduce.matches&&!(cfg.mobileFallback&&coarse.matches))release=dispose;else dispose?.();}}
  catch{root.removeAttribute('data-rdl-bot-rdy');root.dataset.rdlBotErr='1';}
  finally{pending=false;}
 };
 const observer=new IntersectionObserver(entries=>{visible=entries[0].isIntersecting;sync();},{rootMargin:'240px'});observer.observe(root);
 reduce.addEventListener('change',sync);coarse.addEventListener('change',sync);
 return()=>{alive=false;observer.disconnect();reduce.removeEventListener('change',sync);coarse.removeEventListener('change',sync);release?.();};
});
