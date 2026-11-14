import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { a as InputControl, c as SelectControl, l as SwitchControl, n as ChoiceControl, s as RangeControl, t as ButtonControl, u as TextareaControl } from "./controls-DqGpx29l.js";
import { t as ColorField } from "./color-field-DsbXJp0l.js";
import { t as PanelTabs } from "./panel-tabs-BJ7bpkAa.js";
import { i as responsiveDefaults, n as devices, r as hiddenDevices, t as deviceKey } from "./builder-DCO-dmq6.js";
import { t as pickImage } from "./image-picker-61mFi_WJ.js";
import { t as BackgroundField } from "./background-field-Farap2u6.js";
//#region radmanct/src/js/admin/builder-item-layout.js
var { createElement: h$3 } = window.wp.element;
function itemLayoutControls({ values, device, labels, change, direction }) {
	const rtl = direction === "rtl";
	const widthKey = deviceKey(device, "width");
	const inherit = device === "desktop" ? [] : [[
		"inherit",
		labels.inheritAppearance,
		false,
		"image-rotate"
	]];
	const custom = values[widthKey] === "custom" || values[widthKey] === "inherit" && values.width === "custom";
	const row = (key, label, options) => h$3("div", {
		className: "rdct-bld-compact-layout__row",
		key
	}, h$3("span", null, label), h$3(ChoiceControl, {
		label,
		value: values[key],
		options,
		iconOnly: true,
		onChange: (value) => change(key, value)
	}));
	return h$3("div", { className: "rdct-bld-compact-layout" }, row(deviceKey(device, "align"), labels.alignment, [
		...inherit,
		[
			"start",
			labels.start,
			false,
			rtl ? "editor-alignright" : "editor-alignleft"
		],
		[
			"center",
			labels.center,
			false,
			"editor-aligncenter"
		],
		[
			"end",
			labels.end,
			false,
			rtl ? "editor-alignleft" : "editor-alignright"
		]
	]), row(widthKey, labels.elementWidth, [
		...inherit,
		[
			"equal",
			labels.equalWidth,
			false,
			"editor-expand"
		],
		[
			"auto",
			labels.autoWidth,
			false,
			"editor-contract"
		],
		[
			"full",
			labels.fullWidth,
			false,
			"align-full-width"
		],
		[
			"custom",
			labels.customWidth,
			false,
			"edit"
		]
	]), custom && h$3("div", { className: "rdct-bld-compact-layout__size" }, h$3("span", null, labels.customWidth), h$3(RangeControl, {
		label: labels.customWidth,
		numeric: true,
		unit: "%",
		min: 5,
		max: 100,
		value: values[deviceKey(device, "customWidth")] ?? values.customWidth ?? 50,
		onChange: (value) => change(deviceKey(device, "customWidth"), value)
	})));
}
//#endregion
//#region radmanct/src/js/admin/builder-controls.js
var { createElement: h$2, useState: useState$1 } = window.wp.element;
function AppearanceControls({ value = {}, row = false, section = "all", update, labels, device: activeDevice, showDeviceControl = true, showFontSize = true, colorLabel = labels.textColor, direction = window.rdctBuilder?.direction || "ltr" }) {
	const defaults = row ? {
		visibility: "all",
		align: value.rowLayout === "grid" ? "start" : "center",
		rowLayout: "flex",
		direction: "row",
		wrap: "wrap",
		columns: 3,
		mobileColumns: 1,
		gap: 24,
		padding: 16,
		background: "",
		justify: "start",
		color: "",
		mobileLayout: "stack",
		mobileGap: null,
		mobilePadding: null
	} : {
		visibility: "all",
		align: "start",
		width: "equal",
		color: "",
		fontSize: 0,
		mobileFontSize: 0
	};
	const [localDevice, setDevice] = useState$1("desktop");
	const device = activeDevice || localDevice;
	const values = {
		...defaults,
		...responsiveDefaults(row),
		...value
	};
	const inherited = device === "desktop" ? [] : [["inherit", labels.inheritAppearance]];
	const show = (name) => section === "all" || section === name;
	const change = (key, value) => update({
		...values,
		[key]: value,
		...key === "rowLayout" && value === "grid" ? { align: "start" } : {},
		...row && device !== "desktop" && key === deviceKey(device, "direction") ? { [`${device}Layout`]: "inline" } : {}
	});
	const select = (key, title, options, selected = values[key]) => h$2("fieldset", { key }, h$2("legend", null, title), h$2(ChoiceControl, {
		label: title,
		value: selected,
		onChange: (value) => change(key, value),
		options
	}));
	const number = (base, title, max) => {
		const key = deviceKey(device, base);
		const inherit = device !== "desktop" && !(base === "columns" && device === "mobile");
		return h$2("fieldset", { key }, h$2("legend", null, title), inherit && h$2("label", null, h$2(SwitchControl, {
			checked: values[key] === null,
			onChange: (checked) => change(key, checked ? null : values[base])
		}), labels.useDesktopValue), h$2(RangeControl, {
			label: title,
			numeric: true,
			unit: base === "columns" ? "" : "px",
			min: base === "columns" ? 1 : 0,
			max,
			value: values[key] ?? values[base],
			disabled: inherit && values[key] === null,
			onChange: (value) => change(key, value)
		}));
	};
	return h$2("div", { className: "rdct-bld-editor__property-grid" }, showDeviceControl && h$2("label", null, labels.device, h$2(SelectControl, {
		value: device,
		onChange: setDevice,
		options: devices.map(({ id }) => [id, labels[id]])
	})), show("advanced") && h$2("fieldset", null, h$2("legend", null, labels.hideOn), ...devices.map(({ id }) => h$2("label", { key: id }, h$2(SwitchControl, {
		checked: hiddenDevices(values).includes(id),
		onChange: (checked) => {
			const hidden = new Set(hiddenDevices(values));
			if (checked) hidden.add(id);
			else hidden.delete(id);
			update({
				...values,
				visibility: "all",
				hiddenDevices: [...hidden]
			});
		}
	}), labels[id]))), show("layout") && row && select(deviceKey(device, "align"), labels.alignItems, [
		...inherited,
		["start", labels.start],
		["center", labels.center],
		["end", labels.end],
		["stretch", labels.stretch]
	]), show("layout") && !row && itemLayoutControls({
		values,
		device,
		labels,
		change,
		direction
	}), show("layout") && row && select("rowLayout", labels.rowLayout, [["flex", labels.flexLayout], ["grid", labels.gridLayout]]), show("layout") && row && values.rowLayout === "flex" && select(deviceKey(device, "direction"), labels.flexDirection, [
		...inherited,
		["row", labels.horizontalFlow],
		["column", labels.verticalFlow]
	], values[deviceKey(device, "direction")] === "inherit" && values[`${device}Layout`] === "stack" ? "column" : values[deviceKey(device, "direction")]), show("layout") && row && values.rowLayout === "flex" && h$2("label", null, h$2(SwitchControl, {
		checked: values.wrap !== "nowrap",
		onChange: (checked) => change("wrap", checked ? "wrap" : "nowrap")
	}), labels.wrapItems), show("layout") && row && values.rowLayout === "grid" && number("columns", labels.responsiveColumns, device === "mobile" ? 3 : 6), show("layout") && row && select(deviceKey(device, "justify"), labels.justifyItems, [
		...inherited,
		["start", labels.start],
		["center", labels.center],
		["end", labels.end],
		["space-between", labels.spaceBetween]
	]), show("style") && !row && showFontSize && h$2("fieldset", null, h$2("legend", null, labels.responsiveFontSize), h$2(RangeControl, {
		label: labels.responsiveFontSize,
		numeric: true,
		min: 0,
		max: 96,
		value: values[deviceKey(device, "fontSize")],
		onChange: (value) => change(deviceKey(device, "fontSize"), value)
	})), show("style") && h$2(ColorField, {
		label: colorLabel,
		value: values.color,
		resetLabel: labels.inheritAppearance,
		onChange: (value) => change("color", value)
	}), show("layout") && row && ["gap", "padding"].map((key) => number(key, labels[key], key === "gap" ? 64 : 120)), show("style") && row && h$2(BackgroundField, {
		value: {
			...values.backgroundOptions,
			color: values.backgroundOptions?.color || values.background
		},
		labels: labels.backgroundFields,
		onChange: (next) => update({
			...values,
			background: next.color,
			backgroundOptions: next
		})
	}));
}
//#endregion
//#region radmanct/src/js/admin/builder-visual-fields.js
var { createElement: h$1 } = window.wp.element;
function VisualFields({ item, update, labels, device, section = "style" }) {
	const image = item.type === "image";
	const imageKey = (id) => deviceKey(id, image ? "imageId" : "logoImageId");
	const imageLabel = image ? labels.image : labels.logoImage;
	const number = (key, max, fallback = 0) => h$1("fieldset", { key }, h$1("legend", null, labels[key]), h$1(RangeControl, {
		label: labels[key],
		type: "number",
		numeric: true,
		min: 0,
		max,
		value: item[key] ?? fallback,
		onChange: (value) => update({ [key]: value })
	}));
	if (item.type === "logo" || image) return h$1("div", null, section === "content" && image && h$1("p", { className: "description" }, labels.builderImageHint), ...devices.filter(({ id }) => !device || device === id).map(({ id }) => h$1("fieldset", { key: id }, !device && h$1("legend", null, labels[id]), section === "content" && h$1("fieldset", null, h$1("legend", null, imageLabel), h$1(ButtonControl, {
		type: "button",
		onClick: () => pickImage({
			title: imageLabel,
			id: item[imageKey(id)] || 0,
			onSelect: (imageId) => update({ [imageKey(id)]: imageId })
		})
	}, item[imageKey(id)] ? labels.changeImage : labels.chooseImage), !!item[imageKey(id)] && h$1(ButtonControl, {
		type: "button",
		onClick: () => update({ [imageKey(id)]: 0 })
	}, labels.remove), !image && h$1("p", { className: "description" }, id === "desktop" ? labels.logoSiteFallback : labels.logoDeviceFallback)), ...(section === "style" ? [[
		image ? "imageWidth" : "logoWidth",
		512,
		image ? labels.imageResponsiveWidth : labels.logoResponsiveWidth
	], [
		image ? "imageHeight" : "logoHeight",
		256,
		image ? labels.imageResponsiveHeight : labels.logoResponsiveHeight
	]] : []).map(([base, max, label]) => {
		const key = deviceKey(id, base);
		return h$1("fieldset", { key }, h$1("legend", null, label), h$1(RangeControl, {
			label,
			type: "number",
			numeric: true,
			min: 0,
			max,
			value: item[key] ?? 0,
			onChange: (value) => update({ [key]: value })
		}));
	}))), section === "content" && !image && window.rdctBuilder.kind === "header" && [
		"lightSectionLogoId",
		"darkSectionLogoId",
		"stickyLogoId"
	].map((key) => h$1("fieldset", { key }, h$1("legend", null, labels[key]), h$1(ButtonControl, {
		type: "button",
		onClick: () => pickImage({
			title: labels[key],
			id: item[key] || 0,
			onSelect: (id) => update({ [key]: id })
		})
	}, item[key] ? labels.changeImage : labels.chooseImage), !!item[key] && h$1(ButtonControl, {
		type: "button",
		onClick: () => update({ [key]: 0 })
	}, labels.remove))), section === "style" && h$1("p", { className: "description" }, labels.logoSizeHint));
	return h$1("div", null, h$1("label", null, labels.buttonStyle, h$1(SelectControl, {
		value: item.buttonStyle || "solid",
		onChange: (value) => update({ buttonStyle: value }),
		options: [
			"solid",
			"outline",
			"plain"
		].map((value) => [value, labels[`${value}Button`]])
	})), h$1(ColorField, {
		label: labels.buttonBackground,
		value: item.buttonBackground || "",
		fallback: "#21685a",
		resetLabel: labels.inheritAppearance,
		onChange: (value) => update({ buttonBackground: value })
	}), h$1("label", null, h$1(SwitchControl, {
		checked: item.buttonRadius == null,
		onChange: (checked) => update({ buttonRadius: checked ? null : 6 })
	}), labels.inheritButtonRadius), item.buttonRadius != null && number("buttonRadius", 64), number("buttonPaddingX", 64, 19), number("buttonPaddingY", 48, 10));
}
//#endregion
//#region radmanct/src/js/admin/builder-inspector.js
var { createElement: h, useState, useEffect } = window.wp.element;
var fieldLoaders = {
	button: () => __vitePreload(() => import("./builder-button-fields-B1Pa5J_b.js").then((module) => module.ButtonFields), [], import.meta.url),
	menu: () => __vitePreload(() => import("./builder-menu-fields-CV4h8qiL.js").then((module) => module.MenuFields), [], import.meta.url),
	panel: () => __vitePreload(() => import("./builder-menu-panel-fields-DEoZ4eEJ.js").then((module) => module.MenuPanelFields), [], import.meta.url),
	linked: () => __vitePreload(() => import("./builder-linked-fields-DuzZ9GMF.js").then((module) => module.LinkedFields), [], import.meta.url),
	scheme: () => __vitePreload(() => import("./builder-scheme-fields-CdNoKtZM.js").then((module) => module.SchemeFields), [], import.meta.url)
};
function Inspector({ item, row, update, updateRow, labels, config, actions, device = "desktop", onDeviceChange, onLoadingChange }) {
	const [tab, setTab] = useState("content");
	const sharedButton = item?.type === "button" && Object.keys(config.buttonFields || {}).length > 0;
	const fieldKey = sharedButton ? "button" : item?.type === "theme-toggle" ? "scheme" : ["menu", "panel"].includes(item?.type) ? item.type : [
		"icon",
		"contact",
		"social"
	].includes(item?.type) ? "linked" : null;
	const [loaded, setLoaded] = useState({});
	const [failed, setFailed] = useState(null);
	const [attempt, setAttempt] = useState(0);
	const Fields = loaded[fieldKey];
	useEffect(() => {
		if (!fieldKey || Fields) return;
		let active = true;
		setFailed(null);
		fieldLoaders[fieldKey]().then((Component) => {
			if (active) setLoaded((previous) => ({
				...previous,
				[fieldKey]: Component
			}));
		}).catch(() => {
			if (active) setFailed(fieldKey);
		});
		return () => {
			active = false;
		};
	}, [
		fieldKey,
		Fields,
		attempt
	]);
	const loading = !!fieldKey && !Fields && failed !== fieldKey;
	useEffect(() => {
		onLoadingChange?.(loading);
	}, [loading, onLoadingChange]);
	useEffect(() => () => onLoadingChange?.(false), [onLoadingChange]);
	const panelConfig = {
		...config,
		navigationPanels: config.panelFields
	};
	const appearance = (section) => section === "style" && (item?.type === "image" || item?.type === "theme-toggle" || sharedButton) ? null : h(AppearanceControls, {
		row: !item,
		section,
		device,
		showDeviceControl: false,
		showFontSize: item?.type !== "panel" || !!item.triggerLabel?.trim(),
		colorLabel: item?.type === "panel" ? labels.panelTriggerColor : labels.textColor,
		value: item?.appearance || row.appearance,
		labels,
		update: item ? (value) => update({ appearance: value }) : updateRow
	});
	return h("div", { className: "rdct-bld-inspector" }, h("header", { className: "rdct-bld-panel-heading" }, h("h3", null, item ? labels[item.type] : labels.rowSettings), h("span", {
		className: "rdct-bld-device-badge",
		title: labels.device
	}, labels[device])), fieldKey && !Fields && failed === fieldKey && h("div", { role: "alert" }, h("p", null, labels.requestError), h(ButtonControl, { onClick: () => setAttempt((value) => value + 1) }, labels.retryFields)), h(PanelTabs, {
		id: "rdct-inspector",
		value: tab,
		onChange: setTab,
		tabs: [
			{
				id: "content",
				label: labels.contentTab,
				icon: "edit",
				content: h("div", null, !item && appearance("layout"), item && h("div", null, ["logo", "image"].includes(item.type) && h(VisualFields, {
					item,
					update,
					labels,
					device,
					section: "content"
				}), item.type === "container" && h("div", null, h("h4", null, labels.innerLayout), h(AppearanceControls, {
					row: true,
					section: "layout",
					device,
					showDeviceControl: false,
					value: item.layout,
					labels,
					update: (layout) => update({ layout })
				})), fieldKey === "linked" && Fields && h(Fields, {
					item,
					update,
					config,
					labels
				}), ["text", "button"].includes(item.type) && h("label", null, labels.text, h(item.type === "button" ? InputControl : TextareaControl, {
					value: item.text,
					maxLength: 500,
					...item.type === "text" ? { rows: 4 } : {},
					onChange: (value) => update({ text: value })
				})), item.type === "button" && h("label", null, labels.url, h(InputControl, {
					dir: "ltr",
					value: item.url,
					maxLength: 2048,
					onChange: (value) => update({ url: value })
				})), item.type === "button" && h("label", null, h(SwitchControl, {
					checked: item.newTab || false,
					onChange: (newTab) => update({ newTab })
				}), labels.newTab), sharedButton && Fields && h(Fields, {
					item,
					update,
					config,
					labels,
					device,
					section: "content"
				}), item.type === "panel" && Fields && h(Fields, {
					item,
					update,
					labels,
					device,
					config: panelConfig
				}), item.type === "theme-toggle" && h("a", { href: config.settingsUrl.replace(/#.*$/, "#rdct-design") }, labels.colorSchemeSettings), item.type === "theme-toggle" && Fields && h(Fields, {
					item,
					update,
					config,
					labels,
					section: "content"
				}), item.type === "menu" && Fields && h(Fields, {
					item,
					update,
					config,
					labels,
					device
				}), h("div", { className: "rdct-bld-inspector__layout" }, appearance("layout"))))
			},
			{
				id: "style",
				label: labels.styleTab,
				icon: "admin-appearance",
				content: h("div", null, config.kind === "header" && item && ![
					"container",
					"image",
					"block",
					"search",
					"theme-toggle"
				].includes(item.type) && h("label", null, labels.headerColorMode, h(SelectControl, {
					value: item.headerColorMode || "auto",
					onChange: (headerColorMode) => update({ headerColorMode }),
					options: [
						["auto", labels.headerColorAuto],
						["inherit", labels.followSectionColors],
						["fixed", labels.headerColorFixed]
					]
				})), item && [
					"logo",
					"image",
					"button"
				].includes(item.type) && !sharedButton && h(VisualFields, {
					item,
					update,
					labels,
					device
				}), sharedButton && Fields && h(Fields, {
					item,
					update,
					config,
					labels,
					device
				}), item?.type === "theme-toggle" && Fields && h(Fields, {
					item,
					update,
					config,
					labels,
					section: "style"
				}), item?.type === "panel" && Fields && h(Fields, {
					item,
					update,
					labels,
					device,
					group: "style",
					config: panelConfig
				}), item?.type === "menu" && Fields && h(Fields, {
					item,
					update,
					config,
					labels,
					device,
					tab: "style"
				}), appearance("style"))
			},
			{
				id: "advanced",
				label: labels.advancedTab,
				icon: "admin-generic",
				content: h("div", null, appearance("advanced"), h("div", { className: "rdct-bld-editor__actions" }, ...actions))
			}
		]
	}));
}
//#endregion
export { Inspector };
