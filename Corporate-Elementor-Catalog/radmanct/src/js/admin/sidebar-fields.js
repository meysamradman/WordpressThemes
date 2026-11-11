import { SelectField, ToggleField, InputField } from './fields.js';
import { FieldGroup } from '../../../../shared/admin/controls.js';

const { createElement: h } = window.wp.element;

export function SidebarFields({ scope, config, values, change }) {
  const { labels } = config;
  const prefix = `${scope}_sidebar_`;
  const options = [...(config.widgetAreas || [])];
  const selected = values[`${prefix}area`];
  if (selected && !options.some(([id]) => id === selected)) options.push([selected, labels.sidebarUnavailable]);
  return h(FieldGroup, { title: labels.sidebar },
    h(ToggleField, { name: `${prefix}enabled`, label: labels.sidebarEnabled, value: values[`${prefix}enabled`], onChange: change }),
    values[`${prefix}enabled`] && h('div', null,
      h(SelectField, { name: `${prefix}area`, label: labels.sidebarArea, value: selected, options, onChange: change }),
      h(SelectField, { name: `${prefix}position`, label: labels.sidebarPosition, value: values[`${prefix}position`], options: [['start', labels.sidebarStart], ['end', labels.sidebarEnd]], onChange: change }),
      h(InputField, { name: `${prefix}width`, label: labels.sidebarWidth, value: values[`${prefix}width`], type: 'number', numeric: true, min: 240, max: 480, step: 1, onChange: change }),
      config.widgetsUrl && h('a', { className: 'rdct-btn', href: config.widgetsUrl }, labels.manageWidgets)));
}
