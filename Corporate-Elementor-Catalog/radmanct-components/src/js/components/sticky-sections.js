import {loadAnimation} from '../animation.js';
import {reportRuntimeError} from '../../../../shared/frontend/lifecycle.js';

const instances = new WeakMap();
const modes = {stack: () => import('./sticky-sections/stack.js'), zoom: () => import('./sticky-sections/stack.js'), overlap: () => import('./sticky-sections/panels.js'), services: root => root.dataset.transition === 'slide-scale' ? import('./sticky-sections/slides.js') : import('./sticky-sections/panels.js'), split: () => import('./sticky-sections/split.js')};
export function initializeStickySections(root) {
  if (!root || !modes[root.dataset.mode] || root.ownerDocument.defaultView.elementorFrontend?.isEditMode?.()) return () => {};
  if (instances.has(root)) return instances.get(root);
  let alive = true; let cleanup; let pending = false;
  const width = Number.isFinite(Number(root.dataset.minWidth)) ? Math.max(0,Math.min(1920,Number(root.dataset.minWidth))) : 992;
  const media = root.ownerDocument.defaultView.matchMedia(`(min-width: ${width}px) and (prefers-reduced-motion: no-preference)`);
  const start = () => {
    if (!alive || pending || cleanup || !media.matches) return;
    pending = true;
  Promise.all([modes[root.dataset.mode](root),loadAnimation()]).then(([effect,animation]) => {if (alive && root.isConnected && media.matches) cleanup = effect.mount(root, animation);}).catch((error) => reportRuntimeError('sticky-sections',error)).finally(()=>{pending=false;});
  };
  media.addEventListener('change',start); start();
  const release = () => {if (!alive) return; alive = false; media.removeEventListener('change',start); cleanup?.(); instances.delete(root);};
  instances.set(root,release); return release;
}
export function initializeStickySectionGroups(scope = document) {
  const roots = scope.matches?.('[data-rdct-sticky-sections]') ? [scope] : [...scope.querySelectorAll('[data-rdct-sticky-sections]')];
  const cleanups = roots.map(initializeStickySections);
  return () => cleanups.forEach((cleanup) => cleanup());
}
