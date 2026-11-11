import { reportRuntimeError } from '../../../../shared/frontend/lifecycle.js';
import { loadCarouselModules } from './carousel/modules.js';

const instances = new WeakMap();
export function initializeCarousel(root) {
  if (instances.has(root)) return instances.get(root);
  const marquee = root.dataset.mode === 'marquee';
  let options = {};
  try { if (!marquee) options = JSON.parse(root.dataset.options); } catch { return () => {}; }
  if (!options || typeof options !== 'object' || Array.isArray(options)) return () => {};
  let active = true; let cleanup;
  const stack = root.dataset.mode === 'stack';
  const fade = root.dataset.mode === 'slider' && root.dataset.transition === 'fade';
  const module = stack ? import('./carousel/stack.js') : marquee ? import('./marquee.js') : fade ? import('./carousel/fade.js') : import('./carousel/engine.js');
  Promise.all([module, marquee ? [] : loadCarouselModules(options)]).then(([module, features]) => {
    if (!active || !root.isConnected) return;
    cleanup = marquee ? module.initializeMarquee(root) : stack ? module.initializeCarousel(root, undefined, features) : fade ? module.initializeCarousel(root, features) : module.initializeCarousel(root, [], null, features);
  }).catch((error) => reportRuntimeError('carousel', error));
  const destroy = () => {if (!active) return; active = false; cleanup?.(); instances.delete(root);};
  instances.set(root,destroy); return destroy;
}
export function initializeCarousels(scope = document) {
  const roots = scope.matches?.('[data-rdct-carousel]') ? [scope] : [...scope.querySelectorAll('[data-rdct-carousel]')];
  const cleanups = roots.map(initializeCarousel); return () => cleanups.forEach(cleanup => cleanup());
}
