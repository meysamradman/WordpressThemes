import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/components/particles.js
var hosts = /* @__PURE__ */ new WeakMap();
var instances = /* @__PURE__ */ new WeakMap();
function useHost(host) {
	let state = hosts.get(host);
	if (!state) {
		state = {
			count: 0,
			styled: !host.classList.contains("rdct-pt-host"),
			positioned: getComputedStyle(host).position === "static" && !host.classList.contains("rdct-pt-host--static")
		};
		if (state.styled) host.classList.add("rdct-pt-host");
		if (state.positioned) host.classList.add("rdct-pt-host--static");
		hosts.set(host, state);
	}
	state.count++;
	return () => {
		if (--state.count) return;
		if (state.styled) host.classList.remove("rdct-pt-host");
		if (state.positioned) host.classList.remove("rdct-pt-host--static");
		hosts.delete(host);
	};
}
function initializeParticles(root, load = () => __vitePreload(() => import("./particles-BE7xDW6f.js"), [], import.meta.url)) {
	let instance = instances.get(root);
	if (!instance) {
		instance = {
			count: 0,
			cleanup: mountParticles(root, load)
		};
		instances.set(root, instance);
	}
	instance.count++;
	let released = false;
	return () => {
		if (released) return;
		released = true;
		if (--instance.count) return;
		instances.delete(root);
		instance.cleanup();
	};
}
function mountParticles(root, load) {
	const layer = root.querySelector(".rdct-pt__canvas");
	if (!layer) return () => {};
	let config;
	try {
		config = JSON.parse(root.dataset.options);
	} catch {
		return () => {};
	}
	const background = root.dataset.background === "1";
	const widget = root.closest(".elementor-widget, [data-element_type=\"widget\"], [data-rdct-atomic-particles]");
	const host = (widget?.parentElement || root.parentElement)?.closest(".e-con, .e-container, .elementor-column, .elementor-section, [data-rdct-particles-host], section") || widget?.parentElement || root.parentElement || root;
	const hoverOnly = root.dataset.hover === "1";
	const releaseHost = background || hoverOnly ? useHost(host) : () => {};
	if (background || hoverOnly) host.appendChild(layer);
	if (background) layer.classList.add("rdct-pt__canvas--background");
	if (hoverOnly) layer.classList.add("rdct-pt__canvas--hover");
	const placeLayer = () => {
		if (background || !hoverOnly) return;
		const rect = root.getBoundingClientRect(), bounds = host.getBoundingClientRect();
		Object.assign(layer.style, {
			top: `${rect.top - bounds.top + host.scrollTop}px`,
			left: `${rect.left - bounds.left + host.scrollLeft}px`,
			width: `${rect.width}px`,
			height: `${rect.height}px`,
			right: "auto",
			bottom: "auto"
		});
	};
	const media = matchMedia("(prefers-reduced-motion: reduce)");
	let alive = true, visible = !globalThis.IntersectionObserver, hovered = host.matches(":hover") || host.matches(":focus-within");
	let engine, pending = false, factory;
	const canRun = () => alive && root.isConnected && visible && !document.hidden && !media.matches && (!hoverOnly || hovered);
	const sync = () => {
		if (!canRun()) {
			engine?.destroy();
			engine = null;
			return;
		}
		if (engine || pending) return;
		if (factory) {
			placeLayer();
			engine = factory(layer, config, background || hoverOnly ? host : root);
			return;
		}
		pending = true;
		load().then((module) => {
			factory = module.createParticleEngine;
			pending = false;
			if (canRun()) sync();
		}).catch((error) => {
			pending = false;
			reportRuntimeError("particles", error);
		});
	};
	const observer = globalThis.IntersectionObserver ? new IntersectionObserver((entries) => {
		visible = entries[0].isIntersecting;
		sync();
	}, { rootMargin: "100px" }) : null;
	observer?.observe(background ? host : root);
	const resize = config.interactivity?.events?.resize && globalThis.ResizeObserver ? new ResizeObserver(() => {
		placeLayer();
		engine?.resize();
	}) : null;
	resize?.observe(background ? host : root);
	const hover = () => queueMicrotask(() => {
		if (alive) {
			hovered = host.matches(":hover") || host.matches(":focus-within");
			sync();
		}
	});
	const hoverEvents = [
		"mouseenter",
		"mouseleave",
		"focusin",
		"focusout"
	];
	if (hoverOnly) for (const event of hoverEvents) host.addEventListener(event, hover);
	document.addEventListener("visibilitychange", sync);
	media.addEventListener("change", sync);
	sync();
	return () => {
		alive = false;
		observer?.disconnect();
		resize?.disconnect();
		engine?.destroy();
		engine = null;
		document.removeEventListener("visibilitychange", sync);
		media.removeEventListener("change", sync);
		if (hoverOnly) for (const event of hoverEvents) host.removeEventListener(event, hover);
		if (background || hoverOnly) {
			layer.classList.remove("rdct-pt__canvas--background", "rdct-pt__canvas--hover");
			layer.removeAttribute("style");
			root.appendChild(layer);
		}
		releaseHost();
	};
}
function initializeParticleGroups(scope) {
	const cleanups = [...scope.querySelectorAll("[data-rdct-particles]")].map((root) => initializeParticles(root));
	return () => cleanups.forEach((cleanup) => cleanup());
}
//#endregion
export { initializeParticleGroups, initializeParticles };
