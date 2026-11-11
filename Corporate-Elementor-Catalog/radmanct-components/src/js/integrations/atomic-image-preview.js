import { observeRenderedPreviews } from './atomic-render-preview.js';
import { initializeImages } from '../components/image.js';

export function observeImagePreviews(doc, config) { return observeRenderedPreviews(doc, config, 'image', initializeImages); }
