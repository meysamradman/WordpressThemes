import { InputField, SelectField, ToggleField, ColorSetting } from './fields.js';

const { createElement: h } = window.wp.element;
export const cursorKeys = ['custom_cursor', 'cursor_hide_outer', 'cursor_inner_size', 'cursor_outer_size', 'cursor_color', 'cursor_hover_color', 'cursor_blend', 'cursor_image_label', 'cursor_carousel_label','cursor_hover_effect','cursor_follow_duration','cursor_hover_duration','cursor_hover_scale'];

export function CursorFields({ values, change, labels, defaults }) {
  const input = (name, label, type, extra = {}) => h(InputField, { name, label, type, value: values[name], onChange: change, ...extra });
  return h('div', null,
    h('p', null, labels.cursorHint),
    h(ToggleField, { name: 'custom_cursor', label: labels.customCursor, description: labels.customCursorHint, value: values.custom_cursor, onChange: change }),
    h('div', { hidden: !values.custom_cursor },
      h(ToggleField, { name: 'cursor_hide_outer', label: labels.cursorHideOuter, value: values.cursor_hide_outer, onChange: change }),
      input('cursor_inner_size', labels.cursorInnerSize, 'number', { min: 2, max: 24, step: 1, dir: 'ltr' }),
      h('div', { hidden: values.cursor_hide_outer }, input('cursor_outer_size', labels.cursorOuterSize, 'number', { min: 16, max: 120, step: 1, dir: 'ltr' })),
      ...[['cursor_color', labels.cursorColor], ['cursor_hover_color', labels.cursorHoverColor]].map(([name, label]) => h(ColorSetting, { key: name, name, label, value: values[name], onChange: change, fallback: defaults[name], resetValue: defaults[name], resetLabel: labels.resetColor })),
      h(SelectField, { name: 'cursor_blend', label: labels.cursorBlend, value: values.cursor_blend, onChange: change, options: [['normal', labels.normal], ['difference', labels.difference]] }),
      h(SelectField, {name:'cursor_hover_effect',label:labels.cursorHoverEffect,value:values.cursor_hover_effect,onChange:change,options:[['none',labels.cursorHoverNone],['scale',labels.cursorHoverScale],['fill',labels.cursorHoverFill],['magnetic',labels.cursorHoverMagnetic]]}),
      input('cursor_follow_duration',labels.cursorFollowDuration,'number',{min:.05,max:1,step:.01,dir:'ltr'}),
      input('cursor_hover_duration',labels.cursorHoverDuration,'number',{min:.1,max:1.2,step:.01,dir:'ltr'}),
      input('cursor_hover_scale',labels.cursorHoverScaleSize,'number',{min:1,max:4,step:.1,dir:'ltr'}),
      h('div', { hidden: values.cursor_hide_outer },
        input('cursor_image_label', labels.cursorImageLabel, 'text', { maxLength: 80, description: labels.cursorLabelHint }),
        input('cursor_carousel_label', labels.cursorCarouselLabel, 'text', { maxLength: 80, description: labels.cursorLabelHint })),
    ),
  );
}
