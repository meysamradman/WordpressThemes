import { loadAnimation } from '../animation.js';

export async function loadTextReveal(root) {
  const lines = root?.dataset.rdctTextReveal === 'scroll' && root.dataset.revealSplit === 'lines';
  const [vendor, engine] = await Promise.all([loadAnimation(), lines ? import('./text/lines.js') : import('./text/reveal.js')]);
  const initialize = lines ? engine.initializeTextLines : engine.initializeTextReveal;
  return { initializeTextReveal: (target) => initialize(target, vendor) };
}
