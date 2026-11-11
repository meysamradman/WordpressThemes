import {createPageLifecycle,reportRuntimeError} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {loadAnimation} from '../../shared/radmanct/src/js/motion.js';
const page=createPageLifecycle(window);
export function mountMotion(root,key,initialize,{query='(prefers-reduced-motion:no-preference)',load=loadAnimation}={}){
 return page.mountRoot(root,key,()=>({}),()=>{
  const view=root.ownerDocument.defaultView,preference=view.matchMedia(query);
  let alive=true,pending=false,release;
  const sync=async()=>{
   if(!preference.matches){release?.();release=undefined;return;}
   if(!alive||pending||release)return;
   pending=true;
   try{const vendor=await load();if(alive&&preference.matches&&root.isConnected)release=initialize(root,vendor);}
   catch(error){if(alive)reportRuntimeError(key,error,view);}
   finally{pending=false;}
  };
  preference.addEventListener('change',sync);sync();
  return()=>{alive=false;preference.removeEventListener('change',sync);release?.();};
 });
}
