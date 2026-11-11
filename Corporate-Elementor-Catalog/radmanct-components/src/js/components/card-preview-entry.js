import { lifecycle } from '../runtime.js';
import { observeCardPreviews } from './card-preview.js';

lifecycle.mount(() => ({}), () => observeCardPreviews(document), 'card-previews');
