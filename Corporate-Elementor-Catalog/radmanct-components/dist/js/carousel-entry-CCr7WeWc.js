import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/components/carousel/modules.js
async function loadCarouselModules(options) {
	const loads = [];
	if (options.autoplay) loads.push(__vitePreload(() => import("./autoplay-C9yIAFeC.js"), [], import.meta.url));
	if (options.arrows) loads.push(__vitePreload(() => import("./navigation-B_kxBWrx.js"), [], import.meta.url));
	if (options.pagination && options.pagination !== "none") loads.push(__vitePreload(() => import("./pagination-CDrcUOCr.js"), [], import.meta.url));
	return (await Promise.all(loads)).map((module) => module.default);
}
//#endregion
//#region radmanct-components/src/js/components/carousel-entry.js
var instances = /* @__PURE__ */ new WeakMap();
function initializeCarousel(root) {
	if (instances.has(root)) return instances.get(root);
	const marquee = root.dataset.mode === "marquee";
	let options = {};
	try {
		if (!marquee) options = JSON.parse(root.dataset.options);
	} catch {
		return () => {};
	}
	if (!options || typeof options !== "object" || Array.isArray(options)) return () => {};
	let active = true;
	let cleanup;
	const stack = root.dataset.mode === "stack";
	const fade = root.dataset.mode === "slider" && root.dataset.transition === "fade";
	const module = stack ? __vitePreload(() => import("./stack-BLVoHi--.js"), [], import.meta.url) : marquee ? __vitePreload(() => import("./marquee-B1ACBpxP.js"), [], import.meta.url) : fade ? __vitePreload(() => import("./fade-DOkcD0LK.js"), [], import.meta.url) : __vitePreload(() => import("./engine-COsTlRhc.js"), [], import.meta.url);
	Promise.all([module, marquee ? [] : loadCarouselModules(options)]).then(([module, features]) => {
		if (!active || !root.isConnected) return;
		cleanup = marquee ? module.initializeMarquee(root) : stack ? module.initializeCarousel(root, void 0, features) : fade ? module.initializeCarousel(root, features) : module.initializeCarousel(root, [], null, features);
	}).catch((error) => reportRuntimeError("carousel", error));
	const destroy = () => {
		if (!active) return;
		active = false;
		cleanup?.();
		instances.delete(root);
	};
	instances.set(root, destroy);
	return destroy;
}
function initializeCarousels(scope = document) {
	const cleanups = (scope.matches?.("[data-rdct-carousel]") ? [scope] : [...scope.querySelectorAll("[data-rdct-carousel]")]).map(initializeCarousel);
	return () => cleanups.forEach((cleanup) => cleanup());
}
//#endregion
export { initializeCarousel, initializeCarousels };
