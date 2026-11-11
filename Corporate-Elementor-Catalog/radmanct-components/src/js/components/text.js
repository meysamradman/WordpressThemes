import { reportRuntimeError } from '../../../../shared/frontend/lifecycle.js';

const instances = new WeakMap();

export function initializeText(root) {
  if (instances.has(root)) return instances.get(root);
  const mode = root.dataset.rdctTextReveal;
  let active = true; let cleanup = () => {};
  let headerCleanup;
  if (Object.hasOwn(root.dataset, 'rdctTextHeader')) import('./text/header-state.js').then(({initializeHeaderText}) => { if (active) headerCleanup = initializeHeaderText(root); }).catch((error) => reportRuntimeError('text-header', error));
  if (mode === 'marquee') {
    import('./marquee.js').then(({initializeMarquee}) => {if (active) cleanup = initializeMarquee(root);}).catch((error) => reportRuntimeError('text', error));
  } else if (mode === 'scroll' || mode === 'fill') {
    import('./text-reveal-entry.js').then(({loadTextReveal}) => active ? loadTextReveal(root) : null).then((module) => { if (active && module) cleanup = module.initializeTextReveal(root); }).catch((error) => reportRuntimeError('text', error));
  } else if (mode === 'rotate') {
    Promise.all([import('./text/rotate.js'), root.ownerDocument.defaultView.matchMedia('(prefers-reduced-motion: reduce)').matches ? null : import('../animation.js').then(({loadGsap}) => loadGsap())]).then(([{initializeRotator}, vendor]) => { if (active) cleanup = initializeRotator(root, vendor ?? {}); }).catch((error) => reportRuntimeError('text', error));
  }
  const destroy = () => { if (!active) return; active = false; cleanup(); headerCleanup?.(); instances.delete(root); };
  instances.set(root, destroy); return destroy;
}

export function initializeTexts(scope = document) {
  const roots = scope.matches?.('[data-rdct-text-reveal]') ? [scope] : [...scope.querySelectorAll('[data-rdct-text-reveal]')];
  const cleanups = roots.map(initializeText);
  return () => cleanups.forEach((cleanup) => cleanup());
}
