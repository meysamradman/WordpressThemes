import { createPageLifecycle } from '../../../shared/frontend/lifecycle.js';

const page = createPageLifecycle(window);
const features = [
  ['header-contrast', '[data-rdct-header-colors]', () => import('./components/header-contrast.js'), 'initializeHeaderContrasts'],
  ['sticky-header', '[data-rdct-sticky-start]', () => import('./components/sticky-header.js'), 'initializeStickyHeaders'],
  ['menus', '[data-rdct-menu]', () => import('./navigation/manager.js'), 'observeMenus'],
  ['gallery', '[data-rdct-gallery]', () => import('./gallery.js'), 'initializeGalleries'],
  ['scroll-progress', '#rdct-scroll-progress', () => import('./scroll-progress.js'), 'initializeScrollProgress'],
];
for (const [key, selector, load, method] of features) {
  if (document.querySelector(selector)) page.mount(load, (module) => module[method](), key);
}


if ([...document.querySelectorAll('.rdct-cnt table')].some((table) => !table.closest('.rdct-tb'))) {
  page.mount(() => import('./components/content-tables.js'), ({ initializeContentTables }) => initializeContentTables(), 'content-tables');
}
for (const root of document.querySelectorAll('[data-rdct-transparent-header]')) {
  page.mountRoot(root, 'transparent-header', () => import('./components/transparent-header.js'), ({ initializeTransparentHeader }, node) => initializeTransparentHeader(node));
}

const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
const reveal = !reducedMotion && document.body.classList.contains('rdct-reveal-enabled') && document.querySelector('[data-rdct-reveal]');
const pinnedHorizontal = [...document.querySelectorAll('[data-rdct-motion]')].some((node) => { try { const options = JSON.parse(node.dataset.rdctMotion); return options.horizontal === true || options.horizontal === 'yes'; } catch { return false; } });
const smooth = !pinnedHorizontal && document.body.classList.contains('rdct-smooth-scroll-enabled') && document.getElementById('rdct-scroll-wrapper');
const containerMotion = document.querySelector('[data-rdct-motion]');

async function initializeEffects() {
  if (smooth) {
    let smoothTask;
    await page.mount(() => ({}), () => {
      const query = matchMedia('(min-width:48rem) and (hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)');
      const load = () => {
        if (query.matches && !smoothTask) smoothTask = page.mount(() => import('./smooth-scroll.js'), ({ initializeSmoothScroll }) => initializeSmoothScroll(), 'smooth-scroll');
      };
      query.addEventListener('change', load); load();
      return () => query.removeEventListener('change', load);
    }, 'smooth-preferences').ready;
    if (smoothTask) await smoothTask.ready;
  }
  if (containerMotion) page.mount(() => import('./container-motion-manager.js'), ({ observeContainerMotion }) => observeContainerMotion(), 'container-motion');
  if (reveal) page.mount(() => import('./motion.js'), ({ initializeMotion }) => initializeMotion(), 'reveal');
}
if (smooth || reveal || containerMotion) initializeEffects();

if (document.getElementById('rdct-cursor')) {
  page.mount(() => ({}), () => {
    const query = matchMedia('(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)');
    let pending = false; let loaded = false;
    const load = () => {
      if (!query.matches || pending || loaded) return;
      pending = true;
      const task = page.mount(() => import('./cursor.js'), ({ initializeCursor }) => { initializeCursor(); loaded = true; }, 'cursor');
      task.ready?.then(() => { pending = false; });
    };
    query.addEventListener('change', load); load();
    return () => query.removeEventListener('change', load);
  }, 'cursor-preferences');
}
