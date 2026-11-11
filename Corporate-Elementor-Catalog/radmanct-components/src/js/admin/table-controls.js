import {TableGrid} from './table-grid.js';
import {encodeTable,decodeTable} from './table-data.js';
const {createElement:h,createRoot,useState,useEffect} = window.wp.element;
const config = window.rdctTableControl;
function ClassicGrid({owner}) {
  const [value,setValue] = useState(owner.getControlValue());
  useEffect(() => {
    const settings = owner.container?.settings;
    const sync = () => setValue(owner.getControlValue());
    settings?.on(`change:${owner.model.get('name')}`,sync);
    return () => settings?.off(`change:${owner.model.get('name')}`,sync);
  },[owner]);
  return h(TableGrid,{value,labels:config.labels,onChange:(next) => { setValue(next); owner.setValue(next); }});
}
const register = () => {
  const editor = window.elementor;
  if (!config || !editor?.modules?.controls?.BaseData || editor.__rdctTableControl) return;
  editor.__rdctTableControl = true;
  editor.addControlView('rdct-table-grid',editor.modules.controls.BaseData.extend({
    onReady() { this.gridRoot?.unmount(); this.gridRoot = createRoot(this.el.querySelector('[data-rdct-table-editor]')); this.renderGrid(); },
    onExternalChange() { this.renderGrid(); },
    renderGrid() {
      if (!this.gridRoot) return;
      this.gridRoot.render(h(ClassicGrid,{owner:this}));
    },
    onBeforeDestroy() { this.gridRoot?.unmount(); this.gridRoot = null; },
  }));
};
window.addEventListener('elementor/init',register);
window.__elementorEditorV1LoadingPromise?.then(register); register();
const controls = window.elementorV2?.editorControls;
const registry = window.elementorV2?.editorEditingPanel?.controlsRegistry;
const util = {key:'rdct-table-data',create:(value) => ({$$type:'rdct-table-data',value}),extract:(prop) => prop?.value};
const ui = window.elementorV2?.ui;
if (config && controls?.useBoundProp && ui?.TextField && ui.Button && ui.Box && registry && !registry.get('rdct-table-grid')) {
  function AtomicGrid() {
    const {value,setValue,disabled} = controls.useBoundProp(util);
    return h(TableGrid,{value:decodeTable(value),disabled,labels:config.labels,ui,onChange:(next) => setValue(encodeTable(next))});
  }
  registry.register('rdct-table-grid',AtomicGrid,'full',util);
}
