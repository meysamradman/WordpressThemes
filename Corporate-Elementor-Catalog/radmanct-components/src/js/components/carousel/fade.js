import { EffectFade } from 'swiper/modules';
import { initializeCarousel as initialize } from './engine.js';

export function initializeCarousel(root, featureModules = []) {
  return initialize(root, [EffectFade], null, featureModules);
}
