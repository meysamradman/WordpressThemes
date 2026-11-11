import {reportRuntimeError} from '../../../../shared/frontend/lifecycle.js';

const effects = {roll: () => import('./hover/roll.js')};

export function mountMenuHover(root) {
  const load = effects[root.dataset.menuHover];
  if (!load) return () => {};
  let active = true;
  let cleanup;
  load().then(({mount}) => { if (active && root.isConnected) cleanup = mount(root); }).catch((error) => {
    if (active && root.isConnected) reportRuntimeError('menu-hover', error, root.ownerDocument.defaultView);
  });
  return () => { if (!active) return; active = false; cleanup?.(); };
}
