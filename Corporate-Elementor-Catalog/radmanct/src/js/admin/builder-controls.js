import { SelectControl, ChoiceControl, RangeControl, SwitchControl } from '../../../../shared/admin/controls.js';
import { ColorField } from '../../../../shared/admin/color-field.js';
import { BackgroundField } from '../../../../shared/admin/background-field.js';
import { devices, deviceKey, hiddenDevices, responsiveDefaults } from './builder-devices.js';
import {itemLayoutControls} from './builder-item-layout.js';

const { createElement: h, useState } = window.wp.element;
export function AppearanceControls({ value = {}, row = false, section = 'all', update, labels, device: activeDevice, showDeviceControl = true, showFontSize = true, colorLabel = labels.textColor, direction = window.rdctBuilder?.direction || 'ltr' }) {
  const defaults = row ? { visibility: 'all', align: value.rowLayout === 'grid' ? 'start' : 'center', rowLayout: 'flex', direction: 'row', wrap: 'wrap', columns: 3, mobileColumns: 1, gap: 24, padding: 16, background: '', justify: 'start', color: '', mobileLayout: 'stack', mobileGap: null, mobilePadding: null } : { visibility: 'all', align: 'start', width: 'equal', color: '', fontSize: 0, mobileFontSize: 0 };
  const [localDevice, setDevice] = useState('desktop');
  const device = activeDevice || localDevice;
  const values = { ...defaults, ...responsiveDefaults(row), ...value };
  const inherited = device === 'desktop' ? [] : [['inherit', labels.inheritAppearance]];
  const show = (name) => section === 'all' || section === name;
  const change = (key, value) => update({ ...values, [key]: value, ...(key === 'rowLayout' && value === 'grid' ? { align: 'start' } : {}), ...(row && device !== 'desktop' && key === deviceKey(device,'direction') ? {[`${device}Layout`]:'inline'} : {}) });
  const select = (key, title, options, selected = values[key]) => h('fieldset', { key }, h('legend', null, title), h(ChoiceControl, { label: title, value: selected, onChange: (value) => change(key, value), options }));
  const number = (base, title, max) => {
    const key = deviceKey(device, base);
    const inherit = device !== 'desktop' && !(base === 'columns' && device === 'mobile');
    return h('fieldset', { key }, h('legend', null, title),
      inherit && h('label', null, h(SwitchControl, { checked: values[key] === null, onChange: (checked) => change(key, checked ? null : values[base]) }), labels.useDesktopValue),
      h(RangeControl, { label: title, numeric: true, unit: base === 'columns' ? '' : 'px', min: base === 'columns' ? 1 : 0, max, value: values[key] ?? values[base], disabled: inherit && values[key] === null, onChange: (value) => change(key, value) }));
  };
  return h('div', { className: 'rdct-bld-editor__property-grid' },
      showDeviceControl && h('label', null, labels.device, h(SelectControl, { value: device, onChange: setDevice, options: devices.map(({ id }) => [id, labels[id]]) })),
      show('advanced') && h('fieldset', null, h('legend', null, labels.hideOn), ...devices.map(({ id }) => h('label', { key: id },
        h(SwitchControl, { checked: hiddenDevices(values).includes(id), onChange: (checked) => { const hidden = new Set(hiddenDevices(values)); if (checked) hidden.add(id); else hidden.delete(id); update({ ...values, visibility: 'all', hiddenDevices: [...hidden] }); } }), labels[id]))),
      show('layout') && row && select(deviceKey(device, 'align'), labels.alignItems, [...inherited, ['start', labels.start], ['center', labels.center], ['end', labels.end], ['stretch', labels.stretch]]),
      show('layout') && !row && itemLayoutControls({values,device,labels,change,direction}),
      show('layout') && row && select('rowLayout', labels.rowLayout, [['flex', labels.flexLayout], ['grid', labels.gridLayout]]),
      show('layout') && row && values.rowLayout === 'flex' && select(deviceKey(device, 'direction'), labels.flexDirection, [...inherited, ['row', labels.horizontalFlow], ['column', labels.verticalFlow]], values[deviceKey(device,'direction')] === 'inherit' && values[`${device}Layout`] === 'stack' ? 'column' : values[deviceKey(device,'direction')]),
      show('layout') && row && values.rowLayout === 'flex' && h('label', null, h(SwitchControl, {checked:values.wrap !== 'nowrap',onChange:(checked)=>change('wrap',checked?'wrap':'nowrap')}),labels.wrapItems),
      show('layout') && row && values.rowLayout === 'grid' && number('columns', labels.responsiveColumns, device === 'mobile' ? 3 : 6),
      show('layout') && row && select(deviceKey(device, 'justify'), labels.justifyItems, [...inherited, ['start', labels.start], ['center', labels.center], ['end', labels.end], ['space-between', labels.spaceBetween]]),
      show('style') && !row && showFontSize && h('fieldset', null, h('legend', null, labels.responsiveFontSize), h(RangeControl, { label: labels.responsiveFontSize, numeric: true, min: 0, max: 96, value: values[deviceKey(device, 'fontSize')], onChange: (value) => change(deviceKey(device, 'fontSize'), value) })),
      show('style') && h(ColorField, { label: colorLabel, value: values.color, resetLabel: labels.inheritAppearance, onChange: (value) => change('color', value) }),
      show('layout') && row && ['gap', 'padding'].map((key) => number(key, labels[key], key === 'gap' ? 64 : 120)),
      show('style') && row && h(BackgroundField,{value:{...values.backgroundOptions,color:values.backgroundOptions?.color || values.background},labels:labels.backgroundFields,onChange:next=>update({...values,background:next.color,backgroundOptions:next})}),
  );
}
