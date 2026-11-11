import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdl-bot]'))page.mountRoot(root,'robot',()=>({}),()=>{
 const cfg=JSON.parse(root.dataset.rdlBotCfg||'{}'),reduce=matchMedia('(prefers-reduced-motion:reduce)'),coarse=matchMedia('(pointer:coarse)');
 let alive=true,pending=false,release,visible=false,abort;
 const sync=async()=>{
  if(reduce.matches||(cfg.mobileFallback&&coarse.matches)){abort?.abort();release?.();release=undefined;return;}
  if(!alive||pending||release||!visible)return;
  pending=true;const request=abort=new AbortController();
  try{const {mountRobot}=await import('./viewer.js');if(alive&&!request.signal.aborted){const dispose=await mountRobot(root,cfg,()=>alive,request.signal);if(alive&&!request.signal.aborted)release=dispose;else dispose?.();}}
  catch{if(!request.signal.aborted){root.removeAttribute('data-rdl-bot-rdy');root.dataset.rdlBotErr='1';}}
  finally{pending=false;if(request.signal.aborted&&alive&&!reduce.matches&&!(cfg.mobileFallback&&coarse.matches))sync();}
 };
 const observer=new IntersectionObserver(entries=>{visible=entries[0].isIntersecting;sync();},{rootMargin:'240px'});observer.observe(root);
 reduce.addEventListener('change',sync);coarse.addEventListener('change',sync);
 return()=>{alive=false;abort?.abort();observer.disconnect();reduce.removeEventListener('change',sync);coarse.removeEventListener('change',sync);release?.();};
});
