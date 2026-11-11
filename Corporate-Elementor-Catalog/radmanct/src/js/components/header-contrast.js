import { observeHeaderTone } from '../../../../shared/frontend/header-tone.js';

export function initializeHeaderContrasts(scope = document) {
  const cleanups = [...scope.querySelectorAll('[data-rdct-header-colors]')].map(header => {
    const logos = [...header.querySelectorAll('[data-rdct-header-logo]')].filter(root => !root.closest('[data-rdct-header-color="fixed"]')).map(root => ({ root, variants: [...root.querySelectorAll('[data-rdct-logo-state]')] }));
    const release = observeHeaderTone(header, ({ tone, sticky, style }) => {
      const state = sticky && style.getPropertyValue('--rdct-header-tone-sticky').trim() ? 'sticky' : tone;
      if (state) header.dataset.rdctHeaderCurrent = state; else header.removeAttribute('data-rdct-header-current');
      for (const { root, variants } of logos) {
        let selected = sticky && variants.some(node => node.dataset.rdctLogoState === 'sticky') ? 'sticky' : variants.some(node => node.dataset.rdctLogoState === tone) ? tone : 'base';
        const image = variants.find(node => node.dataset.rdctLogoState === selected);
        if (image && !image.hasAttribute('src')) image.src = image.dataset.rdctLogoSrc;
        if (image && (!image.complete || !image.naturalWidth)) selected = 'base';
        if (root.dataset.rdctLogoActive === selected) continue;
        root.dataset.rdctLogoActive = selected;
        for (const node of variants) {
          const visible = node.dataset.rdctLogoState === selected;
          node.hidden = !visible;
        }
      }
    });
    return () => {
      release(); header.removeAttribute('data-rdct-header-current');
      for (const { root, variants } of logos) { root.removeAttribute('data-rdct-logo-active'); for (const node of variants) node.hidden = true; }
    };
  });
  return () => cleanups.forEach(cleanup => cleanup());
}
