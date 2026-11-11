import { InputField, SelectField } from './fields.js';
const { createElement: h } = window.wp.element;

export function ContentLayoutFields({ scope = 'content', values, change, config }) {
  const { labels } = config;
  const mode = `${scope}_layout`;
  const number = (field, range) => {
    const name = `${scope}_${field}`;
    return h(InputField, { key: field, name, label: labels[`layout_${field}`], type: 'number', dir: 'ltr', min: range[0], max: range[1], step: 1, placeholder: labels.inheritAppearance, value: values[name] ?? '', onChange: change });
  };
  return h('div', null,
    h(SelectField, { name: mode, label: labels.layoutMode, value: values[mode], onChange: change, options: [...(scope === 'content' ? [] : [['inherit', labels.inheritLayout]]), ['contained', labels.siteLayoutWidth], ['full', labels.fullContentWidth], ['custom', labels.customLayoutWidth]] }),
    h('div', { hidden: values[mode] === 'inherit' }, ...Object.entries(config.layoutFields).map(([field, range]) => h('div', { key: field, hidden: field === 'width' && values[mode] !== 'custom' }, number(field, range)))));
}

