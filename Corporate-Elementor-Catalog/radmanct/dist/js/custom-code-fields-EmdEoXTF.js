import { n as ChoiceControl } from "./controls-DqGpx29l.js";
//#region radmanct/src/js/admin/custom-code-fields.js
var { createElement: h, useState, useEffect, useRef } = window.wp.element;
function CodeEditor({ value, change, settings, label }) {
	const target = useRef(null);
	const editor = useRef(null);
	const callback = useRef(change);
	callback.current = change;
	useEffect(() => {
		if (!settings || !window.wp.codeEditor?.initialize) return;
		const instance = window.wp.codeEditor.initialize(target.current, settings).codemirror;
		editor.current = instance;
		const update = () => callback.current(instance.getValue());
		instance.on("change", update);
		return () => {
			instance.off("change", update);
			instance.toTextArea();
			editor.current = null;
		};
	}, [settings]);
	useEffect(() => {
		if (editor.current && editor.current.getValue() !== value) editor.current.setValue(value);
	}, [value]);
	return h("div", { className: "rdct-stg-code" }, h("textarea", {
		ref: target,
		dir: "ltr",
		rows: 22,
		value,
		spellCheck: false,
		"aria-label": label,
		onChange: (event) => change(event.target.value)
	}));
}
function CustomCodeFields({ kind, values, change, config }) {
	const { labels } = config;
	const [device, setDevice] = useState("global");
	if (!config.canEditCode) return h("p", null, labels.codePermission);
	const key = `custom_${kind}${kind === "css" && device !== "global" ? `_${device}` : ""}`;
	return h("div", null, h("p", { className: "rdct-stg__hint" }, labels.codeHint), kind === "css" && h(ChoiceControl, {
		label: labels.customCss,
		value: device,
		options: [
			"global",
			"desktop",
			"laptop",
			"tablet",
			"mobile"
		].map((id) => [id, labels[id]]),
		onChange: setDevice
	}), h(CodeEditor, {
		key,
		value: values[key],
		change: (value) => change(key, value),
		settings: config.codeEditor[kind],
		label: kind === "css" ? labels.customCss : labels.customJs
	}));
}
//#endregion
export { CustomCodeFields };
