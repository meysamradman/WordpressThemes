import { observeRenderedPreviews } from './atomic-render-preview.js';
import { initializeFlipBox } from '../components/flipbox.js';
export function observeFlipBoxPreviews(doc, config) { return observeRenderedPreviews(doc, config, 'flipbox', (marker) => initializeFlipBox(marker.querySelector('[data-rdct-flipbox]'))); }
