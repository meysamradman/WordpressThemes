import { InputControl, SelectControl } from '../../../../shared/admin/controls.js';
import { Specifications } from './specifications.js';

const { createElement: h } = window.wp.element;

export function ProductFields({ data, setData, move, labels, contactAvailable, contactSettings }) {
  const pricing = data.pricing || { mode: 'contact', text: '' };
  const update = (patch) => setData((current) => ({ ...current, pricing: { ...(current.pricing || pricing), ...patch } }));
  return h('div', { className: 'rdct-cnt-editor__product' },
    h('fieldset', { className: 'rdct-cnt-editor__pricing' },
      h('legend', null, labels.productDisplay),
      h('label', { className: 'rdct-cnt-editor__field' }, h('span', { className: 'screen-reader-text' }, labels.productDisplay),
        h(SelectControl, { value: pricing.mode, options: [['contact', labels.contactUs], ['price', labels.price]], onChange: (mode) => update({ mode }) })),
      pricing.mode === 'price' && h('label', { className: 'rdct-cnt-editor__field' }, labels.price,
        h(InputControl, { value: pricing.text, dir: 'auto', maxLength: 80, onChange: (text) => update({ text }) })),
      h('p', { className: 'description' }, pricing.mode === 'price' ? labels.priceHint : labels.contactHint),
      pricing.mode === 'contact' && contactAvailable === false && h('div', { className: 'notice notice-warning inline', role: 'status' },
        h('p', null, labels.contactMissing),
        contactSettings && h('p', null, h('a', { href: contactSettings, target: '_blank', rel: 'noopener noreferrer' }, labels.contactSettings)))),
    h(Specifications, { data, setData, move, labels }));
}
