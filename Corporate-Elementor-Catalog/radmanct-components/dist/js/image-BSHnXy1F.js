import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/components/image.js
var instances = /* @__PURE__ */ new WeakMap();
function initializeImage(root) {
	if (instances.has(root)) return instances.get(root);
	const view = root.ownerDocument.defaultView;
	let alive = true;
	let dialog;
	let opener;
	const cleanups = [];
	const mount = (load, method) => load().then((module) => {
		if (alive && root.isConnected) cleanups.push(module[method](root));
	}).catch((error) => reportRuntimeError("image", error));
	if (root.dataset.reveal || root.classList.contains("has-float") || root.classList.contains("has-shadow-motion")) Promise.all([__vitePreload(() => import("./visibility-CDvyYb6T.js"), [], import.meta.url), root.dataset.reveal ? __vitePreload(() => import("./reveal-D-KnWZab.js"), [], import.meta.url) : null]).then(([visibility, reveal]) => {
		if (alive && root.isConnected) cleanups.push(visibility.initializeImageVisibility(root, reveal?.createImageReveal(root)));
	}).catch((error) => reportRuntimeError("image", error));
	if (root.dataset.parallax === "true") mount(() => __vitePreload(() => import("./parallax-ADoqYYlV.js"), [], import.meta.url), "initializeImageParallax");
	if (Number(root.dataset.tilt) > 0) mount(() => __vitePreload(() => import("./tilt-Cs25bZX1.js"), [], import.meta.url), "initializeImageTilt");
	const click = async (e) => {
		const link = e.target.closest("[data-rdct-image-lightbox]");
		if (!link || !root.contains(link) || e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || link.target === "_blank" || link.hasAttribute("download") || !view.HTMLDialogElement?.prototype.showModal) return;
		e.preventDefault();
		try {
			const module = await __vitePreload(() => import("./lightbox-D0fGc_Cu.js"), [], import.meta.url);
			if (root.isConnected && instances.get(root) === cleanup) {
				opener = link;
				dialog = module.openImageLightbox(link);
			}
		} catch {
			if (root.isConnected && instances.get(root) === cleanup) view.location.assign(link.href);
		}
	};
	const lightbox = Boolean(root.querySelector("[data-rdct-image-lightbox]"));
	if (lightbox) root.addEventListener("click", click);
	const cleanup = () => {
		if (!alive) return;
		alive = false;
		cleanups.forEach((release) => release());
		if (lightbox) root.removeEventListener("click", click);
		if (dialog && dialog.opener === opener) dialog.close();
		if (instances.get(root) === cleanup) instances.delete(root);
	};
	instances.set(root, cleanup);
	return cleanup;
}
function initializeImages(scope = document) {
	const cleanups = (scope.matches?.("[data-rdct-image]") ? [scope] : [...scope.querySelectorAll("[data-rdct-image]")]).map(initializeImage);
	return () => cleanups.forEach((cleanup) => cleanup());
}
//#endregion
export { initializeImage, initializeImages };
