//#region \0rolldown/runtime.js
var __defProp = Object.defineProperty;
var __exportAll = (all, no_symbols) => {
	let target = {};
	for (var name in all) __defProp(target, name, {
		get: all[name],
		enumerable: true
	});
	if (!no_symbols) __defProp(target, Symbol.toStringTag, { value: "Module" });
	return target;
};
//#endregion
//#region radmanct-components/src/js/components/hover-border.js
var hover_border_exports = /* @__PURE__ */ __exportAll({
	normalizeBorder: () => normalizeBorder,
	observeBorders: () => observeBorders
});
var observers = /* @__PURE__ */ new WeakMap();
function normalizeBorder(input = {}) {
	const number = (value, fallback, min, max) => typeof value !== "boolean" && value !== "" && value != null && Number.isFinite(Number(value)) ? Math.min(max, Math.max(min, Number(value))) : fallback;
	const color = (value, fallback) => typeof value === "string" && /^#(?:[a-f0-9]{3}|[a-f0-9]{4}|[a-f0-9]{6}|[a-f0-9]{8})$/i.test(value) ? value : fallback;
	return {
		enabled: input.enabled === true || input.enabled === "yes",
		width: number(input.width, 2, .5, 12),
		duration: number(input.duration, 4, 1, 30),
		color_start: color(input.color_start, "#64e8c6"),
		color_end: color(input.color_end, "#8b5cf6"),
		direction: [
			"auto",
			"clockwise",
			"counterclockwise"
		].includes(input.direction) ? input.direction : "auto"
	};
}
function observeBorders(doc = document) {
	if (observers.has(doc)) return observers.get(doc);
	const entries = /* @__PURE__ */ new Map();
	const view = doc.defaultView;
	let active = true;
	let queued = false;
	const scan = () => {
		queued = false;
		if (!active) return;
		for (const [root, entry] of entries) if (!root.isConnected || root.getAttribute("data-rdct-border") !== entry.key) {
			entry.cleanup();
			entries.delete(root);
		}
		for (const root of doc.querySelectorAll("[data-rdct-border]")) {
			if (entries.has(root) || /^(IMG|INPUT|HR|BR|SVG|VIDEO|IFRAME|TEXTAREA|SELECT)$/.test(root.tagName)) continue;
			const key = root.getAttribute("data-rdct-border");
			if (!key || key.length > 2e3) continue;
			let options;
			try {
				options = normalizeBorder(JSON.parse(key));
			} catch {
				continue;
			}
			if (!options.enabled) continue;
			const ring = doc.createElement("span");
			ring.className = "rdct-bdr-ring";
			ring.setAttribute("aria-hidden", "true");
			ring.inert = true;
			if (view.CSS?.registerProperty) ring.classList.add("is-animated");
			const style = view.getComputedStyle(root);
			const positioned = style.position === "static";
			if (positioned) root.classList.add("rdct-bdr-positioned");
			for (const [name, value] of Object.entries({
				width: options.width + "px",
				duration: options.duration + "s",
				start: options.color_start,
				end: options.color_end,
				direction: options.direction === "counterclockwise" || options.direction === "auto" && style.direction === "rtl" ? "reverse" : "normal"
			})) ring.style.setProperty("--rdct-border-" + name, value);
			root.append(ring);
			entries.set(root, {
				key,
				cleanup: () => {
					ring.remove();
					if (positioned) root.classList.remove("rdct-bdr-positioned");
				}
			});
		}
	};
	const observer = new view.MutationObserver(() => {
		if (active && !queued) {
			queued = true;
			queueMicrotask(scan);
		}
	});
	observer.observe(doc.documentElement, {
		subtree: true,
		childList: true,
		attributes: true,
		attributeFilter: ["data-rdct-border"]
	});
	scan();
	const cleanup = () => {
		if (!active) return;
		active = false;
		observer.disconnect();
		for (const entry of entries.values()) entry.cleanup();
		entries.clear();
		observers.delete(doc);
	};
	observers.set(doc, cleanup);
	return cleanup;
}
//#endregion
export { observeBorders as n, hover_border_exports as t };
