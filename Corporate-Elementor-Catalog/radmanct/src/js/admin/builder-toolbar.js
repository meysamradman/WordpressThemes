import { ButtonControl, ChoiceControl, InputControl, SelectControl } from '../../../../shared/admin/controls.js';
import { devices, deviceAt } from './builder-devices.js';

const { createElement: h } = window.wp.element;

export function BuilderToolbar({ labels, kind, title, creating = false, onTitleChange, copySource = '', layouts = [], onCopySource, busy, status = '', width, onWidthChange, dirty, canSave, canUndo, canRedo, onUndo, onRedo, focused, onFocus, onSettings, settingsOpen }) {
  const icons = { desktop: 'desktop', laptop: 'laptop', tablet: 'tablet', mobile: 'smartphone' };
  return h('div', { className: 'rdct-bld-editor__toolbar' },
    h('div', { className: `rdct-bld-toolbar__identity${creating ? ' rdct-bld-toolbar__identity--creating' : ''}` }, h('a', { href: window.rdctBuilder.libraryUrl, className: 'rdct-btn', 'aria-label': labels.layoutsTab }, h('span', { className: 'dashicons dashicons-list-view', 'aria-hidden': true })),
      h(InputControl,{id:'rdct-builder-title',value:title,onChange:onTitleChange,disabled:busy,maxLength:80,required:true,autoFocus:!!window.rdctBuilder.focusTitle,'aria-label':labels.layoutName,placeholder:labels.layoutName}),
      creating && h(SelectControl,{value:copySource,onChange:onCopySource,disabled:busy,'aria-label':labels.copyFrom,options:[['',labels.newLayout],...layouts.map(({id,title})=>[id,title])]})),
    h('div', { className: 'rdct-bld-toolbar__preview' },
      h(ChoiceControl, { label: labels.device, value: deviceAt(width), iconOnly: true,
        options: devices.map(({ id }) => [id, labels[id], false, icons[id]]), onChange: (id) => onWidthChange(devices.find((device) => device.id === id).width) })),
    h('div', { className: 'rdct-bld-toolbar__save' },
      h('span', { className: `rdct-bld-toolbar__status${status ? ' rdct-loading' : dirty ? ' is-dirty' : ''}`, role: 'status', 'aria-live': 'polite', 'aria-atomic': true }, status || (dirty ? labels.unsaved : labels.ready)),
      h('div', { className: 'rdct-bld-editor__actions', role: 'group', 'aria-label': labels.layoutSettings },
        h(ButtonControl, { icon: 'undo', iconOnly: true, label: labels.undo, onClick: onUndo, disabled: !canUndo }),
        h(ButtonControl, { icon: 'redo', iconOnly: true, label: labels.redo, onClick: onRedo, disabled: !canRedo }),
        h(ButtonControl, { icon: focused ? 'editor-contract' : 'editor-expand', iconOnly: true, label: labels.focusMode, 'aria-pressed': focused, onClick: onFocus }),
        h(ButtonControl, { icon: 'admin-generic', iconOnly: true, label: labels[`${kind}Settings`], 'aria-pressed': settingsOpen, onClick: onSettings })),
      h(ButtonControl, { type: 'submit', variant: 'primary', icon: 'saved', label: labels.save, disabled: busy || !canSave, 'aria-busy': !!busy })));
}
