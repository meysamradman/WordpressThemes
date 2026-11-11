import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { loadAnimation } from "./animation-C6xwqvoz.js";
//#region radmanct-components/src/js/components/sticky-sections.js
var instances = /* @__PURE__ */ new WeakMap();
var modes = {
	stack: () => __vitePreload(() => import("./stack-CvKKxAmz.js"), [], import.meta.url),
	zoom: () => __vitePreload(() => import("./stack-CvKKxAmz.js"), [], import.meta.url),
	overlap: () => __vitePreload(() => import("./panels-CAUyCtwL.js"), [], import.meta.url),
	services: (root) => root.dataset.transition === "slide-scale" ? __vitePreload(() => import("./slides-TzW3cbCF.js"), [], import.meta.url) : __vitePreload(() => import("./panels-CAUyCtwL.js"), [], import.meta.url),
	split: () => __vitePreload(() => import("./split-CJCdeDx2.js"), [], import.meta.url)
};
function initializeStickySections(root) {
	if (!root || !modes[root.dataset.mode] || root.ownerDocument.defaultView.elementorFrontend?.isEditMode?.()) return () => {};
	if (instances.has(root)) return instances.get(root);
	let alive = true;
	let cleanup;
	let pending = false;
	const width = Number.isFinite(Number(root.dataset.minWidth)) ? Math.max(0, Math.min(1920, Number(root.dataset.minWidth))) : 992;
	const media = root.ownerDocument.defaultView.matchMedia(`(min-width: ${width}px) and (prefers-reduced-motion: no-preference)`);
	const start = () => {
		if (!alive || pending || cleanup || !media.matches) return;
		pending = true;
		Promise.all([modes[root.dataset.mode](root), loadAnimation()]).then(([effect, animation]) => {
			if (alive && root.isConnected && media.matches) cleanup = effect.mount(root, animation);
		}).catch((error) => reportRuntimeError("sticky-sections", error)).finally(() => {
			pending = false;
		});
	};
	media.addEventListener("change", start);
	start();
	const release = () => {
		if (!alive) return;
		alive = false;
		media.removeEventListener("change", start);
		cleanup?.();
		instances.delete(root);
	};
	instances.set(root, release);
	return release;
}
function initializeStickySectionGroups(scope = document) {
	const cleanups = (scope.matches?.("[data-rdct-sticky-sections]") ? [scope] : [...scope.querySelectorAll("[data-rdct-sticky-sections]")]).map(initializeStickySections);
	return () => cleanups.forEach((cleanup) => cleanup());
}
//#endregion
export { initializeStickySectionGroups, initializeStickySections };
