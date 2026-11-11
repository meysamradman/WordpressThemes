import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/components/curtain.js
var instances = /* @__PURE__ */ new WeakMap();
function initializeCurtain(root) {
	if (instances.has(root)) return instances.get(root);
	const items = [...root.children].filter((item) => item.classList.contains("rdct-curtain__item"));
	const entries = items.map((item) => ({
		item,
		button: item.querySelector(".rdct-curtain__trigger"),
		panel: item.querySelector(".rdct-curtain__panel")
	}));
	if (!entries.length || entries.some(({ button, panel }) => !button || !panel)) return () => {};
	const view = root.ownerDocument.defaultView;
	const images = root.dataset.layout === "images";
	let alive = true;
	let motion;
	const controller = new AbortController();
	const { signal } = controller;
	const desktop = view.matchMedia("(min-width: 62rem)");
	let active = Math.min(entries.length - 1, Math.max(0, Math.trunc(Number(root.dataset.activeItem) || 1) - 1));
	let frame = 0;
	let hoverTimer = 0;
	let hoverIndex = -1;
	let lastPointer;
	const cancelHover = () => {
		view.clearTimeout(hoverTimer);
		hoverTimer = 0;
		hoverIndex = -1;
	};
	const measure = () => {
		frame = 0;
		root.style.removeProperty("--rdct-curtain-content-width");
		const style = view.getComputedStyle(root);
		const rail = Number.parseFloat(style.getPropertyValue?.("--rdct-curtain-rail")) || 88;
		const gap = Number.parseFloat(style.columnGap) || 0;
		const available = root.clientWidth ?? root.getBoundingClientRect().width;
		const width = available - (entries.length - 1) * (rail + gap);
		const wide = desktop.matches && available >= (images ? 600 : 992) && (images || width >= 320);
		root.classList.toggle("is-wide", wide);
		motion?.update(active, wide);
		if (images) return;
		if (!wide) return;
		if (width > 0) root.style.setProperty("--rdct-curtain-content-width", `${width}px`);
	};
	const schedule = () => {
		if (!frame) frame = view.requestAnimationFrame(measure);
	};
	const activate = (index) => {
		if (index < 0 || index >= entries.length) return;
		cancelHover();
		active = index;
		entries.forEach(({ item, button, panel }, i) => {
			const open = i === active;
			if (!images && !open && panel.contains(root.ownerDocument.activeElement)) button.focus();
			item.classList.toggle("is-active", open);
			button.setAttribute(images ? "aria-pressed" : "aria-expanded", String(open));
			if (!images) {
				panel.setAttribute("aria-hidden", String(!open));
				panel.inert = !open;
			}
		});
		schedule();
	};
	root.classList.add("is-enhanced");
	entries.forEach(({ item, button }, index) => {
		button.addEventListener("click", () => activate(index), { signal });
		if (root.dataset.trigger === "hover") button.addEventListener("focus", () => activate(index), { signal });
		button.addEventListener("keydown", (event) => {
			let next;
			const rtl = view.getComputedStyle(root).direction === "rtl";
			if (event.key === "Home") next = 0;
			if (event.key === "End") next = entries.length - 1;
			if (event.key === "ArrowDown" || event.key === (rtl ? "ArrowLeft" : "ArrowRight")) next = (index + 1) % entries.length;
			if (event.key === "ArrowUp" || event.key === (rtl ? "ArrowRight" : "ArrowLeft")) next = (index + entries.length - 1) % entries.length;
			if (next !== void 0) {
				event.preventDefault();
				activate(next);
				entries[next].button.focus();
			}
		}, { signal });
	});
	if (root.dataset.trigger === "hover") {
		root.addEventListener("pointermove", (event) => {
			if (event.pointerType !== "mouse" || !root.classList.contains("is-wide") || root.contains(root.ownerDocument.activeElement)) return;
			if (lastPointer && lastPointer.x === event.clientX && lastPointer.y === event.clientY) return;
			lastPointer = {
				x: event.clientX,
				y: event.clientY
			};
			const index = entries.findIndex(({ item }) => item.contains(event.target));
			if (index === hoverIndex) return;
			cancelHover();
			if (index < 0 || index === active) return;
			hoverIndex = index;
			hoverTimer = view.setTimeout(() => {
				cancelHover();
				if (root.classList.contains("is-wide") && !root.contains(root.ownerDocument.activeElement)) activate(index);
			}, 120);
		}, { signal });
		root.addEventListener("pointerleave", () => {
			cancelHover();
			lastPointer = null;
		}, { signal });
	}
	desktop.addEventListener("change", () => {
		cancelHover();
		schedule();
	}, { signal });
	const observer = typeof view.ResizeObserver === "function" ? new view.ResizeObserver(schedule) : null;
	if (observer) observer.observe(root);
	else view.addEventListener("resize", schedule, { signal });
	activate(active);
	if (images) __vitePreload(async () => {
		const { createImagePanelMotion } = await import("./images-C3N9lHuU.js");
		return { createImagePanelMotion };
	}, [], import.meta.url).then(({ createImagePanelMotion }) => {
		if (!alive || !root.isConnected) return;
		motion = createImagePanelMotion(root, items);
		motion.update(active, root.classList.contains("is-wide"));
	}).catch((error) => reportRuntimeError("image-panels", error, view));
	const cleanup = () => {
		if (!alive) return;
		alive = false;
		motion?.destroy();
		cancelHover();
		controller.abort();
		observer?.disconnect();
		if (frame) view.cancelAnimationFrame(frame);
		root.classList.remove("is-enhanced");
		root.classList.remove("is-wide");
		root.style.removeProperty("--rdct-curtain-content-width");
		entries.forEach(({ item, button, panel }) => {
			item.classList.remove("is-active");
			button.setAttribute(images ? "aria-pressed" : "aria-expanded", images ? "false" : "true");
			panel.removeAttribute("aria-hidden");
			panel.inert = false;
		});
		instances.delete(root);
	};
	instances.set(root, cleanup);
	return cleanup;
}
function initializeCurtains(scope = document) {
	const cleanups = [...scope.querySelectorAll("[data-rdct-curtain]")].map(initializeCurtain);
	return () => cleanups.forEach((cleanup) => cleanup());
}
//#endregion
export { initializeCurtain, initializeCurtains };
