import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeGalleries } from '../radmanct/src/js/gallery.js';
import { openImageLightbox } from '../radmanct-components/src/js/components/image/lightbox.js';

class Element {
  constructor(tag = 'div') {
    this.tagName = tag; this.children = []; this.dataset = {}; this.style = {}; this.attributes = new Map(); this.listeners = new Map(); this.isConnected = true; this.hidden = false;
  }
  append(...children) { children.forEach((node) => { node.parent = this; this.children.push(node); }); }
  setAttribute(key, value) { this.attributes.set(key, value); }
  hasAttribute(key) { return this.attributes.has(key); }
  getAttribute(key) { return this.attributes.get(key); }
  removeAttribute(key) { this.attributes.delete(key); if (key === 'src') this.src = ''; }
  addEventListener(key, callback) { this.listeners.set(key, [...(this.listeners.get(key) || []), callback]); }
  fire(key, fields = {}) { const event = { target: this, button: 0, defaultPrevented: false, preventDefault() { this.defaultPrevented = true; }, ...fields }; (this.listeners.get(key) || []).forEach((callback) => callback(event)); return event; }
  focus() { document.activeElement = this; }
  closest() { return this.tagName === 'a' ? this : this.parent?.closest(); }
  querySelector(selector) { return this.querySelectorAll(selector)[0]; }
  querySelectorAll(selector) {
    const nodes = this.children.flatMap((child) => [child, ...child.querySelectorAll('*')]);
    return selector === '*' ? nodes : nodes.filter((node) => selector === 'a[href]' ? node.tagName === 'a' : selector === 'img' ? node.tagName === 'img' : node.className === selector.slice(1));
  }
  getBoundingClientRect() { return { left: 10, right: 500, top: 10, bottom: 500 }; }
}
class Dialog extends Element {
  constructor() { super('dialog'); this.open = false; }
  showModal() { if (this.fail) throw new Error('Unavailable'); this.open = true; }
  close() { this.open = false; this.fire('close'); }
}
function fixture() {
  const gallery = new Element();
  gallery.dataset = { close: 'Close', previous: 'Previous', next: 'Next', loading: 'Loading', error: 'Image error', position: 'Image %1$s of %2$s' };
  gallery.setAttribute('aria-label', 'Gallery');
  const links = Array.from({ length: 3 }, (_, i) => {
    const link = new Element('a'); link.href = `/image-${i}.jpg`; link.dataset.caption = `Caption ${i}`;
    const image = new Element('img'); image.alt = `Alt ${i}`; link.append(image); gallery.append(link); return link;
  });
  const body = new Element('body'); body.style.overflow = 'auto';
  globalThis.document = { body, createElement: (tag) => tag === 'dialog' ? new Dialog() : new Element(tag), querySelectorAll: (selector) => selector === '[data-rdct-gallery]' ? [gallery] : body.querySelectorAll(selector) };
  globalThis.window = new Element(); window.HTMLDialogElement = Dialog;
  globalThis.HTMLDialogElement = Dialog;
  globalThis.getComputedStyle = () => ({ direction: 'ltr' });
  initializeGalleries();
  const dialog = body.children[0];
  return { gallery, links, dialog, image: dialog.children[1], status: dialog.children[3], close: dialog.children[0].children[1], body };
}

test('gallery loads only the selected large image and restores scrolling, focus and state', () => {
  const { gallery, links, dialog, image, status, close, body } = fixture();
  initializeGalleries();
  assert.equal(body.children.length, 1);
  assert.equal(image.src, undefined);
  assert.equal(gallery.fire('click', { target: links[0] }).defaultPrevented, true);
  assert.equal(dialog.open, true); assert.equal(body.style.overflow, 'hidden');
  assert.equal(image.src, '/image-0.jpg'); assert.equal(status.textContent, 'Loading');
  image.fire('load'); assert.equal(status.textContent, 'Image 1 of 3');
  image.fire('error'); assert.equal(image.hidden, true); assert.equal(status.textContent, 'Image error');
  dialog.children[0].children[2].fire('click');
  assert.equal(image.hidden, false); assert.equal(image.src, '/image-1.jpg');
  close.fire('click');
  assert.equal(image.src, ''); assert.equal(body.style.overflow, 'auto'); assert.equal(document.activeElement, links[0]);
});

test('gallery navigation follows RTL and repeated pagehide closes restored sessions', () => {
  const { gallery, links, dialog, image, body } = fixture();
  gallery.fire('click', { target: links[0] });
  globalThis.getComputedStyle = () => ({ direction: 'rtl' });
  dialog.fire('keydown', { key: 'ArrowRight' });
  assert.equal(image.src, '/image-2.jpg');
  window.fire('pagehide'); assert.equal(dialog.open, false);
  gallery.fire('click', { target: links[1] }); window.fire('pagehide');
  assert.equal(dialog.open, false); assert.equal(body.style.overflow, 'auto');
});

test('failed dialogs and modified or download links retain native navigation', () => {
  const { gallery, links, dialog, body } = fixture();
  for (const fields of [{ ctrlKey: true }, { button: 1 }, { defaultPrevented: true }]) {
    gallery.fire('click', { target: links[0], ...fields }); assert.equal(dialog.open, false);
  }
  links[0].setAttribute('download', '');
  assert.equal(gallery.fire('click', { target: links[0] }).defaultPrevented, false);
  links[0].removeAttribute('download'); links[0].target = '_blank';
  assert.equal(gallery.fire('click', { target: links[0] }).defaultPrevented, false);
  links[0].target = ''; dialog.fail = true;
  assert.equal(gallery.fire('click', { target: links[0] }).defaultPrevented, false);
  assert.equal(body.style.overflow, 'auto');
});

test('Image uses the shared dialog for grouped loading, failure recovery and scroll/focus restoration', () => {
  const { links, body } = fixture();
  document.defaultView = window;
  window.getComputedStyle = () => ({ direction: 'rtl' });
  const query = document.querySelectorAll;
  document.querySelectorAll = (selector) => selector === '[data-rdct-image-lightbox]' ? links : query(selector);
  for (const link of links) {
    link.ownerDocument = document;
    Object.assign(link.dataset, { rdctImageLightbox: 'images', loading: 'Loading', error: 'Image error', position: 'Image %1$s of %2$s', close: 'Close', next: 'Next', previous: 'Previous' });
  }
  links[2].dataset.rdctImageLightbox = 'another-group';
  const state = openImageLightbox(links[0]);
  const dialog = body.children[1]; const image = dialog.children[1]; const status = dialog.children[3];
  assert.equal(body.style.overflow, 'hidden'); assert.equal(image.alt, 'Alt 0');
  assert.equal(status.textContent, 'Loading'); image.fire('load');
  assert.equal(status.textContent, 'Image 1 of 2');
  image.fire('error'); assert.equal(image.hidden, true); assert.equal(status.textContent, 'Image error');
  dialog.fire('keydown', { key: 'ArrowRight' });
  assert.equal(image.src, links[1].href); assert.equal(image.hidden, false);
  state.close(); assert.equal(body.style.overflow, 'auto'); assert.equal(document.activeElement, links[0]);
  assert.equal(state.opener, null); assert.equal(image.src, '');
  assert.equal(openImageLightbox(links[2]), state);
  image.complete = true; image.naturalWidth = 800;
  openImageLightbox(links[2]); assert.equal(status.textContent, 'Image 1 of 1');
  assert.equal(dialog.children[0].children[0].disabled, true);
  window.fire('pagehide'); assert.equal(dialog.open, false); assert.equal(body.style.overflow, 'auto');
});
