import {lockPanelScroll} from './scroll-lock.js';
import { bindImageSwipe } from './swipe.js';

let sequence = 0;

export function createImageDialog(doc, className, buttonClass = '') {
  const view = doc.defaultView || window;
  const dialog = doc.createElement('dialog');
  dialog.className = className;
  const controls = doc.createElement('div'); controls.className = `${className}__controls`;
  const buttons = {};
  for (const name of ['previous', 'close', 'next']) {
    const button = doc.createElement('button'); button.type = 'button'; button.className = buttonClass;
    controls.append(button); buttons[name] = button;
  }
  const image = doc.createElement('img'); image.className = `${className}__image`;
  const caption = doc.createElement('p'); caption.className = `${className}__caption`;
  caption.id = `${className}-caption-${++sequence}`;
  const status = doc.createElement('p'); status.className = `${className}__status`;
  status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite');
  dialog.setAttribute('aria-describedby', caption.id);
  dialog.append(controls, image, caption, status); doc.body.append(dialog);
  let links = []; let index = 0; let opener; let labels = {}; let releaseScroll;
  let releaseSwipe = () => {};
  const rtl = () => view.getComputedStyle ? view.getComputedStyle(dialog).direction === 'rtl' : getComputedStyle(dialog).direction === 'rtl';
  const positionLabel = () => (labels.position || '%1$s / %2$s').replace('%1$s', String(index + 1)).replace('%2$s', String(links.length));
  function loaded() {
    if (!dialog.open) return;
    dialog.setAttribute('aria-busy', 'false'); status.textContent = positionLabel();
  }
  function show(position) {
    if (!dialog.open || !links.length) return;
    index = (position + links.length) % links.length;
    const link = links[index];
    image.hidden = false; status.textContent = labels.loading || '';
    dialog.setAttribute('aria-busy', 'true');
    image.alt = link.querySelector('img')?.alt || link.getAttribute('aria-label') || '';
    caption.textContent = link.dataset.caption || '';
    image.src = link.href || link.dataset.actionUrl;
    buttons.previous.disabled = buttons.next.disabled = links.length < 2;
    if (image.complete && image.naturalWidth > 0) loaded();
  }
  image.addEventListener('load', loaded);
  image.addEventListener('error', () => {
    if (!dialog.open) return;
    image.hidden = true; dialog.setAttribute('aria-busy', 'false'); status.textContent = labels.error || '';
  });
  buttons.close.addEventListener('click', () => dialog.close());
  buttons.previous.addEventListener('click', () => show(index - 1));
  buttons.next.addEventListener('click', () => show(index + 1));
  dialog.addEventListener('keydown', (event) => {
    if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') return;
    event.preventDefault(); show(index + ((event.key === 'ArrowRight') !== rtl() ? 1 : -1));
  });
  dialog.addEventListener('click', (event) => {
    if (event.target !== dialog) return;
    const rect = dialog.getBoundingClientRect();
    if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();
  });
  dialog.addEventListener('close', () => {
    releaseSwipe(); image.removeAttribute('src'); status.textContent = '';
    dialog.setAttribute('aria-busy', 'false');
    if (opener?.isConnected) opener.focus({preventScroll:true});
    releaseScroll?.();releaseScroll=undefined;
    opener = null; links = [];
  });
  const pagehide=()=>{if(dialog.open)dialog.close();};view.addEventListener('pagehide',pagehide);
  return {
    get opener() { return opener; },
    get isOpen() { return dialog.open; },
    close() { if (dialog.open) dialog.close(); },
    destroy() {if(dialog.open)dialog.close();releaseSwipe();releaseScroll?.();releaseScroll=undefined;view.removeEventListener('pagehide',pagehide);dialog.remove();},
    open(link, group, options) {
      const position = group.indexOf(link);
      if (position < 0) return false;
      if (!dialog.open) {
        releaseScroll=lockPanelScroll(doc,dialog);
        try { dialog.showModal(); } catch { releaseScroll();releaseScroll=undefined;return false; }
      }
      opener = link; links = group; labels = options;
      for (const name of ['previous', 'close', 'next']) buttons[name].textContent = labels[name] || '';
      dialog.setAttribute('aria-label', labels.label || '');
      show(position); buttons.close.focus({preventScroll:true}); releaseSwipe();
      releaseSwipe = bindImageSwipe(image, (step) => show(index + step), { enabled: () => dialog.open && links.length > 1, rtl });
      return true;
    },
  };
}
