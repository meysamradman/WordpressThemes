import { a as InputControl, c as SelectControl, s as RangeControl, t as ButtonControl } from "./controls-DqGpx29l.js";
import { t as ColorField } from "./color-field-DsbXJp0l.js";
import { t as pickImage } from "./image-picker-61mFi_WJ.js";
import { t as BuilderIconPicker } from "./builder-icon-picker-BSABpcSi.js";
//#region radmanct/src/js/admin/builder-menu-panel-fields.js
var { createElement: h } = window.wp.element;
function MenuPanelFields({ item, update, config, labels, group = "content", device }) {
	const image = () => {
		pickImage({
			title: config.navigationPanels.panelImageId.label,
			id: item.panelImageId || 0,
			onSelect: (id) => update({ panelImageId: id })
		});
	};
	return h("div", null, Object.entries(config.navigationPanels ?? {}).map(([key, field]) => {
		if (field.group === "submenu") return null;
		if (field.group === "style" !== (group === "style")) return null;
		if (device && (key === "panelFontSize" && device === "mobile" || key === "panelMobileFontSize" && device !== "mobile")) return null;
		if (field.condition && Object.entries(field.condition).some(([path, values]) => !values.includes(item[path] ?? config.navigationPanels[path]?.default))) return null;
		const value = item[key] ?? field.default;
		if (field.color) return h(ColorField, {
			key,
			label: field.label,
			value,
			fallback: field.inheritColor ? item.panelCloseColor || item.panelColor || "#172b3a" : field.default,
			resetLabel: labels.inheritAppearance,
			onChange: (next) => update({ [key]: next || field.default })
		});
		let input;
		const choices = (field.choices === "templates" ? config.panelTemplates : []) ?? [];
		const options = field.options ? Object.entries(field.options) : null;
		if (key === "panelCustomContent" && value && !Object.prototype.hasOwnProperty.call(field.options, value)) options.push([value, `#${value}`]);
		if (field.icon) input = h(BuilderIconPicker, {
			value,
			onChange: (next) => update({ [key]: next || field.default }),
			config,
			labels
		});
		else if (key === "panelImageId") input = h("div", null, h(ButtonControl, {
			type: "button",
			onClick: image
		}, value > 0 ? labels.changeImage : labels.chooseImage), value > 0 && h(ButtonControl, {
			type: "button",
			onClick: () => update({ panelImageId: 0 })
		}, labels.remove));
		else if (field.options || field.choices) input = h(SelectControl, {
			value,
			numeric: !!field.choices,
			options: options ?? [
				[0, labels.none],
				...value && !choices.some((block) => block.id === value) ? [[value, `#${value}`]] : [],
				...choices.filter((block) => block.id !== 0).map((block) => [block.id, block.title])
			],
			onChange: (next) => update({ [key]: next })
		});
		else if (field.min !== void 0) input = h(RangeControl, {
			label: field.label,
			value,
			min: field.min,
			max: field.max,
			numeric: true,
			onChange: (next) => update({ [key]: next })
		});
		else input = h(InputControl, {
			value,
			maxLength: 120,
			onChange: (next) => update({ [key]: next })
		});
		return h("fieldset", { key }, h("legend", null, field.label), input);
	}));
}
//#endregion
export { MenuPanelFields };
