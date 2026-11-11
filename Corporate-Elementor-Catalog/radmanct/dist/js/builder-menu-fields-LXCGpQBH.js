import { c as SelectControl, i as FieldRow, l as SwitchControl, n as ChoiceControl, r as FieldGroup, s as RangeControl } from "./controls-DqGpx29l.js";
import { t as ColorField } from "./color-field-DsbXJp0l.js";
import { t as deviceKey } from "./builder-tbvX7Bt0.js";
import { MenuPanelFields } from "./builder-menu-panel-fields-R9rQJ_tr.js";
//#region radmanct/src/js/admin/builder-menu-fields.js
var { createElement: h } = window.wp.element;
function MenuFields({ item, update, config, labels, tab = "content", device = "desktop" }) {
	const drawerDevices = item.drawerDevices ?? (item.mobileDisplay === "drawer" ? ["mobile"] : []);
	const panelActive = drawerDevices.includes(device);
	const gapKey = deviceKey(device, "menuGap");
	const gap = item[gapKey] ?? null;
	const inheritedGap = [
		"desktop",
		"laptop",
		"tablet",
		"mobile"
	].slice(0, [
		"desktop",
		"laptop",
		"tablet",
		"mobile"
	].indexOf(device)).reverse().map((entry) => item[deviceKey(entry, "menuGap")]).find((value) => value != null) ?? item.menuGap ?? (item.menuLayout === "vertical" || device === "mobile" ? 4 : 24);
	const menu = (key, title, empty) => h("label", null, title, h(SelectControl, {
		value: item[key] || 0,
		numeric: true,
		onChange: (value) => update({ [key]: value }),
		options: [
			[0, empty],
			...!item[key] || config.menus.some((entry) => entry.id === item[key]) ? [] : [[item[key], `#${item[key]}`]],
			...config.menus.map((entry) => [entry.id, entry.title])
		]
	}));
	if (tab === "style") return h("div", null, h("fieldset", null, h("legend", null, labels.menuItemGap), h("div", { className: "rdct-bld__range-reset" }, h(RangeControl, {
		label: labels.menuItemGap,
		value: gap,
		min: 0,
		max: 128,
		numeric: true,
		fallback: inheritedGap,
		placeholder: String(inheritedGap),
		onChange: (value) => update({ [gapKey]: value })
	}), h("button", {
		type: "button",
		className: "button button-small",
		title: labels.inheritAppearance,
		"aria-label": labels.inheritAppearance,
		disabled: gap === null,
		onClick: () => update({ [gapKey]: null })
	}, h("span", {
		className: "dashicons dashicons-image-rotate",
		"aria-hidden": true
	})))), h("label", null, labels.menuSkin, h(SelectControl, {
		value: item.menuSkin || "plain",
		options: Object.entries(config.menuSkins ?? {}),
		onChange: (value) => update({ menuSkin: value })
	})), ...Object.entries(config.menuHoverFields ?? {}).map(([key, field]) => h("label", { key }, field.label, h(SelectControl, {
		value: item[key] ?? field.default,
		options: Object.entries(field.options),
		onChange: (value) => update({ [key]: value })
	}))), h("div", { className: "rdct-admin-fields" }, h(FieldGroup, { title: labels.submenuStyle }, Object.entries(config.navigationPanels ?? {}).filter(([, field]) => field.group === "submenu").map(([key, field]) => h(FieldRow, {
		key,
		label: field.label
	}, field.color ? h(ColorField, {
		label: field.label,
		showLabel: false,
		value: item[key] ?? field.default,
		resetLabel: labels.inheritAppearance,
		onChange: (value) => update({ [key]: value })
	}) : h(RangeControl, {
		label: field.label,
		value: item[key] ?? field.default,
		min: field.min,
		max: field.max,
		numeric: true,
		unit: "ms",
		onChange: (value) => update({ [key]: value === "" ? field.default : value })
	}))))), item.submenuStyle === "mega" && h("div", null, h("fieldset", null, h("legend", null, labels.megaColumns), h(RangeControl, {
		label: labels.megaColumns,
		value: item.megaColumns ?? 3,
		min: 2,
		max: 6,
		numeric: true,
		unit: "",
		onChange: (value) => update({ megaColumns: value })
	})), h("fieldset", null, h("legend", null, labels.megaWidth), h(RangeControl, {
		label: labels.megaWidth,
		value: item.megaWidth ?? 960,
		min: 320,
		max: 1440,
		step: 1,
		numeric: true,
		onChange: (value) => update({ megaWidth: value })
	}))), panelActive && h(MenuPanelFields, {
		item,
		update,
		config,
		labels,
		device,
		group: "style"
	}));
	return h("div", null, device !== "mobile" && menu("menuId", labels.selectMenu, labels.locationMenu), device === "mobile" && menu("mobileMenuId", labels.mobileMenu, labels.sameMenu), h("fieldset", null, h("legend", null, labels.submenuStyle), h(ChoiceControl, {
		label: labels.submenuStyle,
		value: item.submenuStyle || "dropdown",
		options: [["dropdown", labels.dropdownMenu], ["mega", labels.megaMenu]],
		onChange: (value) => update({ submenuStyle: value })
	})), h("p", { className: "description" }, labels.submenuTypeHint), config.menusUrl && h("a", {
		className: "rdct-btn",
		href: config.menusUrl,
		target: "_blank",
		rel: "noopener noreferrer"
	}, labels.manageMenus), h("label", null, labels.submenuTrigger, h(SelectControl, {
		value: item.submenuTrigger || "click",
		options: [["click", labels.clickMenu], ["hover", labels.hoverMenu]],
		onChange: (value) => update({ submenuTrigger: value })
	})), item.submenuStyle === "mega" && h("p", { className: "description" }, labels.megaHint), h("label", null, labels.menuLayout, h(SelectControl, {
		value: item.menuLayout || "horizontal",
		onChange: (value) => update({ menuLayout: value }),
		options: [["horizontal", labels.horizontalMenu], ["vertical", labels.verticalMenu]]
	})), device === "mobile" && h("label", null, labels.mobileMenuDisplay, h(SelectControl, {
		value: item.mobileDisplay || "dropdown",
		onChange: (value) => update({
			mobileDisplay: value,
			drawerDevices: value === "drawer" ? [.../* @__PURE__ */ new Set([...drawerDevices, "mobile"])] : drawerDevices.filter((entry) => entry !== "mobile")
		}),
		options: [
			["dropdown", labels.dropdownMenu],
			["drawer", labels.drawerMenu],
			["expanded", labels.expandedMenu]
		]
	})), device !== "mobile" && h("label", null, h(SwitchControl, {
		checked: panelActive,
		onChange: (checked) => update({ drawerDevices: checked ? [.../* @__PURE__ */ new Set([...drawerDevices, device])] : drawerDevices.filter((entry) => entry !== device) })
	}), labels.useDevicePanel), panelActive && h("label", null, labels.drawerSide, h(SelectControl, {
		value: item.drawerSide || "start",
		onChange: (value) => update({ drawerSide: value }),
		options: [["start", labels.start], ["end", labels.end]]
	})), panelActive && h(MenuPanelFields, {
		item,
		update,
		config,
		labels,
		device
	}));
}
//#endregion
export { MenuFields };
