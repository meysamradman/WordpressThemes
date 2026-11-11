//#region shared/admin/controls.js
var { createElement: h } = window.wp.element;
function LoadingStatus({ label }) {
	return h("p", {
		className: "rdct-loading",
		role: "status",
		"aria-live": "polite"
	}, label);
}
function SelectControl({ value, options, onChange, numeric = false, ...attributes }) {
	return h("select", {
		...attributes,
		value: value ?? "",
		onChange: (event) => onChange(numeric && event.target.value !== "" ? Number(event.target.value) : event.target.value)
	}, ...options.map(([key, label, disabled = false]) => h("option", {
		key,
		value: key,
		disabled
	}, label)));
}
function InputControl({ value, onChange, numeric = false, type = "text", ...attributes }) {
	return h("input", {
		...attributes,
		type,
		value: value ?? "",
		onChange: (event) => onChange(numeric && event.target.value !== "" ? Number(event.target.value) : event.target.value)
	});
}
function CheckboxControl({ checked, onChange, ...attributes }) {
	return h("input", {
		...attributes,
		type: "checkbox",
		checked: Boolean(checked),
		onChange: (event) => onChange(event.target.checked)
	});
}
function SwitchControl({ checked, onChange, className = "", ...attributes }) {
	return h(CheckboxControl, {
		...attributes,
		checked,
		onChange,
		role: "switch",
		className: `rdct-switch ${className}`.trim()
	});
}
function ButtonControl({ children, label, icon, iconOnly = false, variant = "secondary", className = "", type = "button", ...attributes }) {
	return h("button", {
		...attributes,
		type,
		className: `rdct-btn rdct-btn--${variant}${iconOnly ? " rdct-btn--icon" : ""} ${className}`.trim(),
		...iconOnly ? {
			"aria-label": attributes["aria-label"] ?? label,
			title: attributes.title ?? label
		} : {}
	}, icon && h("span", {
		className: `dashicons dashicons-${icon}`,
		"aria-hidden": true
	}), !iconOnly && (children ?? label));
}
function TextareaControl({ value, onChange, ...attributes }) {
	return h("textarea", {
		...attributes,
		value: value ?? "",
		onChange: (event) => onChange(event.target.value)
	});
}
function FieldGroup({ title, hint, children }) {
	return h("section", { className: "rdct-fld" }, title && h("h4", null, title), hint && h("p", { className: "description" }, hint), children);
}
function ChoiceControl({ value, options, onChange, label, iconOnly = false }) {
	return h("div", {
		className: `rdct-choice-control${iconOnly ? " rdct-choice-control--icons" : ""}`,
		role: "group",
		"aria-label": label
	}, ...options.map(([key, title, disabled = false, icon]) => h(ButtonControl, {
		key,
		type: "button",
		label: title,
		icon,
		iconOnly,
		disabled,
		"aria-pressed": value === key,
		onClick: () => onChange(key)
	}, title)));
}
function RangeControl({ value, onChange, label, min = 0, max = 100, step = 1, numeric = false, unit = "px", fallback = min, id, name, ...attributes }) {
	const update = (event) => onChange(numeric && event.target.value !== "" ? Number(event.target.value) : event.target.value);
	return h("div", { className: "rdct-range-control" }, h("input", {
		type: "range",
		min,
		max,
		step,
		value: value === "" || value == null ? fallback : Math.min(max, Math.max(min, Number(value))),
		disabled: attributes.disabled,
		"aria-label": label,
		"aria-describedby": attributes["aria-describedby"],
		onChange: update
	}), h("span", null, h("input", {
		...attributes,
		id,
		name,
		type: "number",
		dir: "ltr",
		min,
		max,
		step,
		value: value ?? "",
		"aria-label": label,
		onChange: update
	}), unit && h("span", { "aria-hidden": true }, unit)));
}
function FieldRow({ label, id, description, children, className = "" }) {
	return h("div", { className: `rdct-admin-field ${className}`.trim() }, h(id ? "label" : "span", id ? { htmlFor: id } : null, label), h("div", null, ...[children].flat(2), description && h("p", { className: "description" }, description)));
}
//#endregion
export { InputControl as a, SelectControl as c, FieldRow as i, SwitchControl as l, ChoiceControl as n, LoadingStatus as o, FieldGroup as r, RangeControl as s, ButtonControl as t, TextareaControl as u };
