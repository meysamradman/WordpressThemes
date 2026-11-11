import { reportRuntimeError } from '../../../../shared/frontend/lifecycle.js';

import { observeAtomicComponents } from './atomic-component-preview.js';
import { observeRenderedPreviews } from './atomic-render-preview.js';
import {observeStickySectionPreviews} from './atomic-sticky-sections-preview.js';

const config = window.rdctAtomicComponents;
if (config?.fields && config?.icons) {
  const stickyCleanup = observeStickySectionPreviews(document,config);
  window.addEventListener('pagehide',(event)=>{if (!event.persisted) stickyCleanup();});
  const buttonCleanup=observeRenderedPreviews(document,config,'button',marker=>{let alive=true;let cleanup;import('../components/button.js').then(({initializeButtons})=>{if(alive)cleanup=initializeButtons(marker);}).catch(error=>reportRuntimeError('button-preview',error));return()=>{alive=false;cleanup?.();};});
  window.addEventListener('pagehide',event=>{if(!event.persisted)buttonCleanup();});
  for (const type of ['taxonomy-list', 'archive-search']) {
    const cleanup = observeRenderedPreviews(document, config, type);
    window.addEventListener('pagehide', (event) => { if (!event.persisted) cleanup(); });
  }
  const accordionCleanup = observeRenderedPreviews(document,config,'accordion');
  window.addEventListener('pagehide',event=>{if(!event.persisted)accordionCleanup();});
  const timelineCleanup = observeRenderedPreviews(document,config,'timeline');
  window.addEventListener('pagehide',event=>{if(!event.persisted)timelineCleanup();});
  const testimonialCleanup = observeRenderedPreviews(document,config,'testimonial');
  window.addEventListener('pagehide',(event) => { if (!event.persisted) testimonialCleanup(); });
  const particlesCleanup = observeRenderedPreviews(document,config,'particles',(marker) => { let active = true; let cleanup; import('../components/particles.js').then(({initializeParticleGroups}) => { if (active) cleanup = initializeParticleGroups(marker); }).catch((error) => reportRuntimeError('atomic-preview', error)); return () => { active = false; cleanup?.(); }; });
  window.addEventListener('pagehide',(event) => { if (!event.persisted) particlesCleanup(); });
  const carouselCleanup = observeRenderedPreviews(document,config,'carousel',(marker) => { let active = true; let cleanup; import('../components/carousel-entry.js').then(({initializeCarousels}) => { if (active) cleanup = initializeCarousels(marker); }).catch((error) => reportRuntimeError('atomic-preview', error)); return () => { active = false; cleanup?.(); }; });
  window.addEventListener('pagehide',(event) => { if (!event.persisted) carouselCleanup(); });
  const textCleanup = observeRenderedPreviews(document,config,'text-reveal',(marker) => { let active = true; let cleanup; import('../components/text.js').then(({initializeTexts}) => { if (active) cleanup = initializeTexts(marker); }).catch((error) => reportRuntimeError('atomic-preview', error)); return () => { active = false; cleanup?.(); }; });
  window.addEventListener('pagehide',(event) => { if (!event.persisted) textCleanup(); });
  const iconCleanup = observeRenderedPreviews(document,config,'icon');
  window.addEventListener('pagehide',(event) => { if (!event.persisted) iconCleanup(); });
  const tableCleanup = observeRenderedPreviews(document,config,'table');
  window.addEventListener('pagehide',(event) => { if (!event.persisted) tableCleanup(); });
  const cleanup = observeAtomicComponents(document, config, () => import('../components/text-reveal-entry.js').then(({loadTextReveal}) => loadTextReveal()));

  let imageAlive = true; let imageCleanup; let imagePending = false;
  const loadImage = () => {
    if (imagePending || !document.querySelector('[data-rdct-atomic-image]')) return;
    imagePending = true;
    import('./atomic-image-preview.js').then(({observeImagePreviews}) => { if (imageAlive) { imageWatcher.disconnect(); imageCleanup = observeImagePreviews(document,config); } }).catch((error) => { imagePending = false; reportRuntimeError('atomic-preview', error); });
  };
  const imageWatcher = new MutationObserver(loadImage); imageWatcher.observe(document.documentElement,{childList:true,subtree:true}); loadImage();

  let iconBoxAlive = true; let iconBoxCleanup; let iconBoxPending = false;
  const loadIconBox = () => {
   if (iconBoxPending || !document.querySelector('[data-rdct-atomic-icon-box]')) return;
   iconBoxPending = true;
   import('./atomic-icon-box-preview.js').then(({observeIconBoxPreviews}) => { if (iconBoxAlive) { iconBoxWatcher.disconnect(); iconBoxCleanup = observeIconBoxPreviews(document, config); } }).catch((error) => { iconBoxPending = false; reportRuntimeError('atomic-preview', error); });
  };
  const iconBoxWatcher = new MutationObserver(loadIconBox); iconBoxWatcher.observe(document.documentElement, {childList:true, subtree:true}); loadIconBox();
  let flipAlive = true; let flipCleanup; let flipPending = false;
  const loadFlip = () => {
   if (flipPending || !document.querySelector('[data-rdct-atomic-flipbox]')) return;
   flipPending = true;
   import('./atomic-flipbox-preview.js').then(({observeFlipBoxPreviews}) => { if (flipAlive) { flipWatcher.disconnect(); flipCleanup = observeFlipBoxPreviews(document, config); } }).catch((error) => { flipPending = false; reportRuntimeError('atomic-preview', error); });
  };
  const flipWatcher = new MutationObserver(loadFlip); flipWatcher.observe(document.documentElement, {childList: true, subtree: true}); loadFlip();
  let boxAlive = true; let boxCleanup; let boxPending = false;
  const loadBox = () => {
    if (boxPending || !document.querySelector('[data-rdct-atomic-box]')) return;
    boxPending = true;
    import('./atomic-box-preview.js').then(({ observeBoxPreviews }) => { if (boxAlive) { boxWatcher.disconnect(); boxCleanup = observeBoxPreviews(document, config); } }).catch((error) => { boxPending = false; reportRuntimeError('atomic-preview', error); });
  };
  const boxWatcher = new MutationObserver(loadBox); boxWatcher.observe(document.documentElement, { childList: true, subtree: true }); loadBox();
  let alive = true; let curtainCleanup; let pending = false;
  const loadCurtain = () => {
    if (pending || !document.querySelector('[data-rdct-atomic-curtain]')) return;
    pending = true;
    import('./atomic-curtain-preview.js').then(({ observeCurtainPreviews }) => {
      if (alive) { watcher.disconnect(); curtainCleanup = observeCurtainPreviews(document, config); }
    }).catch((error) => { pending = false; reportRuntimeError('atomic-preview', error); });
  };
  const watcher = new MutationObserver(loadCurtain);
  watcher.observe(document.documentElement, { childList: true, subtree: true }); loadCurtain();
  window.addEventListener('pagehide', (event) => { if (!event.persisted) { alive = false; imageAlive = false; imageWatcher.disconnect(); imageCleanup?.(); boxAlive = false; iconBoxAlive = false; iconBoxWatcher.disconnect(); iconBoxCleanup?.(); flipAlive = false; flipWatcher.disconnect(); flipCleanup?.(); boxWatcher.disconnect(); watcher.disconnect(); cleanup(); curtainCleanup?.(); boxCleanup?.(); } });
}
