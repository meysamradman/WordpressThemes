import { AppearanceControls } from './builder-controls.js';
import { VisualFields } from './builder-visual-fields.js';
import { SelectControl, InputControl, TextareaControl, SwitchControl, ButtonControl } from '../../../../shared/admin/controls.js';
import { PanelTabs } from '../../../../shared/admin/panel-tabs.js';

const { createElement: h, useState, useEffect } = window.wp.element;
const fieldLoaders = {
  button: () => import('./builder-button-fields.js').then(module => module.ButtonFields),
  menu: () => import('./builder-menu-fields.js').then(module => module.MenuFields),
  panel: () => import('./builder-menu-panel-fields.js').then(module => module.MenuPanelFields),
  linked: () => import('./builder-linked-fields.js').then(module => module.LinkedFields),
  scheme: () => import('./builder-scheme-fields.js').then(module => module.SchemeFields),
};

export function Inspector({ item, row, update, updateRow, labels, config, actions, device = 'desktop', onDeviceChange, onLoadingChange }) {
  const [tab, setTab] = useState('content');
  const sharedButton=item?.type==='button' && Object.keys(config.buttonFields || {}).length>0;
  const fieldKey = sharedButton ? 'button' : item?.type === 'theme-toggle' ? 'scheme' : ['menu','panel'].includes(item?.type) ? item.type : ['icon','contact','social'].includes(item?.type) ? 'linked' : null;
  const [loaded, setLoaded] = useState({});
  const [failed, setFailed] = useState(null);
  const [attempt, setAttempt] = useState(0);
  const Fields = loaded[fieldKey];
  useEffect(() => {
    if (!fieldKey || Fields) return;
    let active = true; setFailed(null);
    fieldLoaders[fieldKey]().then(Component => { if (active) setLoaded(previous => ({...previous, [fieldKey]:Component})); })
      .catch(() => { if (active) setFailed(fieldKey); });
    return () => { active = false; };
  }, [fieldKey, Fields, attempt]);
  const loading = !!fieldKey && !Fields && failed !== fieldKey;
  useEffect(() => { onLoadingChange?.(loading); }, [loading, onLoadingChange]);
  useEffect(() => () => onLoadingChange?.(false), [onLoadingChange]);
  const panelConfig = {...config,navigationPanels:config.panelFields};
  const appearance = (section) => section === 'style' && (item?.type === 'image' || item?.type === 'theme-toggle' || sharedButton) ? null : h(AppearanceControls, { row: !item, section, device, showDeviceControl: false, showFontSize:item?.type !== 'panel' || !!item.triggerLabel?.trim(), colorLabel:item?.type === 'panel' ? labels.panelTriggerColor : labels.textColor, value: item?.appearance || row.appearance, labels, update: item ? (value) => update({ appearance: value }) : updateRow });
  return h('div', { className: 'rdct-bld-inspector' },
    h('header',{className:'rdct-bld-panel-heading'},h('h3', null, item ? labels[item.type] : labels.rowSettings),h('span',{className:'rdct-bld-device-badge',title:labels.device},labels[device])),
    fieldKey && !Fields && failed === fieldKey && h('div', {role:'alert'}, h('p', null, labels.requestError), h(ButtonControl, {onClick:() => setAttempt(value => value + 1)}, labels.retryFields)),
    h(PanelTabs, { id: 'rdct-inspector', value: tab, onChange: setTab, tabs: [
      { id: 'content', label: labels.contentTab, icon: 'edit', content: h('div', null,
      !item && appearance('layout'),
      item && h('div', null,
      ['logo', 'image'].includes(item.type) && h(VisualFields, { item, update, labels, device, section:'content' }),
      item.type === 'container' && h('div',null,h('h4',null,labels.innerLayout),h(AppearanceControls, {row:true, section:'layout', device, showDeviceControl:false, value:item.layout, labels, update:(layout)=>update({layout})})),
      fieldKey === 'linked' && Fields && h(Fields, { item, update, config, labels }),
      ['text', 'button'].includes(item.type) && h('label', null, labels.text, h(item.type === 'button' ? InputControl : TextareaControl, { value: item.text, maxLength: 500, ...(item.type === 'text' ? {rows:4} : {}), onChange: (value) => update({ text: value }) })),
      item.type === 'button' && h('label', null, labels.url, h(InputControl, { dir: 'ltr', value: item.url, maxLength: 2048, onChange: (value) => update({ url: value }) })),
      item.type === 'button' && h('label',null,h(SwitchControl,{checked:item.newTab || false,onChange:(newTab)=>update({newTab})}),labels.newTab),
      sharedButton && Fields && h(Fields,{item,update,config,labels,device,section:'content'}),
      item.type === 'panel' && Fields && h(Fields,{item,update,labels,device,config:panelConfig}),
      item.type === 'theme-toggle' && h('a',{href:config.settingsUrl.replace(/#.*$/,'#rdct-design')},labels.colorSchemeSettings),
      item.type === 'theme-toggle' && Fields && h(Fields,{item,update,config,labels,section:'content'}),
      item.type === 'menu' && Fields && h(Fields, { item, update, config, labels, device }),
      h('div',{className:'rdct-bld-inspector__layout'},appearance('layout')))) },
      { id: 'style', label: labels.styleTab, icon: 'admin-appearance', content: h('div', null,
        config.kind === 'header' && item && !['container','image','block','search','theme-toggle'].includes(item.type) && h('label',null,labels.headerColorMode,h(SelectControl,{value:item.headerColorMode || 'auto',onChange:headerColorMode=>update({headerColorMode}),options:[['auto',labels.headerColorAuto],['inherit',labels.followSectionColors],['fixed',labels.headerColorFixed]]})),
        item && ['logo', 'image', 'button'].includes(item.type) && !sharedButton && h(VisualFields, { item, update, labels, device }),
        sharedButton && Fields && h(Fields,{item,update,config,labels,device}),
        item?.type === 'theme-toggle' && Fields && h(Fields,{item,update,config,labels,section:'style'}),
        item?.type === 'panel' && Fields && h(Fields,{item,update,labels,device,group:'style',config:panelConfig}),
        item?.type === 'menu' && Fields && h(Fields, { item, update, config, labels, device, tab:'style' }),
        appearance('style')) },
      { id: 'advanced', label: labels.advancedTab, icon: 'admin-generic', content: h('div', null, appearance('advanced'), h('div', { className: 'rdct-bld-editor__actions' }, ...actions)) },
    ] }));
}
