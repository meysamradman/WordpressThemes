import { Field, ToggleField } from './fields.js';
import { TextareaControl } from '../../../../shared/admin/controls.js';

const { createElement: h } = window.wp.element;

export function AnalyticsFields({ values, change, labels }) {
  return h('div', null,
    h(ToggleField, { name: 'analytics_enabled', label: labels.analyticsEnabled, description: labels.analyticsHint, value: values.analytics_enabled, onChange: change }),
    h(Field, { name: 'analytics_measurement_id', label: labels.analyticsCode, description: labels.analyticsCodeHint },
      h(TextareaControl, { id: 'rdct-analytics_measurement_id', name: 'radman_settings[analytics_measurement_id]', dir: 'ltr', rows: 4, maxLength: 8192, spellCheck: false, placeholder: 'G-XXXXXXXXXX', value: values.analytics_measurement_id, onChange: (next) => change('analytics_measurement_id', next) })));
}
