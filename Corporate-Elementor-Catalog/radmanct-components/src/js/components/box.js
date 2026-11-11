import {initializePointerTilt} from '../../../../shared/frontend/pointer-tilt.js';

const instances = new WeakMap();
export function initializeBox(root) {
  if (instances.has(root)) return instances.get(root);
  if (root.dataset.rdctBoxMotion !== 'tilt') return () => {};
  const release = initializePointerTilt(root, root, {amount: () => 5, xProperty: '--rdct-box-rotate-x', yProperty: '--rdct-box-rotate-y'});
  let alive = true;
  const cleanup = () => { if (!alive) return; alive = false; release(); instances.delete(root); };
  instances.set(root, cleanup); return cleanup;
}
