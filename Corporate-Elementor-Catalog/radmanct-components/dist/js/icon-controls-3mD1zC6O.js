//#region radmanct-components/src/js/admin/native-icon-values.js
function nativeIconValue(value) {
	const name = typeof value === "string" ? value : localIconName(value);
	return name && /^[a-z][a-z0-9-]{0,70}$/.test(name) ? {
		library: "rdct-local",
		value: `rdct-library rdct-icon-${name}`
	} : {
		library: "",
		value: ""
	};
}
function localIconName(value, names) {
	if (typeof value === "string") return !names || names.includes(value) || value === "" ? value : void 0;
	if (!value || typeof value !== "object") return void 0;
	if (value.value === "" && (!value.library || value.library === "rdct-local")) return "";
	if (value.library !== "rdct-local" || typeof value.value !== "string") return void 0;
	const match = value.value.match(/^rdct-library rdct-icon-([a-z][a-z0-9-]{0,70})$/);
	return match && (!names || names.includes(match[1])) ? match[1] : void 0;
}
function ensureLocalIconLibrary(editor, definition) {
	const libraries = editor?.config?.icons?.libraries;
	if (!Array.isArray(libraries) || definition?.name !== "rdct-local" || !Array.isArray(definition.icons) || !definition.icons.length) return false;
	if (!libraries.some((library) => library?.name === "rdct-local")) libraries.push({
		...definition,
		icons: definition.icons.slice()
	});
	return true;
}
var loadingLibraries = /* @__PURE__ */ new WeakMap();
function prepareNativeIconLibraries(editor, definition, timeout = 1e4) {
	const manager = editor?.iconManager;
	if (!manager?.library?.initIconType || !ensureLocalIconLibrary(editor, definition)) return Promise.reject(/* @__PURE__ */ new Error("Icon libraries unavailable"));
	if (loadingLibraries.has(manager)) return loadingLibraries.get(manager);
	const pending = (async () => {
		const deadline = Date.now() + timeout;
		for (const tab of editor.config.icons.libraries.slice()) {
			if (["all", "recommended"].includes(tab.name) || !tab.icons && !tab.fetchJson) continue;
			const remaining = deadline - Date.now();
			if (remaining <= 0) throw new Error("Icon library timed out");
			await new Promise((resolve, reject) => {
				const timer = setTimeout(() => reject(/* @__PURE__ */ new Error("Icon library timed out")), remaining);
				try {
					manager.library.initIconType(tab, (library) => {
						if (library?.name !== tab.name) return;
						clearTimeout(timer);
						resolve(library);
					});
				} catch (error) {
					clearTimeout(timer);
					reject(error);
				}
			});
		}
	})().finally(() => loadingLibraries.delete(manager));
	loadingLibraries.set(manager, pending);
	return pending;
}
function nativeIconManagerView(value, onChange, names, onInvalid = () => {}) {
	return {
		model: { get: (key) => key === "recommended" ? { "rdct-local": names } : false },
		getControlValue: () => nativeIconValue(value),
		setValue: (icon) => {
			const next = localIconName(icon, names);
			if (next === void 0) {
				onInvalid();
				return;
			}
			onChange(next);
		},
		applySavedValue: () => {}
	};
}
//#endregion
//#region radmanct-components/src/js/admin/icon-controls.js
var { createElement: h, useState, useEffect, useRef } = window.wp.element;
var config = window.rdctIconControls;
function registerClassic() {
	const editor = window.elementor;
	if (config) ensureLocalIconLibrary(editor, config.library);
	const NativeIcons = editor?.getControlView?.("icons");
	const BaseData = editor?.modules?.controls?.BaseData;
	if (!config || !NativeIcons?.prototype?.setDefaultIconLibraryLabel || !BaseData || editor.__rdctIconControl) return;
	editor.__rdctIconControl = true;
	editor.addControlView("rdct-icon-picker", NativeIcons.extend({
		async openPicker() {
			try {
				await prepareNativeIconLibraries(editor, config.library);
				if (!this.isDestroyed) NativeIcons.prototype.openPicker.call(this);
			} catch {
				editor.notifications?.showToast?.({ message: config.loadError });
			}
		},
		getControlValue() {
			return nativeIconValue(BaseData.prototype.getControlValue.call(this));
		},
		getCleanControlValue() {
			const value = BaseData.prototype.getControlValue.call(this);
			return value && value !== this.model.get("default") ? value : void 0;
		},
		setValue(value, ...args) {
			const name = localIconName(value, config.names);
			if (name === void 0) {
				editor.notifications?.showToast?.({ message: config.localOnly });
				return;
			}
			return BaseData.prototype.setValue.call(this, name, ...args);
		},
		setDefaultIconLibraryLabel(defaultIcon, container) {
			return NativeIcons.prototype.setDefaultIconLibraryLabel.call(this, nativeIconValue(defaultIcon), container);
		}
	}));
}
window.addEventListener("elementor/init", registerClassic);
window.__elementorEditorV1LoadingPromise?.then(registerClassic);
registerClassic();
var controls = window.elementorV2?.editorControls;
var registry = window.elementorV2?.editorEditingPanel?.controlsRegistry;
var ui = window.elementorV2?.ui;
if (config && controls?.useBoundProp && ui?.Stack && ui.Button && ui.Typography && registry && !registry.get("rdct-icon-picker")) {
	const util = window.elementorV2.editorProps.stringPropTypeUtil;
	function AtomicPicker() {
		const bound = controls.useBoundProp(util);
		const [error, setError] = useState(false);
		const [loading, setLoading] = useState(false);
		const selected = nativeIconValue(bound.value);
		const state = useRef();
		state.current = bound;
		useEffect(() => {
			state.current = bound;
			return () => {
				state.current = null;
			};
		}, []);
		const choose = async () => {
			const manager = window.elementor?.iconManager;
			if (bound.disabled || loading || !manager) return;
			setError(false);
			setLoading(true);
			try {
				await prepareNativeIconLibraries(window.elementor, config.library);
				if (!state.current || state.current.disabled || JSON.stringify(state.current.path) !== JSON.stringify(bound.path)) return;
				const view = nativeIconManagerView(state.current.value, (next) => {
					if (!state.current || state.current.disabled) return;
					if (JSON.stringify(state.current.path) !== JSON.stringify(bound.path)) return;
					setError(false);
					state.current.setValue(next);
				}, config.names, () => {
					if (state.current) setError(true);
				});
				manager.show({ view });
			} catch {
				if (state.current) setError(true);
			} finally {
				if (state.current) setLoading(false);
			}
		};
		return h(ui.Stack, { gap: 1 }, h(ui.Button, {
			variant: "outlined",
			fullWidth: true,
			disabled: bound.disabled || loading,
			onClick: choose,
			"aria-haspopup": "dialog",
			"aria-busy": loading
		}, selected.value && h("i", {
			className: selected.value,
			"aria-hidden": true,
			style: {
				marginInlineEnd: ".5em",
				flexShrink: 0
			}
		}), bound.value ? config.labels[bound.value] ?? bound.value : config.choose), h(ui.Button, {
			size: "small",
			disabled: bound.disabled || !bound.value,
			onClick: () => {
				if (!bound.disabled) {
					setError(false);
					bound.setValue("");
				}
			}
		}, config.none), h(ui.Typography, {
			variant: "caption",
			...error ? {
				role: "alert",
				color: "error"
			} : {}
		}, error ? config.loadError : config.localOnly));
	}
	registry.register("rdct-icon-picker", AtomicPicker, "full", util);
}
//#endregion
