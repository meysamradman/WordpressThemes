import { createImageDialog } from '../../../shared/frontend/image-dialog.js';

export function initializeGalleries() {
  if (!window.HTMLDialogElement || !HTMLDialogElement.prototype.showModal) return;
  document.querySelectorAll('[data-rdct-gallery]').forEach((gallery) => {
    if (gallery.dataset.enhanced) return;
    const links = [...gallery.querySelectorAll('a[href]')];
    if (!links.length) return;
    gallery.dataset.enhanced = 'true';
    const dialog = createImageDialog(document, 'rdct-gal-dialog', 'rdct-button');
    gallery.addEventListener('click', (event) => {
      const link = event.target.closest('a[href]');
      if (!links.includes(link) || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === '_blank' || link.hasAttribute('download') || dialog.isOpen) return;
      if (dialog.open(link, links, { ...gallery.dataset, label: gallery.getAttribute('aria-label') })) event.preventDefault();
    });
  });
}
