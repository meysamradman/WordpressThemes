import { c as SelectControl, l as SwitchControl, r as FieldGroup, s as RangeControl, t as ButtonControl } from "./controls-DqGpx29l.js";
import { t as ColorField } from "./color-field-DsbXJp0l.js";
import { r as request } from "./builder-api-CYWTbvUK.js";
import { n as TabList } from "./panel-tabs-BJ7bpkAa.js";
import { n as devices, t as deviceKey } from "./builder-tbvX7Bt0.js";
//#region radmanct/src/js/admin/builder-backup.js
var formats = /* @__PURE__ */ new Set(["rdct-builder", "radman-builder"]);
function downloadBackup(layout, kind, origin, environment = globalThis) {
	const blob = new environment.Blob([JSON.stringify({
		format: "rdct-builder",
		version: 1,
		kind,
		origin,
		layout
	})], { type: "application/json" });
	if (blob.size > 262144) throw new Error("Backup exceeds the file size limit");
	const url = environment.URL.createObjectURL(blob);
	let anchor;
	try {
		anchor = environment.document.createElement("a");
		anchor.href = url;
		anchor.download = `rdct-${kind}.json`;
		anchor.hidden = true;
		environment.document.body.append(anchor);
		anchor.click();
	} catch (error) {
		environment.URL.revokeObjectURL(url);
		throw error;
	} finally {
		anchor?.remove();
	}
	environment.setTimeout(() => environment.URL.revokeObjectURL(url), 1e3);
}
async function readBackup(file, kind, origin) {
	if (!file || !Number.isFinite(file.size) || file.size > 262144 || file.size < 0) return null;
	const contents = await file.text();
	if (contents.length > 262144) return null;
	let backup;
	try {
		backup = JSON.parse(contents);
	} catch {
		return null;
	}
	if (!backup || typeof backup !== "object" || Array.isArray(backup) || !formats.has(backup.format) || backup.version !== 1 || backup.kind !== kind || backup.origin !== origin) return null;
	return backup.layout;
}
function createBackupImporter(request, kind, origin) {
	let pending;
	const cancel = (notify = false) => {
		const session = pending;
		pending = null;
		if (session) {
			session.controller.abort();
			if (notify) session.status("cancelled");
		}
	};
	const load = async (file, id, { isCurrent, confirm, apply, report, errorMessage, status = () => {} }) => {
		cancel();
		const controller = new AbortController();
		const session = {
			controller,
			status
		};
		pending = session;
		const active = () => pending === session && !controller.signal.aborted && isCurrent();
		try {
			status("reading");
			const layout = await readBackup(file, kind, origin);
			if (!active()) return;
			if (!layout) throw new Error(errorMessage);
			status("validating");
			const result = await request("validate", id, { layout }, controller.signal);
			if (!active()) return;
			if (!confirm()) {
				status("cancelled");
				return;
			}
			if (active()) {
				apply(result.layout);
				status("applied");
			}
		} catch (error) {
			if (active() && error?.name !== "AbortError") {
				status("error");
				report(error?.message || errorMessage);
			}
		} finally {
			if (pending === session) pending = null;
		}
	};
	return {
		load,
		cancel
	};
}
//#endregion
//#region radmanct/src/js/admin/builder-translations.js
var { createElement: h$3, useState: useState$2, useEffect: useEffect$1 } = window.wp.element;
var { labels: labels$1 } = window.rdctBuilder;
function Translations({ stored, dirty, busy, run }) {
	const state = stored.translation;
	const [source, setSource] = useState$2(state?.source || "");
	const [existing, setExisting] = useState$2({});
	useEffect$1(() => {
		setSource(state?.source || "");
		setExisting({});
	}, [stored.id, state?.source]);
	if (!state?.items?.length) return null;
	const unavailable = dirty || busy;
	const linked = new Set(state.items.map((entry) => entry.id).filter(Boolean));
	const options = [["", labels$1.translationCopy], ...stored.layouts.filter((entry) => entry.id !== stored.id && !linked.has(entry.id)).map((entry) => [entry.id, entry.title])];
	return h$3("section", {
		className: "rdct-bld-library",
		"aria-label": labels$1.translations
	}, h$3("strong", null, labels$1.translations), h$3("label", null, labels$1.originalLanguage, h$3(SelectControl, {
		value: source,
		disabled: unavailable || state.bound,
		options: state.items.map((entry) => [entry.code, entry.title]),
		onChange: setSource
	})), ...state.items.filter((entry) => entry.code !== source).map((entry) => h$3("div", {
		key: entry.code,
		className: "rdct-bld-editor__actions"
	}, h$3("span", null, entry.title), entry.id ? h$3(ButtonControl, {
		type: "button",
		disabled: unavailable,
		onClick: () => run("read", {}, entry.id)
	}, labels$1.editTranslation) : h$3("div", null, h$3(SelectControl, {
		value: existing[entry.code] || "",
		disabled: unavailable,
		"aria-label": `${labels$1.translationLayout}: ${entry.title}`,
		options,
		onChange: (id) => setExisting((value) => ({
			...value,
			[entry.code]: id
		}))
	}), h$3(ButtonControl, {
		type: "button",
		disabled: unavailable,
		onClick: () => run("translate", {
			source_language: source,
			language: entry.code,
			revision: stored.data.revision,
			existing: existing[entry.code] || ""
		})
	}, existing[entry.code] ? labels$1.connectTranslation : labels$1.addTranslation)))), dirty && h$3("p", { role: "status" }, labels$1.translationSaveFirst));
}
//#endregion
//#region radmanct/src/js/admin/builder-library.js
var { createElement: h$2, useState: useState$1, useRef, useEffect } = window.wp.element;
var config = window.rdctBuilder;
var { labels } = config;
function Library({ stored, layout, dirty, busy = false, run, change, onNew, mode = "layouts" }) {
	const input = useRef(null);
	const importButton = useRef(null);
	const [restore, setRestore] = useState$1({ phase: "idle" });
	const importer = useRef(null);
	if (!importer.current) importer.current = createBackupImporter(request, config.kind, config.origin);
	const current = useRef(null);
	current.current = {
		id: stored.id,
		revision: stored.data.revision,
		layout,
		busy
	};
	useEffect(() => {
		importer.current.cancel(true);
	}, [
		stored.id,
		stored.data.revision,
		layout,
		busy,
		mode
	]);
	useEffect(() => () => importer.current.cancel(), []);
	const phase = restore.id === stored.id && restore.revision === stored.data.revision && !busy ? restore.phase : "idle";
	const restoring = phase === "reading" || phase === "validating";
	const action = (...args) => {
		importer.current.cancel(true);
		run(...args);
	};
	const button = (text, onClick, disabled = false, extra = {}) => h$2(ButtonControl, {
		type: "button",
		onClick,
		disabled,
		...extra
	}, text);
	function create(blank) {
		if (blank && dirty && !confirm(labels.discardConfirm)) return;
		onNew?.(blank ? {
			version: 1,
			enabled: false,
			rows: []
		} : layout);
	}
	function exportLayout() {
		try {
			downloadBackup(layout, config.kind, config.origin);
			setRestore({ phase: "idle" });
		} catch {
			setRestore({
				phase: "export-error",
				error: labels.exportError,
				id: stored.id,
				revision: stored.data.revision
			});
		}
	}
	function importLayout(event) {
		const file = event.target.files[0];
		event.target.value = "";
		if (!file || busy) return;
		const snapshot = current.current;
		return importer.current.load(file, stored.id, {
			isCurrent: () => !current.current.busy && current.current.id === snapshot.id && current.current.revision === snapshot.revision && current.current.layout === snapshot.layout,
			confirm: () => confirm(labels.importConfirm),
			apply: change,
			errorMessage: labels.importError,
			report: (error) => setRestore({
				phase: "error",
				error,
				id: snapshot.id,
				revision: snapshot.revision
			}),
			status: (value) => setRestore({
				phase: value,
				id: snapshot.id,
				revision: snapshot.revision
			})
		});
	}
	return h$2("section", { className: "rdct-bld-library" }, mode === "layouts" && h$2("label", null, labels.library, h$2(SelectControl, {
		value: stored.id,
		options: stored.layouts.map((entry) => [entry.id, entry.title]),
		onChange: (id) => {
			if (!dirty || confirm(labels.discardConfirm)) action("read", {}, id);
		}
	})), mode === "layouts" && h$2("div", { className: "rdct-bld-library__actions" }, button(labels.newLayout, () => create(true), stored.layouts.length >= 13), button(labels.copyLayout, () => create(false), stored.layouts.length >= 13), button(labels.renameLayout, () => document.getElementById("rdct-builder-title")?.focus()), button(labels.deleteLayout, () => {
		if (confirm(labels.deleteConfirm)) action("delete");
	}, stored.creating || dirty)), mode === "backups" && h$2("div", { className: "rdct-bld-library__actions" }, button(labels.exportLayout, exportLayout, restoring), button(labels.importLayout, () => input.current.click(), false, { ref: importButton }), restoring && button(labels.cancelImport, () => {
		importer.current.cancel(true);
		importButton.current?.focus();
	}), h$2("input", {
		ref: input,
		type: "file",
		accept: ".json,application/json",
		hidden: true,
		onChange: importLayout
	})), mode === "translations" && h$2(Translations, {
		stored,
		dirty,
		busy,
		run
	}), ["error", "export-error"].includes(phase) && h$2("p", { role: "alert" }, restore.error || labels.restore_error), h$2("p", {
		role: "status",
		"aria-live": "polite",
		"aria-atomic": true
	}, [
		"idle",
		"error",
		"export-error"
	].includes(phase) ? "" : labels[`restore_${phase}`]));
}
//#endregion
//#region radmanct/src/js/admin/builder-layout-fields.js
var { createElement: h$1 } = window.wp.element;
function LayoutFields({ layout, change, labels, device, onDeviceChange, showDeviceControl = true }) {
	return h$1("div", { className: "rdct-bld-layout-fields" }, h$1("label", null, labels.layoutWidth, h$1(SelectControl, {
		value: layout.width || "full",
		onChange: (width) => change({
			...layout,
			width
		}),
		options: [
			["full", labels.fullLayoutWidth],
			["contained", labels.containedWidth],
			["custom", labels.customLayoutWidth]
		]
	})), ...[[
		"customWidth",
		labels.customLayoutWidth,
		320,
		2560
	]].filter(([key]) => key !== "customWidth" || layout.width === "custom").map(([key, label, min, max]) => h$1("fieldset", { key }, h$1("legend", null, label), h$1(RangeControl, {
		label,
		type: "number",
		dir: "ltr",
		min,
		max,
		placeholder: labels.inheritAppearance,
		value: layout[key] ?? "",
		onChange: (value) => change({
			...layout,
			[key]: value === "" ? null : Number(value)
		})
	}))), device && showDeviceControl && h$1("label", null, labels.device, h$1(SelectControl, {
		value: device,
		onChange: onDeviceChange,
		options: devices.map(({ id }) => [id, labels[id]])
	})), h$1("fieldset", null, h$1("legend", null, labels.contentGutter), ...devices.filter(({ id }) => !device || device === id).map(({ id }) => {
		const key = deviceKey(id, "gutter");
		return h$1("fieldset", { key }, h$1("legend", null, labels[id]), h$1(RangeControl, {
			label: labels[id],
			type: "number",
			dir: "ltr",
			min: 0,
			max: 160,
			placeholder: labels.inheritAppearance,
			value: layout[key] ?? "",
			onChange: (value) => change({
				...layout,
				[key]: value === "" ? null : Number(value)
			})
		}));
	})), false);
}
//#endregion
//#region radmanct/src/js/admin/builder-settings-panel.js
var { createElement: h, useState } = window.wp.element;
function SettingsPanel({ stored, layout, dirty, busy, run, change, labels, kind, device, onDeviceChange, onNew }) {
	const [section, setSection] = useState(window.rdctBuilder.initialSettingsTab || "general");
	const border = {
		style: "none",
		color: "",
		top: 0,
		bottom: 0,
		start: 0,
		end: 0,
		...layout.border
	};
	const changeBorder = (key, value) => change({
		...layout,
		border: {
			...border,
			[key]: value
		}
	});
	const tabs = [
		{
			id: "general",
			label: labels.generalTab
		},
		{
			id: "layouts",
			label: labels.layoutsTab
		},
		{
			id: "backups",
			label: labels.backupsTab
		},
		{
			id: "translations",
			label: labels.translationsTab
		}
	];
	return h("div", { className: "rdct-bld-settings" }, h(TabList, {
		id: "rdct-global-settings",
		className: "rdct-bld-settings-tabs",
		value: section,
		onChange: setSection,
		tabs
	}), h("div", {
		className: "rdct-pnl-content",
		role: "tabpanel",
		id: `rdct-global-settings-${section}-panel`,
		"aria-labelledby": `rdct-global-settings-${section}-tab`
	}, section === "general" ? h("div", null, h(FieldGroup, { title: labels.layoutStatus }, h("label", null, h(SwitchControl, {
		checked: !!layout.enabled,
		onChange: (enabled) => change({
			...layout,
			enabled
		})
	}), labels.enabled)), h(FieldGroup, { title: labels.layoutTitle }, h(LayoutFields, {
		layout,
		change,
		labels,
		device,
		showDeviceControl: false
	})), h(FieldGroup, { title: labels.layoutBorder }, h("label", null, labels.borderStyle, h(SelectControl, {
		value: border.style,
		onChange: (value) => changeBorder("style", value),
		options: [
			["none", labels.borderNone],
			["solid", labels.borderSolid],
			["dashed", labels.borderDashed],
			["dotted", labels.borderDotted],
			["double", labels.borderDouble]
		]
	})), border.style !== "none" && h("div", null, ...[
		"top",
		"bottom",
		"start",
		"end"
	].map((side) => h("fieldset", { key: side }, h("legend", null, labels[`border${side}`]), h(RangeControl, {
		label: labels[`border${side}`],
		type: "number",
		dir: "ltr",
		min: 0,
		max: 20,
		value: border[side],
		onChange: (value) => changeBorder(side, value === "" ? 0 : Number(value))
	}))), h(ColorField, {
		label: labels.borderColor,
		value: border.color,
		fallback: "#182522",
		resetLabel: labels.inheritAppearance,
		onChange: (value) => changeBorder("color", value)
	}))), kind === "header" && h(FieldGroup, { title: labels.headerBehavior }, kind === "header" && h("label", null, h(SwitchControl, {
		checked: !!layout.transparent,
		onChange: (transparent) => change({
			...layout,
			transparent
		})
	}), labels.transparentHeader), kind === "header" && layout.transparent && h("label", null, labels.transparentDevices, h(SelectControl, {
		value: layout.transparentDevices || "all",
		onChange: (transparentDevices) => change({
			...layout,
			transparentDevices
		}),
		options: [["all", labels.allDevices], ["desktop", labels.desktop]]
	})), kind === "header" && h("label", null, labels.stickyHeader, h(SelectControl, {
		value: layout.sticky || "off",
		onChange: (sticky) => change({
			...layout,
			sticky
		}),
		options: [
			["off", labels.stickyOff],
			["desktop", labels.desktop],
			["all", labels.allDevices]
		]
	})), kind === "header" && layout.sticky && layout.sticky !== "off" && h("label", null, labels.stickyStart, h(SelectControl, {
		value: layout.stickyStart || "immediate",
		onChange: (stickyStart) => change({
			...layout,
			stickyStart
		}),
		options: [["immediate", labels.stickyImmediate], ["after-first-section", labels.stickyAfterSection]]
	})), h("label", null, h(SwitchControl, {
		checked: !!layout.headerColors?.enabled,
		onChange: (enabled) => change({
			...layout,
			headerColors: {
				...layout.headerColors,
				enabled
			}
		})
	}), labels.followSectionColors), layout.headerColors?.enabled && h("div", null, ...[
		"light",
		"dark",
		"sticky"
	].map((state) => h(ColorField, {
		key: state,
		label: labels[`headerColor${state}`],
		value: layout.headerColors[state] || "",
		fallback: state === "dark" ? "#ffffff" : "#182522",
		resetLabel: labels.inheritAppearance,
		onChange: (value) => change({
			...layout,
			headerColors: {
				...layout.headerColors,
				[state]: value
			}
		})
	})), h("p", { className: "description" }, labels.headerColorsHint))), h("a", {
		className: "rdct-btn rdct-bld-settings__site",
		href: window.rdctBuilder.settingsUrl
	}, labels.siteSettings)) : h(Library, {
		stored,
		layout,
		dirty,
		busy,
		run,
		change,
		onNew,
		mode: section
	})));
}
//#endregion
export { SettingsPanel };
