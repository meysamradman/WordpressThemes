import { r as FieldGroup } from "./controls-DqGpx29l.js";
import { c as ToggleField, o as InputField, s as SelectField, t as ContentLayoutFields } from "./settings-Dw9Ggmle.js";
import { t as BackgroundField } from "./background-field-Farap2u6.js";
import { t as PageTitleFields } from "./page-title-fields-DHhcVJML.js";
//#region radmanct/src/js/admin/content-color-fields.js
var { createElement: h$2 } = window.wp.element;
function ContentColorFields({ scope, values, change, config }) {
	return h$2(FieldGroup, { title: config.labels.contentColors }, h$2("p", null, config.labels.contentColorsHint), h$2(BackgroundField, {
		value: {
			...values[`${scope}_background`],
			color: values[`${scope}_background`]?.color || values[`${scope}_color_background`]
		},
		labels: config.labels.backgroundFields,
		onChange: (next) => {
			change(`${scope}_background`, next);
			change(`${scope}_color_background`, next.color);
		}
	}));
}
//#endregion
//#region radmanct/src/js/admin/sidebar-fields.js
var { createElement: h$1 } = window.wp.element;
function SidebarFields({ scope, config, values, change }) {
	const { labels } = config;
	const prefix = `${scope}_sidebar_`;
	const options = [...config.widgetAreas || []];
	const selected = values[`${prefix}area`];
	if (selected && !options.some(([id]) => id === selected)) options.push([selected, labels.sidebarUnavailable]);
	return h$1(FieldGroup, { title: labels.sidebar }, h$1(ToggleField, {
		name: `${prefix}enabled`,
		label: labels.sidebarEnabled,
		value: values[`${prefix}enabled`],
		onChange: change
	}), values[`${prefix}enabled`] && h$1("div", null, h$1(SelectField, {
		name: `${prefix}area`,
		label: labels.sidebarArea,
		value: selected,
		options,
		onChange: change
	}), h$1(SelectField, {
		name: `${prefix}position`,
		label: labels.sidebarPosition,
		value: values[`${prefix}position`],
		options: [["start", labels.sidebarStart], ["end", labels.sidebarEnd]],
		onChange: change
	}), h$1(InputField, {
		name: `${prefix}width`,
		label: labels.sidebarWidth,
		value: values[`${prefix}width`],
		type: "number",
		numeric: true,
		min: 240,
		max: 480,
		step: 1,
		onChange: change
	}), config.widgetsUrl && h$1("a", {
		className: "rdct-btn",
		href: config.widgetsUrl
	}, labels.manageWidgets)));
}
//#endregion
//#region radmanct/src/js/admin/content-view-fields.js
var { createElement: h } = window.wp.element;
function ContentViewFields({ scope, config, values, change }) {
	const { labels } = config;
	const [content, view] = scope.split("_");
	const renderer = `${scope}_renderer`;
	return h("div", null, !config.contentModules[content] && h("p", { className: "description" }, labels.moduleDisabled), config.contentModules[content] && h("div", { className: "rdct-stg__actions" }, config.contentLinks?.items?.[content] && h("a", {
		className: "rdct-btn",
		href: config.contentLinks.items[content]
	}, labels.manageContentItems), config.contentLinks?.archives?.[content] && h("a", {
		className: "rdct-btn",
		href: config.contentLinks.archives[content],
		target: "_blank",
		rel: "noopener noreferrer"
	}, labels.viewContentArchive)), h(SelectField, {
		name: renderer,
		label: view === "single" ? labels.singleDesign : labels.archiveDesign,
		value: values[renderer],
		onChange: change,
		options: [["custom", labels.customDesign], ["elementor", labels.elementorDesign]]
	}), values[renderer] === "elementor" && !config.elementorPro && h("p", { className: "description" }, labels.proUnavailable), h(FieldGroup, {
		title: labels.layout,
		hint: labels.contentLayoutsHint
	}, h(ContentLayoutFields, {
		scope,
		config,
		values,
		change
	})), h(ContentColorFields, {
		scope,
		config,
		values,
		change
	}), h(PageTitleFields, {
		idPrefix: `rdct-${scope}-title`,
		value: values[`${scope}_page_title`],
		change: (next) => change(`${scope}_page_title`, next),
		labels
	}), h(SidebarFields, {
		scope,
		config,
		values,
		change
	}));
}
//#endregion
export { ContentViewFields };
