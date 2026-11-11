import {loadGsap} from '../animation.js';
import { reportRuntimeError } from '../../../../shared/frontend/lifecycle.js';
const instances=new WeakMap();
export function initializeButton(root) {
  if(instances.has(root))return instances.get(root);
  const motion=root.dataset.textEffect?.startsWith('liquid-') || root.dataset.magnetic==='true' || root.dataset.iconMagnetic==='true';
  const media=motion ? root.ownerDocument.defaultView.matchMedia('(hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)') : null;
  let alive=true;let generation=0;let releases=[];let actionRelease;
  if(root.dataset.action && root.dataset.action!=='link')import('./button/actions.js').then(module=>{if(alive && root.isConnected)actionRelease=module.initializeActions(root);}).catch(error=>reportRuntimeError('button-actions',error));
  const stop=()=>{generation++;releases.forEach(release=>release());releases=[];};
  const mount=()=>{
    stop();if(!media?.matches)return;const current=generation;
    const engine=loadGsap();
    const load=(promise,method)=>Promise.all([engine,promise]).then(([vendor,module])=>{if(alive && root.isConnected && current===generation)releases.push(module[method](root,vendor));}).catch(error=>reportRuntimeError('button',error));
    if(root.dataset.textEffect?.startsWith('liquid-'))load(import('./button/liquid.js'),'initializeLiquid');
    if(root.dataset.magnetic==='true' || root.dataset.iconMagnetic==='true')load(import('./button/magnetic.js'),'initializeMagnetic');
  };
  const cleanup=()=>{alive=false;stop();actionRelease?.();media?.removeEventListener('change',mount);instances.delete(root);};
  instances.set(root,cleanup);media?.addEventListener('change',mount);mount();return cleanup;
}
export function initializeButtons(scope){const releases=[...scope.querySelectorAll('[data-rdct-button]')].map(initializeButton);return()=>releases.forEach(release=>release());}
