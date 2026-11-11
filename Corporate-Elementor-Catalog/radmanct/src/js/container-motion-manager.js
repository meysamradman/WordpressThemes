import { reportRuntimeError } from '../../../shared/frontend/lifecycle.js';

const observers = new WeakMap();
export function observeContainerMotion(scope = document, preview = false) {
  observers.get(scope)?.();
  preview ||= !!scope.defaultView?.rdctMotion;
  const instances = new Map();
  const changed = new Set();
  const replayRoots = new Set();
  const paused = new WeakMap();
  const query = matchMedia('(prefers-reduced-motion: no-preference)');
  const view = scope.defaultView;
  let current = true; let queued = false; let pending = null; let playing = false;
  const initialize = () => pending ||= import('./components/container-motion.js').catch((error) => { pending = null; reportRuntimeError('container-motion', error); return null; });
  async function scan() {
    const play = playing; playing = false;
    const selected = new Set(replayRoots); replayRoots.clear();
    queued = false;
    if (!current) return;
    for (const [root, entry] of instances) {
      if (!query.matches || (entry.minimum && view && view.innerWidth < entry.minimum) || !root.isConnected || changed.has(root) || root.getAttribute('data-rdct-motion') !== entry.key) { entry.cleanup?.(); instances.delete(root); }
    }
    changed.clear();
    if (!query.matches) return;
    for (const root of scope.querySelectorAll('[data-rdct-motion]')) {
      const key = root.getAttribute('data-rdct-motion');
      if (paused.get(root) === key) continue;
      paused.delete(root);
      if (instances.has(root) || !key || key.length > 12000) continue;
      let options;
      try { options = JSON.parse(key); } catch { continue; }
      if (!options || typeof options !== 'object' || Array.isArray(options) || (!options.animate && !options.parallax && !options.reveal && !options.horizontal)) continue;
      const horizontal = options.horizontal === true || options.horizontal === 'yes';
      const width = Number(options.horizontal_min_width ?? 1024);
      const minimum = horizontal ? Math.max(320,Math.min(1920,Number.isFinite(width) ? width : 1024)) : 0;
      if (horizontal && (preview || (view && view.innerWidth < minimum))) continue;
      const entry = { key, cleanup: null, minimum }; instances.set(root, entry);
      const module = await initialize();
      if (!module || !current || !query.matches || !root.isConnected || instances.get(root) !== entry || root.getAttribute('data-rdct-motion') !== key) { if (instances.get(root) === entry) instances.delete(root); continue; }
      entry.cleanup = module.initializeContainerMotion(root, options, preview, play || selected.has(root));
    }
  }
  const queue = () => { if (!queued && current) { queued = true; queueMicrotask(scan); } };
  const observer = new MutationObserver((records) => {
    for (const record of records) {
      let root = (record.type === 'childList' ? record.target : record.target.parentElement)?.closest?.('[data-rdct-motion]');
      while (root) {
        if (instances.has(root) && root.getAttribute('data-rdct-horizontal-owner') !== 'true') changed.add(root);
        root = root.parentElement?.closest('[data-rdct-motion]');
      }
    }
    queue();
  });
  observer.observe(scope.documentElement || scope, { subtree: true, childList: true, attributes: true, attributeFilter: ['data-rdct-motion', 'data-rdct-native-motion'] });
  query.addEventListener('change', queue);
  view?.addEventListener('resize', queue, {passive:true});
  const replay = (event) => {
    if (!current) return;
    if (event?.detail !== undefined && event.detail !== null) {
      const root = event.detail.root;
      if (!root?.isConnected || ![...scope.querySelectorAll('[data-rdct-motion]')].includes(root)) return;
      paused.delete(root);
      replayRoots.add(root); instances.get(root)?.cleanup?.(); instances.delete(root);
    } else {
      for (const root of scope.querySelectorAll('[data-rdct-motion]')) paused.delete(root);
      playing = true; for (const entry of instances.values()) entry.cleanup?.(); instances.clear();
    }
    queue();
  };
  scope.addEventListener('rdct:motion:play', replay);
  const reset = (event) => {
    const root = event?.detail?.root;
    if (!preview || !current || !root?.isConnected || ![...scope.querySelectorAll('[data-rdct-motion]')].includes(root)) return;
    paused.set(root, root.getAttribute('data-rdct-motion'));
    replayRoots.delete(root); instances.get(root)?.cleanup?.(); instances.delete(root);
  };
  if (preview) scope.addEventListener('rdct:motion:reset', reset);
  scan();
  const cleanup = () => {
    if (!current) return;
    current = false; observer.disconnect(); query.removeEventListener('change', queue); scope.removeEventListener('rdct:motion:play', replay);
    scope.removeEventListener('rdct:motion:reset', reset);
    view?.removeEventListener('resize', queue);
    for (const entry of instances.values()) entry.cleanup?.(); instances.clear(); changed.clear(); replayRoots.clear();
    if (observers.get(scope) === cleanup) observers.delete(scope);
  };
  observers.set(scope, cleanup);
  return cleanup;
}
