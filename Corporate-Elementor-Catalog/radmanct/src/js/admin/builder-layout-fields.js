import { SelectControl, RangeControl } from '../../../../shared/admin/controls.js';
import { devices, deviceKey } from './builder-devices.js';
const { createElement: h } = window.wp.element;

export function LayoutFields({ layout, change, labels, device, onDeviceChange, showDeviceControl = true }) {
  return h('div', {className:'rdct-bld-layout-fields'},
    h('label', null, labels.layoutWidth, h(SelectControl, { value: layout.width || 'full', onChange: (width) => change({ ...layout, width }), options: [['full', labels.fullLayoutWidth], ['contained', labels.containedWidth], ['custom', labels.customLayoutWidth]] })),
    ...[['customWidth', labels.customLayoutWidth, 320, 2560]].filter(([key]) => key !== 'customWidth' || layout.width === 'custom').map(([key, label, min, max]) =>
      h('fieldset', { key }, h('legend', null, label), h(RangeControl, { label, type: 'number', dir: 'ltr', min, max, placeholder: labels.inheritAppearance, value: layout[key] ?? '', onChange: (value) => change({ ...layout, [key]: value === '' ? null : Number(value) }) }))),
    device && showDeviceControl && h('label', null, labels.device, h(SelectControl, { value: device, onChange: onDeviceChange, options: devices.map(({ id }) => [id, labels[id]]) })),
    h('fieldset', null, h('legend', null, labels.contentGutter), ...devices.filter(({ id }) => !device || device === id).map(({ id }) => {
      const key = deviceKey(id, 'gutter');
      return h('fieldset', { key }, h('legend', null, labels[id]), h(RangeControl, { label: labels[id], type: 'number', dir: 'ltr', min: 0, max: 160, placeholder: labels.inheritAppearance, value: layout[key] ?? '', onChange: (value) => change({ ...layout, [key]: value === '' ? null : Number(value) }) }));
    })),
    false);
}
