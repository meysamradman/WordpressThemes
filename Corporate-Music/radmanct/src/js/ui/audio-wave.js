import {observeSizes} from '../runtime/observers.js';

export function waveHeights(count,seed='') {
 let state=2166136261;
 for(const char of seed)state=Math.imul(state^char.charCodeAt(0),16777619);
 let previous=.5;
 return Array.from({length:count},(_,index)=>{
  state=(Math.imul(state,1664525)+1013904223)>>>0;
  previous=previous*.55+(state/4294967296)*.45;
  return Math.round((.28+previous*.72)*(.62+.38*Math.sin(Math.PI*index/Math.max(1,count-1)))*86);
 });
}

export function initializeAudioWave(root,audio) {
 const wave=root.querySelector('[data-rdc-wave]');
 if(!wave)return()=>{};
 const doc=root.ownerDocument,view=doc.defaultView;
 const layers=wave.querySelectorAll('[data-rdc-wave-layer]');
 const motion=view.matchMedia('(prefers-reduced-motion: reduce)');
 const releases=[];let count=0,seed='',visible=false,buffering=false;
 const listen=(node,type,handler)=>{node.addEventListener(type,handler);releases.push(()=>node.removeEventListener(type,handler));};
 const sync=()=>{wave.dataset.rdcWaveRun=String(!audio.paused&&!audio.ended&&!buffering&&!doc.hidden&&visible&&!motion.matches);};
 const render=()=>{
  const width=wave.clientWidth;
  if(!width)return;
  const nextCount=Math.max(24,Math.min(224,Math.round(width/7))),nextSeed=audio.currentSrc||audio.getAttribute('src')||'';
  if(nextCount===count&&nextSeed===seed)return;
  count=nextCount;seed=nextSeed;
  const bars=waveHeights(count,seed).map((height,index)=>{
   const bar=doc.createElement('span');bar.className='rdc-player__bar';
   bar.style.setProperty('--rdc-bar-height',`${height}%`);
   bar.style.setProperty('--rdc-bar-delay',`${(-1700*2.2*index/count).toFixed(2)}ms`);
   return bar;
  });
  for(const layer of layers)layer.replaceChildren(...bars.map(bar=>bar.cloneNode()));
 };
 releases.push(observeSizes(view,[wave],render));
 if(view.IntersectionObserver){
  const observer=new view.IntersectionObserver(entries=>{visible=entries[0].isIntersecting;sync();});
  observer.observe(wave);releases.push(()=>observer.disconnect());
 }else visible=true;
 for(const type of ['play','playing','pause','ended','waiting','emptied','error'])listen(audio,type,()=>{
  buffering=['waiting','emptied','error'].includes(type);render();sync();
 });
 listen(doc,'visibilitychange',sync);listen(motion,'change',sync);
 render();sync();
 return()=>{for(const release of releases)release();delete wave.dataset.rdcWaveRun;for(const layer of layers)layer.replaceChildren();};
}
