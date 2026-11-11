const sizes=new WeakMap(),mutations=new WeakMap();
const deliver=(view,callback,records)=>{try{callback(records);}catch(error){if(view.reportError)view.reportError(error);else view.console?.error(error);}};
export function observeSizes(view,nodes,callback){
 if(!view.ResizeObserver)return()=>{};
 let state=sizes.get(view);
 if(!state){
  state={targets:new Map()};
  state.observer=new view.ResizeObserver(entries=>{
   const batches=new Map();
   for(const entry of entries)for(const subscriber of state.targets.get(entry.target)||[]){
    if(!batches.has(subscriber))batches.set(subscriber,[]);
    batches.get(subscriber).push(entry);
   }
   for(const [subscriber,records] of batches)if(subscriber.alive)deliver(view,subscriber.callback,records);
  });sizes.set(view,state);
 }
 const subscriber={alive:true,callback},targets=[...new Set(nodes.filter(Boolean))];
 for(const node of targets){
  if(!state.targets.has(node)){state.targets.set(node,new Set());state.observer.observe(node);}
  state.targets.get(node).add(subscriber);
 }
 return()=>{
  if(!subscriber.alive)return;subscriber.alive=false;
  for(const node of targets){const owners=state.targets.get(node);owners?.delete(subscriber);if(!owners?.size){state.observer.unobserve?.(node);state.targets.delete(node);}}
  if(!state.targets.size){state.observer.disconnect();sizes.delete(view);}
 };
}
export function observeMutations(view,callback,doc=view.document){
 if(!view.MutationObserver)return()=>{};
 let state=mutations.get(view);
 if(!state){
  state={callbacks:new Set()};
  state.observer=new view.MutationObserver(records=>{for(const subscriber of [...state.callbacks])if(state.callbacks.has(subscriber))deliver(view,subscriber.callback,records);});
  state.observer.observe(doc.documentElement,{childList:true,subtree:true});mutations.set(view,state);
 }
 const subscriber={callback};state.callbacks.add(subscriber);let alive=true;
 return()=>{if(!alive)return;alive=false;state.callbacks.delete(subscriber);if(!state.callbacks.size){state.observer.disconnect();mutations.delete(view);}};
}
