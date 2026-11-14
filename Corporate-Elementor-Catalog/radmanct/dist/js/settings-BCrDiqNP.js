import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { a as InputControl, c as SelectControl, i as FieldRow, l as SwitchControl, o as LoadingStatus, s as RangeControl, t as ButtonControl, u as TextareaControl } from "./controls-DqGpx29l.js";
import { t as navigateTabs } from "./tab-navigation-BleN_2tI.js";
import { t as ColorField } from "./color-field-DsbXJp0l.js";
//#region shared/admin/section-navigation.js
var { createElement: h$5, useState: useState$1, useEffect: useEffect$1 } = window.wp.element;
function SectionNavigation({ groups, value, onChange, label, id = "rdct" }) {
	const parent = groups.find((group) => group.items.some((item) => item.id === value))?.id;
	const [expanded, setExpanded] = useState$1(() => parent ?? null);
	useEffect$1(() => {
		setExpanded(parent ?? null);
	}, [parent]);
	return h$5("nav", {
		className: "rdct-adm-menu rdct-adm-scroll",
		"aria-label": label
	}, ...groups.map((group) => {
		const open = expanded === group.id;
		return h$5("section", { key: group.id }, h$5("h3", null, h$5(ButtonControl, {
			variant: "ghost",
			icon: group.icon,
			className: "rdct-adm-menu__toggle",
			"aria-expanded": open,
			"aria-controls": `${id}-group-${group.id}`,
			onClick: () => setExpanded((current) => current === group.id ? null : group.id)
		}, h$5("span", null, group.label), h$5("span", {
			className: "dashicons dashicons-arrow-down-alt2 rdct-adm-menu__arrow",
			"aria-hidden": true
		}))), h$5("div", {
			className: "rdct-adm-menu__reveal",
			"data-open": open,
			"aria-hidden": !open
		}, h$5("div", {
			id: `${id}-group-${group.id}`,
			inert: open ? void 0 : "",
			className: "rdct-adm-menu__children",
			role: "tablist",
			"aria-label": group.label,
			"aria-orientation": "vertical",
			onKeyDown: (event) => navigateTabs(event, onChange, "vertical")
		}, ...group.items.map((item, index) => h$5(ButtonControl, {
			key: item.id,
			variant: "ghost",
			className: "rdct-adm-menu__item",
			id: `${id}-tab-${item.id}`,
			"data-section": item.id,
			"data-tab-id": item.id,
			role: "tab",
			"aria-selected": value === item.id,
			"aria-controls": `${id}-panel-${item.id}`,
			tabIndex: value === item.id || !group.items.some((entry) => entry.id === value) && index === 0 ? 0 : -1,
			onClick: () => onChange(item.id)
		}, item.label)))));
	}));
}
//#endregion
//#region radmanct/src/js/admin/settings-fonts.js
function primaryFontGroup(config, values) {
	return values.font_language && values.font_language !== "auto" ? values.font_language : config.primaryFontGroup;
}
function fontGroups(config, values) {
	return values.mixed_language_fonts || config.multilingualFonts ? ["persian", "latin"] : [primaryFontGroup(config, values)];
}
//#endregion
//#region radmanct/src/js/admin/fields.js
var { createElement: h$4 } = window.wp.element;
function ColorSetting({ name, label, value, onChange, fallback, resetLabel, resetValue = "", description }) {
	return h$4(Field, {
		name,
		label,
		description
	}, h$4(InputControl, {
		type: "hidden",
		name: `radman_settings[${name}]`,
		value: value ?? "",
		onChange: () => {}
	}), h$4(ColorField, {
		id: `rdct-${name}`,
		label,
		showLabel: false,
		value: value ?? "",
		fallback,
		resetLabel,
		onChange: (next) => onChange(name, next || resetValue)
	}));
}
function Field({ label, name, description, children }) {
	return h$4(FieldRow, {
		className: "rdct-stg__field",
		label,
		id: `rdct-${name}`
	}, children, description && h$4("p", {
		className: "rdct-stg__hint",
		id: `rdct-${name}-hint`
	}, description));
}
function InputField({ name, label, description, value, onChange, children, ...attributes }) {
	return h$4(Field, {
		name,
		label,
		description
	}, h$4(attributes.type === "number" ? RangeControl : InputControl, {
		...attributes.type === "number" ? { label } : {},
		id: `rdct-${name}`,
		name: `radman_settings[${name}]`,
		value,
		onChange: (next) => onChange(name, next),
		"aria-describedby": description ? `rdct-${name}-hint` : void 0,
		...attributes
	}), children);
}
function SelectField({ name, label, description, value, onChange, options, children, ...attributes }) {
	return h$4(Field, {
		name,
		label,
		description
	}, h$4(SelectControl, {
		id: `rdct-${name}`,
		name: `radman_settings[${name}]`,
		value,
		onChange: (next) => onChange(name, next),
		options,
		"aria-describedby": description ? `rdct-${name}-hint` : void 0,
		...attributes
	}), children);
}
function FontField({ name, label, choices, value, onChange, preview, language, registry, description, previewLabel, inherited, weight }) {
	const family = registry[value || inherited]?.family ?? "sans-serif";
	return h$4(SelectField, {
		label,
		name,
		description,
		value,
		onChange,
		options: choices
	}, preview && h$4("p", {
		className: "rdct-stg__font-preview",
		lang: language,
		dir: language === "fa" ? "rtl" : "ltr",
		"aria-label": previewLabel,
		style: {
			fontFamily: `"${family}", sans-serif`,
			fontWeight: weight || void 0
		}
	}, preview));
}
function FontFaces({ config, values, groups = fontGroups(config, values) }) {
	return h$4("style", null, [...new Set(Object.entries(values).filter(([key, value]) => groups.some((group) => key.endsWith(`${group}_font`)) && value).map(([, value]) => value))].flatMap((key) => {
		const font = config.fonts[key];
		if (!font) return [];
		return font.files.map(([file, weight]) => `@font-face{font-family:"${font.family}";src:url("${config.fontBase}${file.split("/").map(encodeURIComponent).join("/")}") format("woff2");font-weight:${weight};font-style:normal;font-display:swap;}`);
	}).join(""));
}
function ToggleField({ name, label, description, value, onChange }) {
	return h$4(Field, {
		name,
		label,
		description
	}, h$4("input", {
		type: "hidden",
		name: `radman_settings[${name}]`,
		value: "0"
	}), h$4(SwitchControl, {
		id: `rdct-${name}`,
		name: `radman_settings[${name}]`,
		value: "1",
		checked: value,
		"aria-describedby": description ? `rdct-${name}-hint` : void 0,
		onChange: (next) => onChange(name, next)
	}));
}
//#endregion
//#region radmanct/src/js/admin/settings-save.js
async function saveSettings(config, values, request = fetch) {
	const body = new URLSearchParams({
		action: "rdct_save_settings",
		nonce: config.save.nonce,
		values: JSON.stringify(values)
	});
	const response = await request(config.save.url, {
		method: "POST",
		credentials: "same-origin",
		cache: "no-store",
		body
	});
	const result = await response.json().catch(() => null);
	const saved = result?.data?.values;
	if (!response.ok || result?.success !== true || !saved || typeof saved !== "object" || Array.isArray(saved) || Object.keys(config.defaults).some((key) => !Object.hasOwn(saved, key))) throw new Error(typeof result?.data?.message === "string" ? result.data.message : config.labels.saveError);
	return saved;
}
function mergeSaved(current, submitted, saved) {
	return Object.fromEntries(Object.entries(saved).map(([key, value]) => [key, current[key] === submitted[key] ? value : current[key]]));
}
//#endregion
//#region radmanct/src/js/admin/settings-reset.js
function resetSettings(current, defaults, section, sectionKeys = [], canEditCode = false, primaryGroup = "persian") {
	const keys = Object.keys(defaults).filter((key) => {
		if (!canEditCode && (key.startsWith("custom_css") || key === "custom_js")) return false;
		if (!section) return true;
		if (section === "typography_loading") return [
			"block_google_fonts",
			"font_language",
			"mixed_language_fonts"
		].includes(key);
		if (["typography_primary", "typography_secondary"].includes(section)) {
			const group = section === "typography_primary" ? primaryGroup : primaryGroup === "persian" ? "latin" : "persian";
			return key.includes(`${group}_`) || /^(body|headings|h[1-6]|link|span)(?:_hover)?_color$/.test(key);
		}
		if (section === "typography") return /^(body|headings|h[1-6]|link|span)(?:_hover)?_color$/.test(key) || key.endsWith("_font") || key.endsWith("_weight") || key.endsWith("_line_height") || /^(body|headings|h[1-6]|link|span)_(persian|latin)(_(tablet|mobile))?_size$/.test(key) || [
			"block_google_fonts",
			"font_language",
			"mixed_language_fonts"
		].includes(key);
		if (section === "layout") return key === "container_width" || key === "reading_width" || key.startsWith("content_");
		if (section === "design") return key === "brand_color";
		if (/^(blog|catalog|portfolio)_(archive|single)$/.test(section)) return key.startsWith(`${section}_`);
		return sectionKeys.includes(key);
	});
	return {
		...current,
		...Object.fromEntries(keys.map((key) => [key, defaults[key]]))
	};
}
//#endregion
//#region radmanct/src/js/admin/cursor-fields.js
var { createElement: h$3 } = window.wp.element;
var cursorKeys = [
	"custom_cursor",
	"cursor_hide_outer",
	"cursor_inner_size",
	"cursor_outer_size",
	"cursor_color",
	"cursor_hover_color",
	"cursor_blend",
	"cursor_image_label",
	"cursor_carousel_label",
	"cursor_hover_effect",
	"cursor_follow_duration",
	"cursor_hover_duration",
	"cursor_hover_scale"
];
function CursorFields({ values, change, labels, defaults }) {
	const input = (name, label, type, extra = {}) => h$3(InputField, {
		name,
		label,
		type,
		value: values[name],
		onChange: change,
		...extra
	});
	return h$3("div", null, h$3("p", null, labels.cursorHint), h$3(ToggleField, {
		name: "custom_cursor",
		label: labels.customCursor,
		description: labels.customCursorHint,
		value: values.custom_cursor,
		onChange: change
	}), h$3("div", { hidden: !values.custom_cursor }, h$3(ToggleField, {
		name: "cursor_hide_outer",
		label: labels.cursorHideOuter,
		value: values.cursor_hide_outer,
		onChange: change
	}), input("cursor_inner_size", labels.cursorInnerSize, "number", {
		min: 2,
		max: 24,
		step: 1,
		dir: "ltr"
	}), h$3("div", { hidden: values.cursor_hide_outer }, input("cursor_outer_size", labels.cursorOuterSize, "number", {
		min: 16,
		max: 120,
		step: 1,
		dir: "ltr"
	})), ...[["cursor_color", labels.cursorColor], ["cursor_hover_color", labels.cursorHoverColor]].map(([name, label]) => h$3(ColorSetting, {
		key: name,
		name,
		label,
		value: values[name],
		onChange: change,
		fallback: defaults[name],
		resetValue: defaults[name],
		resetLabel: labels.resetColor
	})), h$3(SelectField, {
		name: "cursor_blend",
		label: labels.cursorBlend,
		value: values.cursor_blend,
		onChange: change,
		options: [["normal", labels.normal], ["difference", labels.difference]]
	}), h$3(SelectField, {
		name: "cursor_hover_effect",
		label: labels.cursorHoverEffect,
		value: values.cursor_hover_effect,
		onChange: change,
		options: [
			["none", labels.cursorHoverNone],
			["scale", labels.cursorHoverScale],
			["fill", labels.cursorHoverFill],
			["magnetic", labels.cursorHoverMagnetic]
		]
	}), input("cursor_follow_duration", labels.cursorFollowDuration, "number", {
		min: .05,
		max: 1,
		step: .01,
		dir: "ltr"
	}), input("cursor_hover_duration", labels.cursorHoverDuration, "number", {
		min: .1,
		max: 1.2,
		step: .01,
		dir: "ltr"
	}), input("cursor_hover_scale", labels.cursorHoverScaleSize, "number", {
		min: 1,
		max: 4,
		step: .1,
		dir: "ltr"
	}), h$3("div", { hidden: values.cursor_hide_outer }, input("cursor_image_label", labels.cursorImageLabel, "text", {
		maxLength: 80,
		description: labels.cursorLabelHint
	}), input("cursor_carousel_label", labels.cursorCarouselLabel, "text", {
		maxLength: 80,
		description: labels.cursorLabelHint
	}))));
}
//#endregion
//#region radmanct/src/js/admin/layout-fields.js
var { createElement: h$2 } = window.wp.element;
function ContentLayoutFields({ scope = "content", values, change, config }) {
	const { labels } = config;
	const mode = `${scope}_layout`;
	const number = (field, range) => {
		const name = `${scope}_${field}`;
		return h$2(InputField, {
			key: field,
			name,
			label: labels[`layout_${field}`],
			type: "number",
			dir: "ltr",
			min: range[0],
			max: range[1],
			step: 1,
			placeholder: labels.inheritAppearance,
			value: values[name] ?? "",
			onChange: change
		});
	};
	return h$2("div", null, h$2(SelectField, {
		name: mode,
		label: labels.layoutMode,
		value: values[mode],
		onChange: change,
		options: [
			...scope === "content" ? [] : [["inherit", labels.inheritLayout]],
			["contained", labels.siteLayoutWidth],
			["full", labels.fullContentWidth],
			["custom", labels.customLayoutWidth]
		]
	}), h$2("div", { hidden: values[mode] === "inherit" }, ...Object.entries(config.layoutFields).map(([field, range]) => h$2("div", {
		key: field,
		hidden: field === "width" && values[mode] !== "custom"
	}, number(field, range)))));
}
//#endregion
//#region radmanct/src/js/admin/analytics-fields.js
var { createElement: h$1 } = window.wp.element;
function AnalyticsFields({ values, change, labels }) {
	return h$1("div", null, h$1(ToggleField, {
		name: "analytics_enabled",
		label: labels.analyticsEnabled,
		description: labels.analyticsHint,
		value: values.analytics_enabled,
		onChange: change
	}), h$1(Field, {
		name: "analytics_measurement_id",
		label: labels.analyticsCode,
		description: labels.analyticsCodeHint
	}, h$1(TextareaControl, {
		id: "rdct-analytics_measurement_id",
		name: "radman_settings[analytics_measurement_id]",
		dir: "ltr",
		rows: 4,
		maxLength: 8192,
		spellCheck: false,
		placeholder: "G-XXXXXXXXXX",
		value: values.analytics_measurement_id,
		onChange: (next) => change("analytics_measurement_id", next)
	})));
}
//#endregion
//#region radmanct/src/js/admin/settings-app.js
var { createElement: h, useState, useEffect, useRef } = window.wp.element;
var sections = {
	analytics: ["analytics_enabled", "analytics_measurement_id"],
	custom_css: [
		"custom_css",
		"custom_css_desktop",
		"custom_css_laptop",
		"custom_css_tablet",
		"custom_css_mobile"
	],
	custom_js: ["custom_js"],
	identity: ["svg_uploads"],
	typography_primary: [],
	typography_secondary: [],
	typography_loading: [
		"font_language",
		"mixed_language_fonts",
		"block_google_fonts"
	],
	design: [
		"brand_color",
		"color_scheme",
		"dark_background",
		"dark_surface",
		"dark_text",
		"dark_muted",
		"dark_border"
	],
	layout: [],
	page_title: ["page_title", "page_title_typography"],
	blog_archive: [],
	blog_single: [],
	catalog_archive: [],
	catalog_single: [],
	portfolio_archive: [],
	portfolio_single: [],
	blog_typography: ["blog_typography"],
	portfolio_typography: ["portfolio_typography"],
	catalog_typography: ["catalog_typography"],
	header_footer: [],
	motion: [
		"scroll_reveal",
		"smooth_scroll",
		"smooth_duration",
		"custom_scrollbar",
		"scrollbar_width",
		"scrollbar_thumb",
		"scrollbar_track"
	],
	cursor: cursorKeys,
	progress: [
		"scroll_progress",
		"scroll_progress_height",
		"scroll_progress_color",
		"scroll_progress_track"
	],
	preloader: [
		"preloader",
		"preloader_style",
		"preloader_background",
		"preloader_background_secondary",
		"preloader_color"
	]
};
function SettingsApp({ config }) {
	const { labels } = config;
	const [active, setActive] = useState(() => {
		const hash = window.location.hash.replace("#rdct-", "");
		const key = hash === "typography" ? "typography_primary" : hash;
		return Object.hasOwn(sections, key) && (!key.startsWith("custom_") || config.canEditCode) ? key : "identity";
	});
	const [values, setValues] = useState(config.values);
	const [savedValues, setSavedValues] = useState(config.values);
	const saveAction = useRef(null);
	const [builderDirty, setBuilderDirty] = useState(false);
	const [builderBusy, setBuilderBusy] = useState(false);
	const [builderMessage, setBuilderMessage] = useState("");
	const builderSave = useRef(null);
	const builderPending = useRef(false);
	const registerBuilderSave = useRef((save) => {
		builderSave.current = save;
	});
	const [panels, setPanels] = useState({});
	const [panelError, setPanelError] = useState("");
	const BuilderSettings = panels.header_footer;
	const TypographyFields = panels.typography;
	const ColorSettingsFields = panels.design;
	const ContentViewFields = panels.content;
	const PageTitleSettings = panels.page_title;
	const ContentTypographyFields = panels.content_typography;
	const contentTypographyView = /^(blog|portfolio|catalog)_typography$/.test(active);
	const contentView = /^(blog|catalog|portfolio)_(archive|single)$/.test(active);
	const typographyView = active.startsWith("typography_");
	const primaryGroup = primaryFontGroup(config, values);
	const fontGroup = active === "typography_secondary" ? primaryGroup === "persian" ? "latin" : "persian" : primaryGroup;
	const panelKey = contentTypographyView ? "content_typography" : contentView ? "content" : typographyView ? "typography" : active;
	useEffect(() => {
		const loaders = {
			page_title: () => __vitePreload(() => import("./page-title-settings-FhmPMPzB.js"), [], import.meta.url),
			content_typography: () => __vitePreload(() => import("./content-typography-fields-DHoJLOsp.js"), [], import.meta.url),
			content: () => __vitePreload(() => import("./content-view-fields-DFY1plpV.js"), [], import.meta.url),
			design: () => __vitePreload(() => import("./color-settings-fields-C4waMNTQ.js"), [], import.meta.url),
			typography: () => __vitePreload(() => import("./typography-fields-B70eGrZZ.js").then((n) => n.n), [], import.meta.url),
			header_footer: () => __vitePreload(() => import("./builder-site-settings-BP62zseL.js"), [], import.meta.url)
		};
		if (!loaders[panelKey] || panels[panelKey]) return;
		let current = true;
		setPanelError("");
		loaders[panelKey]().then((module) => {
			if (current) setPanels((values) => ({
				...values,
				[panelKey]: module.PageTitleSettings || module.ContentViewFields || module.ContentTypographyFields || module.BuilderSiteSettings || module.TypographyFields || module.ColorSettingsFields
			}));
		}).catch(() => {
			if (current) setPanelError(panelKey);
		});
		return () => {
			current = false;
		};
	}, [panelKey, panels]);
	const [CodeFields, setCodeFields] = useState(null);
	const [codeError, setCodeError] = useState(false);
	useEffect(() => {
		if (!active.startsWith("custom_") || !config.canEditCode || CodeFields) return;
		let current = true;
		__vitePreload(() => import("./custom-code-fields-EmdEoXTF.js").then((module) => {
			if (current) setCodeFields(() => module.CustomCodeFields);
		}), [], import.meta.url).catch(() => {
			if (current) setCodeError(true);
		});
		return () => {
			current = false;
		};
	}, [active, CodeFields]);
	const dirty = JSON.stringify(values) !== JSON.stringify(savedValues);
	const change = (key, value) => {
		setBuilderMessage("");
		setValues((current) => ({
			...current,
			[key]: typeof value === "function" ? value(current[key] || {}) : value
		}));
	};
	const color = (name, label) => h(ColorSetting, {
		key: name,
		name,
		label,
		value: values[name],
		onChange: change,
		fallback: config.defaults[name],
		resetValue: config.defaults[name],
		resetLabel: labels.resetColor
	});
	const saveCurrent = async () => {
		if (builderPending.current || !(dirty || builderDirty)) return;
		builderPending.current = true;
		setBuilderBusy(true);
		setBuilderMessage(labels.saving);
		const submitted = values;
		try {
			if (builderDirty && (!builderSave.current || await builderSave.current() === false)) throw new Error(labels.saveError);
			if (dirty) {
				const saved = await saveSettings(config, submitted);
				setSavedValues(saved);
				setValues((current) => mergeSaved(current, submitted, saved));
			}
			setBuilderMessage(labels.saved);
		} catch (error) {
			setBuilderMessage(error?.message || labels.saveError);
		} finally {
			builderPending.current = false;
			setBuilderBusy(false);
		}
	};
	saveAction.current = saveCurrent;
	useEffect(() => {
		const form = document.querySelector(".rdct-stg-form");
		const submit = (event) => {
			event.preventDefault();
			saveAction.current?.();
		};
		form?.addEventListener("submit", submit);
		return () => form?.removeEventListener("submit", submit);
	}, []);
	useEffect(() => {
		const path = `${window.location.pathname}${window.location.search}#rdct-${active}`;
		window.history.replaceState(null, "", path);
		const referer = document.querySelector(".rdct-stg-form input[name=\"_wp_http_referer\"]");
		if (referer) referer.value = path;
	}, [active]);
	useEffect(() => {
		const beforeUnload = (event) => {
			if (!dirty && !builderDirty) return;
			event.preventDefault();
			event.returnValue = "";
		};
		window.addEventListener("beforeunload", beforeUnload);
		return () => {
			window.removeEventListener("beforeunload", beforeUnload);
		};
	}, [dirty, builderDirty]);
	const loadingPanel = !builderBusy && h(LoadingStatus, { label: config.builder.labels.loading });
	const loadError = () => h("div", { role: "alert" }, h("p", null, labels.panelLoadError), h(ButtonControl, { onClick: () => window.location.reload() }, labels.reloadPanel));
	const reset = (all) => {
		if (!window.confirm(all ? labels.resetAllConfirm : labels.resetSectionConfirm)) return;
		setValues((current) => resetSettings(current, config.defaults, all ? null : active, sections[active], config.canEditCode, primaryFontGroup(config, current)));
	};
	const groups = [
		{
			id: "general",
			label: labels.general,
			icon: "admin-settings",
			keys: ["identity", "analytics"]
		},
		{
			id: "appearance",
			label: labels.appearanceGroup,
			icon: "art",
			keys: [
				"design",
				"layout",
				"page_title",
				"header_footer"
			]
		},
		{
			id: "typography",
			label: labels.typography,
			icon: "editor-textcolor",
			keys: [
				"typography_primary",
				"typography_secondary",
				"typography_loading"
			]
		},
		...[
			"blog",
			"portfolio",
			"catalog"
		].map((scope) => ({
			id: scope,
			label: labels[scope],
			icon: scope === "blog" ? "admin-post" : scope === "portfolio" ? "portfolio" : "products",
			keys: [
				`${scope}_typography`,
				`${scope}_archive`,
				`${scope}_single`
			]
		})),
		{
			id: "effects",
			label: labels.effects,
			icon: "admin-customizer",
			keys: [
				"motion",
				"cursor",
				"progress",
				"preloader"
			]
		},
		...config.canEditCode ? [{
			id: "code",
			label: labels.customCode,
			icon: "editor-code",
			keys: ["custom_css", "custom_js"]
		}] : []
	].map((group) => ({
		...group,
		items: group.keys.map((id) => ({
			id,
			label: id.endsWith("_typography") ? labels.typography : id.endsWith("_archive") ? labels.archiveView : id.endsWith("_single") ? labels.singleView : labels[id]
		}))
	}));
	return h("div", { className: "rdct-stg" }, h(FontFaces, {
		config,
		values
	}), config.canEditCode && Object.keys(config.defaults).filter((key) => key.startsWith("custom_css") || key === "custom_js").map((key) => h("input", {
		key,
		type: "hidden",
		name: `radman_settings[${key}]`,
		value: values[key]
	})), h("aside", { className: "rdct-stg__sidebar rdct-adm-sidebar" }, h("div", { className: "rdct-stg__brand" }, h("img", {
		src: config.logo,
		alt: labels.themeName,
		width: 72,
		height: 44
	}), h("small", { dir: "ltr" }, `v${config.version}`)), h(SectionNavigation, {
		groups,
		value: active,
		onChange: (next) => {
			if (!builderPending.current && (!builderDirty || confirm(config.builder.labels.discardConfirm))) setActive(next);
		},
		label: labels.title
	})), h("div", { className: "rdct-stg__body" }, h("header", { className: "rdct-stg__toolbar" }, h("div", null, h("h2", null, labels.title), h("span", {
		className: `rdct-stg__status${dirty || builderDirty ? " is-dirty" : ""}${builderBusy ? " rdct-loading" : ""}`,
		role: "status"
	}, (builderMessage === labels.saved && (dirty || builderDirty) ? labels.unsaved : builderMessage) || (dirty || builderDirty ? labels.unsaved : labels.upToDate))), h("div", { className: "rdct-stg__actions" }, !["identity", "header_footer"].includes(active) && h(ButtonControl, {
		icon: "image-rotate",
		onClick: () => reset(false)
	}, labels.resetSection), active !== "header_footer" && h(ButtonControl, {
		icon: "backup",
		onClick: () => reset(true)
	}, labels.resetAll), h(ButtonControl, {
		type: "submit",
		icon: "saved",
		variant: "primary",
		disabled: builderBusy || !(dirty || builderDirty),
		"aria-busy": builderBusy
	}, labels.save))), active === "header_footer" && h("section", {
		id: "rdct-panel-header_footer",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-header_footer",
		className: "rdct-stg__panel"
	}, BuilderSettings ? h(BuilderSettings, {
		config: config.builder,
		saving: builderBusy,
		onDirty: (value) => {
			setBuilderDirty(value);
			if (value) setBuilderMessage("");
		},
		registerSave: registerBuilderSave.current
	}) : panelError === "header_footer" ? loadError() : loadingPanel), h("section", {
		id: "rdct-panel-identity",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-identity",
		hidden: active !== "identity",
		className: "rdct-stg__panel"
	}, h("h3", null, labels.identity), h("div", { className: "rdct-stg__identity" }, config.siteLogo && h("img", {
		src: config.siteLogo,
		alt: config.siteTitle
	}), h("strong", null, config.siteTitle)), h("p", null, labels.identityHint), h("a", {
		className: "rdct-btn",
		href: config.identityUrl
	}, labels.customizeIdentity), h(ToggleField, {
		name: "svg_uploads",
		label: labels.svgUploads,
		description: labels.svgUploadsHint,
		value: values.svg_uploads,
		onChange: change
	})), h("section", {
		id: "rdct-panel-analytics",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-analytics",
		hidden: active !== "analytics",
		className: "rdct-stg__panel"
	}, h("h3", null, labels.analytics), h(AnalyticsFields, {
		values,
		change,
		labels
	})), typographyView && h("section", {
		id: `rdct-panel-${active}`,
		role: "tabpanel",
		"aria-labelledby": `rdct-tab-${active}`,
		className: "rdct-stg__panel"
	}, h("h3", null, labels[active]), active === "typography_loading" && h(ToggleField, {
		name: "block_google_fonts",
		label: labels.blockGoogleFonts,
		description: labels.blockGoogleFontsHint,
		value: values.block_google_fonts,
		onChange: change
	}), TypographyFields ? h(TypographyFields, {
		config,
		values,
		change,
		group: fontGroup,
		mode: active === "typography_loading" ? "loading" : "fonts"
	}) : panelError === "typography" ? loadError() : loadingPanel), h("section", {
		id: "rdct-panel-motion",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-motion",
		hidden: active !== "motion",
		className: "rdct-stg__panel"
	}, h("p", { className: "rdct-stg__breadcrumb" }, labels.effects), h("h3", null, labels.motion), h(ToggleField, {
		name: "scroll_reveal",
		label: labels.scrollReveal,
		description: labels.scrollRevealHint,
		value: values.scroll_reveal,
		onChange: change
	}), h(ToggleField, {
		name: "smooth_scroll",
		label: labels.smoothScroll,
		description: labels.smoothScrollHint,
		value: values.smooth_scroll,
		onChange: change
	}), values.smooth_scroll && h(InputField, {
		name: "smooth_duration",
		label: labels.smoothDuration,
		type: "number",
		min: .1,
		max: 3,
		step: .1,
		numeric: true,
		value: values.smooth_duration,
		onChange: change
	}), h("h4", null, labels.scrollbar), h(ToggleField, {
		name: "custom_scrollbar",
		label: labels.customScrollbar,
		value: values.custom_scrollbar,
		onChange: change
	}), h("div", { hidden: !values.custom_scrollbar }, h(InputField, {
		name: "scrollbar_width",
		label: labels.scrollbarWidth,
		type: "number",
		min: 4,
		max: 20,
		step: 1,
		numeric: true,
		value: values.scrollbar_width,
		onChange: change
	}), color("scrollbar_thumb", labels.scrollbarThumb), color("scrollbar_track", labels.scrollbarTrack), h("p", { className: "rdct-stg__hint" }, labels.scrollbarHint))), h("section", {
		id: "rdct-panel-cursor",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-cursor",
		hidden: active !== "cursor",
		className: "rdct-stg__panel"
	}, h("p", { className: "rdct-stg__breadcrumb" }, labels.effects), h("h3", null, labels.cursor), h(CursorFields, {
		values,
		change,
		labels,
		defaults: config.defaults
	})), h("section", {
		id: "rdct-panel-progress",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-progress",
		hidden: active !== "progress",
		className: "rdct-stg__panel"
	}, h("p", { className: "rdct-stg__breadcrumb" }, labels.effects), h("h3", null, labels.progress), h(ToggleField, {
		name: "scroll_progress",
		label: labels.scrollProgress,
		description: labels.scrollProgressHint,
		value: values.scroll_progress,
		onChange: change
	}), h("div", { hidden: !values.scroll_progress }, h(InputField, {
		name: "scroll_progress_height",
		label: labels.progressHeight,
		type: "number",
		min: 1,
		max: 8,
		step: 1,
		dir: "ltr",
		value: values.scroll_progress_height,
		onChange: change
	}), ...[["scroll_progress_color", labels.progressColor], ["scroll_progress_track", labels.progressTrack]].map(([name, label]) => color(name, label)))), h("section", {
		id: "rdct-panel-preloader",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-preloader",
		hidden: active !== "preloader",
		className: "rdct-stg__panel"
	}, h("p", { className: "rdct-stg__breadcrumb" }, labels.effects), h("h3", null, labels.preloader), h(ToggleField, {
		name: "preloader",
		label: labels.preloaderEnabled,
		description: labels.preloaderHint,
		value: values.preloader,
		onChange: change
	}), h("div", { hidden: !values.preloader }, h(SelectField, {
		name: "preloader_style",
		label: labels.preloaderStyle,
		description: labels.preloaderSafety,
		value: values.preloader_style,
		onChange: change,
		options: [
			["curtain", labels.curtain],
			["fade", labels.fade],
			["sliding", labels.sliding],
			["spinner", labels.spinner],
			["spinner-classic", labels.spinnerClassic],
			["dissolve", labels.dissolve],
			["image", labels.preloaderImage]
		]
	}), color("preloader_background", labels.preloaderBackground), values.preloader_style === "curtain" && h(ColorSetting, {
		name: "preloader_background_secondary",
		label: labels.preloaderBackgroundSecondary,
		value: values.preloader_background_secondary,
		fallback: values.preloader_color,
		resetLabel: labels.inheritAppearance,
		onChange: change
	}), ["spinner", "spinner-classic"].includes(values.preloader_style) && color("preloader_color", labels.preloaderColor))), h("section", {
		id: "rdct-panel-design",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-design",
		hidden: active !== "design",
		className: "rdct-stg__panel"
	}, h("h3", null, labels.design), ColorSettingsFields ? h(ColorSettingsFields, {
		config,
		values,
		change
	}) : panelError === "design" ? loadError() : loadingPanel), h("section", {
		id: "rdct-panel-layout",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-layout",
		hidden: active !== "layout",
		className: "rdct-stg__panel"
	}, h("h3", null, labels.layout), h("p", null, labels.layoutOwnershipHint), h(InputField, {
		name: "container_width",
		label: labels.containerWidth,
		description: labels.widthHint,
		type: "number",
		dir: "ltr",
		min: 960,
		max: 1600,
		step: 1,
		value: values.container_width,
		onChange: change
	}), h(InputField, {
		name: "reading_width",
		label: labels.readingWidth,
		type: "number",
		dir: "ltr",
		min: 320,
		max: 1280,
		step: 1,
		value: values.reading_width,
		onChange: change
	}), h(ContentLayoutFields, {
		values,
		change,
		config
	})), contentTypographyView && h("section", {
		id: `rdct-panel-${active}`,
		role: "tabpanel",
		"aria-labelledby": `rdct-tab-${active}`,
		className: "rdct-stg__panel"
	}, h("h3", null, `${labels[active.split("_")[0]]} / ${labels.typography}`), ContentTypographyFields ? h(ContentTypographyFields, {
		key: active,
		scope: active.split("_")[0],
		config,
		values,
		change
	}) : panelError === "content_typography" ? loadError() : loadingPanel), active === "page_title" && h("section", {
		id: "rdct-panel-page_title",
		role: "tabpanel",
		"aria-labelledby": "rdct-tab-page_title",
		className: "rdct-stg__panel"
	}, PageTitleSettings ? h(PageTitleSettings, {
		config,
		values,
		change
	}) : panelError === "page_title" ? loadError() : loadingPanel), contentView && h("section", {
		id: `rdct-panel-${active}`,
		role: "tabpanel",
		"aria-labelledby": `rdct-tab-${active}`,
		className: "rdct-stg__panel"
	}, h("h3", null, `${labels[active.split("_")[0]]} — ${active.endsWith("_archive") ? labels.archiveView : labels.singleView}`), ContentViewFields ? h(ContentViewFields, {
		key: active,
		scope: active,
		config,
		values,
		change
	}) : panelError === "content" ? loadError() : loadingPanel), config.canEditCode && ["custom_css", "custom_js"].map((key) => h("section", {
		key,
		id: `rdct-panel-${key}`,
		role: "tabpanel",
		"aria-labelledby": `rdct-tab-${key}`,
		hidden: active !== key,
		className: "rdct-stg__panel"
	}, active === key && (CodeFields ? h(CodeFields, {
		kind: key === "custom_css" ? "css" : "js",
		values,
		change,
		config
	}) : !codeError && loadingPanel), codeError && h("p", { role: "alert" }, labels.codeEditorUnavailable)))));
}
//#endregion
//#region radmanct/src/js/admin/settings.js
var container = document.getElementById("rdct-settings-root");
if (container && window.rdctSettings && window.wp?.element) {
	const { createElement, createRoot } = window.wp.element;
	createRoot(container).render(createElement(SettingsApp, { config: window.rdctSettings }));
}
//#endregion
export { SelectField as a, FontField as i, Field as n, ToggleField as o, FontFaces as r, fontGroups as s, ColorSetting as t };
