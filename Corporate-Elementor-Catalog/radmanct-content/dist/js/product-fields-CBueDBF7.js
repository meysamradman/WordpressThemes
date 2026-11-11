import { n as SelectControl, r as TextareaControl, t as InputControl } from "./content-jbdCS8Sn.js";
//#region radmanct-content/src/js/admin/specifications.js
var { createElement: h$1 } = window.wp.element;
var button = (text, onClick, disabled = false) => h$1("button", {
	type: "button",
	className: "button",
	onClick,
	disabled
}, text);
var newId = () => `s${Array.from(crypto.getRandomValues(/* @__PURE__ */ new Uint32Array(4)), (value) => value.toString(16).padStart(8, "0")).join("")}`;
function Specifications({ data, setData, move, labels }) {
	function update(id, key, value) {
		setData((current) => ({
			...current,
			specifications: current.specifications.map((row) => row.id === id ? {
				...row,
				[key]: value
			} : row)
		}));
	}
	const rows = data.specifications.map((row, index) => {
		const fields = [
			["label", 100],
			["value", 500],
			["unit", 25]
		].map(([key, maxLength]) => h$1("label", { key }, labels[key], h$1(key === "value" ? TextareaControl : InputControl, {
			value: row[key],
			maxLength,
			rows: key === "value" ? 2 : void 0,
			onChange: (value) => update(row.id, key, value)
		})));
		return h$1("div", {
			key: row.id,
			className: "rdct-cnt-editor__spec"
		}, ...fields, h$1("div", { className: "rdct-cnt-editor__actions" }, button(labels.up, () => move("specifications", index, -1), index === 0), button(labels.down, () => move("specifications", index, 1), index === data.specifications.length - 1), button(labels.remove, () => setData((current) => ({
			...current,
			specifications: current.specifications.filter((entry) => entry.id !== row.id)
		})))));
	});
	return h$1("section", { className: "rdct-cnt-editor__section" }, h$1("div", { className: "rdct-cnt-editor__heading" }, h$1("h3", null, labels.specifications), button(labels.addSpec, () => setData((current) => ({
		...current,
		specifications: [...current.specifications, {
			id: newId(),
			label: "",
			value: "",
			unit: ""
		}]
	})), data.specifications.length >= 40)), ...rows);
}
//#endregion
//#region radmanct-content/src/js/admin/product-fields.js
var { createElement: h } = window.wp.element;
function ProductFields({ data, setData, move, labels, contactAvailable, contactSettings }) {
	const pricing = data.pricing || {
		mode: "contact",
		text: ""
	};
	const update = (patch) => setData((current) => ({
		...current,
		pricing: {
			...current.pricing || pricing,
			...patch
		}
	}));
	return h("div", { className: "rdct-cnt-editor__product" }, h("fieldset", { className: "rdct-cnt-editor__pricing" }, h("legend", null, labels.productDisplay), h("label", { className: "rdct-cnt-editor__field" }, h("span", { className: "screen-reader-text" }, labels.productDisplay), h(SelectControl, {
		value: pricing.mode,
		options: [["contact", labels.contactUs], ["price", labels.price]],
		onChange: (mode) => update({ mode })
	})), pricing.mode === "price" && h("label", { className: "rdct-cnt-editor__field" }, labels.price, h(InputControl, {
		value: pricing.text,
		dir: "auto",
		maxLength: 80,
		onChange: (text) => update({ text })
	})), h("p", { className: "description" }, pricing.mode === "price" ? labels.priceHint : labels.contactHint), pricing.mode === "contact" && contactAvailable === false && h("div", {
		className: "notice notice-warning inline",
		role: "status"
	}, h("p", null, labels.contactMissing), contactSettings && h("p", null, h("a", {
		href: contactSettings,
		target: "_blank",
		rel: "noopener noreferrer"
	}, labels.contactSettings)))), h(Specifications, {
		data,
		setData,
		move,
		labels
	}));
}
//#endregion
export { ProductFields };
