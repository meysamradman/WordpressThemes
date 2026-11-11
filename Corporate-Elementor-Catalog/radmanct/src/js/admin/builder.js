import { request, builderSaveRequest } from './builder-api.js';
import { resolveSelection } from './builder-selection.js';
import { newId, cloneItem, createItem } from './builder-elements.js';
import { commit, undo, redo } from './builder-history.js';
import { SelectControl, InputControl, ButtonControl } from '../../../../shared/admin/controls.js';
import { dropIndex } from './builder-drag.js';
import {containers, findContainer, updateContainer, withinLimits} from './builder-tree.js';
import * as mutations from './builder-mutations.js';
import { historyShortcut } from './builder-shortcuts.js';
import { devices, deviceAt } from './builder-devices.js';
import { BuilderToolbar } from './builder-toolbar.js';
import { Preview } from './builder-preview.js';
import { TabList } from '../../../../shared/admin/panel-tabs.js';

const { createElement: h, createRoot, useState, useEffect, useRef } = window.wp.element;
const config = window.rdctBuilder;
const labels = config.labels;
const button = (text, onClick, disabled = false, extra = {}) => h(ButtonControl, { onClick, disabled, ...extra }, text);

function App() {
  const [history, setHistory] = useState({ past: [], present: config.data.layout, future: [] });
  const layout = history.present;
  const setLayout = (value) => setHistory((state) => commit(state, value));
  const [over, setOver] = useState('');
  const [dragView,setDragView] = useState(null);
  const [dropTarget,setDropTarget] = useState(null);
  const pointerCallbacks = useRef({});
  const pointerController = useRef(null);
  const [pointerEngine,setPointerEngine] = useState(null);
  useEffect(()=>{let active=true;import('./builder-pointer-drag.js').then((module)=>{if(active)setPointerEngine(()=>module.createPointerDrag);}).catch(()=>{if(active){setFailed(true);setMessage(labels.requestError);}});return()=>{active=false;};},[]);
  if (pointerEngine && !pointerController.current) pointerController.current = pointerEngine(Object.fromEntries(['start','move','end','cancel'].map((name)=>[name,(...args)=>pointerCallbacks.current[name]?.(...args)])));
  const [selected, setSelected] = useState(null);
  const [panel, setPanel] = useState(config.initialSettingsTab ? 'layout' : 'elements');
  const [focused, setFocused] = useState(false);
  const [search, setSearch] = useState('');
  const [previewWidth, setPreviewWidth] = useState(1440);
  const device = deviceAt(previewWidth);
  const changePreviewWidth = (width) => { if (Number.isFinite(width)) setPreviewWidth(Math.round(Math.min(2560, Math.max(320, width)))); };
  const selectDevice = (id) => { const entry = devices.find((value) => value.id === id); if (entry) setPreviewWidth(entry.width); };
  const { row: selectedRow, item: selectedItem } = resolveSelection(layout, selected);
  const activePanel = panel;
  const select = (rowId, itemId = null) => { setSelected({ rowId, itemId }); setPanel('settings'); };
  const drag = useRef(null);
  const canvasDrop = useRef(null);
  const suppressClick = useRef(false);
  useEffect(() => {
    document.body.classList.add('rdct-bld-screen');
    const cancel = () => pointerController.current?.cancel();
    window.addEventListener('blur',cancel);
    return () => {pointerController.current?.cancel();window.removeEventListener('blur',cancel);document.body.classList.remove('rdct-bld-screen');};
  }, []);
  useEffect(() => {
    if (!focused) return;
    const overflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    return () => { document.body.style.overflow = overflow; };
  }, [focused]);
  const focusTarget = useRef(null);
  useEffect(() => {
    if (focusTarget.current) {
      document.querySelector(`[data-builder-focus="${focusTarget.current}"]`)?.focus();
      focusTarget.current = null;
    }
  }, [layout]);
  const [stored, setStored] = useState({ id: config.id, data: config.data, layouts: config.layouts, defaults: config.defaults || {}, translation: config.translation || null, creating:!!config.creating });
  const [draftTitle, setDraftTitle] = useState(config.creating ? '' : config.layouts.find(entry=>entry.id===config.id)?.title || '');
  const [copySource, setCopySource] = useState(config.copySource || '');
  const previewDirection = stored.translation?.direction || config.direction;
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState('');
  const [failed, setFailed] = useState(false);
  useEffect(() => {if (!message || failed || busy) return; const timer = setTimeout(() => setMessage(''), 4500); return () => clearTimeout(timer);}, [message,failed,busy]);
  const [Inspector, setInspector] = useState(null);
  const [Navigator, setNavigator] = useState(null);
  const [SettingsPanel, setSettingsPanel] = useState(null);
  const [panelErrors, setPanelErrors] = useState({});
  const [panelAttempt, setPanelAttempt] = useState(0);
  const [previewPending, setPreviewPending] = useState(true);
  const [inspectorPending, setInspectorPending] = useState(false);
  const panelPending = !panelErrors[activePanel] && (activePanel === 'layout' && !SettingsPanel || activePanel === 'structure' && !Navigator || activePanel === 'settings' && selectedRow && (!Inspector || inspectorPending));
  const loadingStatus = busy ? message || labels.loading : panelPending ? labels.loading : previewPending ? labels.previewLoading : '';
  useEffect(() => {
    if (activePanel !== 'layout' || SettingsPanel) return;
    let current = true;
    setPanelErrors(previous => previous.layout ? {...previous,layout:false} : previous);
    import('./builder-settings-panel.js').then((module) => { if (current) setSettingsPanel(() => module.SettingsPanel); }).catch(() => { if (current) setPanelErrors(previous => ({...previous, layout:true})); });
    return () => { current = false; };
  }, [activePanel, SettingsPanel, panelAttempt]);
  useEffect(() => {
    if (activePanel !== 'structure' || Navigator) return;
    let current = true;
    setPanelErrors(previous => previous.structure ? {...previous,structure:false} : previous);
    import('./builder-navigator.js').then((module) => { if (current) setNavigator(() => module.Navigator); }).catch(() => { if (current) setPanelErrors(previous => ({...previous, structure:true})); });
    return () => { current = false; };
  }, [activePanel, Navigator, panelAttempt]);
  useEffect(() => {
    if (activePanel !== 'settings' || Inspector) return;
    let current = true;
    setPanelErrors(previous => previous.settings ? {...previous,settings:false} : previous);
    import('./builder-inspector.js').then((module) => {
      if (current) setInspector(() => module.Inspector);
    }).catch(() => { if (current) setPanelErrors(previous => ({...previous, settings:true})); });
    return () => { current = false; };
  }, [activePanel, Inspector, panelAttempt]);
  const pending = useRef(false);
  async function run(operation, fields = {}, id = stored.id) {
    if (pending.current) return;
    pending.current = true; setBusy(true); setMessage(['save','create','rename'].includes(operation) ? labels.saving : labels.loading); setFailed(false);
    try {
      const result = await request(operation, id, fields);
      if (operation === 'delete') { window.location.assign(config.libraryUrl); return; }
      setStored(result);
      if (!result.warning) setDraftTitle(result.layouts.find(entry=>entry.id===result.id)?.title || '');
      if (!['save','create'].includes(operation)) { setSelected(null); setPanel('elements'); }
      const url = new URL(window.location.href);
      url.searchParams.set('layout', result.id);
      url.searchParams.delete('new');
      url.searchParams.delete('copy');
      url.searchParams.delete('rename');
      window.history.replaceState(null, '', url);
      setHistory({ past: [], present: result.data.layout, future: [] });
      setFailed(!!result.warning);
      setMessage(result.warning || (['save','create','rename'].includes(operation) ? labels.saved : ['default', 'inherit'].includes(operation) ? labels.assigned : ''));
    } catch (error) { setMessage(error.message); setFailed(true); }
    finally { pending.current = false; setBusy(false); }
  }
  const serialized = JSON.stringify(layout);
  const dirty = serialized !== JSON.stringify(stored.data.layout) || (stored.creating ? (!!draftTitle.trim() || layout.rows.length > 0) : draftTitle.trim() !== stored.layouts.find(entry=>entry.id===stored.id)?.title);
  const canSave = dirty && (!layout.enabled || layout.rows.some((row) => row.items.length)) && !!draftTitle.trim();
  useEffect(() => {
    const warn = (event) => {
      if (dirty) { event.preventDefault(); event.returnValue = ''; }
    };
    const submit = (event) => { event.preventDefault(); if (canSave && !pending.current) {const command=builderSaveRequest(stored,layout,draftTitle);run(command.operation,command.fields,command.id);} };
    const form = document.getElementById('rdct-builder-form');
    window.addEventListener('beforeunload', warn);
    form.addEventListener('submit', submit);
    return () => { window.removeEventListener('beforeunload', warn); form.removeEventListener('submit', submit); };
  }, [dirty, serialized, stored, draftTitle]);

  function beginCreate(value) {
    setCopySource('');
    setStored({...stored,id:'default',creating:true,translation:null,data:{layout:value,revision:0}});
    setHistory({past:[],present:value,future:[]});setDraftTitle('');setSelected(null);setPanel('elements');setMessage('');
    const url = new URL(window.location.href);url.searchParams.delete('layout');url.searchParams.delete('copy');url.searchParams.set('new','1');window.history.replaceState(null,'',url);
  }

  async function copyDraft(id) {
    if (pending.current || id === copySource || (dirty && !confirm(labels.discardConfirm))) return;
    pending.current = true; setBusy(true); setFailed(false); setMessage(labels.loading);
    try {
      const value = id ? (await request('read', id)).data.layout : {version:1,enabled:false,rows:[]};
      setLayout(value); setCopySource(id); setSelected(null); setMessage('');
      const url = new URL(window.location.href);
      if (id) url.searchParams.set('copy',id); else url.searchParams.delete('copy');
      window.history.replaceState(null,'',url);
    } catch (error) { setMessage(error.message); setFailed(true); }
    finally { pending.current=false; setBusy(false); }
  }

  function changeRow(id, update) {
    setLayout((value) => {const next = updateContainer(value, id, update); return withinLimits(next) ? next : value;});
  }
  function moveRow(from, to) {
    setLayout((value) => {
      const next = mutations.moveRow(value, from, to);
      if (next !== value) focusTarget.current = value.rows[from].id;
      return next;
    });
  }
  function duplicateRow(row) {
    setLayout((value) => mutations.duplicateRow(value, row.id));
  }
  function moveItem(sourceId, itemId, targetId, targetIndex) {
    setLayout((value) => {
      const next = mutations.moveItem(value, sourceId, itemId, targetId, targetIndex);
      if (next !== value) focusTarget.current = itemId;
      return next;
    });
  }
  function drop(event, rowId, itemIndex = null) {
    event.preventDefault(); event.stopPropagation();
    const data = drag.current;
    if (!data) return;
    if (data.type) {
      addElement(data.type, rowId, itemIndex);
    } else if (data.itemId) {
      const row = findContainer(layout, rowId);
      if (row) moveItem(data.rowId, data.itemId, rowId, dropIndex(layout, data, rowId, itemIndex ?? row.items.length));
      else if (!rowId && layout.rows.length < 8) {
        const added={id:newId(),appearance:{rowLayout:'flex'},items:[]};
        setLayout((value)=>mutations.moveItem({...value,rows:[...value.rows,added]},data.rowId,data.itemId,added.id,0));
        select(added.id,data.itemId);
      }
    } else {
      moveRow(layout.rows.findIndex((entry) => entry.id === data.rowId), layout.rows.findIndex((entry) => entry.id === rowId));
    }
    drag.current = null; setOver('');
  }
  const pointerTarget = (event) => {
    const canvas = canvasDrop.current?.(event.clientX, event.clientY);
    if (canvas) return canvas;
    const target = document.elementFromPoint(event.clientX, event.clientY)?.closest('[data-builder-row]');
    return target ? { rowId: target.dataset.builderRow, index: target.dataset.builderIndex === undefined ? null : Number(target.dataset.builderIndex), token: target.dataset.builderToken } : null;
  };
  const clearDrag = () => {drag.current=null;setOver('');setDragView(null);setDropTarget(null);};
  pointerCallbacks.current = {
    start:(data)=>{drag.current=data;},
    move:(event,data,point)=>{
      const target=pointerTarget(event);
      setOver(target?.token || '');setDropTarget(target);
      setDragView({...point,label:labels[data.type] || labels[findContainer(layout,data.rowId)?.items.find((item)=>item.id === data.itemId)?.type] || labels.row});
    },
    end:(event)=>{const target=pointerTarget(event);suppressClick.current=true;setTimeout(()=>{suppressClick.current=false;},0);if(target) drop(event,target.rowId,target.index);clearDrag();},
    cancel:clearDrag,
  };
  const dragProps = (data) => ({...pointerController.current?.bind(data),onClickCapture:(event)=>{if(suppressClick.current){event.preventDefault();event.stopPropagation();}}});
  const dragHandle = (data, label) => button(null, undefined, false, { ...dragProps(data), 'data-builder-focus': data.itemId || data.rowId, className: 'rdct-bld-editor__handle', icon:'move', iconOnly:true, label, variant: 'ghost', 'aria-label': label, title: label });
  const dropProps = (rowId, itemIndex = null, token = rowId) => ({
    'data-builder-row': rowId ?? '', 'data-builder-token': token, ...(itemIndex === null ? {} : { 'data-builder-index': itemIndex }),
  });

  function addRow(mode = 'flex') {
    if (layout.rows.length >= 8) return;
    const row = { id: newId(), appearance: {rowLayout: mode === 'grid' ? 'grid' : 'flex'}, items: [] };
    setLayout({ ...layout, rows: [...layout.rows, row] });
    setSelected({ rowId: row.id }); setPanel('elements');
  }
  function addElement(type, targetRowId = selectedItem?.type === 'container' ? selectedItem.id : selectedRow?.id, targetIndex = null) {
    if (!config.elementTypes.includes(type)) return;
    const target = findContainer(layout, targetRowId);
    if (target?.items.length >= 6 || (!target && layout.rows.length >= 8)) { setFailed(true); setMessage(labels.capacityHint); return; }
    const row = target || { id: newId(), items: [] };
    const item = createItem(type, config);
    const items = [...row.items]; items.splice(targetIndex ?? items.length, 0, item);
    const next = { ...row, items };
    const updated = target ? updateContainer(layout, row.id, () => next) : {...layout, rows:[...layout.rows,next]};
    if (!withinLimits(updated)) {setFailed(true);setMessage(labels.capacityHint);return;}
    setLayout(updated);
    select(row.id, item.id);
  }
  const updateItem = (patch) => changeRow(selectedRow.id, (row) => ({ ...row, items: row.items.map((entry) => entry.id === selectedItem.id ? { ...entry, ...patch } : entry) }));
  const selectedRowIndex = layout.rows.findIndex((entry) => entry.id === selectedRow?.id);
  const selectedItemIndex = selectedRow?.items.findIndex((entry) => entry.id === selectedItem?.id) ?? -1;
  const itemActions = selectedItem ? [
    button(labels.up, () => moveItem(selectedRow.id, selectedItem.id, selectedRow.id, selectedItemIndex - 1), selectedItemIndex === 0, { key: 'up' }),
    button(labels.down, () => moveItem(selectedRow.id, selectedItem.id, selectedRow.id, selectedItemIndex + 1), selectedItemIndex === selectedRow.items.length - 1, { key: 'down' }),
    button(labels.previous, () => moveItem(selectedRow.id, selectedItem.id, layout.rows[selectedRowIndex - 1].id, 6), selectedRowIndex <= 0 || layout.rows[selectedRowIndex - 1]?.items.length >= 6, { key: 'previous' }),
    button(labels.next, () => moveItem(selectedRow.id, selectedItem.id, layout.rows[selectedRowIndex + 1].id, 6), selectedRowIndex < 0 || selectedRowIndex === layout.rows.length - 1 || layout.rows[selectedRowIndex + 1]?.items.length >= 6, { key: 'next' }),
    button(labels.moveFirst,()=>moveItem(selectedRow.id,selectedItem.id,selectedRow.id,0),selectedItemIndex===0,{key:'first',label:labels.moveFirst,icon:'controls-skipback',iconOnly:true}),
    button(labels.duplicate, () => {
      const copy = cloneItem(selectedItem);
      changeRow(selectedRow.id, (row) => ({ ...row, items: [...row.items.slice(0, selectedItemIndex + 1), copy, ...row.items.slice(selectedItemIndex + 1)] }));
      select(selectedRow.id, copy.id);
    }, selectedRow.items.length >= 6, { key: 'duplicate', label: labels.duplicate, icon: 'admin-page', iconOnly: true }),
    button(labels.remove, () => { changeRow(selectedRow.id, (row) => ({ ...row, items: row.items.filter((entry) => entry.id !== selectedItem.id) })); select(selectedRow.id); }, false, { key: 'remove', label: labels.remove, variant: 'danger', icon: 'trash', iconOnly: true }),
  ] : [];
  const removeRow = (id) => {
    if (confirm(labels.removeConfirm)) setLayout((value) => ({ ...value, rows: value.rows.filter((row) => row.id !== id) }));
  };
  const rowActions = selectedRow ? [
    button(labels.up, () => moveRow(selectedRowIndex, selectedRowIndex - 1), selectedRowIndex === 0, { key: 'up' }),
    button(labels.down, () => moveRow(selectedRowIndex, selectedRowIndex + 1), selectedRowIndex === layout.rows.length - 1, { key: 'down' }),
    button(labels.duplicate, () => duplicateRow(selectedRow), layout.rows.length >= 8, { key: 'duplicate', label: labels.duplicate, icon: 'admin-page', iconOnly: true }),
    button(labels.remove, () => removeRow(selectedRow.id), false, { key: 'remove', label: labels.remove, variant: 'danger', icon: 'trash', iconOnly: true }),
  ] : [];
  const selectionActions = selectedItem ? itemActions : rowActions;

  return h('div', { className: `rdct-bld-editor${focused ? ' is-focused' : ''}${dragView ? ' is-dragging' : ''}`, onKeyDown: (event) => {
    const shortcut = historyShortcut(event);
    if (shortcut && !pending.current) { event.preventDefault(); setHistory(shortcut === 'undo' ? undo : redo); }
    if (event.key === 'Escape') { pointerController.current?.cancel(); drag.current = null; setOver(''); if (focused) setFocused(false); }
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') { event.preventDefault(); if (canSave && !pending.current) run('save', { layout, revision: stored.data.revision }); }
  } },
    dragView && h('div',{className:'rdct-bld-drag-ghost',style:{left:dragView.x+16,top:dragView.y+16},'aria-hidden':true},h('span',{className:'dashicons dashicons-move'}),dragView.label),
    h('input', { type: 'hidden', name: 'layout', value: serialized }),
    h('input', { type: 'hidden', name: 'id', value: stored.id }),
    h('input', { type: 'hidden', name: 'revision', value: stored.data.revision }),
    !busy && message && h('div', { role: failed ? 'alert' : 'status', className: `rdct-bld-toast${failed ? ' is-error' : ''}` }, h('span', null, message), h(ButtonControl, {icon:'no-alt',iconOnly:true,label:labels.dismiss,onClick:()=>setMessage('')})),
    h('fieldset', { disabled: busy, className: 'rdct-bld-editor__fieldset' },
      h(BuilderToolbar, { labels, kind: config.kind,
        creating:!!stored.creating,title:draftTitle,onTitleChange:setDraftTitle,
        copySource,layouts:stored.layouts,onCopySource:copyDraft,busy,status:loadingStatus,
        width: previewWidth, onWidthChange: changePreviewWidth,
        dirty, canSave, canUndo: !!history.past.length, canRedo: !!history.future.length, onUndo: () => setHistory(undo), onRedo: () => setHistory(redo),
        focused, onFocus: () => setFocused(!focused), onSettings: () => setPanel('layout'), settingsOpen: activePanel === 'layout' }),
      h('div', { className: 'rdct-bld-workspace' },
        h('aside', { className: 'rdct-bld-sidebar rdct-adm-sidebar', 'aria-label': labels.editorPanel },
          activePanel === 'layout' ? h('header',{className:'rdct-bld-panel-heading'},
            h(ButtonControl,{icon:'arrow-left-alt2',iconOnly:true,label:labels.backToEditor,onClick:()=>setPanel(selectedRow?'settings':'elements')}),
            h('h3',{id:'rdct-builder-layout-heading'},labels[`${config.kind}Settings`])) :
          h(TabList, { id: 'rdct-builder', className: 'rdct-bld-sidebar__switch', value: activePanel,
            onChange: setPanel,
            tabs: [{ id: 'elements', label: labels.elements, icon: 'screenoptions' }, { id: 'structure', label: labels.structure, icon: 'editor-table' }, { id: 'settings', label: labels.editElement, icon: 'edit' }] }),
          h('div', { className: 'rdct-adm-scroll', role: activePanel==='layout'?'region':'tabpanel', id: `rdct-builder-${activePanel}-panel`, 'aria-labelledby': activePanel==='layout'?'rdct-builder-layout-heading':`rdct-builder-${activePanel}-tab` },
          panelErrors[activePanel] ? h('div', {role:'alert'}, h('p',null,labels.requestError), h(ButtonControl,{onClick:()=>{setPanelErrors(previous=>({...previous,[activePanel]:false}));setPanelAttempt(value=>value+1);}},labels.retryFields)) :
          activePanel === 'layout' ? (SettingsPanel ? h(SettingsPanel, { stored, layout, dirty, busy, run, change: setLayout, onNew:beginCreate, labels, kind: config.kind, device, onDeviceChange: selectDevice }) : null) :
          activePanel === 'structure' ? (Navigator ? h(Navigator, { layout, selectedRow, selectedItem, labels, over, dragHandle, dragProps, dropProps, select, moveRow, moveItem, duplicateRow,
            removeRow,
            addToRow: (rowId) => { setSelected({ rowId }); setPanel('elements'); },
          }) : null) :
          activePanel === 'elements' || !selectedRow ? h('div', { className: 'rdct-pnl-content' },
            h(InputControl, { type: 'search', value: search, 'aria-label': labels.searchElements, placeholder: labels.searchElements, onChange: setSearch }),
            !!layout.rows.length && h('div',{className:'rdct-bld-palette-target'},h('span',null,labels.targetRow),h(ButtonControl,{variant:'ghost',icon:'editor-table',onClick:()=>setPanel('structure')},`${labels.row} ${Math.max(0,containers(layout).findIndex(row=>row.id===(selectedItem?.type==='container'?selectedItem.id:selectedRow?.id)))+1}`)),
            h('div', { className: 'rdct-bld-palette' }, ...config.elementTypes.filter((type) => labels[type].toLocaleLowerCase().includes(search.trim().toLocaleLowerCase())).map((type) =>
              button(h('span', null, h('span', { className: `dashicons dashicons-${({ logo: 'format-image', image: 'format-image', menu: 'menu', text: 'editor-textcolor', button: 'button', icon: 'star-empty', contact: 'phone', social: 'share', 'theme-toggle': 'lightbulb', block: 'screenoptions', search: 'search', copyright: 'editor-code' })[type] || 'plus-alt2'}`, 'aria-hidden': true }), h('span', null, labels[type])), () => { if (!suppressClick.current) addElement(type); }, false, { key: type, ...dragProps({ type }) }))),
            button(labels.addRow, addRow, layout.rows.length >= 8)) : selectedRow ? (Inspector ? h(Inspector, {
              key: selectedItem?.id || selectedRow.id, row: selectedRow, item: selectedItem, labels, config, update: updateItem, device, onDeviceChange: selectDevice, onLoadingChange:setInspectorPending,
              updateRow: (appearance) => changeRow(selectedRow.id, (row) => ({ ...row, appearance })), actions: selectionActions,
            }) : null) : h('p',{className:'rdct-pnl-content'},labels.selectElement))),
        h('div', { className: 'rdct-bld-canvas' },
          h(Preview, { layout, id: stored.id, direction: previewDirection, selected: { rowId: selectedRow?.id, itemId: selectedItem?.id }, disabled: busy, dragProps, dropTarget, dropResolver: canvasDrop, over, actions: selectionActions.slice(selectedItem ? -3 : -2), viewportWidth: previewWidth, onLoadingChange:setPreviewPending,
            onResize: (rowId,itemId,appearance) => changeRow(rowId,(row)=>({...row,items:row.items.map((item)=>item.id === itemId ? {...item,appearance} : item)})),
            onSelect: ({ id, item }) => {
            const row = containers(layout).find((entry) => item ? entry.items.some((element) => element.id === id) : entry.id === id);
            if (row) select(row.id, item ? id : null);
          } }),
          h('div', { className: `rdct-bld-canvas__add${over === 'new-row' ? ' is-over' : ''}`, ...dropProps(null,0,'new-row') }, h('span',null,labels.addRow), h('div',{className:'rdct-bld-editor__actions'},button(labels.flexLayout,()=>addRow('flex'),layout.rows.length>=8,{icon:'arrow-right-alt',variant:'ghost'}),button(labels.gridLayout,()=>addRow('grid'),layout.rows.length>=8,{icon:'grid-view',variant:'ghost'})))))));

}

const root = document.getElementById('rdct-builder-root');
if (root && config) createRoot(root).render(h(App));
