import { t as ButtonControl } from "./controls-DqGpx29l.js";
import { t as navigateTabs } from "./tab-navigation-BleN_2tI.js";
//#region shared/admin/panel-tabs.js
var { createElement: h } = window.wp.element;
var selectedTab = (tabs, value) => tabs.find((tab) => tab.id === value && !tab.disabled)?.id ?? tabs.find((tab) => !tab.disabled)?.id;
function TabList({ id, value, onChange, tabs, className = "", label, orientation = "horizontal" }) {
	const active = selectedTab(tabs, value);
	return h("div", {
		className: `rdct-pnl-tabs ${className}`.trim(),
		role: "tablist",
		"aria-label": label,
		"aria-orientation": orientation,
		onKeyDown: (event) => navigateTabs(event, onChange, orientation)
	}, ...tabs.map((tab) => h(ButtonControl, {
		key: tab.id,
		id: `${id}-${tab.id}-tab`,
		"data-tab-id": tab.id,
		disabled: tab.disabled,
		type: "button",
		role: "tab",
		"aria-selected": active === tab.id,
		"aria-controls": `${id}-${tab.id}-panel`,
		tabIndex: active === tab.id ? 0 : -1,
		onClick: () => onChange(tab.id)
	}, tab.icon && h("span", {
		className: `dashicons dashicons-${tab.icon}`,
		"aria-hidden": true
	}), tab.label)));
}
function PanelTabs({ id, value, onChange, tabs, label, orientation }) {
	value = selectedTab(tabs, value);
	return h("div", null, h(TabList, {
		id,
		value,
		onChange,
		tabs,
		label,
		orientation
	}), ...tabs.map((tab) => h("div", {
		key: tab.id,
		id: `${id}-${tab.id}-panel`,
		role: "tabpanel",
		"aria-labelledby": `${id}-${tab.id}-tab`,
		hidden: value !== tab.id,
		className: "rdct-pnl-content"
	}, tab.content)));
}
//#endregion
export { TabList as n, PanelTabs as t };
