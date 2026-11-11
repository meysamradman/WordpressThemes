import { initializeCarousel as initialize } from './engine.js';
import { responsiveBreakpoints } from '../responsive.js';
import { reportRuntimeError } from '../../../../../shared/frontend/lifecycle.js';

export function initializeCarousel(root, load = () => import('./stack-effect.js'), featureModules = []) {
  let options;
  try { options = JSON.parse(root.dataset.options); } catch { return () => {}; }
  const view = root.ownerDocument.defaultView;
  const {tablet} = responsiveBreakpoints(options);
  const media = view.matchMedia(`(min-width: ${tablet}px) and (prefers-reduced-motion: no-preference)`);
  let cleanup, state, effect, loading, generation = 0, alive = true;
  const mount = () => {
    if (!alive) return;
    const current = ++generation;
    if (cleanup) { state = cleanup.snapshot?.(); cleanup(); cleanup = null; }
    if (!media.matches) { cleanup = initialize(root, [], state, featureModules); return; }
    if (effect) { cleanup = initialize(root, [effect], state, featureModules); return; }
    loading ||= load();
    loading.then((module) => {
      effect = module.EffectCreative;
      if (alive && root.isConnected !== false && generation === current && media.matches) cleanup = initialize(root, [effect], state, featureModules);
    }).catch((error) => {
      loading = null;
      if (!alive || root.isConnected === false || generation !== current) return;
      reportRuntimeError('carousel-stack',error);
      cleanup = initialize(root, [], state, featureModules);
    });
  };
  media.addEventListener('change', mount);
  mount();
  return () => { if (!alive) return; alive = false; media.removeEventListener('change', mount); cleanup?.(); };
}
