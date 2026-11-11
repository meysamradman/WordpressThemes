import { BackgroundField } from '../../../../shared/admin/background-field.js';
import { FieldGroup } from '../../../../shared/admin/controls.js';

const { createElement: h } = window.wp.element;

export function ContentColorFields({ scope, values, change, config }) {
  return h(FieldGroup, { title: config.labels.contentColors },
    h('p', null, config.labels.contentColorsHint),
    h(BackgroundField,{value:{...values[`${scope}_background`],color:values[`${scope}_background`]?.color || values[`${scope}_color_background`]},labels:config.labels.backgroundFields,onChange:next=>{change(`${scope}_background`,next);change(`${scope}_color_background`,next.color);}}));
}
