import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { a as InputControl, c as SelectControl, n as ChoiceControl, t as ButtonControl } from "./controls-DqGpx29l.js";
import { r as request, t as builderSaveRequest } from "./builder-api-CYWTbvUK.js";
import { n as TabList } from "./panel-tabs-BJ7bpkAa.js";
//#region radmanct/src/js/admin/builder-tree.js
function containers(layout) {
	const result = [];
	const visit = (node, depth, parentId = null) => {
		result.push(node.type === "container" ? {
			...node,
			appearance: node.layout || {},
			depth,
			parentId
		} : node);
		for (const item of node.items || []) if (item.type === "container") visit(item, depth + 1, node.id);
	};
	layout.rows.forEach((row) => visit(row, 0));
	return result;
}
var findContainer = (layout, id) => containers(layout).find((row) => row.id === id);
function updateContainer(layout, id, update) {
	const visit = (node) => {
		if (node.id === id) {
			if (node.type !== "container") return update(node);
			const next = update({
				...node,
				appearance: node.layout || {}
			});
			return {
				...node,
				items: next.items,
				layout: next.appearance
			};
		}
		const items = node.items.map((item) => item.type === "container" ? visit(item) : item);
		return {
			...node,
			items
		};
	};
	return {
		...layout,
		rows: layout.rows.map(visit)
	};
}
function withinLimits(layout) {
	const rows = containers(layout);
	return rows.every((row) => (row.depth ?? 0) <= 2 && row.items.length <= 6) && rows.reduce((count, row) => count + row.items.length, layout.rows.length) <= 96;
}
//#endregion
//#region radmanct/src/js/admin/builder-selection.js
function previewContentHeight(content) {
	return Math.max(48, Math.ceil(content ? content.getBoundingClientRect().height + content.offsetTop : 0));
}
function resolveSelection(layout, selection) {
	const rows = containers(layout);
	if (!selection?.itemId && selection?.rowId && !layout.rows.some((row) => row.id === selection.rowId)) selection = {
		...selection,
		itemId: selection.rowId
	};
	const row = rows.find((entry) => entry.items.some((item) => item.id === selection?.itemId)) || rows.find((entry) => entry.id === selection?.rowId) || layout.rows[0] || null;
	return {
		row,
		item: row?.items.find((entry) => entry.id === selection?.itemId) || null
	};
}
function pickPreviewTarget(document, x, y) {
	for (const attribute of ["data-builder-preview-item", "data-builder-preview-row"]) for (const node of [...document.querySelectorAll(`[${attribute}]`)].reverse()) {
		const rect = node.getBoundingClientRect();
		if (rect.width && rect.height && x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom) return {
			id: node.getAttribute(attribute),
			item: attribute === "data-builder-preview-item"
		};
	}
	return null;
}
//#endregion
//#region radmanct/src/js/admin/builder-elements.js
var newId = () => `b${Array.from(crypto.getRandomValues(/* @__PURE__ */ new Uint32Array(4)), (value) => value.toString(16).padStart(8, "0")).join("")}`;
function cloneItem(item) {
	return {
		...item,
		id: newId(),
		...item.items ? { items: item.items.map(cloneItem) } : {},
		...item.links ? { links: item.links.map((link) => ({
			...link,
			id: newId()
		})) } : {}
	};
}
function createItem(type, config) {
	const common = {
		id: newId(),
		type
	};
	if (type === "container") return {
		...common,
		appearance: { width: "equal" },
		layout: {
			rowLayout: "flex",
			direction: "row",
			wrap: "nowrap",
			gap: 12,
			padding: 0,
			mobileLayout: "inline"
		},
		items: []
	};
	if ([
		"logo",
		"image",
		"panel",
		"theme-toggle",
		"button",
		"icon",
		"contact",
		"social"
	].includes(type)) common.appearance = { width: "auto" };
	if (["icon", "contact"].includes(type)) return {
		...common,
		icon: type === "icon" ? "phone" : "",
		text: "",
		url: "",
		size: 20,
		direction: "auto"
	};
	if (type === "panel") return {
		...common,
		panelContentType: "empty",
		panelTemplateId: 0,
		panelMode: "sidebar",
		panelMotion: "standard"
	};
	if (type === "social") return {
		...common,
		links: [],
		size: 20
	};
	if (type === "button") return {
		...common,
		text: config.labels?.button || "Button",
		url: ""
	};
	if (type === "menu" && config.kind === "footer") return {
		...common,
		menuId: 0,
		menuLayout: "vertical",
		mobileDisplay: "expanded"
	};
	return {
		...common,
		...["text", "button"].includes(type) ? { text: "" } : {},
		...type === "button" ? { url: "" } : {},
		...type === "menu" ? { menuId: 0 } : {}
	};
}
//#endregion
//#region radmanct/src/js/admin/builder-history.js
function commit(state, value) {
	const next = typeof value === "function" ? value(state.present) : value;
	if (JSON.stringify(next) === JSON.stringify(state.present)) return state;
	return {
		past: [...state.past.slice(-19), state.present],
		present: next,
		future: []
	};
}
function undo(state) {
	if (!state.past.length) return state;
	return {
		past: state.past.slice(0, -1),
		present: state.past.at(-1),
		future: [state.present, ...state.future]
	};
}
function redo(state) {
	if (!state.future.length) return state;
	return {
		past: [...state.past, state.present],
		present: state.future[0],
		future: state.future.slice(1)
	};
}
//#endregion
//#region radmanct/src/js/admin/builder-drag.js
function findPreviewDrop(targets, x, y, layout, mobile = false, rtl = false, device = mobile ? "mobile" : "desktop") {
	const contains = ({ rect }) => rect.width > 0 && rect.height > 0 && x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom;
	const region = targets.filter((entry) => !entry.item && contains(entry)).sort((a, b) => (findContainer(layout, b.rowId)?.depth ?? 0) - (findContainer(layout, a.rowId)?.depth ?? 0) || a.rect.width * a.rect.height - b.rect.width * b.rect.height)[0];
	const item = targets.find((entry) => entry.item && (!region || entry.rowId === region.rowId) && contains(entry));
	const target = item || region;
	if (!target) return null;
	const row = findContainer(layout, target.rowId);
	if (!row) return null;
	if (!item) return {
		rowId: row.id,
		index: row.items.length,
		token: row.id
	};
	const index = row.items.findIndex((entry) => entry.id === item.id);
	if (index < 0) return null;
	const grid = row.appearance?.rowLayout === "grid";
	const columns = device === "mobile" ? row.appearance?.mobileColumns ?? 1 : (device === "desktop" ? null : row.appearance?.[`${device}Columns`]) ?? row.appearance?.columns ?? 3;
	const override = device === "desktop" ? "inherit" : row.items[index].appearance?.[`${device}Width`];
	const width = !override || override === "inherit" ? row.items[index].appearance?.width : override;
	const flow = row.appearance?.[`${device}Layout`] ?? (mobile ? "stack" : "inline");
	const after = (device === "desktop" ? row.appearance?.direction : !row.appearance?.[`${device}Direction`] || row.appearance[`${device}Direction`] === "inherit" ? row.appearance?.direction : row.appearance[`${device}Direction`]) === "column" && !grid || (grid ? columns === 1 : flow === "stack") || width === "full" ? y > item.rect.top + item.rect.height / 2 : rtl ? x < item.rect.left + item.rect.width / 2 : x > item.rect.left + item.rect.width / 2;
	return {
		rowId: row.id,
		index: index + Number(after),
		token: item.id
	};
}
function dropIndex(layout, source, rowId, index) {
	const row = findContainer(layout, rowId);
	if (!row) return 0;
	const before = row.items.findIndex((item) => item.id === source.itemId);
	return source.rowId === rowId && before >= 0 && before < index ? index - 1 : index;
}
function previewDropLine(target, targets, layout, mobile = false, rtl = false, device = mobile ? "mobile" : "desktop") {
	if (!target) return null;
	const row = findContainer(layout, target.rowId);
	const region = targets.find((entry) => !entry.item && entry.id === target.rowId)?.rect;
	if (!row || !region) return null;
	const last = target.index >= row.items.length;
	const itemId = row.items[last ? row.items.length - 1 : target.index]?.id;
	const rect = targets.find((entry) => entry.item && entry.id === itemId)?.rect;
	if (!rect) return {
		...region,
		inside: true
	};
	const direction = row.appearance?.[`${device}Direction`];
	const flow = row.appearance?.[`${device}Layout`] ?? (mobile ? "stack" : "inline");
	if ((direction && direction !== "inherit" ? direction : row.appearance?.direction) === "column" || flow === "stack" || row.appearance?.rowLayout === "grid" && (row.appearance?.[`${device}Columns`] ?? row.appearance.columns) === 1) return {
		left: region.left,
		top: (last ? rect.bottom : rect.top) - 1.5,
		width: region.width,
		height: 3
	};
	return {
		left: (rtl ? last ? rect.left : rect.right : last ? rect.right : rect.left) - 1.5,
		top: region.top,
		width: 3,
		height: region.height
	};
}
//#endregion
//#region radmanct/src/js/admin/builder-mutations.js
function moveRow(layout, from, to) {
	if (from < 0 || to < 0 || from >= layout.rows.length || to >= layout.rows.length || from === to) return layout;
	const rows = [...layout.rows];
	rows.splice(to, 0, rows.splice(from, 1)[0]);
	return {
		...layout,
		rows
	};
}
function duplicateRow(layout, rowId) {
	const index = layout.rows.findIndex((row) => row.id === rowId);
	if (index < 0 || layout.rows.length >= 8) return layout;
	const row = layout.rows[index];
	const copy = {
		...row,
		id: newId(),
		items: row.items.map(cloneItem)
	};
	const rows = [...layout.rows];
	rows.splice(index + 1, 0, copy);
	const next = {
		...layout,
		rows
	};
	return withinLimits(next) ? next : layout;
}
function moveItem(layout, sourceId, itemId, targetId, targetIndex) {
	const source = findContainer(layout, sourceId);
	const target = findContainer(layout, targetId);
	const item = source?.items.find((entry) => entry.id === itemId);
	if (!item || !target || sourceId !== targetId && target.items.length >= 6) return layout;
	if (item.type === "container" && containers({ rows: [item] }).some((row) => row.id === targetId)) return layout;
	let next = updateContainer(layout, sourceId, (row) => ({
		...row,
		items: row.items.filter((entry) => entry.id !== itemId)
	}));
	next = updateContainer(next, targetId, (row) => {
		const items = [...row.items];
		items.splice(Math.min(Math.max(0, targetIndex), items.length), 0, item);
		return {
			...row,
			items
		};
	});
	return withinLimits(next) ? next : layout;
}
//#endregion
//#region radmanct/src/js/admin/builder-shortcuts.js
function historyShortcut(event) {
	if (!(event.ctrlKey || event.metaKey) || event.altKey || event.isComposing || event.defaultPrevented) return null;
	if (event.target?.closest?.("input, textarea, select, [contenteditable]:not([contenteditable=\"false\"]), [role=\"textbox\"]")) return null;
	const key = event.key?.toLowerCase();
	if (key === "z") return event.shiftKey ? "redo" : "undo";
	return key === "y" && !event.shiftKey ? "redo" : null;
}
//#endregion
//#region radmanct/src/js/admin/builder-devices.js
var devices = [
	{
		id: "desktop",
		width: 1440
	},
	{
		id: "laptop",
		width: 1200
	},
	{
		id: "tablet",
		width: 900
	},
	{
		id: "mobile",
		width: 390
	}
];
var deviceAt = (width) => width < 768 ? "mobile" : width < 1024 ? "tablet" : width < 1440 ? "laptop" : "desktop";
var deviceKey = (device, key) => device === "desktop" ? key : `${device}${key[0].toUpperCase()}${key.slice(1)}`;
function responsiveDefaults(row) {
	const values = { hiddenDevices: [] };
	for (const device of [
		"laptop",
		"tablet",
		"mobile"
	]) {
		values[`${device}Align`] = "inherit";
		if (row) {
			values[`${device}Justify`] = "inherit";
			values[`${device}Direction`] = "inherit";
		} else values[`${device}Width`] = "inherit";
		if (device === "mobile") continue;
		if (row) {
			for (const field of [
				"Gap",
				"Padding",
				"Columns"
			]) values[`${device}${field}`] = null;
			values[`${device}Layout`] = "inherit";
		} else values[`${device}FontSize`] = 0;
	}
	return values;
}
function hiddenDevices(value) {
	const result = new Set(value.hiddenDevices || []);
	if (value.visibility === "desktop") result.add("mobile");
	if (value.visibility === "mobile") [
		"desktop",
		"laptop",
		"tablet"
	].forEach((device) => result.add(device));
	return [...result];
}
//#endregion
//#region radmanct/src/js/admin/builder-toolbar.js
var { createElement: h$3 } = window.wp.element;
function BuilderToolbar({ labels, kind, title, creating = false, onTitleChange, copySource = "", layouts = [], onCopySource, busy, status = "", width, onWidthChange, dirty, canSave, canUndo, canRedo, onUndo, onRedo, focused, onFocus, onSettings, settingsOpen }) {
	const icons = {
		desktop: "desktop",
		laptop: "laptop",
		tablet: "tablet",
		mobile: "smartphone"
	};
	return h$3("div", { className: "rdct-bld-editor__toolbar" }, h$3("div", { className: `rdct-bld-toolbar__identity${creating ? " rdct-bld-toolbar__identity--creating" : ""}` }, h$3("a", {
		href: window.rdctBuilder.libraryUrl,
		className: "rdct-btn",
		"aria-label": labels.layoutsTab
	}, h$3("span", {
		className: "dashicons dashicons-list-view",
		"aria-hidden": true
	})), h$3(InputControl, {
		id: "rdct-builder-title",
		value: title,
		onChange: onTitleChange,
		disabled: busy,
		maxLength: 80,
		required: true,
		autoFocus: !!window.rdctBuilder.focusTitle,
		"aria-label": labels.layoutName,
		placeholder: labels.layoutName
	}), creating && h$3(SelectControl, {
		value: copySource,
		onChange: onCopySource,
		disabled: busy,
		"aria-label": labels.copyFrom,
		options: [["", labels.newLayout], ...layouts.map(({ id, title }) => [id, title])]
	})), h$3("div", { className: "rdct-bld-toolbar__preview" }, h$3(ChoiceControl, {
		label: labels.device,
		value: deviceAt(width),
		iconOnly: true,
		options: devices.map(({ id }) => [
			id,
			labels[id],
			false,
			icons[id]
		]),
		onChange: (id) => onWidthChange(devices.find((device) => device.id === id).width)
	})), h$3("div", { className: "rdct-bld-toolbar__save" }, h$3("span", {
		className: `rdct-bld-toolbar__status${status ? " rdct-loading" : dirty ? " is-dirty" : ""}`,
		role: "status",
		"aria-live": "polite",
		"aria-atomic": true
	}, status || (dirty ? labels.unsaved : labels.ready)), h$3("div", {
		className: "rdct-bld-editor__actions",
		role: "group",
		"aria-label": labels.layoutSettings
	}, h$3(ButtonControl, {
		icon: "undo",
		iconOnly: true,
		label: labels.undo,
		onClick: onUndo,
		disabled: !canUndo
	}), h$3(ButtonControl, {
		icon: "redo",
		iconOnly: true,
		label: labels.redo,
		onClick: onRedo,
		disabled: !canRedo
	}), h$3(ButtonControl, {
		icon: focused ? "editor-contract" : "editor-expand",
		iconOnly: true,
		label: labels.focusMode,
		"aria-pressed": focused,
		onClick: onFocus
	}), h$3(ButtonControl, {
		icon: "admin-generic",
		iconOnly: true,
		label: labels[`${kind}Settings`],
		"aria-pressed": settingsOpen,
		onClick: onSettings
	})), h$3(ButtonControl, {
		type: "submit",
		variant: "primary",
		icon: "saved",
		label: labels.save,
		disabled: busy || !canSave,
		"aria-busy": !!busy
	})));
}
//#endregion
//#region radmanct/src/js/admin/builder-resize.js
var { createElement: h$2, useRef: useRef$2, useState: useState$2 } = window.wp.element;
var resizePercent = (width, parentWidth, delta = 0) => Math.max(5, Math.min(100, Math.round((width + delta) / Math.max(1, parentWidth) * 100)));
function ResizeHandle({ target, parent, item, device, rtl, label, scale, onCommit }) {
	const drag = useRef$2(null);
	const [draft, setDraft] = useState$2(null);
	if (!parent?.rect.width) return null;
	const initial = resizePercent(target.rect.width, parent.rect.width);
	const commit = (value) => onCommit({
		...item.appearance,
		[deviceKey(device, "width")]: "custom",
		[deviceKey(device, "customWidth")]: value
	});
	return h$2("div", { className: "rdct-bld-resize" }, draft !== null && h$2("div", {
		className: "rdct-bld-resize__guide",
		style: { inlineSize: parent.rect.width * draft / 100 }
	}), draft !== null && h$2("output", { className: "rdct-bld-resize__value" }, `${draft}%`), h$2("button", {
		type: "button",
		className: "rdct-bld-resize__handle",
		role: "slider",
		"aria-label": label,
		"aria-valuemin": 5,
		"aria-valuemax": 100,
		"aria-valuenow": draft ?? initial,
		"aria-orientation": "horizontal",
		onClick: (event) => event.stopPropagation(),
		onPointerDown: (event) => {
			event.preventDefault();
			event.stopPropagation();
			event.currentTarget.setPointerCapture(event.pointerId);
			drag.current = {
				x: event.clientX,
				value: initial
			};
			setDraft(initial);
		},
		onPointerMove: (event) => {
			if (!drag.current) return;
			const value = resizePercent(target.rect.width, parent.rect.width, (event.clientX - drag.current.x) * (rtl ? -1 : 1) / scale);
			drag.current.value = value;
			setDraft(value);
		},
		onPointerUp: () => {
			if (drag.current) commit(drag.current.value);
			drag.current = null;
			setDraft(null);
		},
		onPointerCancel: () => {
			drag.current = null;
			setDraft(null);
		},
		onKeyDown: (event) => {
			if (event.key === "Escape") {
				drag.current = null;
				setDraft(null);
				return;
			}
			if (![
				"ArrowLeft",
				"ArrowRight",
				"Home",
				"End"
			].includes(event.key)) return;
			event.preventDefault();
			event.stopPropagation();
			const step = (event.shiftKey ? 10 : 1) * (event.key === "ArrowRight" ? 1 : -1) * (rtl ? -1 : 1);
			commit(event.key === "Home" ? 5 : event.key === "End" ? 100 : Math.max(5, Math.min(100, initial + step)));
		}
	}));
}
//#endregion
//#region radmanct/src/js/admin/builder-preview-loader.js
function loadPreview({ request, id, layout, direction, errorMessage, onStart, onSuccess, onError, delay = 400 }) {
	const controller = new AbortController();
	let current = true;
	onStart();
	const timer = setTimeout(async () => {
		try {
			const result = await request("preview", id, {
				layout: {
					...layout,
					enabled: false
				},
				direction
			}, controller.signal);
			if (!current) return;
			if (typeof result?.html !== "string" || !result.html.trim()) throw new Error(errorMessage);
			onSuccess(result.html);
		} catch (error) {
			if (current && error?.name !== "AbortError") onError(error?.message || errorMessage);
		}
	}, delay);
	return () => {
		current = false;
		clearTimeout(timer);
		controller.abort();
	};
}
//#endregion
//#region radmanct/src/js/admin/builder-preview-menu.js
function prepareMenuPreview(doc) {
	for (const root of doc.querySelectorAll("[data-rdct-menu]")) {
		const button = root.querySelector(":scope > .rdct-menu-toggle");
		const body = root.querySelector(":scope > .rdct-nav, :scope > [data-rdct-panel-body]");
		if (!button || !body) continue;
		root.dataset.enhanced = "preview";
		button.hidden = false;
	}
}
//#endregion
//#region radmanct/src/js/admin/builder-preview.js
var { createElement: h$1, useState: useState$1, useEffect: useEffect$1, useRef: useRef$1 } = window.wp.element;
var { labels: labels$1, direction: initialDirection } = window.rdctBuilder;
function Preview({ layout, id, selected, onSelect, disabled, dragProps, dropResolver, over, actions = [], viewportWidth, direction = initialDirection, onResize, dropTarget, onLoadingChange }) {
	const open = true;
	const width = viewportWidth ?? 1440;
	const device = deviceAt(width);
	const mobile = device === "mobile";
	const [html, setHtml] = useState$1("");
	const [status, setStatus] = useState$1("");
	const [failed, setFailed] = useState$1(false);
	const [attempt, setAttempt] = useState$1(0);
	const [renderedKey, setRenderedKey] = useState$1("");
	const frame = useRef$1(null);
	const viewport = useRef$1(null);
	const [availableWidth, setAvailableWidth] = useState$1(0);
	const scale = availableWidth > 0 ? Math.min(1, availableWidth / width) : 1;
	const renderedHtml = useRef$1("");
	const [height, setHeight] = useState$1(48);
	const [ready, setReady] = useState$1(false);
	const [targets, setTargets] = useState$1([]);
	const serialized = JSON.stringify(layout);
	const key = JSON.stringify([
		id,
		direction,
		serialized
	]);
	const interactive = renderedKey === key && ready && !status && !disabled;
	const loading = !failed && (renderedKey !== key || !ready || !!status);
	useEffect$1(() => {
		onLoadingChange?.(loading);
	}, [loading, onLoadingChange]);
	useEffect$1(() => () => onLoadingChange?.(false), [onLoadingChange]);
	useEffect$1(() => {
		const node = viewport.current;
		if (!node) return;
		const measure = () => {
			const style = getComputedStyle(node);
			setAvailableWidth(Math.max(1, node.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight)));
		};
		const observer = typeof ResizeObserver === "function" ? new ResizeObserver(measure) : null;
		observer?.observe(node);
		window.addEventListener("resize", measure);
		measure();
		return () => {
			observer?.disconnect();
			window.removeEventListener("resize", measure);
		};
	}, []);
	useEffect$1(() => {
		if (!ready) return;
		const content = frame.current?.contentDocument?.querySelector(".rdct-preview");
		if (!content) return;
		let current = true;
		let pendingFrame = 0;
		const rowByItem = new Map(containers(layout).flatMap((row) => row.items.map((item) => [item.id, row.id])));
		const measure = () => {
			pendingFrame = 0;
			if (!current) return;
			setHeight(previewContentHeight(content));
			setTargets([...content.querySelectorAll("[data-builder-preview-row], [data-builder-preview-item]")].map((node) => {
				const itemId = node.getAttribute("data-builder-preview-item");
				const id = itemId || node.getAttribute("data-builder-preview-row");
				const bounds = node.getBoundingClientRect();
				return {
					id,
					item: !!itemId,
					rowId: itemId ? rowByItem.get(id) : id,
					rect: {
						left: bounds.left,
						top: bounds.top,
						width: bounds.width,
						height: bounds.height,
						right: bounds.right,
						bottom: bounds.bottom
					}
				};
			}).filter((target) => target.rect.width && target.rect.height));
		};
		const queue = () => {
			if (current && !pendingFrame) pendingFrame = requestAnimationFrame(measure);
		};
		const observer = typeof ResizeObserver === "function" ? new ResizeObserver(queue) : null;
		observer?.observe(content);
		queue();
		frame.current.contentWindow.addEventListener("resize", queue);
		const view = frame.current.contentWindow;
		(frame.current.contentDocument.fonts?.ready)?.then(queue).catch(() => {});
		return () => {
			current = false;
			observer?.disconnect();
			cancelAnimationFrame(pendingFrame);
			view.removeEventListener("resize", queue);
		};
	}, [
		open,
		ready,
		html,
		width,
		serialized
	]);
	const highlight = () => {
		const doc = frame.current?.contentDocument;
		if (!doc) return;
		const accent = getComputedStyle(frame.current).getPropertyValue("--rdct-admin-accent").trim() || "Highlight";
		doc.querySelectorAll("[data-builder-preview-item], [data-builder-preview-row]").forEach((node) => {
			const active = selected?.itemId ? node.getAttribute("data-builder-preview-item") === selected.itemId : node.getAttribute("data-builder-preview-row") === selected?.rowId;
			node.style.outline = active ? `2px solid ${accent}` : "";
			node.style.outlineOffset = active ? "-2px" : "";
		});
	};
	useEffect$1(highlight, [selected, html]);
	useEffect$1(() => loadPreview({
		request,
		id,
		layout: JSON.parse(serialized),
		direction,
		errorMessage: labels$1.requestError,
		delay: renderedHtml.current ? 400 : 0,
		onStart: () => {
			setStatus(labels$1.previewLoading);
			setFailed(false);
		},
		onSuccess: (value) => {
			if (renderedHtml.current !== value) {
				renderedHtml.current = value;
				setReady(false);
				setHtml(value);
			}
			setRenderedKey(key);
			setStatus("");
		},
		onError: (message) => {
			setStatus(message);
			setFailed(true);
		}
	}), [
		serialized,
		direction,
		id,
		attempt
	]);
	const resolveDrop = (x, y) => {
		const rect = frame.current?.getBoundingClientRect();
		if (!rect || !interactive || x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) return null;
		return findPreviewDrop(targets, (x - rect.left) / scale, (y - rect.top) / scale, layout, mobile, direction === "rtl", device) || (!layout.rows.length ? {
			rowId: null,
			index: 0,
			token: "empty"
		} : null);
	};
	dropResolver.current = resolveDrop;
	const insertion = previewDropLine(dropTarget, targets, layout, mobile, direction === "rtl", device);
	useEffect$1(() => () => {
		dropResolver.current = null;
	}, [dropResolver]);
	return h$1("section", {
		className: "rdct-bld-preview",
		"aria-busy": loading
	}, h$1("div", null, failed && h$1("p", { role: "alert" }, status), failed && h$1(ButtonControl, {
		disabled,
		onClick: () => setAttempt((value) => value + 1)
	}, labels$1.retryPreview), h$1("div", {
		className: "rdct-bld-preview__viewport",
		ref: viewport
	}, html && h$1("div", {
		className: "rdct-bld-preview__stage",
		style: {
			width: `${width * scale}px`,
			height: `${height * scale}px`
		}
	}, h$1("div", {
		className: "rdct-bld-preview__frame",
		style: {
			width: `${width}px`,
			transform: `scale(${scale})`
		}
	}, h$1("iframe", {
		ref: frame,
		title: labels$1.preview,
		srcDoc: html,
		sandbox: "allow-same-origin",
		"data-vue-devtools-ignore": "",
		referrerPolicy: "no-referrer",
		tabIndex: -1,
		onError: () => {
			setStatus(labels$1.requestError);
			setFailed(true);
		},
		style: { height: `${height}px` },
		onLoad: () => {
			const doc = frame.current?.contentDocument;
			if (!doc) return;
			prepareMenuPreview(doc);
			setHeight(previewContentHeight(doc.querySelector(".rdct-preview")));
			setReady(true);
			highlight();
		}
	}), h$1("div", {
		className: "rdct-bld-preview__selection",
		onClick: (event) => {
			if (!interactive) return;
			const doc = frame.current?.contentDocument;
			const rect = frame.current?.getBoundingClientRect();
			if (!doc || !rect) return;
			const target = pickPreviewTarget(doc, (event.clientX - rect.left) / scale, (event.clientY - rect.top) / scale);
			if (target) onSelect(target);
		}
	}, ...(interactive ? targets : []).map((target) => {
		const active = target.item ? selected?.itemId === target.id : !selected?.itemId && selected?.rowId === target.id;
		const row = findContainer(layout, target.rowId);
		const item = row?.items.find((entry) => entry.id === target.id);
		if (!row || target.item && !item || !target.item && row.type === "container") return null;
		const title = target.item ? labels$1[item?.type] : row.type === "container" ? labels$1.container : `${labels$1.row} ${layout.rows.indexOf(row) + 1}`;
		return h$1("div", {
			key: `${target.item}-${target.id}`,
			className: `rdct-bld-preview__box${target.item ? " is-item" : " is-row"}${item?.type === "container" ? " is-container" : ""}${active ? " is-active" : ""}${over === target.id ? " is-drop" : ""}`,
			style: {
				left: target.rect.left,
				top: target.rect.top,
				width: target.rect.width,
				height: target.rect.height
			}
		}, h$1("button", {
			type: "button",
			className: "rdct-bld-preview__target",
			"aria-label": title,
			"aria-pressed": active,
			disabled: disabled || !ready || !!status,
			...dragProps({
				rowId: target.rowId,
				...target.item ? { itemId: target.id } : {}
			}),
			onClick: (event) => {
				event.stopPropagation();
				onSelect({
					id: target.id,
					item: target.item
				});
			}
		}), h$1("div", {
			className: "rdct-bld-preview__tools",
			onClick: (event) => event.stopPropagation()
		}, h$1(ButtonControl, {
			variant: "ghost",
			title,
			"aria-label": title,
			disabled,
			...dragProps(!target.item && row.type === "container" ? {
				rowId: row.parentId,
				itemId: row.id
			} : {
				rowId: target.rowId,
				...target.item ? { itemId: target.id } : {}
			}),
			onClick: () => onSelect({
				id: target.id,
				item: target.item
			})
		}, title), active && actions), active && target.item && onResize && row.appearance?.rowLayout !== "grid" && h$1(ResizeHandle, {
			target,
			parent: targets.find((entry) => !entry.item && entry.id === row.id),
			item,
			device,
			rtl: direction === "rtl",
			label: labels$1.resizeWidth,
			scale,
			onCommit: (appearance) => onResize(row.id, item.id, appearance)
		}));
	}), insertion && h$1("div", {
		className: `rdct-bld-drop-line${insertion.inside ? " is-inside" : ""}`,
		style: {
			left: insertion.left,
			top: insertion.top,
			width: insertion.width,
			height: insertion.height
		},
		"aria-hidden": true
	})))))));
}
//#endregion
//#region radmanct/src/js/admin/builder.js
var { createElement: h, createRoot, useState, useEffect, useRef } = window.wp.element;
var config = window.rdctBuilder;
var labels = config.labels;
var button = (text, onClick, disabled = false, extra = {}) => h(ButtonControl, {
	onClick,
	disabled,
	...extra
}, text);
function App() {
	const [history, setHistory] = useState({
		past: [],
		present: config.data.layout,
		future: []
	});
	const layout = history.present;
	const setLayout = (value) => setHistory((state) => commit(state, value));
	const [over, setOver] = useState("");
	const [dragView, setDragView] = useState(null);
	const [dropTarget, setDropTarget] = useState(null);
	const pointerCallbacks = useRef({});
	const pointerController = useRef(null);
	const [pointerEngine, setPointerEngine] = useState(null);
	useEffect(() => {
		let active = true;
		__vitePreload(() => import("./builder-pointer-drag-DtZ5SJuA.js").then((module) => {
			if (active) setPointerEngine(() => module.createPointerDrag);
		}), [], import.meta.url).catch(() => {
			if (active) {
				setFailed(true);
				setMessage(labels.requestError);
			}
		});
		return () => {
			active = false;
		};
	}, []);
	if (pointerEngine && !pointerController.current) pointerController.current = pointerEngine(Object.fromEntries([
		"start",
		"move",
		"end",
		"cancel"
	].map((name) => [name, (...args) => pointerCallbacks.current[name]?.(...args)])));
	const [selected, setSelected] = useState(null);
	const [panel, setPanel] = useState(config.initialSettingsTab ? "layout" : "elements");
	const [focused, setFocused] = useState(false);
	const [search, setSearch] = useState("");
	const [previewWidth, setPreviewWidth] = useState(1440);
	const device = deviceAt(previewWidth);
	const changePreviewWidth = (width) => {
		if (Number.isFinite(width)) setPreviewWidth(Math.round(Math.min(2560, Math.max(320, width))));
	};
	const selectDevice = (id) => {
		const entry = devices.find((value) => value.id === id);
		if (entry) setPreviewWidth(entry.width);
	};
	const { row: selectedRow, item: selectedItem } = resolveSelection(layout, selected);
	const activePanel = panel;
	const select = (rowId, itemId = null) => {
		setSelected({
			rowId,
			itemId
		});
		setPanel("settings");
	};
	const drag = useRef(null);
	const canvasDrop = useRef(null);
	const suppressClick = useRef(false);
	useEffect(() => {
		document.body.classList.add("rdct-bld-screen");
		const cancel = () => pointerController.current?.cancel();
		window.addEventListener("blur", cancel);
		return () => {
			pointerController.current?.cancel();
			window.removeEventListener("blur", cancel);
			document.body.classList.remove("rdct-bld-screen");
		};
	}, []);
	useEffect(() => {
		if (!focused) return;
		const overflow = document.body.style.overflow;
		document.body.style.overflow = "hidden";
		return () => {
			document.body.style.overflow = overflow;
		};
	}, [focused]);
	const focusTarget = useRef(null);
	useEffect(() => {
		if (focusTarget.current) {
			document.querySelector(`[data-builder-focus="${focusTarget.current}"]`)?.focus();
			focusTarget.current = null;
		}
	}, [layout]);
	const [stored, setStored] = useState({
		id: config.id,
		data: config.data,
		layouts: config.layouts,
		defaults: config.defaults || {},
		translation: config.translation || null,
		creating: !!config.creating
	});
	const [draftTitle, setDraftTitle] = useState(config.creating ? "" : config.layouts.find((entry) => entry.id === config.id)?.title || "");
	const [copySource, setCopySource] = useState(config.copySource || "");
	const previewDirection = stored.translation?.direction || config.direction;
	const [busy, setBusy] = useState(false);
	const [message, setMessage] = useState("");
	const [failed, setFailed] = useState(false);
	useEffect(() => {
		if (!message || failed || busy) return;
		const timer = setTimeout(() => setMessage(""), 4500);
		return () => clearTimeout(timer);
	}, [
		message,
		failed,
		busy
	]);
	const [Inspector, setInspector] = useState(null);
	const [Navigator, setNavigator] = useState(null);
	const [SettingsPanel, setSettingsPanel] = useState(null);
	const [panelErrors, setPanelErrors] = useState({});
	const [panelAttempt, setPanelAttempt] = useState(0);
	const [previewPending, setPreviewPending] = useState(true);
	const [inspectorPending, setInspectorPending] = useState(false);
	const panelPending = !panelErrors[activePanel] && (activePanel === "layout" && !SettingsPanel || activePanel === "structure" && !Navigator || activePanel === "settings" && selectedRow && (!Inspector || inspectorPending));
	const loadingStatus = busy ? message || labels.loading : panelPending ? labels.loading : previewPending ? labels.previewLoading : "";
	useEffect(() => {
		if (activePanel !== "layout" || SettingsPanel) return;
		let current = true;
		setPanelErrors((previous) => previous.layout ? {
			...previous,
			layout: false
		} : previous);
		__vitePreload(() => import("./builder-settings-panel-p8reW96c.js").then((module) => {
			if (current) setSettingsPanel(() => module.SettingsPanel);
		}), [], import.meta.url).catch(() => {
			if (current) setPanelErrors((previous) => ({
				...previous,
				layout: true
			}));
		});
		return () => {
			current = false;
		};
	}, [
		activePanel,
		SettingsPanel,
		panelAttempt
	]);
	useEffect(() => {
		if (activePanel !== "structure" || Navigator) return;
		let current = true;
		setPanelErrors((previous) => previous.structure ? {
			...previous,
			structure: false
		} : previous);
		__vitePreload(() => import("./builder-navigator-eDD9agv_.js").then((module) => {
			if (current) setNavigator(() => module.Navigator);
		}), [], import.meta.url).catch(() => {
			if (current) setPanelErrors((previous) => ({
				...previous,
				structure: true
			}));
		});
		return () => {
			current = false;
		};
	}, [
		activePanel,
		Navigator,
		panelAttempt
	]);
	useEffect(() => {
		if (activePanel !== "settings" || Inspector) return;
		let current = true;
		setPanelErrors((previous) => previous.settings ? {
			...previous,
			settings: false
		} : previous);
		__vitePreload(() => import("./builder-inspector-CfLsrWvz.js").then((module) => {
			if (current) setInspector(() => module.Inspector);
		}), [], import.meta.url).catch(() => {
			if (current) setPanelErrors((previous) => ({
				...previous,
				settings: true
			}));
		});
		return () => {
			current = false;
		};
	}, [
		activePanel,
		Inspector,
		panelAttempt
	]);
	const pending = useRef(false);
	async function run(operation, fields = {}, id = stored.id) {
		if (pending.current) return;
		pending.current = true;
		setBusy(true);
		setMessage([
			"save",
			"create",
			"rename"
		].includes(operation) ? labels.saving : labels.loading);
		setFailed(false);
		try {
			const result = await request(operation, id, fields);
			if (operation === "delete") {
				window.location.assign(config.libraryUrl);
				return;
			}
			setStored(result);
			if (!result.warning) setDraftTitle(result.layouts.find((entry) => entry.id === result.id)?.title || "");
			if (!["save", "create"].includes(operation)) {
				setSelected(null);
				setPanel("elements");
			}
			const url = new URL(window.location.href);
			url.searchParams.set("layout", result.id);
			url.searchParams.delete("new");
			url.searchParams.delete("copy");
			url.searchParams.delete("rename");
			window.history.replaceState(null, "", url);
			setHistory({
				past: [],
				present: result.data.layout,
				future: []
			});
			setFailed(!!result.warning);
			setMessage(result.warning || ([
				"save",
				"create",
				"rename"
			].includes(operation) ? labels.saved : ["default", "inherit"].includes(operation) ? labels.assigned : ""));
		} catch (error) {
			setMessage(error.message);
			setFailed(true);
		} finally {
			pending.current = false;
			setBusy(false);
		}
	}
	const serialized = JSON.stringify(layout);
	const dirty = serialized !== JSON.stringify(stored.data.layout) || (stored.creating ? !!draftTitle.trim() || layout.rows.length > 0 : draftTitle.trim() !== stored.layouts.find((entry) => entry.id === stored.id)?.title);
	const canSave = dirty && (!layout.enabled || layout.rows.some((row) => row.items.length)) && !!draftTitle.trim();
	useEffect(() => {
		const warn = (event) => {
			if (dirty) {
				event.preventDefault();
				event.returnValue = "";
			}
		};
		const submit = (event) => {
			event.preventDefault();
			if (canSave && !pending.current) {
				const command = builderSaveRequest(stored, layout, draftTitle);
				run(command.operation, command.fields, command.id);
			}
		};
		const form = document.getElementById("rdct-builder-form");
		window.addEventListener("beforeunload", warn);
		form.addEventListener("submit", submit);
		return () => {
			window.removeEventListener("beforeunload", warn);
			form.removeEventListener("submit", submit);
		};
	}, [
		dirty,
		serialized,
		stored,
		draftTitle
	]);
	function beginCreate(value) {
		setCopySource("");
		setStored({
			...stored,
			id: "default",
			creating: true,
			translation: null,
			data: {
				layout: value,
				revision: 0
			}
		});
		setHistory({
			past: [],
			present: value,
			future: []
		});
		setDraftTitle("");
		setSelected(null);
		setPanel("elements");
		setMessage("");
		const url = new URL(window.location.href);
		url.searchParams.delete("layout");
		url.searchParams.delete("copy");
		url.searchParams.set("new", "1");
		window.history.replaceState(null, "", url);
	}
	async function copyDraft(id) {
		if (pending.current || id === copySource || dirty && !confirm(labels.discardConfirm)) return;
		pending.current = true;
		setBusy(true);
		setFailed(false);
		setMessage(labels.loading);
		try {
			const value = id ? (await request("read", id)).data.layout : {
				version: 1,
				enabled: false,
				rows: []
			};
			setLayout(value);
			setCopySource(id);
			setSelected(null);
			setMessage("");
			const url = new URL(window.location.href);
			if (id) url.searchParams.set("copy", id);
			else url.searchParams.delete("copy");
			window.history.replaceState(null, "", url);
		} catch (error) {
			setMessage(error.message);
			setFailed(true);
		} finally {
			pending.current = false;
			setBusy(false);
		}
	}
	function changeRow(id, update) {
		setLayout((value) => {
			const next = updateContainer(value, id, update);
			return withinLimits(next) ? next : value;
		});
	}
	function moveRow$1(from, to) {
		setLayout((value) => {
			const next = moveRow(value, from, to);
			if (next !== value) focusTarget.current = value.rows[from].id;
			return next;
		});
	}
	function duplicateRow$1(row) {
		setLayout((value) => duplicateRow(value, row.id));
	}
	function moveItem$1(sourceId, itemId, targetId, targetIndex) {
		setLayout((value) => {
			const next = moveItem(value, sourceId, itemId, targetId, targetIndex);
			if (next !== value) focusTarget.current = itemId;
			return next;
		});
	}
	function drop(event, rowId, itemIndex = null) {
		event.preventDefault();
		event.stopPropagation();
		const data = drag.current;
		if (!data) return;
		if (data.type) addElement(data.type, rowId, itemIndex);
		else if (data.itemId) {
			const row = findContainer(layout, rowId);
			if (row) moveItem$1(data.rowId, data.itemId, rowId, dropIndex(layout, data, rowId, itemIndex ?? row.items.length));
			else if (!rowId && layout.rows.length < 8) {
				const added = {
					id: newId(),
					appearance: { rowLayout: "flex" },
					items: []
				};
				setLayout((value) => moveItem({
					...value,
					rows: [...value.rows, added]
				}, data.rowId, data.itemId, added.id, 0));
				select(added.id, data.itemId);
			}
		} else moveRow$1(layout.rows.findIndex((entry) => entry.id === data.rowId), layout.rows.findIndex((entry) => entry.id === rowId));
		drag.current = null;
		setOver("");
	}
	const pointerTarget = (event) => {
		const canvas = canvasDrop.current?.(event.clientX, event.clientY);
		if (canvas) return canvas;
		const target = document.elementFromPoint(event.clientX, event.clientY)?.closest("[data-builder-row]");
		return target ? {
			rowId: target.dataset.builderRow,
			index: target.dataset.builderIndex === void 0 ? null : Number(target.dataset.builderIndex),
			token: target.dataset.builderToken
		} : null;
	};
	const clearDrag = () => {
		drag.current = null;
		setOver("");
		setDragView(null);
		setDropTarget(null);
	};
	pointerCallbacks.current = {
		start: (data) => {
			drag.current = data;
		},
		move: (event, data, point) => {
			const target = pointerTarget(event);
			setOver(target?.token || "");
			setDropTarget(target);
			setDragView({
				...point,
				label: labels[data.type] || labels[findContainer(layout, data.rowId)?.items.find((item) => item.id === data.itemId)?.type] || labels.row
			});
		},
		end: (event) => {
			const target = pointerTarget(event);
			suppressClick.current = true;
			setTimeout(() => {
				suppressClick.current = false;
			}, 0);
			if (target) drop(event, target.rowId, target.index);
			clearDrag();
		},
		cancel: clearDrag
	};
	const dragProps = (data) => ({
		...pointerController.current?.bind(data),
		onClickCapture: (event) => {
			if (suppressClick.current) {
				event.preventDefault();
				event.stopPropagation();
			}
		}
	});
	const dragHandle = (data, label) => button(null, void 0, false, {
		...dragProps(data),
		"data-builder-focus": data.itemId || data.rowId,
		className: "rdct-bld-editor__handle",
		icon: "move",
		iconOnly: true,
		label,
		variant: "ghost",
		"aria-label": label,
		title: label
	});
	const dropProps = (rowId, itemIndex = null, token = rowId) => ({
		"data-builder-row": rowId ?? "",
		"data-builder-token": token,
		...itemIndex === null ? {} : { "data-builder-index": itemIndex }
	});
	function addRow(mode = "flex") {
		if (layout.rows.length >= 8) return;
		const row = {
			id: newId(),
			appearance: { rowLayout: mode === "grid" ? "grid" : "flex" },
			items: []
		};
		setLayout({
			...layout,
			rows: [...layout.rows, row]
		});
		setSelected({ rowId: row.id });
		setPanel("elements");
	}
	function addElement(type, targetRowId = selectedItem?.type === "container" ? selectedItem.id : selectedRow?.id, targetIndex = null) {
		if (!config.elementTypes.includes(type)) return;
		const target = findContainer(layout, targetRowId);
		if (target?.items.length >= 6 || !target && layout.rows.length >= 8) {
			setFailed(true);
			setMessage(labels.capacityHint);
			return;
		}
		const row = target || {
			id: newId(),
			items: []
		};
		const item = createItem(type, config);
		const items = [...row.items];
		items.splice(targetIndex ?? items.length, 0, item);
		const next = {
			...row,
			items
		};
		const updated = target ? updateContainer(layout, row.id, () => next) : {
			...layout,
			rows: [...layout.rows, next]
		};
		if (!withinLimits(updated)) {
			setFailed(true);
			setMessage(labels.capacityHint);
			return;
		}
		setLayout(updated);
		select(row.id, item.id);
	}
	const updateItem = (patch) => changeRow(selectedRow.id, (row) => ({
		...row,
		items: row.items.map((entry) => entry.id === selectedItem.id ? {
			...entry,
			...patch
		} : entry)
	}));
	const selectedRowIndex = layout.rows.findIndex((entry) => entry.id === selectedRow?.id);
	const selectedItemIndex = selectedRow?.items.findIndex((entry) => entry.id === selectedItem?.id) ?? -1;
	const itemActions = selectedItem ? [
		button(labels.up, () => moveItem$1(selectedRow.id, selectedItem.id, selectedRow.id, selectedItemIndex - 1), selectedItemIndex === 0, { key: "up" }),
		button(labels.down, () => moveItem$1(selectedRow.id, selectedItem.id, selectedRow.id, selectedItemIndex + 1), selectedItemIndex === selectedRow.items.length - 1, { key: "down" }),
		button(labels.previous, () => moveItem$1(selectedRow.id, selectedItem.id, layout.rows[selectedRowIndex - 1].id, 6), selectedRowIndex <= 0 || layout.rows[selectedRowIndex - 1]?.items.length >= 6, { key: "previous" }),
		button(labels.next, () => moveItem$1(selectedRow.id, selectedItem.id, layout.rows[selectedRowIndex + 1].id, 6), selectedRowIndex < 0 || selectedRowIndex === layout.rows.length - 1 || layout.rows[selectedRowIndex + 1]?.items.length >= 6, { key: "next" }),
		button(labels.moveFirst, () => moveItem$1(selectedRow.id, selectedItem.id, selectedRow.id, 0), selectedItemIndex === 0, {
			key: "first",
			label: labels.moveFirst,
			icon: "controls-skipback",
			iconOnly: true
		}),
		button(labels.duplicate, () => {
			const copy = cloneItem(selectedItem);
			changeRow(selectedRow.id, (row) => ({
				...row,
				items: [
					...row.items.slice(0, selectedItemIndex + 1),
					copy,
					...row.items.slice(selectedItemIndex + 1)
				]
			}));
			select(selectedRow.id, copy.id);
		}, selectedRow.items.length >= 6, {
			key: "duplicate",
			label: labels.duplicate,
			icon: "admin-page",
			iconOnly: true
		}),
		button(labels.remove, () => {
			changeRow(selectedRow.id, (row) => ({
				...row,
				items: row.items.filter((entry) => entry.id !== selectedItem.id)
			}));
			select(selectedRow.id);
		}, false, {
			key: "remove",
			label: labels.remove,
			variant: "danger",
			icon: "trash",
			iconOnly: true
		})
	] : [];
	const removeRow = (id) => {
		if (confirm(labels.removeConfirm)) setLayout((value) => ({
			...value,
			rows: value.rows.filter((row) => row.id !== id)
		}));
	};
	const rowActions = selectedRow ? [
		button(labels.up, () => moveRow$1(selectedRowIndex, selectedRowIndex - 1), selectedRowIndex === 0, { key: "up" }),
		button(labels.down, () => moveRow$1(selectedRowIndex, selectedRowIndex + 1), selectedRowIndex === layout.rows.length - 1, { key: "down" }),
		button(labels.duplicate, () => duplicateRow$1(selectedRow), layout.rows.length >= 8, {
			key: "duplicate",
			label: labels.duplicate,
			icon: "admin-page",
			iconOnly: true
		}),
		button(labels.remove, () => removeRow(selectedRow.id), false, {
			key: "remove",
			label: labels.remove,
			variant: "danger",
			icon: "trash",
			iconOnly: true
		})
	] : [];
	const selectionActions = selectedItem ? itemActions : rowActions;
	return h("div", {
		className: `rdct-bld-editor${focused ? " is-focused" : ""}${dragView ? " is-dragging" : ""}`,
		onKeyDown: (event) => {
			const shortcut = historyShortcut(event);
			if (shortcut && !pending.current) {
				event.preventDefault();
				setHistory(shortcut === "undo" ? undo : redo);
			}
			if (event.key === "Escape") {
				pointerController.current?.cancel();
				drag.current = null;
				setOver("");
				if (focused) setFocused(false);
			}
			if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === "s") {
				event.preventDefault();
				if (canSave && !pending.current) run("save", {
					layout,
					revision: stored.data.revision
				});
			}
		}
	}, dragView && h("div", {
		className: "rdct-bld-drag-ghost",
		style: {
			left: dragView.x + 16,
			top: dragView.y + 16
		},
		"aria-hidden": true
	}, h("span", { className: "dashicons dashicons-move" }), dragView.label), h("input", {
		type: "hidden",
		name: "layout",
		value: serialized
	}), h("input", {
		type: "hidden",
		name: "id",
		value: stored.id
	}), h("input", {
		type: "hidden",
		name: "revision",
		value: stored.data.revision
	}), !busy && message && h("div", {
		role: failed ? "alert" : "status",
		className: `rdct-bld-toast${failed ? " is-error" : ""}`
	}, h("span", null, message), h(ButtonControl, {
		icon: "no-alt",
		iconOnly: true,
		label: labels.dismiss,
		onClick: () => setMessage("")
	})), h("fieldset", {
		disabled: busy,
		className: "rdct-bld-editor__fieldset"
	}, h(BuilderToolbar, {
		labels,
		kind: config.kind,
		creating: !!stored.creating,
		title: draftTitle,
		onTitleChange: setDraftTitle,
		copySource,
		layouts: stored.layouts,
		onCopySource: copyDraft,
		busy,
		status: loadingStatus,
		width: previewWidth,
		onWidthChange: changePreviewWidth,
		dirty,
		canSave,
		canUndo: !!history.past.length,
		canRedo: !!history.future.length,
		onUndo: () => setHistory(undo),
		onRedo: () => setHistory(redo),
		focused,
		onFocus: () => setFocused(!focused),
		onSettings: () => setPanel("layout"),
		settingsOpen: activePanel === "layout"
	}), h("div", { className: "rdct-bld-workspace" }, h("aside", {
		className: "rdct-bld-sidebar rdct-adm-sidebar",
		"aria-label": labels.editorPanel
	}, activePanel === "layout" ? h("header", { className: "rdct-bld-panel-heading" }, h(ButtonControl, {
		icon: "arrow-left-alt2",
		iconOnly: true,
		label: labels.backToEditor,
		onClick: () => setPanel(selectedRow ? "settings" : "elements")
	}), h("h3", { id: "rdct-builder-layout-heading" }, labels[`${config.kind}Settings`])) : h(TabList, {
		id: "rdct-builder",
		className: "rdct-bld-sidebar__switch",
		value: activePanel,
		onChange: setPanel,
		tabs: [
			{
				id: "elements",
				label: labels.elements,
				icon: "screenoptions"
			},
			{
				id: "structure",
				label: labels.structure,
				icon: "editor-table"
			},
			{
				id: "settings",
				label: labels.editElement,
				icon: "edit"
			}
		]
	}), h("div", {
		className: "rdct-adm-scroll",
		role: activePanel === "layout" ? "region" : "tabpanel",
		id: `rdct-builder-${activePanel}-panel`,
		"aria-labelledby": activePanel === "layout" ? "rdct-builder-layout-heading" : `rdct-builder-${activePanel}-tab`
	}, panelErrors[activePanel] ? h("div", { role: "alert" }, h("p", null, labels.requestError), h(ButtonControl, { onClick: () => {
		setPanelErrors((previous) => ({
			...previous,
			[activePanel]: false
		}));
		setPanelAttempt((value) => value + 1);
	} }, labels.retryFields)) : activePanel === "layout" ? SettingsPanel ? h(SettingsPanel, {
		stored,
		layout,
		dirty,
		busy,
		run,
		change: setLayout,
		onNew: beginCreate,
		labels,
		kind: config.kind,
		device,
		onDeviceChange: selectDevice
	}) : null : activePanel === "structure" ? Navigator ? h(Navigator, {
		layout,
		selectedRow,
		selectedItem,
		labels,
		over,
		dragHandle,
		dragProps,
		dropProps,
		select,
		moveRow: moveRow$1,
		moveItem: moveItem$1,
		duplicateRow: duplicateRow$1,
		removeRow,
		addToRow: (rowId) => {
			setSelected({ rowId });
			setPanel("elements");
		}
	}) : null : activePanel === "elements" || !selectedRow ? h("div", { className: "rdct-pnl-content" }, h(InputControl, {
		type: "search",
		value: search,
		"aria-label": labels.searchElements,
		placeholder: labels.searchElements,
		onChange: setSearch
	}), !!layout.rows.length && h("div", { className: "rdct-bld-palette-target" }, h("span", null, labels.targetRow), h(ButtonControl, {
		variant: "ghost",
		icon: "editor-table",
		onClick: () => setPanel("structure")
	}, `${labels.row} ${Math.max(0, containers(layout).findIndex((row) => row.id === (selectedItem?.type === "container" ? selectedItem.id : selectedRow?.id))) + 1}`)), h("div", { className: "rdct-bld-palette" }, ...config.elementTypes.filter((type) => labels[type].toLocaleLowerCase().includes(search.trim().toLocaleLowerCase())).map((type) => button(h("span", null, h("span", {
		className: `dashicons dashicons-${{
			logo: "format-image",
			image: "format-image",
			menu: "menu",
			text: "editor-textcolor",
			button: "button",
			icon: "star-empty",
			contact: "phone",
			social: "share",
			"theme-toggle": "lightbulb",
			block: "screenoptions",
			search: "search",
			copyright: "editor-code"
		}[type] || "plus-alt2"}`,
		"aria-hidden": true
	}), h("span", null, labels[type])), () => {
		if (!suppressClick.current) addElement(type);
	}, false, {
		key: type,
		...dragProps({ type })
	}))), button(labels.addRow, addRow, layout.rows.length >= 8)) : selectedRow ? Inspector ? h(Inspector, {
		key: selectedItem?.id || selectedRow.id,
		row: selectedRow,
		item: selectedItem,
		labels,
		config,
		update: updateItem,
		device,
		onDeviceChange: selectDevice,
		onLoadingChange: setInspectorPending,
		updateRow: (appearance) => changeRow(selectedRow.id, (row) => ({
			...row,
			appearance
		})),
		actions: selectionActions
	}) : null : h("p", { className: "rdct-pnl-content" }, labels.selectElement))), h("div", { className: "rdct-bld-canvas" }, h(Preview, {
		layout,
		id: stored.id,
		direction: previewDirection,
		selected: {
			rowId: selectedRow?.id,
			itemId: selectedItem?.id
		},
		disabled: busy,
		dragProps,
		dropTarget,
		dropResolver: canvasDrop,
		over,
		actions: selectionActions.slice(selectedItem ? -3 : -2),
		viewportWidth: previewWidth,
		onLoadingChange: setPreviewPending,
		onResize: (rowId, itemId, appearance) => changeRow(rowId, (row) => ({
			...row,
			items: row.items.map((item) => item.id === itemId ? {
				...item,
				appearance
			} : item)
		})),
		onSelect: ({ id, item }) => {
			const row = containers(layout).find((entry) => item ? entry.items.some((element) => element.id === id) : entry.id === id);
			if (row) select(row.id, item ? id : null);
		}
	}), h("div", {
		className: `rdct-bld-canvas__add${over === "new-row" ? " is-over" : ""}`,
		...dropProps(null, 0, "new-row")
	}, h("span", null, labels.addRow), h("div", { className: "rdct-bld-editor__actions" }, button(labels.flexLayout, () => addRow("flex"), layout.rows.length >= 8, {
		icon: "arrow-right-alt",
		variant: "ghost"
	}), button(labels.gridLayout, () => addRow("grid"), layout.rows.length >= 8, {
		icon: "grid-view",
		variant: "ghost"
	})))))));
}
var root = document.getElementById("rdct-builder-root");
if (root && config) createRoot(root).render(h(App));
//#endregion
export { newId as a, responsiveDefaults as i, devices as n, findContainer as o, hiddenDevices as r, deviceKey as t };
