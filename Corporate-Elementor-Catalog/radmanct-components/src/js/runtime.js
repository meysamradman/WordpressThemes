import { createPageLifecycle } from '../../../shared/frontend/lifecycle.js';

export const lifecycle = createPageLifecycle(window);

let borders;
export function observePageBorders() {
  if (borders) return;
  borders = lifecycle.mount(() => import('./components/hover-border.js'), ({ observeBorders }) => observeBorders(document), 'hover-border');
}
