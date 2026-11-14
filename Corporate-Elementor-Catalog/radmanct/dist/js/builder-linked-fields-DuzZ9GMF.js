import { a as InputControl, c as SelectControl, t as ButtonControl } from "./controls-DqGpx29l.js";
import { a as newId } from "./builder-DCO-dmq6.js";
import { t as BuilderIconPicker } from "./builder-icon-picker-BSABpcSi.js";
//#region radmanct/src/js/admin/builder-linked-fields.js
var { createElement: h } = window.wp.element;
function IconChoice({ value = "", change, config, labels }) {
	return h("div", null, h("p", null, labels.icon), h(BuilderIconPicker, {
		value,
		onChange: change,
		config,
		labels
	}));
}
function LinkedFields({ item, update, config, labels }) {
	const field = (key, title, values, change) => h("label", null, title, h(InputControl, {
		value: values[key] || "",
		maxLength: key === "url" ? 2048 : 200,
		dir: key === "url" ? "ltr" : "auto",
		onChange: (value) => change({ [key]: value })
	}));
	return h("div", { className: "rdct-bld-linked" }, h("label", null, labels.iconSize, h(InputControl, {
		type: "number",
		numeric: true,
		value: item.size ?? 20,
		min: 8,
		max: 64,
		onChange: (value) => update({ size: value })
	})), item.type !== "social" ? h("div", null, field("text", labels.linkLabel, item, update), field("url", labels.url, item, update), h(IconChoice, {
		value: item.icon,
		change: (icon) => update({ icon }),
		config,
		labels
	}), item.type === "contact" && h("label", null, labels.textDirection, h(SelectControl, {
		value: item.direction || "auto",
		onChange: (value) => update({ direction: value }),
		options: [
			["auto", labels.autoDirection],
			["rtl", "RTL"],
			["ltr", "LTR"]
		]
	})), h("p", { className: "description" }, labels.contactHint)) : h("div", null, h("p", { className: "description" }, labels.socialHint), ...(item.links || []).map((link, index) => {
		const change = (patch) => update({ links: item.links.map((entry) => entry.id === link.id ? {
			...entry,
			...patch
		} : entry) });
		const move = (offset) => {
			const links = [...item.links];
			links.splice(index + offset, 0, links.splice(index, 1)[0]);
			update({ links });
		};
		return h("fieldset", {
			key: link.id,
			className: "rdct-bld-linked__entry"
		}, h("legend", null, `${labels.socialLink} ${index + 1}`), field("text", labels.linkLabel, link, change), field("url", labels.url, link, change), h(IconChoice, {
			value: link.icon,
			change: (icon) => change({ icon }),
			config,
			labels
		}), h("div", { className: "rdct-bld-editor__actions" }, h(ButtonControl, {
			type: "button",
			disabled: index === 0,
			onClick: () => move(-1)
		}, labels.up), h(ButtonControl, {
			type: "button",
			disabled: index === item.links.length - 1,
			onClick: () => move(1)
		}, labels.down), h(ButtonControl, {
			type: "button",
			onClick: () => update({ links: item.links.filter((entry) => entry.id !== link.id) })
		}, labels.remove)));
	}), h(ButtonControl, {
		type: "button",
		disabled: (item.links || []).length >= 8,
		onClick: () => update({ links: [...item.links || [], {
			id: newId(),
			text: "",
			url: "",
			icon: "external-link"
		}] })
	}, labels.addSocialLink)));
}
//#endregion
export { LinkedFields };
