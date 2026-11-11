import { Library } from './builder-library.js';
import { SelectControl, SwitchControl, FieldGroup, RangeControl } from '../../../../shared/admin/controls.js';
import {TabList} from '../../../../shared/admin/panel-tabs.js';
import { ColorField } from '../../../../shared/admin/color-field.js';
import { LayoutFields } from './builder-layout-fields.js';
const { createElement: h, useState } = window.wp.element;

export function SettingsPanel({ stored, layout, dirty, busy, run, change, labels, kind, device, onDeviceChange, onNew }) {
  const [section,setSection]=useState(window.rdctBuilder.initialSettingsTab || 'general');
  const border = {style:'none',color:'',top:0,bottom:0,start:0,end:0,...layout.border};
  const changeBorder = (key,value) => change({...layout,border:{...border,[key]:value}});
  const tabs=[{id:'general',label:labels.generalTab},{id:'layouts',label:labels.layoutsTab},{id:'backups',label:labels.backupsTab},{id:'translations',label:labels.translationsTab}];
  return h('div', { className: 'rdct-bld-settings' },
    h(TabList,{id:'rdct-global-settings',className:'rdct-bld-settings-tabs',value:section,onChange:setSection,tabs}),
    h('div',{className:'rdct-pnl-content',role:'tabpanel',id:`rdct-global-settings-${section}-panel`,'aria-labelledby':`rdct-global-settings-${section}-tab`},
    section==='general' ? h('div',null,
    h(FieldGroup,{title:labels.layoutStatus},h('label',null,h(SwitchControl,{checked:!!layout.enabled,onChange:enabled=>change({...layout,enabled})}),labels.enabled)),
    h(FieldGroup,{title:labels.layoutTitle},h(LayoutFields,{layout,change,labels,device,showDeviceControl:false})),
    h(FieldGroup,{title:labels.layoutBorder},
      h('label',null,labels.borderStyle,h(SelectControl,{value:border.style,onChange:value=>changeBorder('style',value),options:[['none',labels.borderNone],['solid',labels.borderSolid],['dashed',labels.borderDashed],['dotted',labels.borderDotted],['double',labels.borderDouble]]})),
      border.style !== 'none' && h('div',null,
        ...['top','bottom','start','end'].map(side=>h('fieldset',{key:side},h('legend',null,labels[`border${side}`]),h(RangeControl,{label:labels[`border${side}`],type:'number',dir:'ltr',min:0,max:20,value:border[side],onChange:value=>changeBorder(side,value === '' ? 0 : Number(value))}))),
        h(ColorField,{label:labels.borderColor,value:border.color,fallback:'#182522',resetLabel:labels.inheritAppearance,onChange:value=>changeBorder('color',value)}))),
    kind === 'header' && h(FieldGroup, {title:labels.headerBehavior},
    kind === 'header' && h('label', null, h(SwitchControl, { checked: !!layout.transparent, onChange: (transparent) => change({ ...layout, transparent }) }), labels.transparentHeader),
    kind === 'header' && layout.transparent && h('label',null,labels.transparentDevices,h(SelectControl,{value:layout.transparentDevices || 'all',onChange:transparentDevices=>change({...layout,transparentDevices}),options:[['all',labels.allDevices],['desktop',labels.desktop]]})),
    kind === 'header' && h('label', null, labels.stickyHeader, h(SelectControl, { value: layout.sticky || 'off', onChange: (sticky) => change({ ...layout, sticky }), options: [['off', labels.stickyOff], ['desktop', labels.desktop], ['all', labels.allDevices]] })),
    kind === 'header' && layout.sticky && layout.sticky !== 'off' && h('label', null, labels.stickyStart, h(SelectControl, {value:layout.stickyStart || 'immediate',onChange:stickyStart=>change({...layout,stickyStart}),options:[['immediate',labels.stickyImmediate],['after-first-section',labels.stickyAfterSection]]})),
    h('label', null, h(SwitchControl, {checked:!!layout.headerColors?.enabled,onChange:enabled=>change({...layout,headerColors:{...layout.headerColors,enabled}})}), labels.followSectionColors),
    layout.headerColors?.enabled && h('div', null,
      ...['light','dark','sticky'].map(state=>h(ColorField,{key:state,label:labels[`headerColor${state}`],value:layout.headerColors[state] || '',fallback:state==='dark'?'#ffffff':'#182522',resetLabel:labels.inheritAppearance,onChange:value=>change({...layout,headerColors:{...layout.headerColors,[state]:value}})})),
      h('p',{className:'description'},labels.headerColorsHint)),
    ),h('a', {className: 'rdct-btn rdct-bld-settings__site', href: window.rdctBuilder.settingsUrl}, labels.siteSettings)) :
    h(Library, { stored, layout, dirty, busy, run, change, onNew, mode:section })));
}
