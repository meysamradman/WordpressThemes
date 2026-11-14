const queues=new WeakMap();
// Owners may leave independently; a removed component cannot cancel another's refresh.
export function createRefreshRequest(view,ScrollTrigger){
 let vendors=queues.get(view);if(!vendors){vendors=new WeakMap();queues.set(view,vendors);}
 let queue=vendors.get(ScrollTrigger);
 if(!queue){queue={owners:new Set(),timer:null};vendors.set(ScrollTrigger,queue);}
 const owner={};let alive=true;
 const cancel=()=>{if(queue.timer!==null)view.clearTimeout(queue.timer);queue.timer=null;};
 const run=safe=>{cancel();queue.owners.clear();ScrollTrigger.refresh(safe);};
 return {
  request(){if(!alive)return;queue.owners.add(owner);if(queue.timer===null)queue.timer=view.setTimeout(()=>run(true),100);},
  flush(){if(alive)run();},
  cancel(){queue.owners.delete(owner);if(!queue.owners.size)cancel();},
  dispose(){alive=false;this.cancel();},
 };
}
