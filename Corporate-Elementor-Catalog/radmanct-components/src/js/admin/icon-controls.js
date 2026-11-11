import { nativeIconValue, localIconName, nativeIconManagerView, ensureLocalIconLibrary, prepareNativeIconLibraries } from './native-icon-values.js';

const { createElement: h, useState, useEffect, useRef } = window.wp.element;
const config = window.rdctIconControls;

function registerClassic() {
  const editor = window.elementor;
  if (config) ensureLocalIconLibrary(editor, config.library);
  const NativeIcons = editor?.getControlView?.('icons');
  const BaseData = editor?.modules?.controls?.BaseData;
  if (!config || !NativeIcons?.prototype?.setDefaultIconLibraryLabel || !BaseData || editor.__rdctIconControl) return;
  editor.__rdctIconControl = true;
  editor.addControlView('rdct-icon-picker', NativeIcons.extend({
    async openPicker() {
      try {
        await prepareNativeIconLibraries(editor,config.library);
        if (!this.isDestroyed) NativeIcons.prototype.openPicker.call(this);
      } catch { editor.notifications?.showToast?.({message:config.loadError}); }
    },
    getControlValue() { return nativeIconValue(BaseData.prototype.getControlValue.call(this)); },
    getCleanControlValue() {
      const value = BaseData.prototype.getControlValue.call(this);
      return value && value !== this.model.get('default') ? value : undefined;
    },
    setValue(value, ...args) {
      const name = localIconName(value, config.names);
      if (name === undefined) { editor.notifications?.showToast?.({ message: config.localOnly }); return; }
      return BaseData.prototype.setValue.call(this, name, ...args);
    },
    setDefaultIconLibraryLabel(defaultIcon, container) {
      return NativeIcons.prototype.setDefaultIconLibraryLabel.call(this, nativeIconValue(defaultIcon), container);
    },
  }));
}

window.addEventListener('elementor/init', registerClassic);
window.__elementorEditorV1LoadingPromise?.then(registerClassic);
registerClassic();

const controls = window.elementorV2?.editorControls;
const registry = window.elementorV2?.editorEditingPanel?.controlsRegistry;
const ui = window.elementorV2?.ui;
if (config && controls?.useBoundProp && ui?.Stack && ui.Button && ui.Typography && registry && !registry.get('rdct-icon-picker')) {
  const util = window.elementorV2.editorProps.stringPropTypeUtil;
  function AtomicPicker() {
    const bound = controls.useBoundProp(util);
    const [error, setError] = useState(false);
    const [loading, setLoading] = useState(false);
    const selected = nativeIconValue(bound.value);
    const state = useRef();
    state.current = bound;
    useEffect(() => { state.current = bound; return () => { state.current = null; }; }, []);
    const choose = async () => {
      const manager = window.elementor?.iconManager;
      if (bound.disabled || loading || !manager) return;
      setError(false);
      setLoading(true);
      try {
        await prepareNativeIconLibraries(window.elementor,config.library);
        if (!state.current || state.current.disabled || JSON.stringify(state.current.path) !== JSON.stringify(bound.path)) return;
        const view = nativeIconManagerView(state.current.value, (next) => {
          if (!state.current || state.current.disabled) return;
          if (JSON.stringify(state.current.path) !== JSON.stringify(bound.path)) return;
          setError(false); state.current.setValue(next);
        }, config.names, () => { if (state.current) setError(true); });
        manager.show({ view });
      } catch { if (state.current) setError(true); }
      finally { if (state.current) setLoading(false); }
    };
    return h(ui.Stack, { gap: 1 },
      h(ui.Button, { variant: 'outlined', fullWidth: true, disabled: bound.disabled || loading, onClick: choose, 'aria-haspopup': 'dialog', 'aria-busy':loading },
        selected.value && h('i', { className: selected.value, 'aria-hidden': true, style: { marginInlineEnd: '.5em', flexShrink: 0 } }),
        bound.value ? (config.labels[bound.value] ?? bound.value) : config.choose),
      h(ui.Button, { size: 'small', disabled: bound.disabled || !bound.value, onClick: () => { if (!bound.disabled) { setError(false); bound.setValue(''); } } }, config.none),
      h(ui.Typography, { variant: 'caption', ...(error ? { role: 'alert', color: 'error' } : {}) }, error ? config.loadError : config.localOnly));
  }
  registry.register('rdct-icon-picker', AtomicPicker, 'full', util);
}
