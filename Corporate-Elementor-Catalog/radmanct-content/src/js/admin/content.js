import { InputControl } from '../../../../shared/admin/controls.js';

const { createElement: h, createRoot, useState, useEffect, useRef } = window.wp.element;
const config = window.rdctContent;
const labels = config.labels;
const button = (text, onClick, disabled = false) => h('button', { type: 'button', className: 'button', onClick, disabled }, text);

function App() {
  const [data, setData] = useState(config.data);
  const [images, setImages] = useState(config.images);
  const [ProductFields, setProductFields] = useState(null);
  const [specsError, setSpecsError] = useState(false);
  useEffect(() => {
    let current = true;
    if (config.product) import('./product-fields.js').then((module) => { if (current) setProductFields(() => module.ProductFields); }).catch(() => { if (current) setSpecsError(true); });
    return () => { current = false; };
  }, []);
  const field = useRef(null);
  const frame = useRef(null);
  const currentData = useRef(data);
  currentData.current = data;
  const serialized = JSON.stringify(data);
  useEffect(() => { field.current?.dispatchEvent(new Event('change', { bubbles: true })); }, [serialized]);
  useEffect(() => () => { frame.current?.off(); frame.current?.close(); }, []);
  function selectImages() {
    if (frame.current) { frame.current.open(); return; }
    const picker = window.wp.media({ title: labels.gallery, library: { type: 'image' }, button: { text: labels.addImages }, multiple: true });
    frame.current = picker;
    picker.on('open', () => {
      const selection = picker.state().get('selection');
      selection.reset();
      currentData.current.gallery.forEach((id) => selection.add(window.wp.media.attachment(id)));
    });
    picker.on('select', () => {
      const selected = picker.state().get('selection').toJSON().filter((image) => image.type === 'image').slice(0, 20);
      setImages((current) => ({ ...current, ...Object.fromEntries(selected.map((image) => [image.id, image.sizes?.thumbnail?.url || image.url])) }));
      setData((current) => ({ ...current, gallery: selected.map((image) => image.id) }));
    });
    picker.open();
  }
  function move(key, index, offset) {
    setData((current) => {
      const values = [...current[key]];
      if (index + offset < 0 || index + offset >= values.length) return current;
      values.splice(index + offset, 0, values.splice(index, 1)[0]);
      return { ...current, [key]: values };
    });
  }
  return h('div', { className: 'rdct-cnt-editor' },
    h('input', { ref: field, type: 'hidden', name: 'radman_content_data', value: serialized }),
    config.product && h('section', { className: 'rdct-cnt-editor__section' }, h('label', { className: 'rdct-cnt-editor__field' }, labels.code,
      h(InputControl, { dir: 'auto', value: data.code, maxLength: 40, onChange: (value) => setData({ ...data, code: value }) }))),
    h('section', { className: 'rdct-cnt-editor__section', 'aria-labelledby': 'rdct-gallery-title' },
    h('div', { className: 'rdct-cnt-editor__heading' }, h('h3', { id: 'rdct-gallery-title' }, labels.gallery), button(labels.addImages, selectImages)),
    h('p', { className: 'description' }, labels.galleryLimit),
    !data.gallery.length && h('p', null, labels.empty),
    h('ul', { className: 'rdct-cnt-editor__gallery' }, ...data.gallery.map((id, index) => h('li', { key: id },
      images[id] ? h('img', { src: images[id], alt: '', width: 120, height: 100 }) : h('span', null, `#${id}`),
      h('div', { className: 'rdct-cnt-editor__actions' },
        button(labels.up, () => move('gallery', index, -1), index === 0), button(labels.down, () => move('gallery', index, 1), index === data.gallery.length - 1),
        button(labels.remove, () => setData((current) => ({ ...current, gallery: current.gallery.filter((value) => value !== id) })))))))),
    config.product && ProductFields && h(ProductFields, { data, setData, move, labels, contactAvailable: config.contactAvailable, contactSettings: config.contactSettings }),
    specsError && h('p', { role: 'alert' }, labels.productError));
}

const root = document.getElementById('rdct-content-root');
if (root) createRoot(root).render(h(App));
