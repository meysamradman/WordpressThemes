import { a as InputControl, c as SelectControl, l as SwitchControl, s as RangeControl } from "./controls-DqGpx29l.js";
import { t as ColorField } from "./color-field-DsbXJp0l.js";
import { t as BuilderIconPicker } from "./builder-icon-picker-BSABpcSi.js";
//#region radmanct/src/js/admin/builder-scheme-fields.js
var { createElement: h } = window.wp.element;
function SchemeFields({ item, update, config, labels, section = "content" }) {
	const fields = config.schemeFields || {};
	const current = item.schemeOptions || {};
	const change = (key, value) => update({ schemeOptions: {
		...current,
		[key]: value
	} });
	return h("div", null, Object.entries(fields).map(([key, field]) => {
		if (field.group === "content" !== (section === "content")) return null;
		if (field.condition && Object.entries(field.condition).some(([path, values]) => !values.includes(current[path] ?? fields[path]?.default))) return null;
		const value = current[key] ?? field.default;
		let control;
		if (field.icon) control = h(BuilderIconPicker, {
			value,
			onChange: (next) => change(key, next || ""),
			config,
			labels
		});
		else if (field.color) return h(ColorField, {
			key,
			label: field.label,
			value,
			fallback: item.appearance?.color || "#172b3a",
			resetLabel: labels.inheritAppearance,
			onChange: (next) => change(key, next || "")
		});
		else if (field.options) control = h(SelectControl, {
			value,
			options: Object.entries(field.options),
			onChange: (next) => change(key, next)
		});
		else if (typeof field.default === "boolean") control = h(SwitchControl, {
			checked: value,
			onChange: (next) => change(key, next)
		});
		else if (field.min !== void 0) control = h(RangeControl, {
			label: field.label,
			value,
			min: field.min,
			max: field.max,
			unit: field.unit || "px",
			step: field.step || 1,
			onChange: (next) => change(key, next)
		});
		else control = h(InputControl, {
			value,
			maxLength: 80,
			onChange: (next) => change(key, next)
		});
		return h("fieldset", { key }, h("legend", null, field.label), control);
	}));
}
//#endregion
export { SchemeFields };
