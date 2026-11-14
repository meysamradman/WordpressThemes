import { a as SelectField, t as ColorSetting } from "./settings-7-33mTbp.js";
//#region radmanct/src/js/admin/color-settings-fields.js
var { createElement: h } = window.wp.element;
function ColorSettingsFields({ config, values, change }) {
	const { labels } = config;
	return h("div", null, h(ColorSetting, {
		name: "brand_color",
		label: labels.brandColor,
		value: values.brand_color,
		onChange: change,
		fallback: config.defaults.brand_color,
		resetValue: config.defaults.brand_color,
		resetLabel: labels.resetColor
	}), h(SelectField, {
		name: "color_scheme",
		label: labels.colorScheme,
		value: values.color_scheme,
		onChange: change,
		options: [
			["off", labels.schemeOff],
			["light", labels.schemeLight],
			["dark", labels.schemeDark],
			["system", labels.schemeSystem]
		]
	}), values.color_scheme !== "off" && h("div", null, ...[
		["dark_background", "darkBackground"],
		["dark_surface", "darkSurface"],
		["dark_text", "darkText"],
		["dark_muted", "darkMuted"],
		["dark_border", "darkBorder"]
	].map(([name, label]) => h(ColorSetting, {
		key: name,
		name,
		label: labels[label],
		value: values[name],
		onChange: change,
		fallback: config.defaults[name],
		resetValue: config.defaults[name],
		resetLabel: labels.resetColor
	}))));
}
//#endregion
export { ColorSettingsFields };
