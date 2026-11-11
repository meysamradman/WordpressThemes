const enteredRoots=new WeakSet();
// These paused entrances must keep their start state while scroll scenes refresh.
// A deferred startAt render can otherwise flush during GSAP's revert pass.
const entry={duration:1.6,ease:'power4.out',stagger:.16,lazy:false};
const heroEntry={...entry,duration:1.1,stagger:.07,transformPerspective:800};
export function addEntrance(timeline,nodes,at=0,preset='group',profile=entry){
 const targets=(Array.isArray(nodes)?nodes:[nodes]).filter(Boolean);
 const text=preset==='paragraph';
 if(targets.length)timeline.fromTo(targets,{y:'2.8125rem',rotationX:text?45:0,autoAlpha:0,transformOrigin:'50% 50% 0px'},{y:0,rotationX:0,autoAlpha:1,...profile},at+(text?.2:0));
}
function splitWords(node,entrance=false){
 // Keep large editor texts intact instead of manufacturing hundreds of spans.
 const limit=entrance?80:200;
 if((node.textContent||'').trim().split(/\s+/u,limit+1).length>limit)return {words:[],restore(){}};
 const doc=node.ownerDocument,records=[],words=[];
 const visit=parent=>{
  for(const child of [...(parent.childNodes||[])]){
   if(child.nodeType===3&&child.textContent.trim()){
    const parts=child.textContent.split(/(\s+)/u).filter(Boolean).map(text=>{
     if(/^\s+$/u.test(text))return doc.createTextNode(text);
     const span=doc.createElement('span');span.textContent=text;
     if(entrance)span.className='rdc-tx-en__wd';
     words.push(span);return span;
    });
    child.replaceWith(...parts);records.push({original:child,parts});
   }else if(child.nodeType===1&&!child.matches('script,style,button,[aria-hidden="true"]'))visit(child);
  }
 };
 visit(node);let active=true;
 return {words,restore(){if(!active)return;active=false;for(const {original,parts}of records){parts[0].replaceWith(original);parts.slice(1).forEach(part=>part.remove());}}};
}
export function addHeadingEntrance(timeline,node,at=0,profile=entry){
 const {words,restore}=splitWords(node,true);
 const direction=node.closest?.('[dir]')?.dir||node.ownerDocument.documentElement?.dir||'rtl';
 if(words.length)timeline.fromTo(words,{x:direction==='rtl'?'-1.25rem':'1.25rem',rotationX:10,autoAlpha:0,transformOrigin:'50% 50% 0px'},{x:0,rotationX:0,autoAlpha:1,...profile},at);
 else addEntrance(timeline,node,at,'group',profile);
 return restore;
}
export function addTextFill(timeline,node,at=0,{duration=.3,stagger=.8}={}){
 if(!node)return()=>{};
 const {words,restore}=splitWords(node);
 const targets=words.length?words:node;
 timeline.set(targets,{opacity:.22},at);
 timeline.to(targets,{opacity:1,duration:words.length?duration:duration+stagger,...(words.length?{stagger:{amount:stagger}}:{}),ease:'none',lazy:false},at);
 return restore;
}

export function initializeEntrance(root,{gsap},{viewport=false,manual=false,targets:providedTargets,profile='default'}={}){
 const doc=root.ownerDocument,preference=doc.defaultView.matchMedia('(prefers-reduced-motion:reduce)');
 const gate=!viewport&&!manual?doc.documentElement:null;
 let alive=true,played=enteredRoots.has(root),context,timeline,observer;
 let requested=!viewport&&!manual;
 const targets=providedTargets?.filter(Boolean)||[...root.querySelectorAll('[data-rdc-en]')];
 const wordCleanups=[];
 const timing=profile==='hero'?heroEntry:entry;
 const releaseGate=state=>{
  if(gate?.dataset?.rdcEnt!=='pending')return;
  gate.dataset.rdcEnt=state;doc.dispatchEvent(new Event('rdc:ent-rdy'));
 };
 const clear=()=>{context?.revert();context=undefined;timeline=undefined;wordCleanups.splice(0).forEach(release=>release());releaseGate('fallback');};
 const prepare=()=>{
  if(played||context||preference.matches||!targets.length||gate?.dataset?.rdcEnt==='fallback')return;
  context=gsap.context(()=>{},root);
  try{context.add(()=>{
   timeline=gsap.timeline({paused:true});
   targets.forEach((node,index)=>{
    const at=index*timing.stagger;
    if(node.matches?.('h1,h2,h3,h4,h5,h6,[data-rdc-en="heading"]')){
     // Only the words fade. Their parent must not retain the initial hidden state.
     gsap.set(node,{autoAlpha:1});
     wordCleanups.push(addHeadingEntrance(timeline,node,at,timing));
    }else addEntrance(timeline,node,at,node.matches?.('p,[data-rdc-en="paragraph"]')?'paragraph':'group',timing);
   });
  });}catch(error){clear();throw error;}
 };
 const start=()=>{
  if(!alive||!requested||played||preference.matches||!root.isConnected||doc.getElementById('rdc-pre'))return;
  // If loading already fell back to readable text, never hide it for a late entrance.
  if(gate?.dataset?.rdcEnt==='fallback'){clear();played=true;enteredRoots.add(root);return;}
  if(!targets.length){releaseGate('fallback');return;}
  prepare();
  if(!timeline)return;
  played=true;enteredRoots.add(root);observer?.disconnect();observer=undefined;
  releaseGate('ready');
  timeline.play(0);
 };
 const fallback=()=>{clear();played=true;enteredRoots.add(root);};
 const changed=()=>{if(preference.matches){clear();}else{prepare();start();}};
 if(viewport&&!manual&&!played){
  const Observer=doc.defaultView.IntersectionObserver;
  if(Observer){observer=new Observer(entries=>{if(entries.some(entry=>entry.isIntersecting)){requested=true;start();}},{threshold:.12});observer.observe(root);}
  else requested=true;
 }
 doc.addEventListener('rdc:pre-done',start);if(gate)doc.addEventListener('rdc:ent-fb',fallback);preference.addEventListener('change',changed);prepare();start();
 const release=()=>{if(!alive)return;alive=false;observer?.disconnect();observer=undefined;doc.removeEventListener('rdc:pre-done',start);if(gate)doc.removeEventListener('rdc:ent-fb',fallback);preference.removeEventListener('change',changed);clear();};
 release.play=()=>{requested=true;start();};
 release.finish=()=>{
  if(!alive)return;
  played=true;enteredRoots.add(root);observer?.disconnect();observer=undefined;
  releaseGate('ready');timeline?.progress(1).pause();
 };
 return release;
}
