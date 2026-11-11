import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/components/text.js
var instances = /* @__PURE__ */ new WeakMap();
function initializeText(root) {
	if (instances.has(root)) return instances.get(root);
	const mode = root.dataset.rdctTextReveal;
	let active = true;
	let cleanup = () => {};
	let headerCleanup;
	if (Object.hasOwn(root.dataset, "rdctTextHeader")) __vitePreload(async () => {
		const { initializeHeaderText } = await import("./header-state-D4GFfZU1.js");
		return { initializeHeaderText };
	}, [], import.meta.url).then(({ initializeHeaderText }) => {
		if (active) headerCleanup = initializeHeaderText(root);
	}).catch((error) => reportRuntimeError("text-header", error));
	if (mode === "marquee") __vitePreload(async () => {
		const { initializeMarquee } = await import("./marquee-B1ACBpxP.js");
		return { initializeMarquee };
	}, [], import.meta.url).then(({ initializeMarquee }) => {
		if (active) cleanup = initializeMarquee(root);
	}).catch((error) => reportRuntimeError("text", error));
	else if (mode === "scroll" || mode === "fill") __vitePreload(async () => {
		const { loadTextReveal } = await import("./text-reveal-entry-BwiRBWHI.js");
		return { loadTextReveal };
	}, [], import.meta.url).then(({ loadTextReveal }) => active ? loadTextReveal(root) : null).then((module) => {
		if (active && module) cleanup = module.initializeTextReveal(root);
	}).catch((error) => reportRuntimeError("text", error));
	else if (mode === "rotate") Promise.all([__vitePreload(() => import("./rotate-CEzIKJHj.js"), [], import.meta.url), root.ownerDocument.defaultView.matchMedia("(prefers-reduced-motion: reduce)").matches ? null : __vitePreload(async () => {
		const { loadGsap } = await import("./animation-C6xwqvoz.js");
		return { loadGsap };
	}, [], import.meta.url).then(({ loadGsap }) => loadGsap())]).then(([{ initializeRotator }, vendor]) => {
		if (active) cleanup = initializeRotator(root, vendor ?? {});
	}).catch((error) => reportRuntimeError("text", error));
	const destroy = () => {
		if (!active) return;
		active = false;
		cleanup();
		headerCleanup?.();
		instances.delete(root);
	};
	instances.set(root, destroy);
	return destroy;
}
function initializeTexts(scope = document) {
	const cleanups = (scope.matches?.("[data-rdct-text-reveal]") ? [scope] : [...scope.querySelectorAll("[data-rdct-text-reveal]")]).map(initializeText);
	return () => cleanups.forEach((cleanup) => cleanup());
}
//#endregion
export { initializeText, initializeTexts };
