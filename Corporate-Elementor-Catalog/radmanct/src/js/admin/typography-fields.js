import { fontGroups } from './settings-fonts.js';
import { FontField, FontFaces, SelectField, ToggleField, Field, ColorSetting } from './fields.js';
import { InputControl, ChoiceControl } from '../../../../shared/admin/controls.js';
import {toRem, fromRem} from './typography-size.js';

const { createElement: h, useState, useEffect, useRef } = window.wp.element;

function FontSizeControl({value, onChange, ...attributes}) {
  const [draft, setDraft] = useState(() => toRem(value));
  const sent = useRef(value);
  useEffect(() => { if (value !== sent.current) { sent.current = value; setDraft(toRem(value)); } }, [value]);
  return h(InputControl, {...attributes, type:'number', dir:'ltr', min:.5, max:12.5, step:'any', value:draft,
    onChange:next=>{setDraft(next); const stored=fromRem(next);sent.current=stored;onChange(stored);},
    onBlur:()=>setDraft(toRem(value)),
  });
}

export function TypographyFields({ config, values, change, group: requestedGroup, mode = 'fonts', resolveValues, inheritLabel }) {
  const { labels } = config;
  const [selectedGroup] = useState(config.primaryFontGroup);
  const [device, setDevice] = useState('');
  const groups = ['persian', 'latin'];
  const group = groups.includes(requestedGroup) ? requestedGroup : groups.includes(selectedGroup) ? selectedGroup : groups[0];
  const fontFor = (role, state = values) => {
    const effective = resolveValues ? resolveValues(state) : state;
    const find = key => effective[key === 'body' ? `${group}_font` : `${key}_${group}_font`] || (key === 'body' ? '' : find(['headings', 'link', 'span'].includes(key) ? 'body' : 'headings'));
    return find(role);
  };
  const fontChange = (name, value) => {
    change(name, value);
    const next = { ...values, [name]: value };
    for (const role of ['body', ...config.fontRoles]) {
      const key = `${role}_${group}_weight`;
      if (next[key] && !config.fontWeights?.[fontFor(role, next)]?.includes(String(next[key]))) change(key, '');
    }
  };
  const fields = (role) => {
    const bodyKey = `${group}_font`;
    const name = role === 'body' ? bodyKey : `${role}_${bodyKey}`;
    const language = group === 'persian' ? 'fa' : 'en';
    const inherited = resolveValues ? fontFor(role, {...values, [name]:''}) : ['headings', 'link', 'span'].includes(role) ? values[bodyKey] : values[`headings_${bodyKey}`] || values[bodyKey];
    const weightName = `${role}_${group}_weight`;
    const options = (config.fontWeights?.[fontFor(role)] || []).map((weight) => [weight, `${labels.fontWeightLabels[weight] || weight} (${weight})`]);
    const lineName = `${role}_${group}_line_height`;
    const sizeName = `${role}_${group}${device}_size`;
    return h('fieldset', { key: name, className: 'rdct-stg__typography-role' },
      h('legend', null, role === 'body' ? labels.bodyFonts : role === 'headings' ? labels.headingFonts : role === 'link' ? labels.linkFonts : role === 'span' ? labels.spanFonts : role.toUpperCase()), h(FontField, {
      key: name, name, label: labels.fontFamily,
      choices: role === 'body' && !resolveValues ? config.fontChoices[group] : [['', inheritLabel || (['headings', 'link', 'span'].includes(role) ? labels.inheritBodyFont : labels.inheritHeadingFont)], ...config.fontChoices[group]],
      value: values[name] || '', inherited, onChange: fontChange, registry: config.fonts,
      preview: role === 'body' ? config.samples[language] ?? (language === 'fa' ? labels.persianPreview : labels.latinPreview) : '', previewLabel: labels.fontPreview, language, weight: values[weightName],
    }), h(SelectField, { name: weightName, label: labels.fontWeight, value: values[weightName] || '', onChange: change, options: [['', labels.inheritFontWeight], ...options] }),
      h(Field, { name: sizeName, label: labels.fontSize },
        h(FontSizeControl, {key:sizeName, id:`rdct-${sizeName}`, value:values[sizeName] || '', placeholder:labels.inheritLineHeight, 'aria-describedby':'rdct-font-size-hint', onChange:next=>change(sizeName,next)}),
        h('input', {type:'hidden', name:`radman_settings[${sizeName}]`, value:values[sizeName] || ''})),
      h(Field, { name: lineName, label: labels.lineHeight }, h(InputControl, { id: `rdct-${lineName}`, name: `radman_settings[${lineName}]`, type: 'number', dir: 'ltr', min: .8, max: 4, step: .05, value: values[lineName] || '', placeholder: labels.inheritLineHeight, onChange: next => change(lineName, next) })),
      h(ColorSetting, { name: `${role}_color`, label: labels.textColor, value: values[`${role}_color`], onChange: change, resetLabel: labels.resetColor }),
      role === 'link' && h(ColorSetting, { name: 'link_hover_color', label: labels.linkHoverColor, value: values.link_hover_color, onChange: change, resetLabel: labels.resetColor }));
  };
  if (mode === 'loading') return h('div', null,
    h(SelectField, { name: 'font_language', label: labels.defaultFontLanguage, value: values.font_language, onChange: change, options: [['auto', labels.automaticFontLanguage], ['persian', labels.arabicScript], ['latin', labels.latinScript]] }),
    h(ToggleField, { name: 'mixed_language_fonts', label: labels.mixedLanguageFonts, description: labels.mixedLanguageFontsHint, value: values.mixed_language_fonts, onChange: change }));
  return h('div', null,
    !fontGroups(config, values).includes(group) && h(FontFaces, { config, values, groups: [group] }),
    ...['body', ...config.fontRoles].flatMap((role) => {
      const name = `${role === 'body' ? '' : `${role}_`}${group === 'persian' ? 'latin' : 'persian'}_font`;
      const weightName = `${role}_${group === 'persian' ? 'latin' : 'persian'}_weight`;
      return [name, weightName, `${role}_${group === 'persian' ? 'latin' : 'persian'}_line_height`].map((key) => h('input', { key, type: 'hidden', name: `radman_settings[${key}]`, value: values[key] || '' }));
    }),
    ...['body', ...config.fontRoles].flatMap(role => groups.flatMap(id => ['', '_tablet', '_mobile'].filter(point => id !== group || point !== device).map(point => {
      const key = `${role}_${id}${point}_size`;
      return h('input', { key, type: 'hidden', name: `radman_settings[${key}]`, value: values[key] || '' });
    }))),
    h(ChoiceControl, { label: labels.fontSize, value: device, options: [['', labels.desktop], ['_tablet', labels.tablet], ['_mobile', labels.mobile]], onChange: setDevice }),
    h('p',{id:'rdct-font-size-hint',className:'rdct-stg__hint'},labels.fontSizeHint),
    ...(config.typographyRoles || ['body', ...config.fontRoles]).map(fields));
}
