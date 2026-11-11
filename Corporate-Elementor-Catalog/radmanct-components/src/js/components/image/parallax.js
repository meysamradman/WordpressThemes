import {loadAnimation} from '../../animation.js';
import {initializeMediaParallax} from '../../../../../shared/frontend/media-parallax.js';

export function initializeImageParallax(root, load = loadAnimation) {
  const frame = root.querySelector(':scope > .rdct-im__float > .rdct-im__layout > .rdct-im__tilt > .rdct-im__figure > .rdct-im__visual > .rdct-im__parallax-frame');
  return initializeMediaParallax(root, {
    frame, target:frame?.querySelector('.rdct-im__parallax-media'),
    amount:root.dataset.parallaxAmount, minWidth:root.dataset.parallaxMinWidth, direction:root.dataset.parallaxDirection,
  }, load);
}
