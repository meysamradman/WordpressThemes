// This design owns its lifecycle and local engine. It imports no sibling sample.
const instances=new WeakMap();
export function initializeParticles(root,load=()=>import('./engine.js')){
 const existing=instances.get(root);if(existing)return existing;
 const doc=root.ownerDocument,view=doc.defaultView,layer=root.querySelector('[data-rdl-ptc-canvas]'),button=root.querySelector('[data-rdl-ptc-pause]');
 if(!layer||!button)return()=>{};
 let raw;try{raw=JSON.parse(root.dataset.rdlPtcOptions||'{}');}catch{return()=>{};}
 const clamp=(value,min,max,fallback)=>Number.isFinite(Number(value))?Math.min(max,Math.max(min,Number(value))):fallback;
 const number=clamp(raw.number,0,72,64),size=clamp(raw.size,.5,6,2.4),speed=clamp(raw.speed,0,2,.65),distance=clamp(raw.distance,50,180,150);
 const media=view.matchMedia('(prefers-reduced-motion:reduce)'),fine=view.matchMedia('(hover:hover) and (pointer:fine)'),events=new AbortController();
 let alive=true,visible=!view.IntersectionObserver,paused=false,engine,factory,pending=false,resizeFrame,width=0,height=0;
 const stop=()=>{engine?.destroy();engine=undefined;root.dataset.rdlPtcState=media.matches?'static':paused?'paused':'idle';root.removeAttribute('data-rdl-ptc-count');};
 const canRun=()=>alive&&root.isConnected&&visible&&!paused&&!media.matches&&!doc.hidden&&number>0;
 const start=()=>{
  const rect=layer.getBoundingClientRect();width=Math.round(rect.width);height=Math.round(rect.height);
  if(width<1||height<1)return;
  const color=view.getComputedStyle(root).getPropertyValue('--rdl-ptc-dot').trim();
  const count=Math.min(number,Math.max(Math.min(12,number),Math.round(number*Math.min(1,width*height/760000))));
  engine=factory(layer,{
   particles:{number:{value:count,density:{enable:false}},color:{value:color},shape:{type:'circle'},opacity:{value:.7,random:true},size:{value:size,random:true},line_linked:{enable:true,distance,color,opacity:.24,width:.7},move:{enable:true,speed,out_mode:'out'}},
   interactivity:{detect_on:'canvas',events:{onhover:{enable:fine.matches,mode:'grab'},onclick:{enable:false},resize:false},modes:{grab:{distance:160,line_linked:{opacity:.4}}}},retina_detect:true,
  },root);
  root.dataset.rdlPtcState='running';root.dataset.rdlPtcCount=String(engine.count);
 };
 const sync=async()=>{
  button.hidden=media.matches||number===0;button.setAttribute('aria-pressed',String(paused));button.textContent=paused?'ادامهٔ حرکت':'توقف حرکت';
  if(!canRun()){stop();return;}
  if(engine||pending)return;
  if(factory){start();return;}
  pending=true;
  try{const module=await load();factory=module.createParticleEngine;if(canRun())start();}
  catch{stop();button.hidden=true;}
  finally{pending=false;}
 };
 const resize=()=>{
  if(!alive||resizeFrame)return;
  resizeFrame=view.requestAnimationFrame(()=>{resizeFrame=undefined;const rect=layer.getBoundingClientRect();if(Math.round(rect.width)===width&&Math.round(rect.height)===height)return;stop();sync();});
 };
 const observer=view.IntersectionObserver?new view.IntersectionObserver(entries=>{visible=entries.some(entry=>entry.isIntersecting);sync();}):null;
 observer?.observe(root);
 const sizes=view.ResizeObserver?new view.ResizeObserver(resize):null;sizes?.observe(root);
 const on=(target,name,handler)=>target.addEventListener(name,handler,{signal:events.signal});
 const dispose=()=>{if(!alive)return;alive=false;events.abort();observer?.disconnect();sizes?.disconnect();if(resizeFrame)view.cancelAnimationFrame(resizeFrame);stop();button.hidden=true;root.removeAttribute('data-rdl-ptc-state');instances.delete(root);};
 on(button,'click',()=>{paused=!paused;sync();});on(media,'change',sync);on(fine,'change',()=>{stop();sync();});on(doc,'visibilitychange',sync);
 on(view,'pagehide',event=>{stop();if(!event.persisted)dispose();});on(view,'pageshow',sync);
 if(!sizes)on(view,'resize',resize);
 instances.set(root,dispose);sync();return dispose;
}
for(const root of document.querySelectorAll('[data-rdl-ptc]'))initializeParticles(root);
