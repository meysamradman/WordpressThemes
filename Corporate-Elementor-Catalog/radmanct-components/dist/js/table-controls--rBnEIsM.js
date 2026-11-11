//#region radmanct-components/src/js/admin/table-data.js
var limits = {
	columns: 12,
	rows: 100
};
function normalizeTable(value) {
	const columns = (Array.isArray(value?.columns) ? value.columns : ["", ""]).slice(0, limits.columns).map((cell) => typeof cell === "string" ? cell : "");
	if (!columns.length) columns.push("");
	return {
		columns,
		rows: (Array.isArray(value?.rows) ? value.rows : [{ cells: [] }]).slice(0, limits.rows).map((row) => ({ cells: columns.map((_, i) => typeof row?.cells?.[i] === "string" ? row.cells[i] : "") }))
	};
}
function changeColumn(input, index, action) {
	const data = normalizeTable(input);
	if (action === "add" && data.columns.length < limits.columns) {
		data.columns.push("");
		data.rows.forEach((row) => row.cells.push(""));
	}
	if (action === "remove" && data.columns.length > 1 && index >= 0 && index < data.columns.length) {
		data.columns.splice(index, 1);
		data.rows.forEach((row) => row.cells.splice(index, 1));
	}
	const next = index + (action === "earlier" ? -1 : 1);
	if (["earlier", "later"].includes(action) && index >= 0 && index < data.columns.length && next >= 0 && next < data.columns.length) for (const cells of [data.columns, ...data.rows.map((row) => row.cells)]) [cells[index], cells[next]] = [cells[next], cells[index]];
	return data;
}
var wrap = ($$type, value) => ({
	$$type,
	value
});
function encodeTable(value) {
	const data = normalizeTable(value);
	const strings = (values) => wrap("rdct-table-strings", values.map((cell) => wrap("string", cell)));
	return {
		columns: strings(data.columns),
		rows: wrap("rdct-table-rows", data.rows.map((row) => wrap("rdct-table-row", { cells: strings(row.cells) })))
	};
}
function decodeTable(value) {
	const strings = (prop) => (Array.isArray(prop?.value) ? prop.value : []).map((cell) => cell?.value);
	return normalizeTable({
		columns: strings(value?.columns),
		rows: (Array.isArray(value?.rows?.value) ? value.rows.value : []).map((row) => ({ cells: strings(row?.value?.cells) }))
	});
}
//#endregion
//#region radmanct-components/src/js/admin/table-grid.js
var { createElement: h$1, useState: useState$1 } = window.wp.element;
function TableGrid({ value, onChange, disabled = false, labels, ui }) {
	const data = normalizeTable(value);
	const [error, setError] = useState$1(false);
	const emit = (next) => {
		const normalized = normalizeTable(next);
		if (new TextEncoder().encode(JSON.stringify(normalized)).byteLength > 131072) {
			setError(true);
			return;
		}
		setError(false);
		onChange(normalized);
	};
	const button = (text, label, action, inactive = false) => h$1(ui?.Button || "button", {
		type: "button",
		"aria-label": label,
		title: label,
		disabled: disabled || inactive,
		onClick: action,
		...ui ? {
			size: "small",
			variant: "outlined"
		} : { className: "e-btn e-btn-txt-border" }
	}, text);
	const input = (value, label, maxLength, multiline, onChange) => ui ? h$1(ui.TextField, {
		size: "small",
		fullWidth: true,
		value,
		disabled,
		multiline,
		minRows: multiline ? 3 : void 0,
		inputProps: {
			maxLength,
			"aria-label": label
		},
		onChange
	}) : h$1(multiline ? "textarea" : "input", {
		className: "elementor-input",
		type: multiline ? void 0 : "text",
		value,
		disabled,
		rows: multiline ? 3 : void 0,
		maxLength,
		"aria-label": label,
		onChange
	});
	const moveRow = (index, step) => {
		const rows = [...data.rows];
		[rows[index], rows[index + step]] = [rows[index + step], rows[index]];
		emit({
			...data,
			rows
		});
	};
	const grid = h$1("fieldset", {
		className: `rdct-tbl-editor rdct-tbl-editor--${ui ? "atomic" : "classic"}`,
		disabled
	}, h$1("legend", null, labels.table), error && h$1("p", { role: "alert" }, labels.limit), h$1("div", {
		className: "rdct-tbl-editor__scroll",
		tabIndex: 0,
		"aria-label": labels.table
	}, h$1("table", null, h$1("thead", null, h$1("tr", null, h$1("th", null, labels.row), ...data.columns.map((column, index) => h$1("th", { key: index }, input(column, `${labels.column} ${index + 1}`, 500, false, (event) => emit({
		...data,
		columns: data.columns.map((cell, i) => i === index ? event.target.value : cell)
	})), h$1("div", { className: "rdct-tbl-editor__actions" }, button("‹", labels.earlier, () => emit(changeColumn(data, index, "earlier")), index === 0), button("›", labels.later, () => emit(changeColumn(data, index, "later")), index === data.columns.length - 1), button("×", labels.removeColumn, () => emit(changeColumn(data, index, "remove")), data.columns.length === 1)))))), h$1("tbody", null, ...data.rows.map((row, index) => h$1("tr", { key: index }, h$1("th", { scope: "row" }, index + 1, h$1("div", { className: "rdct-tbl-editor__actions" }, button("↑", labels.up, () => moveRow(index, -1), index === 0), button("↓", labels.down, () => moveRow(index, 1), index === data.rows.length - 1), button("+", labels.duplicate, () => emit({
		...data,
		rows: [
			...data.rows.slice(0, index + 1),
			{ cells: [...row.cells] },
			...data.rows.slice(index + 1)
		]
	}), data.rows.length >= limits.rows), button("×", labels.removeRow, () => emit({
		...data,
		rows: data.rows.filter((_, i) => i !== index)
	})))), ...row.cells.map((cell, column) => h$1("td", { key: column }, input(cell, `${labels.row} ${index + 1}, ${labels.column} ${column + 1}`, 3e3, true, (event) => emit({
		...data,
		rows: data.rows.map((entry, i) => i === index ? { cells: entry.cells.map((text, j) => j === column ? event.target.value : text) } : entry)
	}))))))))), h$1("div", { className: "rdct-tbl-editor__actions" }, button(labels.addColumn, labels.addColumn, () => emit(changeColumn(data, 0, "add")), data.columns.length >= limits.columns), button(labels.addRow, labels.addRow, () => emit({
		...data,
		rows: [...data.rows, { cells: data.columns.map(() => "") }]
	}), data.rows.length >= limits.rows)));
	return ui ? h$1(ui.Box, { sx: (theme) => ({ "--rdct-table-border": theme.palette.divider }) }, grid) : grid;
}
//#endregion
//#region radmanct-components/src/js/admin/table-controls.js
var { createElement: h, createRoot, useState, useEffect } = window.wp.element;
var config = window.rdctTableControl;
function ClassicGrid({ owner }) {
	const [value, setValue] = useState(owner.getControlValue());
	useEffect(() => {
		const settings = owner.container?.settings;
		const sync = () => setValue(owner.getControlValue());
		settings?.on(`change:${owner.model.get("name")}`, sync);
		return () => settings?.off(`change:${owner.model.get("name")}`, sync);
	}, [owner]);
	return h(TableGrid, {
		value,
		labels: config.labels,
		onChange: (next) => {
			setValue(next);
			owner.setValue(next);
		}
	});
}
var register = () => {
	const editor = window.elementor;
	if (!config || !editor?.modules?.controls?.BaseData || editor.__rdctTableControl) return;
	editor.__rdctTableControl = true;
	editor.addControlView("rdct-table-grid", editor.modules.controls.BaseData.extend({
		onReady() {
			this.gridRoot?.unmount();
			this.gridRoot = createRoot(this.el.querySelector("[data-rdct-table-editor]"));
			this.renderGrid();
		},
		onExternalChange() {
			this.renderGrid();
		},
		renderGrid() {
			if (!this.gridRoot) return;
			this.gridRoot.render(h(ClassicGrid, { owner: this }));
		},
		onBeforeDestroy() {
			this.gridRoot?.unmount();
			this.gridRoot = null;
		}
	}));
};
window.addEventListener("elementor/init", register);
window.__elementorEditorV1LoadingPromise?.then(register);
register();
var controls = window.elementorV2?.editorControls;
var registry = window.elementorV2?.editorEditingPanel?.controlsRegistry;
var util = {
	key: "rdct-table-data",
	create: (value) => ({
		$$type: "rdct-table-data",
		value
	}),
	extract: (prop) => prop?.value
};
var ui = window.elementorV2?.ui;
if (config && controls?.useBoundProp && ui?.TextField && ui.Button && ui.Box && registry && !registry.get("rdct-table-grid")) {
	function AtomicGrid() {
		const { value, setValue, disabled } = controls.useBoundProp(util);
		return h(TableGrid, {
			value: decodeTable(value),
			disabled,
			labels: config.labels,
			ui,
			onChange: (next) => setValue(encodeTable(next))
		});
	}
	registry.register("rdct-table-grid", AtomicGrid, "full", util);
}
//#endregion
