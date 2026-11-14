import {loadAnimation} from '../motion.js';
import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeMediaParallax} from '../runtime/media-parallax.js';

export function initializePortfolioMedia(archive, load) {
  const view = archive.ownerDocument.defaultView;
  const page = createPageLifecycle(view);
  const mounted = new Map();
  const loadVendor = load || loadAnimation;
  const sync = () => {
    for (const [node, release] of mounted) if (!node.isConnected) { release(); mounted.delete(node); }
    for (const node of archive.querySelectorAll('[data-rdc-prj-mot]')) {
      if (mounted.has(node)) continue;
      mounted.set(node, page.mountRoot(node, 'portfolio-media', () => ({initializeMediaParallax}), (module, frame) => module.initializeMediaParallax(frame, {
        frame, target:frame.querySelector('.rdc-prj__px'), amount:6, minWidth:768,
      }, loadVendor)));
    }
  };
  archive.addEventListener('rdc:arc-upd', sync);
  sync();
  return () => { archive.removeEventListener('rdc:arc-upd', sync); page.dispose(); mounted.clear(); };
}

if (typeof document !== 'undefined') {
  const page = createPageLifecycle(window);
  for (const archive of document.querySelectorAll('.rdc-prj-arc')) {
    page.mountRoot(archive, 'portfolio-media-manager', () => ({}), () => initializePortfolioMedia(archive));
  }
}
