import { t as createNativeColorControl } from "./atomic-color-control-DA5gYLoc.js";
//#region radmanct-components/src/js/admin/atomic-native-fields.js
var wrap$1 = (type, value) => ({
	$$type: type,
	value
});
function nativeRichValue(value) {
	if (value?.$$type && value.$$type !== "string") return value;
	const text = value?.$$type === "string" ? value.value : value;
	return wrap$1("escaped-html", typeof text === "string" ? text : "");
}
function nativeImageValue(value) {
	if (value?.$$type && value.$$type !== "number") return value;
	const id = value?.$$type === "number" ? value.value : value;
	if (!Number.isSafeInteger(id) || id <= 0) return null;
	return wrap$1("image", {
		src: wrap$1("image-src", { id: wrap$1("image-attachment-id", id) }),
		size: wrap$1("string", "large")
	});
}
function nativeEditorRow(row, fields) {
	const value = { ...row.value };
	for (const [key, field] of Object.entries(fields)) if (field.rich) value[key] = nativeRichValue(value[key] ?? field.default);
	else if (field.media) value[key] = nativeImageValue(value[key]);
	return {
		...row,
		value
	};
}
function registerNativeRichText(registry, controls, element, props) {
	if (registry.get("rdct-rich-text") || !controls.InlineEditingControl) return;
	const h = element.createElement;
	function RichTextControl({ context } = {}) {
		const bound = controls.useBoundProp();
		const value = nativeRichValue(bound.value ?? bound.baseValue ?? bound.propType.default);
		return h(controls.PropProvider, {
			propType: {
				kind: "object",
				shape: { content: bound.propType }
			},
			value: { content: value },
			isDisabled: () => bound.disabled ?? bound.isDisabled?.(bound.propType),
			setValue: (next, options, meta) => bound.setValue(next.content, options, meta)
		}, h(controls.PropKeyProvider, { bind: "content" }, h(controls.InlineEditingControl, { context })));
	}
	registry.register("rdct-rich-text", RichTextControl, "full", props.escapedHtmlPropTypeUtil);
}
function createNativeTemplateSelect(controls, h) {
	return function TemplateSelect({ options = [], ariaLabel }) {
		const bound = controls.useBoundProp();
		const raw = bound.value ?? bound.baseValue ?? bound.propType.default;
		const value = String(raw?.value ?? 0);
		const choices = options.map((option) => ({
			...option,
			value: String(option.value)
		}));
		const allowed = new Set(choices.map((option) => option.value));
		const disabled = () => bound.disabled ?? bound.isDisabled?.(bound.propType);
		return h(controls.PropProvider, {
			propType: {
				kind: "object",
				shape: { selection: bound.propType }
			},
			value: { selection: wrap$1("string", value) },
			isDisabled: disabled,
			setValue: (next, settings, meta) => {
				const selected = next.selection?.value ?? "0";
				if (disabled() || !allowed.has(selected) || !/^\d+$/.test(selected) || !Number.isSafeInteger(Number(selected))) return;
				bound.setValue(wrap$1("number", Number(selected)), settings, meta);
			}
		}, h(controls.PropKeyProvider, { bind: "selection" }, h(controls.SelectControl, {
			options: choices,
			ariaLabel
		})));
	};
}
function registerNativeTemplateSelect(registry, controls, element, props) {
	if (registry.get("rdct-template-select") || !controls.SelectControl) return;
	registry.register("rdct-template-select", createNativeTemplateSelect(controls, element.createElement), "full", props.numberPropTypeUtil);
}
//#endregion
//#region shared/admin/image-picker.js
function pickMedia({ title, id = 0, onSelect, type = "image", multiple = false, maximum = 50, media = window.wp?.media }) {
	if (!media) return;
	if (![
		"image",
		"video",
		"audio"
	].includes(type)) return;
	let active = true;
	let selectedOnce = false;
	const frame = media({
		title,
		multiple,
		library: { type }
	});
	frame.once("open", () => {
		if (id > 0) frame.state().get("selection").add(media.attachment(id));
	});
	frame.once("select", () => {
		if (!active || selectedOnce) return;
		selectedOnce = true;
		if (multiple) {
			const ids = [...new Set(frame.state().get("selection").toJSON().filter((item) => !item.type || item.type === type).map((item) => Number(item.id)).filter((value) => Number.isSafeInteger(value) && value > 0))].slice(0, maximum);
			if (ids.length) onSelect(ids);
			return;
		}
		const item = frame.state().get("selection").first()?.toJSON();
		if (item?.type && item.type !== type) return;
		const selected = Number(item?.id);
		if (Number.isSafeInteger(selected) && selected > 0) onSelect(selected);
	});
	const release = () => {
		if (active) {
			active = false;
			frame.remove();
		}
	};
	frame.once("close", () => queueMicrotask(release));
	frame.open();
	return () => {
		if (active) {
			frame.close();
			release();
		}
	};
}
function pickImage(options) {
	return pickMedia({
		...options,
		type: "image"
	});
}
//#endregion
//#region radmanct-components/src/js/admin/atomic-repeater-control.js
var wrap = (type, value) => ({
	$$type: type,
	value
});
function matchesFieldConditions(row, fields, conditions = {}) {
	return Object.entries(conditions).every(([name, expected]) => {
		const negative = name.endsWith("!");
		const key = negative ? name.slice(0, -1) : name;
		const current = row.value?.[key]?.value ?? fields[key]?.default;
		const matches = (Array.isArray(expected) ? expected : [expected]).includes(current);
		return negative ? !matches : matches;
	});
}
function createRepeaterItem(fields, values = {}, itemType = "rdct-curtain-item") {
	return nativeEditorRow(wrap(itemType, Object.fromEntries(Object.entries(fields).filter(([key, field]) => !(field.image || field.size || field.media) || values[key]).map(([key, field]) => [key, field.image || field.size ? values[key] : wrap(typeof field.default === "number" ? "number" : typeof field.default === "boolean" ? "boolean" : "string", values[key] ?? field.default)]))), fields);
}
function appendImageItems(rows, ids, fields, maximum = 50, itemType = "rdct-carousel-item") {
	const next = rows.slice(0, maximum);
	for (const id of new Set(ids)) {
		if (next.length >= maximum) break;
		if (Number.isSafeInteger(id) && id > 0) next.push(createRepeaterItem(fields, {
			image_id: id,
			content_type: "content",
			title: ""
		}, itemType));
	}
	return next;
}
function registerItemRepeater(registry, controls, config, element, ui, props, media) {
	const controlKey = config?.controlKey ?? "rdct-curtain-items";
	const maximum = config?.maximum ?? 12;
	if (!config || registry.get(controlKey) || !controls.Repeater || !controls.PropProvider || !controls.PropKeyProvider || !controls.TextControl || !controls.TextAreaControl || !controls.ColorControl || !ui?.Stack) return;
	const { createElement: h, createContext, useContext, useState } = element;
	const Context = createContext(null);
	const propTypeKey = config.propTypeKey ?? controlKey;
	const util = {
		key: propTypeKey,
		create: (value) => wrap(propTypeKey, value),
		extract: (prop) => prop?.value ?? []
	};
	const ColorControl = createNativeColorControl(controls, h, props.stringPropTypeUtil);
	const TemplateSelect = createNativeTemplateSelect(controls, h);
	function BatchImages({ rows, commit, disabled }) {
		const latest = element.useRef({
			rows,
			commit,
			disabled
		});
		latest.current = {
			rows,
			commit,
			disabled
		};
		const cancel = element.useRef(null);
		element.useEffect(() => () => cancel.current?.(), []);
		return h(ui.Button, {
			type: "button",
			variant: "outlined",
			size: "small",
			disabled: disabled || rows.length >= maximum,
			onClick: () => {
				cancel.current?.();
				cancel.current = pickImage({
					media,
					title: config.labels.addImages,
					multiple: true,
					maximum,
					onSelect: (ids) => {
						const current = latest.current;
						if (!current.disabled) current.commit(appendImageItems(current.rows, ids, config.fields, maximum, config.itemType));
					}
				});
			}
		}, config.labels.addImages);
	}
	function Field({ row, index, fieldKey, field }) {
		const context = useContext(Context);
		if (!matchesFieldConditions(row, config.fields, field.condition)) return null;
		if (field.image || field.media) return controls.ImageControl ? h(controls.PropKeyProvider, { bind: fieldKey }, h(controls.ImageControl, {
			...config.imageProps,
			label: field.label
		})) : null;
		if (field.size) return controls.SizeControl ? h(ui.Stack, { spacing: 1 }, h(controls.ControlFormLabel, null, field.label), h(controls.PropKeyProvider, { bind: fieldKey }, h(controls.SizeControl, {
			min: field.min,
			enablePropTypeUnits: true,
			disableCustom: true,
			ariaLabel: field.label
		}))) : null;
		const choices = fieldKey === "icon" ? config.icons : field.template ? config.templates : field.options;
		let input;
		if (field.template) input = h(TemplateSelect, {
			options: Object.entries(choices).map(([value, label]) => ({
				value,
				label
			})),
			ariaLabel: field.label
		});
		else if (choices) input = h(controls.SelectControl, {
			options: Object.entries(choices).map(([value, label]) => ({
				value,
				label
			})),
			ariaLabel: field.label
		});
		else if (field.rich) input = h(controls.InlineEditingControl, { context: context.editorContext });
		else if (field.color) input = h(ColorControl);
		else if (typeof field.default === "boolean") input = h(controls.SwitchControl);
		else if (typeof field.default === "number") input = h(controls.NumberControl, {
			min: field.min,
			max: field.max,
			step: field.step,
			shouldForceInt: !field.step
		});
		else input = h(field.multiline ? controls.TextAreaControl : controls.TextControl, { ariaLabel: field.label });
		return h(ui.Stack, { spacing: 1 }, h(controls.ControlFormLabel, null, field.label), h(controls.PropKeyProvider, { bind: fieldKey }, input));
	}
	function Content({ value: row, index }) {
		row = nativeEditorRow(row, config.fields);
		const context = useContext(Context);
		const [group, setGroup] = useState(config.initialGroup ?? "normal");
		const fields = Object.entries(config.fields).filter(([, field]) => !field.internal && (!field.group || field.group === group));
		const body = h(ui.Stack, { spacing: 2 }, config.groups !== false && h(ui.ToggleButtonGroup, {
			size: "tiny",
			exclusive: true,
			value: group,
			disabled: context.disabled,
			onChange: (_, next) => {
				if (next) setGroup(next);
			}
		}, Object.entries(config.groupOptions ?? {
			normal: config.labels.normal,
			active: config.labels.active
		}).map(([key, label]) => h(ui.ToggleButton, {
			key,
			value: key
		}, label))), fields.map(([fieldKey, field]) => h(Field, {
			key: fieldKey,
			row,
			index,
			fieldKey,
			field
		})));
		const item = h(controls.PropProvider, {
			propType: context.itemType,
			value: row.value,
			isDisabled: () => context.disabled,
			setValue: (next, options, meta) => context.replace(index, {
				...row,
				value: next
			}, options, meta)
		}, body);
		return h(controls.PropProvider, {
			propType: context.arrayType,
			value: context.rows,
			setValue: context.commit,
			isDisabled: () => context.disabled
		}, h(controls.PropKeyProvider, { bind: String(index) }, item));
	}
	function ItemsControl({ context: editorContext } = {}) {
		const bound = controls.useBoundProp(util);
		const rows = Array.isArray(bound.value) ? bound.value : [];
		const commit = (next, options, meta) => {
			if (!bound.disabled && Array.isArray(next) && next.length <= maximum) bound.setValue(next, options, meta);
		};
		const replace = (index, row, options, meta) => commit(rows.map((existing, i) => i === index ? row : existing), options, meta);
		const itemSchema = bound.propType.item_prop_type;
		const itemType = itemSchema.kind === "union" ? itemSchema.prop_types[config.itemType ?? "rdct-curtain-item"] : itemSchema;
		const context = {
			disabled: bound.disabled,
			editorContext,
			arrayType: bound.propType,
			rows,
			commit,
			itemType,
			replace,
			update: (index, changes) => replace(index, {
				...rows[index],
				value: {
					...rows[index].value,
					...Object.fromEntries(Object.entries(changes).map(([key, value]) => [key, wrap(typeof value === "number" ? "number" : "string", value)]))
				}
			})
		};
		const repeater = h(controls.Repeater, {
			label: config.labels.item,
			values: rows,
			setValues: commit,
			disabled: bound.disabled,
			showToggle: false,
			showDuplicate: rows.length < maximum,
			disableAddItemButton: rows.length >= maximum,
			openOnAdd: true,
			isSortable: true,
			itemSettings: {
				initialValues: createRepeaterItem(config.fields, {}, config.itemType ?? "rdct-curtain-item"),
				Icon: () => null,
				Label: ({ value }) => value.value?.title?.value || config.labels.item,
				Content
			}
		});
		return h(Context.Provider, { value: context }, config.batchImages && ui.Button && media ? h(ui.Stack, { spacing: 1 }, h(BatchImages, {
			rows,
			commit,
			disabled: bound.disabled
		}), repeater) : repeater);
	}
	registry.register(controlKey, ItemsControl, "full", util);
}
//#endregion
//#region radmanct-components/src/js/admin/atomic-curtain-controls.js
if (typeof window !== "undefined") {
	const controls = window.elementorV2?.editorControls;
	const registry = window.elementorV2?.editorEditingPanel?.controlsRegistry;
	if (controls && registry) registerNativeRichText(registry, controls, window.wp.element, window.elementorV2.editorProps);
	if (controls && registry) registerNativeTemplateSelect(registry, controls, window.wp.element, window.elementorV2.editorProps);
	const profiles = (window.rdctCarouselProfileControls ?? []).map((profile) => ({
		...window.rdctCarouselControls,
		...profile,
		fields: Object.fromEntries(profile.fields.map((key) => [key, window.rdctCarouselControls.fields[key]]))
	}));
	if (controls && registry) {
		for (const config of [
			window.rdctAccordionControls,
			window.rdctTimelineControls,
			window.rdctStickySectionControls,
			window.rdctCurtainControls,
			window.rdctCarouselControls,
			...profiles,
			window.rdctTestimonialControls,
			window.rdctTextControls,
			window.rdctTextSegmentsControls,
			window.rdctParticleColors
		]) if (config) registerItemRepeater(registry, controls, {
			...config,
			imageProps: window.rdctImageControlProps
		}, window.wp.element, window.elementorV2.ui, window.elementorV2.editorProps, window.wp.media);
	}
}
//#endregion
export { createRepeaterItem as curtainItem, registerItemRepeater as registerCurtainControls };
