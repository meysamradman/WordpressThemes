import { createImageDialog } from '../../../../../shared/frontend/image-dialog.js';

const dialogs = new WeakMap();
export function openImageLightbox(opener) {
  const doc = opener.ownerDocument;
  let dialog = dialogs.get(doc);
  if (!dialog) { dialog = createImageDialog(doc, 'rdct-im-dialog'); dialogs.set(doc, dialog); }
  const group = opener.dataset.rdctImageLightbox;
  const links = group ? [...doc.querySelectorAll('[data-rdct-image-lightbox]')].filter((link) => link.dataset.rdctImageLightbox === group) : [opener];
  if (!dialog.open(opener, links, { ...opener.dataset, label: opener.getAttribute('aria-label') })) throw new Error('Image dialog unavailable');
  return dialog;
}
