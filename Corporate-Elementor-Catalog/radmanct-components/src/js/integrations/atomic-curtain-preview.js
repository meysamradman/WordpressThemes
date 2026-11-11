import { initializeCurtains } from '../components/curtain.js';
import { observeRenderedPreviews } from './atomic-render-preview.js';
export function observeCurtainPreviews(doc, config) { return observeRenderedPreviews(doc, config, 'curtain', (marker) => initializeCurtains(marker)); }
