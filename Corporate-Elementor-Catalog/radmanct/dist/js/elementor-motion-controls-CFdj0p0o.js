//#region radmanct/src/js/admin/elementor-motion-controls.js
function registerMotionControls(panel, controls, ui, react, props, config) {
	if (!panel?.SettingsField || !panel.useElement || !controls?.SwitchControl || !ui?.Popover || !ui.Divider || !react || !props || !config || panel.controlsRegistry.get("rdct-motion")) return;
	const { createElement: h, useState } = react;
	const timing = /* @__PURE__ */ new Set([
		"duration",
		"stagger",
		"delay"
	]);
	const transformGroups = /* @__PURE__ */ new Set([
		"from",
		"to",
		"parallax_from",
		"parallax_to"
	]);
	const icon = (reset = false) => h("svg", {
		viewBox: "0 0 24 24",
		width: 16,
		height: 16,
		fill: "none",
		stroke: "currentColor",
		strokeWidth: 1.8,
		"aria-hidden": true
	}, h("path", { d: reset ? "M4 10a8 8 0 1 1 1 8M4 4v6h6" : "m4 16-1 5 5-1L20 8l-4-4L4 16Zm9-9 4 4" }));
	function Reset({ field }) {
		const util = field.type === "boolean" ? props.booleanPropTypeUtil : field.options || field.type === "text" ? props.stringPropTypeUtil : props.numberPropTypeUtil;
		const bound = controls.useBoundProp(util);
		return h(ui.IconButton, {
			size: "tiny",
			title: config.labels.reset,
			"aria-label": config.labels.reset,
			disabled: bound.disabled,
			onClick: () => bound.resetValue ? bound.resetValue() : bound.setValue(field.default)
		}, icon(true));
	}
	function NumericRange({ field, name }) {
		const bound = controls.useBoundProp(props.numberPropTypeUtil);
		const { settings } = panel.useElement();
		const unitSetting = field.unit_key && settings?.[`rm_motion_${field.unit_key}`];
		const unit = unitSetting?.disabled ? config.fields[field.unit_key]?.default : unitSetting?.value ?? config.fields[field.unit_key]?.default;
		const relative = field.unit_key && unit !== "px";
		const limit = name === "parallax_distance" ? 500 : 100;
		const min = relative ? -limit : field.ui_min ?? field.min;
		const max = relative ? limit : field.ui_max ?? field.max;
		const step = field.unit_key ? relative ? .1 : 1 : field.step ?? (timing.has(name) || /_(?:scaleX|scaleY|opacity)$/.test(name) ? .01 : 1);
		return h(ui.Stack, {
			direction: "row",
			spacing: 2,
			alignItems: "center"
		}, h(ui.Box, { sx: {
			flex: 1,
			minWidth: 0
		} }, h(ui.Slider, {
			size: "small",
			min,
			max,
			step,
			value: Math.min(max, Math.max(min, bound.value ?? field.default)),
			disabled: bound.disabled,
			"aria-label": field.label,
			onChange: (_, value) => {
				if (typeof value === "number") bound.setValue(value);
			}
		})), h(ui.Box, { sx: {
			flex: "0 0 5.5rem",
			minWidth: 0
		} }, h(controls.NumberControl, {
			min,
			max,
			step,
			shouldForceInt: false
		})));
	}
	function Field({ name, layout }) {
		const field = config.fields[name];
		const label = name === "animate" && !layout ? config.labels.animate : field.label;
		const input = field.type === "boolean" ? h(controls.SwitchControl) : field.type === "text" ? h(controls.TextControl, {
			placeholder: field.default,
			ariaLabel: label,
			sx: { "& input": {
				direction: "ltr",
				textAlign: "left"
			} }
		}) : field.options ? h(controls.SelectControl, { options: Object.entries(field.options).map(([value, label]) => ({
			value,
			label
		})) }) : timing.has(name) || transformGroups.has(field.group) || ["parallax_perspective", "parallax_distance"].includes(name) ? h(NumericRange, {
			field,
			name
		}) : h(controls.NumberControl, {
			min: field.min,
			max: field.max,
			step: .01,
			shouldForceInt: false
		});
		if (field.type === "boolean") return h(ui.Stack, {
			direction: "row",
			spacing: 2,
			alignItems: "center",
			justifyContent: "space-between"
		}, h(controls.ControlFormLabel, null, label), h(panel.SettingsField, {
			bind: `rm_motion_${name}`,
			propDisplayName: label
		}, input));
		return h(ui.Stack, { spacing: 1 }, h(controls.ControlFormLabel, null, label), h(panel.SettingsField, {
			bind: `rm_motion_${name}`,
			propDisplayName: label
		}, h(ui.Stack, {
			direction: "row",
			spacing: 1,
			alignItems: "center"
		}, h(ui.Box, { sx: {
			flex: 1,
			minWidth: 0
		} }, input), h(Reset, { field }))));
	}
	function Popup({ group, layout, exclude = [] }) {
		const [anchor, setAnchor] = useState(null);
		const label = config.groups[group];
		const { settings } = panel.useElement();
		const targetSetting = settings?.rm_motion_targets;
		const targets = targetSetting?.disabled ? config.fields.targets?.default : targetSetting?.value ?? config.fields.targets?.default;
		return h(ui.Stack, null, h(ui.Stack, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between"
		}, h(controls.ControlFormLabel, null, label), h(ui.IconButton, {
			size: "tiny",
			title: label,
			"aria-label": label,
			onClick: (event) => setAnchor(event.currentTarget),
			"aria-haspopup": "dialog",
			"aria-expanded": !!anchor
		}, icon())), h(ui.Popover, {
			open: !!anchor,
			anchorEl: anchor,
			onClose: () => setAnchor(null),
			disableScrollLock: true,
			anchorOrigin: {
				vertical: "bottom",
				horizontal: "center"
			},
			transformOrigin: {
				vertical: "top",
				horizontal: "center"
			}
		}, h(ui.Stack, {
			spacing: 2,
			sx: {
				p: 2,
				width: 300,
				maxWidth: "80vw"
			}
		}, h(ui.Typography, { variant: "subtitle2" }, label), Object.entries(config.fields).filter(([name, field]) => field.group === group && !exclude.includes(name) && (layout || !["targets", "inner"].includes(name)) && (name !== "inner" || targets === "contents") && Object.entries(field.condition ?? {}).every(([key, expected]) => {
			const raw = settings?.[`rm_motion_${key}`];
			return (Array.isArray(expected) ? expected : [expected]).includes(raw?.disabled ? config.fields[key]?.default : raw?.value ?? config.fields[key]?.default);
		})).map(([name]) => h(Field, {
			key: name,
			name,
			layout
		})))));
	}
	function Motion({ group, layout }) {
		const { settings, element } = panel.useElement();
		const value = (key) => {
			const raw = settings?.[`rm_motion_${key}`];
			return raw?.disabled ? config.fields[key].default : raw?.value ?? config.fields[key].default;
		};
		const popup = (group, exclude = []) => h(Popup, {
			key: group,
			group,
			layout,
			exclude
		});
		const field = (name) => h(Field, {
			key: name,
			name,
			layout
		});
		const play = () => {
			if (element?.id) window.elementor?.channels?.editor?.trigger("rm_motion_play", { id: element.id });
		};
		const playButton = () => h(ui.Button, {
			size: "small",
			variant: "contained",
			color: "success",
			onClick: play,
			disabled: !element?.id
		}, config.labels.play);
		const actions = () => h(ui.Stack, {
			direction: "row",
			spacing: 1
		}, playButton(), h(ui.Button, {
			size: "small",
			variant: "outlined",
			disabled: !element?.id,
			onClick: () => window.elementor?.channels?.editor?.trigger("rm_motion_reset", { id: element.id })
		}, config.labels.resetPreview));
		if (group === "scroll") return h(ui.Stack, { spacing: 2 }, ["horizontal", "reveal"].map((name) => h(ui.Stack, {
			key: name,
			spacing: 1
		}, field(name), value(name) && popup(name, [name]), name === "reveal" && value(name) && actions())));
		return h(ui.Stack, { spacing: 2 }, field("parallax"), value("parallax") && h(ui.Stack, { spacing: 1 }, popup("parallax", ["parallax"]), popup("parallax_from"), popup("parallax_to")), h(ui.Divider), field("animate"), value("animate") && h(ui.Stack, { spacing: 1 }, layout && field("targets"), layout && value("targets") === "contents" && field("inner"), actions(), popup("settings", [
			"animate",
			"targets",
			"inner"
		]), value("preset") === "custom" && popup("from"), value("preset") === "custom" && popup("to")), value("animate") && value("parallax") && (!layout || value("targets") === "self") && h(ui.Typography, {
			variant: "caption",
			color: "text.secondary"
		}, config.labels.self), !value("animate") && value("parallax") && actions(), (value("animate") || value("parallax")) && h(ui.Typography, {
			variant: "caption",
			color: "text.secondary"
		}, config.labels.notice));
	}
	panel.controlsRegistry.register("rdct-motion", Motion, "full");
}
if (typeof window !== "undefined") {
	const v2 = window.elementorV2;
	registerMotionControls(v2?.editorEditingPanel, v2?.editorControls, v2?.ui, window.wp?.element, v2?.editorProps, window.rdctMotionControls);
}
//#endregion
export { registerMotionControls };
