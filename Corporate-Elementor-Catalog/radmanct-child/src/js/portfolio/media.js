import {createPageLifecycle} from '../../../../shared/frontend/lifecycle.js';
import {initializeMediaParallax} from '../../../../shared/frontend/media-parallax.js';

export function initializePortfolioMedia(archive, load) {
  const view = archive.ownerDocument.defaultView;
  const page = createPageLifecycle(view);
  const mounted = new Map();
  let pending;
  const loadVendor = load || (() => {
    if (!pending) {
      const url = new URL(archive.dataset.animationSrc, view.location.href);
      if (url.origin !== view.location.origin || !/\/dist\/js\/animation-vendor-[^/]+\.min\.js$/.test(url.pathname)) return Promise.reject(new Error('Invalid local animation source'));
      pending = import(/* @vite-ignore */ url.href).catch(error => { pending = undefined; throw error; });
    }
    return pending;
  });
  const sync = () => {
    for (const [node, release] of mounted) if (!node.isConnected) { release(); mounted.delete(node); }
    for (const node of archive.querySelectorAll('[data-rdct-project-motion]')) {
      if (mounted.has(node)) continue;
      mounted.set(node, page.mountRoot(node, 'portfolio-media', () => ({initializeMediaParallax}), (module, frame) => module.initializeMediaParallax(frame, {
        frame, target:frame.querySelector('.rdct-client-project__parallax'), amount:6, minWidth:768,
      }, loadVendor)));
    }
  };
  archive.addEventListener('rdct:archive-updated', sync);
  sync();
  return () => { archive.removeEventListener('rdct:archive-updated', sync); page.dispose(); mounted.clear(); };
}

if (typeof document !== 'undefined') {
  const page = createPageLifecycle(window);
  for (const archive of document.querySelectorAll('.rdct-client-portfolio-archive[data-animation-src]')) {
    page.mountRoot(archive, 'portfolio-media-manager', () => ({}), () => initializePortfolioMedia(archive));
  }
}
