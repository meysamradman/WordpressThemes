import { c as SelectControl, i as FieldRow, l as SwitchControl, r as FieldGroup, s as RangeControl } from "./controls-DqGpx29l.js";
import { t as ColorField } from "./color-field-DsbXJp0l.js";
import { t as BackgroundField } from "./background-field-Farap2u6.js";
//#region radmanct/src/js/admin/page-title-fields.js
var { createElement: h } = window.wp.element;
function PageTitleFields({ value = {}, change, labels, global = false, idPrefix = "rdct-title" }) {
	const s = {
		mode: global ? "hide" : "inherit",
		custom: global,
		shape: "plain",
		background: {},
		height: 280,
		breadcrumbs: true,
		overlay: "#000000",
		overlayOpacity: 0,
		...value
	};
	const breadcrumbMode = s.breadcrumbMode ?? (s.custom ? s.breadcrumbs ? "show" : "hide" : "inherit");
	const set = (key, next) => change({
		...s,
		[key]: next
	});
	const row = (key, label, control) => h(FieldRow, {
		label,
		id: `${idPrefix}-${key}`
	}, control);
	const choices = [
		["inherit", labels.inheritAppearance],
		["show", labels.titleShow],
		["hide", labels.titleHide]
	];
	return h("div", { className: "rdct-page-title-fields" }, h(FieldGroup, { title: labels.page_title }, row("display", labels.titleDisplay, h(SelectControl, {
		id: `${idPrefix}-display`,
		value: s.mode,
		onChange: (n) => set("mode", n),
		options: global ? choices.slice(1) : choices
	})), row("breadcrumbs", labels.titleBreadcrumbs, global ? h(SwitchControl, {
		id: `${idPrefix}-breadcrumbs`,
		checked: s.breadcrumbs,
		onChange: (n) => set("breadcrumbs", n)
	}) : h(SelectControl, {
		id: `${idPrefix}-breadcrumbs`,
		value: breadcrumbMode,
		onChange: (n) => set("breadcrumbMode", n),
		options: choices
	})), !global && row("custom", labels.titleCustom, h(SwitchControl, {
		id: `${idPrefix}-custom`,
		checked: s.custom,
		onChange: (n) => change({
			...s,
			breadcrumbMode,
			custom: n
		})
	})), (global || s.custom) && h("div", null, row("shape", labels.titleShape, h(SelectControl, {
		id: `${idPrefix}-shape`,
		value: s.shape,
		onChange: (n) => set("shape", n),
		options: [["plain", labels.titlePlain], ["notched", labels.titleNotched]]
	})), h(BackgroundField, {
		value: s.background,
		onChange: (n) => set("background", n),
		labels: labels.backgroundFields,
		media: true
	}), row("height", labels.titleHeight, h(RangeControl, {
		id: `${idPrefix}-height`,
		label: labels.titleHeight,
		numeric: true,
		min: 120,
		max: 900,
		value: s.height,
		onChange: (n) => set("height", n === "" ? 120 : n)
	})), row("overlay", labels.titleOverlay, h(ColorField, {
		id: `${idPrefix}-overlay`,
		label: labels.titleOverlay,
		showLabel: false,
		value: s.overlay,
		onChange: (n) => set("overlay", n || "#000000"),
		resetLabel: labels.inheritAppearance
	})), row("opacity", labels.titleOverlayOpacity, h(RangeControl, {
		id: `${idPrefix}-opacity`,
		label: labels.titleOverlayOpacity,
		numeric: true,
		min: 0,
		max: 1,
		step: .05,
		unit: "",
		value: s.overlayOpacity,
		onChange: (n) => set("overlayOpacity", n === "" ? 0 : n)
	})))));
}
//#endregion
export { PageTitleFields as t };
