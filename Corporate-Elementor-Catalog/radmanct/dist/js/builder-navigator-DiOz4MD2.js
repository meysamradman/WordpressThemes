import { t as ButtonControl } from "./controls-DqGpx29l.js";
import { o as findContainer } from "./builder-DCO-dmq6.js";
//#region radmanct/src/js/admin/builder-navigator.js
var { createElement: h } = window.wp.element;
function Navigator({ layout, selectedRow, selectedItem, labels, over, dragHandle, dragProps, dropProps, select, moveRow, moveItem, duplicateRow, removeRow, addToRow }) {
	const action = (icon, label, onClick, disabled = false) => h(ButtonControl, {
		icon,
		label,
		iconOnly: true,
		variant: icon === "trash" ? "danger" : "ghost",
		onClick,
		disabled
	});
	const renderItems = (row) => row.items.map((item, itemIndex) => h("div", { key: item.id }, h("div", {
		className: `rdct-bld-editor__item${selectedItem?.id === item.id ? " is-selected" : ""}${over === item.id ? " is-over" : ""}`,
		...dropProps(row.id, itemIndex, item.id)
	}, dragHandle({
		rowId: row.id,
		itemId: item.id
	}, labels[item.type]), h(ButtonControl, {
		className: "rdct-bld-editor__select",
		"aria-pressed": selectedItem?.id === item.id,
		...dragProps({
			rowId: row.id,
			itemId: item.id
		}),
		onClick: () => select(row.id, item.id)
	}, labels[item.type]), action("arrow-up-alt2", labels.up, () => moveItem(row.id, item.id, row.id, itemIndex - 1), itemIndex === 0), action("arrow-down-alt2", labels.down, () => moveItem(row.id, item.id, row.id, itemIndex + 1), itemIndex === row.items.length - 1)), item.type === "container" && h("div", {
		className: "rdct-bld-navigator__children",
		...dropProps(item.id)
	}, ...renderItems(findContainer(layout, item.id)), h(ButtonControl, {
		onClick: () => addToRow(item.id),
		disabled: item.items.length >= 6
	}, labels.add))));
	return h("div", { className: "rdct-bld-navigator" }, h("h3", null, labels.structure), !layout.rows.length && h("p", null, labels.empty), ...layout.rows.map((row, index) => h("section", {
		key: row.id,
		className: `rdct-bld-editor__row${over === row.id ? " is-over" : ""}`,
		...dropProps(row.id)
	}, h("div", { className: "rdct-bld-editor__rowbar" }, dragHandle({ rowId: row.id }, `${labels.row} ${index + 1}`), h(ButtonControl, {
		className: "rdct-bld-editor__select",
		"aria-pressed": selectedRow?.id === row.id && !selectedItem,
		onClick: () => select(row.id)
	}, `${labels.row} ${index + 1}`), action("arrow-up-alt2", labels.up, () => moveRow(index, index - 1), index === 0), action("arrow-down-alt2", labels.down, () => moveRow(index, index + 1), index === layout.rows.length - 1), action("admin-page", labels.duplicate, () => duplicateRow(row), layout.rows.length >= 8), action("trash", labels.remove, () => removeRow(row.id))), ...renderItems(row), h(ButtonControl, {
		onClick: () => addToRow(row.id),
		disabled: row.items.length >= 6
	}, labels.add))));
}
//#endregion
export { Navigator };
