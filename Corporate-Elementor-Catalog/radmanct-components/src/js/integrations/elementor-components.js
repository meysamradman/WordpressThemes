import { lifecycle, observePageBorders } from '../runtime.js';

let registered = false;
function register() {
  const Base = window.elementorModules?.frontend?.handlers?.Base;
  if (registered || !window.elementorFrontend?.hooks || !Base) return;
  registered = true;
  window.elementorFrontend.hooks.addAction('frontend/element_ready/global', ($element) => {
    const root = $element?.[0];
    if (root && (root.matches('[data-rdct-border]') || root.querySelector('[data-rdct-border]'))) observePageBorders();
  });
  const features = [
    ['button',['rdct-button','rdct-atomic-button'],'[data-rdct-button]',()=>import('../components/button.js'),'initializeButton'],
    ['sticky-sections', ['rdct-sticky-sections','rdct-atomic-sticky-sections',...['stack','overlap','services','zoom','split'].flatMap((mode)=>[`rdct-sticky-${mode}`,`rdct-atomic-sticky-${mode}`])], '[data-rdct-sticky-sections]',()=>import('../components/sticky-sections.js'),'initializeStickySections'],
    ['particles', ['rdct-particles', 'rdct-atomic-particles'], '[data-rdct-particles]', () => import('../components/particles.js'), 'initializeParticles'],
    ['text', ['radman-text-reveal', 'rdct-atomic-text-reveal'], '[data-rdct-text-reveal]', () => import('../components/text.js'), 'initializeText'],
    ['carousel', ['rdct-carousel', 'rdct-atomic-carousel', ...['cards','logos','slider','marquee','stack','testimonials'].flatMap((profile) => [`rdct-carousel-${profile}`, `rdct-atomic-carousel-${profile}`])], '[data-rdct-carousel]', () => import('../components/carousel-entry.js'), 'initializeCarousel'],
    ['image', ['rdct-image', 'rdct-atomic-image'], '[data-rdct-image]', () => import('../components/image.js'), 'initializeImage'],
    ['curtain', ['rdct-curtain', 'rdct-atomic-curtain'], '[data-rdct-curtain]', () => import('../components/curtain.js'), 'initializeCurtain'],
    ['box', ['rdct-box', 'rdct-atomic-box'], '[data-rdct-box-motion]', () => import('../components/box.js'), 'initializeBox'],
    ['flipbox', ['rdct-flipbox', 'rdct-atomic-flipbox'], '[data-rdct-flipbox]', () => import('../components/flipbox.js'), 'initializeFlipBox'],
  ];
  for (const [key, names, selector, load, method] of features) {
    class Handler extends Base {
      onInit() {
        super.onInit();
        this.release?.();
        const root = this.$element[0].querySelector(selector);
        if (root) this.release = lifecycle.mountRoot(root, key, load, (module, node) => module[method](node));
      }
      onDestroy() { this.release?.(); super.onDestroy(); }
    }
    for (const name of names) window.elementorFrontend.hooks.addAction(`frontend/element_ready/${name}.default`, ($element) => window.elementorFrontend.elementsHandler.addHandler(Handler, { $element }));
  }
}

register();
window.addEventListener('elementor/frontend/init', register, { once: true });
