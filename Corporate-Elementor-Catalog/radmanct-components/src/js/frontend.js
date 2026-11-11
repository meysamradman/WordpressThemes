import { lifecycle, observePageBorders } from './runtime.js';

const features = [
  ['button','[data-rdct-button]',()=>import('./components/button.js'),'initializeButton'],
  ['text-header', '[data-rdct-text-header]', () => import('./components/text/header-state.js'), 'initializeHeaderText'],
  ['sticky-sections','[data-rdct-sticky-sections]',()=>import('./components/sticky-sections.js'),'initializeStickySections'],
  ['particles', '[data-rdct-particles]', () => import('./components/particles.js'), 'initializeParticles'],
  ['text', '[data-rdct-text-reveal=scroll], [data-rdct-text-reveal=fill], [data-rdct-text-reveal=rotate], [data-rdct-text-reveal=marquee]', () => import('./components/text.js'), 'initializeText'],
  ['curtain', '[data-rdct-curtain]', () => import('./components/curtain.js'), 'initializeCurtain'],
  ['box', '[data-rdct-box-motion]', () => import('./components/box.js'), 'initializeBox'],
  ['flipbox', '[data-rdct-flipbox]', () => import('./components/flipbox.js'), 'initializeFlipBox'],
  ['image', '[data-rdct-image]', () => import('./components/image.js'), 'initializeImage'],
  ['carousel', '[data-rdct-carousel]', () => import('./components/carousel-entry.js'), 'initializeCarousel'],
];

for (const [key, selector, load, method] of features) {
  for (const root of document.querySelectorAll(selector)) {
    lifecycle.mountRoot(root, key, load, (module, node) => module[method](node));
  }
}
if (document.querySelector('[data-rdct-border]')) observePageBorders();

let comboboxesMounted = false;
const discoverComboboxes = () => {
  if (comboboxesMounted || !document.querySelector('select[data-rdct-combobox]')) return;
  comboboxesMounted = true;
  lifecycle.mount(() => import('./controls/combobox.js'), ({ observeComboboxes }) => observeComboboxes(), 'comboboxes');
};
lifecycle.mount(() => ({}), () => {
  discoverComboboxes();
  document.addEventListener('rdct:archive-updated', discoverComboboxes);
  return () => document.removeEventListener('rdct:archive-updated', discoverComboboxes);
}, 'combobox-discovery');

let cardPreviewsMounted = false;
const discoverCardPreviews = () => {
  if (cardPreviewsMounted || !document.querySelector('[data-rdct-card-preview]')) return;
  cardPreviewsMounted = true;
  lifecycle.mount(() => import('./components/card-preview-entry.js'), () => {}, 'card-preview-discovery');
};
lifecycle.mount(() => ({}), () => {
  discoverCardPreviews();
  document.addEventListener('rdct:archive-updated', discoverCardPreviews);
  return () => document.removeEventListener('rdct:archive-updated', discoverCardPreviews);
}, 'card-preview-discovery-listener');
