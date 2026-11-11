import {ChoiceControl,RangeControl} from '../../../../shared/admin/controls.js';
import {deviceKey} from './builder-devices.js';

const {createElement:h} = window.wp.element;

export function itemLayoutControls({values,device,labels,change,direction}) {
  const rtl = direction === 'rtl';
  const widthKey = deviceKey(device,'width');
  const inherit = device === 'desktop' ? [] : [['inherit',labels.inheritAppearance,false,'image-rotate']];
  const custom = values[widthKey] === 'custom' || (values[widthKey] === 'inherit' && values.width === 'custom');
  const row = (key,label,options) => h('div',{className:'rdct-bld-compact-layout__row',key},h('span',null,label),
    h(ChoiceControl,{label,value:values[key],options,iconOnly:true,onChange:(value)=>change(key,value)}));
  return h('div',{className:'rdct-bld-compact-layout'},
    row(deviceKey(device,'align'),labels.alignment,[...inherit,
      ['start',labels.start,false,rtl?'editor-alignright':'editor-alignleft'],
      ['center',labels.center,false,'editor-aligncenter'],
      ['end',labels.end,false,rtl?'editor-alignleft':'editor-alignright']]),
    row(widthKey,labels.elementWidth,[...inherit,
      ['equal',labels.equalWidth,false,'editor-expand'],
      ['auto',labels.autoWidth,false,'editor-contract'],
      ['full',labels.fullWidth,false,'align-full-width'],
      ['custom',labels.customWidth,false,'edit']]),
    custom && h('div',{className:'rdct-bld-compact-layout__size'},h('span',null,labels.customWidth),
      h(RangeControl,{label:labels.customWidth,numeric:true,unit:'%',min:5,max:100,value:values[deviceKey(device,'customWidth')] ?? values.customWidth ?? 50,onChange:(value)=>change(deviceKey(device,'customWidth'),value)})));
}
