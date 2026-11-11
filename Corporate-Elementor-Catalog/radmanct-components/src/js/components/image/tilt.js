import {initializePointerTilt} from '../../../../../shared/frontend/pointer-tilt.js';

export function initializeImageTilt(root) {
  const target = root.querySelector('.rdct-im__tilt');
  if (!target) return () => {};
  return initializePointerTilt(root, target, {
    amount: () => Math.min(10, Math.max(0, Number(root.dataset.tilt))) * 4,
    xProperty: '--rdct-im-rx', yProperty: '--rdct-im-ry',
  });
}
