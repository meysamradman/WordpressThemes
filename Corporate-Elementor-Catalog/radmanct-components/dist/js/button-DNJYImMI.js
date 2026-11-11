import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { loadGsap } from "./animation-C6xwqvoz.js";
//#region radmanct-components/src/js/components/button.js
var instances = /* @__PURE__ */ new WeakMap();
function initializeButton(root) {
	if (instances.has(root)) return instances.get(root);
	const media = root.dataset.textEffect?.startsWith("liquid-") || root.dataset.magnetic === "true" || root.dataset.iconMagnetic === "true" ? root.ownerDocument.defaultView.matchMedia("(hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)") : null;
	let alive = true;
	let generation = 0;
	let releases = [];
	let actionRelease;
	if (root.dataset.action && root.dataset.action !== "link") __vitePreload(() => import("./actions-BMwuD5JM.js").then((module) => {
		if (alive && root.isConnected) actionRelease = module.initializeActions(root);
	}), [], import.meta.url).catch((error) => reportRuntimeError("button-actions", error));
	const stop = () => {
		generation++;
		releases.forEach((release) => release());
		releases = [];
	};
	const mount = () => {
		stop();
		if (!media?.matches) return;
		const current = generation;
		const engine = loadGsap();
		const load = (promise, method) => Promise.all([engine, promise]).then(([vendor, module]) => {
			if (alive && root.isConnected && current === generation) releases.push(module[method](root, vendor));
		}).catch((error) => reportRuntimeError("button", error));
		if (root.dataset.textEffect?.startsWith("liquid-")) load(__vitePreload(() => import("./liquid-BWVKQusO.js"), [], import.meta.url), "initializeLiquid");
		if (root.dataset.magnetic === "true" || root.dataset.iconMagnetic === "true") load(__vitePreload(() => import("./magnetic-BB9GbQLs.js"), [], import.meta.url), "initializeMagnetic");
	};
	const cleanup = () => {
		alive = false;
		stop();
		actionRelease?.();
		media?.removeEventListener("change", mount);
		instances.delete(root);
	};
	instances.set(root, cleanup);
	media?.addEventListener("change", mount);
	mount();
	return cleanup;
}
function initializeButtons(scope) {
	const releases = [...scope.querySelectorAll("[data-rdct-button]")].map(initializeButton);
	return () => releases.forEach((release) => release());
}
//#endregion
export { initializeButton, initializeButtons };
