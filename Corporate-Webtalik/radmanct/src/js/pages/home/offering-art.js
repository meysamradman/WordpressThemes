import {createPageLifecycle} from '../../runtime/lifecycle.js';
import {initializeOfferingArt} from '../../runtime/art-orbit.js';
export {orbitPoint,initializeOfferingArt} from '../../runtime/art-orbit.js';

if (typeof window !== 'undefined' && typeof document !== 'undefined') {
  const page = createPageLifecycle(window);
  for (const root of document.querySelectorAll('[data-rdc-ofr-art]')) {
    page.mountRoot(root, 'client-offering-art', () => ({}), () => initializeOfferingArt(root));
  }
}
