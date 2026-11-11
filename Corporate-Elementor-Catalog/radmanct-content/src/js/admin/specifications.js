import '../../css/admin/specifications.css';
import { InputControl, TextareaControl } from '../../../../shared/admin/controls.js';

const { createElement: h } = window.wp.element;
const button = (text, onClick, disabled = false) => h('button', { type: 'button', className: 'button', onClick, disabled }, text);
const newId = () => `s${Array.from(crypto.getRandomValues(new Uint32Array(4)), (value) => value.toString(16).padStart(8, '0')).join('')}`;

export function Specifications({ data, setData, move, labels }) {
  function update(id, key, value) {
    setData((current) => ({ ...current, specifications: current.specifications.map((row) => row.id === id ? { ...row, [key]: value } : row) }));
  }
  const rows = data.specifications.map((row, index) => {
    const fields = [['label', 100], ['value', 500], ['unit', 25]].map(([key, maxLength]) => h('label', { key }, labels[key],
      h(key === 'value' ? TextareaControl : InputControl, { value: row[key], maxLength, rows: key === 'value' ? 2 : undefined, onChange: (value) => update(row.id, key, value) })));
    return h('div', { key: row.id, className: 'rdct-cnt-editor__spec' }, ...fields,
      h('div', { className: 'rdct-cnt-editor__actions' },
        button(labels.up, () => move('specifications', index, -1), index === 0),
        button(labels.down, () => move('specifications', index, 1), index === data.specifications.length - 1),
        button(labels.remove, () => setData((current) => ({ ...current, specifications: current.specifications.filter((entry) => entry.id !== row.id) })))));
  });
  return h('section', { className: 'rdct-cnt-editor__section' },
    h('div', { className: 'rdct-cnt-editor__heading' }, h('h3', null, labels.specifications),
      button(labels.addSpec, () => setData((current) => ({ ...current, specifications: [...current.specifications, { id: newId(), label: '', value: '', unit: '' }] })), data.specifications.length >= 40)), ...rows);
}
