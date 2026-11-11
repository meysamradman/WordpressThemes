import { observeRenderedPreviews } from './atomic-render-preview.js';
import { initializeBox } from '../components/box.js';

export function observeBoxPreviews(doc, config) {
  return observeRenderedPreviews(doc, config, 'box', (marker) => {
    const root = marker.querySelector('[data-rdct-box-motion]');
    return root ? initializeBox(root) : () => {};
  });
}
