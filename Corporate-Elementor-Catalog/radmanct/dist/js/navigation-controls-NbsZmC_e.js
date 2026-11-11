//#region radmanct-components/src/js/admin/atomic-color-control.js
function normalizePickerColor(value, canvasContext) {
	if (value == null || value === "") return null;
	if (typeof value !== "string") return void 0;
	if (/^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{8})$/i.test(value)) return value;
	if (/^#[a-f0-9]{4}$/i.test(value)) return `#${[...value.slice(1)].map((part) => part + part).join("")}`;
	if (value === "transparent") return "#00000000";
	const rgb = value.match(/^rgba?\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)(?:\s*,\s*(0(?:\.\d+)?|1(?:\.0+)?|\.\d+))?\s*\)$/i);
	if (rgb && rgb.slice(1, 4).every((part) => Number(part) <= 255)) {
		const bytes = rgb.slice(1, 4).map((part) => Math.round(Number(part)));
		if (rgb[4] !== void 0) bytes.push(Math.round(Number(rgb[4]) * 255));
		return `#${bytes.map((part) => part.toString(16).padStart(2, "0")).join("")}`;
	}
	if (!canvasContext || !globalThis.CSS?.supports("color", value) || /var\(|currentcolor|inherit|initial|unset|revert/i.test(value)) return void 0;
	canvasContext.clearRect(0, 0, 1, 1);
	canvasContext.fillStyle = value;
	canvasContext.fillRect(0, 0, 1, 1);
	const bytes = [...canvasContext.getImageData(0, 0, 1, 1).data];
	if (bytes[3] === 255) bytes.pop();
	return `#${bytes.map((part) => part.toString(16).padStart(2, "0")).join("")}`;
}
function createNativeColorControl(controls, h, util) {
	let context;
	return function NativeColorControl() {
		const { setValue } = controls.useBoundProp(util);
		return h(controls.ColorControl, {
			propTypeUtil: util,
			onChange: (next) => {
				if (context === void 0 && typeof document !== "undefined") context = document.createElement("canvas").getContext("2d");
				const normalized = normalizePickerColor(next, context);
				if (normalized !== void 0) setValue(normalized);
			}
		});
	};
}
//#endregion
//#region radmanct/src/js/admin/navigation-controls.js
var v2 = window.elementorV2;
var controls = v2?.editorControls;
var registry = v2?.editorEditingPanel?.controlsRegistry;
if (controls && registry) {
	const { createElement: h } = window.wp.element;
	const { Select, MenuItem } = v2.ui;
	if (!registry.get("rdct-navigation-choice")) registry.register("rdct-navigation-choice", function Choice({ options }) {
		const bound = controls.useBoundProp(v2.editorProps.numberPropTypeUtil);
		const value = bound.value ?? 0;
		const choices = options.some((item) => Number(item.value) === value) ? options : [{
			value,
			label: `#${value}`
		}, ...options];
		return h(Select, {
			size: "tiny",
			fullWidth: true,
			value,
			disabled: bound.disabled,
			onChange: (event) => bound.setValue(Number(event.target.value))
		}, choices.map(({ value, label }) => h(MenuItem, {
			key: value,
			value
		}, label)));
	}, "full", v2.editorProps.numberPropTypeUtil);
	if (!registry.get("rdct-navigation-color")) registry.register("rdct-navigation-color", createNativeColorControl(controls, h, v2.editorProps.stringPropTypeUtil), "full", v2.editorProps.stringPropTypeUtil);
}
//#endregion
