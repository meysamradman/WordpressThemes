import { observeHeaderTone } from '../../../../../shared/frontend/header-tone.js';
const instances = new WeakMap();

export function initializeHeaderText(root) {
  if (instances.has(root)) return instances.get(root);
  const header = root.closest('.rdct-bld--header, [data-rdct-header]');
  if (!header) return () => {};
  const release = observeHeaderTone(header, ({ tone, sticky }) => {
    const style = root.ownerDocument.defaultView.getComputedStyle(root);
    const state = sticky && style.getPropertyValue('--rdct-text-header-sticky').trim() ? 'sticky' : tone && style.getPropertyValue(`--rdct-text-header-${tone}`).trim() ? tone : '';
    if (state) root.dataset.headerCurrent = state; else root.removeAttribute('data-header-current');
  });
  const destroy = () => { release(); root.removeAttribute('data-header-current'); instances.delete(root); };
  instances.set(root, destroy);
  return destroy;
}
