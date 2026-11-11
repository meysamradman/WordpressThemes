import { fontGroups } from './settings-fonts.js';
import { SelectControl, InputControl, SwitchControl, RangeControl, FieldRow } from '../../../../shared/admin/controls.js';
import { ColorField } from '../../../../shared/admin/color-field.js';

const { createElement: h } = window.wp.element;

export function ColorSetting({ name, label, value, onChange, fallback, resetLabel, resetValue = '', description }) {
  return h(Field, { name, label, description },
    h(InputControl, { type: 'hidden', name: `radman_settings[${name}]`, value: value ?? '', onChange: () => {} }),
    h(ColorField, { id: `rdct-${name}`, label, showLabel: false, value: value ?? '', fallback, resetLabel, onChange: (next) => onChange(name, next || resetValue) }));
}

export function Field({ label, name, description, children }) {
  return h(FieldRow, {className:'rdct-stg__field',label,id:`rdct-${name}`}, children, description && h('p', {className:'rdct-stg__hint',id:`rdct-${name}-hint`}, description));
}

export function InputField({ name, label, description, value, onChange, children, ...attributes }) {
  return h(Field, { name, label, description },
    h(attributes.type === 'number' ? RangeControl : InputControl, { ...(attributes.type === 'number' ? { label } : {}), id: `rdct-${name}`, name: `radman_settings[${name}]`, value, onChange: (next) => onChange(name, next), 'aria-describedby': description ? `rdct-${name}-hint` : undefined, ...attributes }), children);
}

export function SelectField({ name, label, description, value, onChange, options, children, ...attributes }) {
  return h(Field, { name, label, description },
    h(SelectControl, { id: `rdct-${name}`, name: `radman_settings[${name}]`, value, onChange: (next) => onChange(name, next), options, 'aria-describedby': description ? `rdct-${name}-hint` : undefined, ...attributes }), children);
}

export function FontField({ name, label, choices, value, onChange, preview, language, registry, description, previewLabel, inherited, weight }) {
  const family = registry[value || inherited]?.family ?? 'sans-serif';
  return h(SelectField, { label, name, description, value, onChange, options: choices },
    preview && h('p', { className: 'rdct-stg__font-preview', lang: language, dir: language === 'fa' ? 'rtl' : 'ltr', 'aria-label': previewLabel, style: { fontFamily: `"${family}", sans-serif`, fontWeight: weight || undefined } }, preview),
  );
}

export function FontFaces({ config, values, groups = fontGroups(config, values) }) {
  const css = [...new Set(Object.entries(values).filter(([key, value]) => groups.some((group) => key.endsWith(`${group}_font`)) && value).map(([, value]) => value))].flatMap((key) => {
    const font = config.fonts[key];
    if (!font) return [];
    return font.files.map(([file, weight]) => `@font-face{font-family:"${font.family}";src:url("${config.fontBase}${file.split('/').map(encodeURIComponent).join('/')}") format("woff2");font-weight:${weight};font-style:normal;font-display:swap;}`);
  }).join('');
  return h('style', null, css);
}

export function ToggleField({ name, label, description, value, onChange }) {
  return h(Field, { name, label, description },
    h('input', { type: 'hidden', name: `radman_settings[${name}]`, value: '0' }),
    h(SwitchControl, { id: `rdct-${name}`, name: `radman_settings[${name}]`, value: '1', checked: value, 'aria-describedby': description ? `rdct-${name}-hint` : undefined, onChange: (next) => onChange(name, next) }),
  );
}
