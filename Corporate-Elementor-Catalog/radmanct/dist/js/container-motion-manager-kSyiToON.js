import { t as __exportAll } from "./rolldown-runtime-D7D4PA-g.js";
import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct/src/js/container-motion-manager.js
var container_motion_manager_exports = /* @__PURE__ */ __exportAll({ observeContainerMotion: () => observeContainerMotion });
var observers = /* @__PURE__ */ new WeakMap();
function observeContainerMotion(scope = document, preview = false) {
	observers.get(scope)?.();
	preview || (preview = !!scope.defaultView?.rdctMotion);
	const instances = /* @__PURE__ */ new Map();
	const changed = /* @__PURE__ */ new Set();
	const replayRoots = /* @__PURE__ */ new Set();
	const paused = /* @__PURE__ */ new WeakMap();
	const query = matchMedia("(prefers-reduced-motion: no-preference)");
	const view = scope.defaultView;
	let current = true;
	let queued = false;
	let pending = null;
	let playing = false;
	const initialize = () => pending || (pending = __vitePreload(() => import("./container-motion-BJpkRbC1.js"), [], import.meta.url).catch((error) => {
		pending = null;
		reportRuntimeError("container-motion", error);
		return null;
	}));
	async function scan() {
		const play = playing;
		playing = false;
		const selected = new Set(replayRoots);
		replayRoots.clear();
		queued = false;
		if (!current) return;
		for (const [root, entry] of instances) if (!query.matches || entry.minimum && view && view.innerWidth < entry.minimum || !root.isConnected || changed.has(root) || root.getAttribute("data-rdct-motion") !== entry.key) {
			entry.cleanup?.();
			instances.delete(root);
		}
		changed.clear();
		if (!query.matches) return;
		for (const root of scope.querySelectorAll("[data-rdct-motion]")) {
			const key = root.getAttribute("data-rdct-motion");
			if (paused.get(root) === key) continue;
			paused.delete(root);
			if (instances.has(root) || !key || key.length > 12e3) continue;
			let options;
			try {
				options = JSON.parse(key);
			} catch {
				continue;
			}
			if (!options || typeof options !== "object" || Array.isArray(options) || !options.animate && !options.parallax && !options.reveal && !options.horizontal) continue;
			const horizontal = options.horizontal === true || options.horizontal === "yes";
			const width = Number(options.horizontal_min_width ?? 1024);
			const minimum = horizontal ? Math.max(320, Math.min(1920, Number.isFinite(width) ? width : 1024)) : 0;
			if (horizontal && (preview || view && view.innerWidth < minimum)) continue;
			const entry = {
				key,
				cleanup: null,
				minimum
			};
			instances.set(root, entry);
			const module = await initialize();
			if (!module || !current || !query.matches || !root.isConnected || instances.get(root) !== entry || root.getAttribute("data-rdct-motion") !== key) {
				if (instances.get(root) === entry) instances.delete(root);
				continue;
			}
			entry.cleanup = module.initializeContainerMotion(root, options, preview, play || selected.has(root));
		}
	}
	const queue = () => {
		if (!queued && current) {
			queued = true;
			queueMicrotask(scan);
		}
	};
	const observer = new MutationObserver((records) => {
		for (const record of records) {
			let root = (record.type === "childList" ? record.target : record.target.parentElement)?.closest?.("[data-rdct-motion]");
			while (root) {
				if (instances.has(root) && root.getAttribute("data-rdct-horizontal-owner") !== "true") changed.add(root);
				root = root.parentElement?.closest("[data-rdct-motion]");
			}
		}
		queue();
	});
	observer.observe(scope.documentElement || scope, {
		subtree: true,
		childList: true,
		attributes: true,
		attributeFilter: ["data-rdct-motion", "data-rdct-native-motion"]
	});
	query.addEventListener("change", queue);
	view?.addEventListener("resize", queue, { passive: true });
	const replay = (event) => {
		if (!current) return;
		if (event?.detail !== void 0 && event.detail !== null) {
			const root = event.detail.root;
			if (!root?.isConnected || ![...scope.querySelectorAll("[data-rdct-motion]")].includes(root)) return;
			paused.delete(root);
			replayRoots.add(root);
			instances.get(root)?.cleanup?.();
			instances.delete(root);
		} else {
			for (const root of scope.querySelectorAll("[data-rdct-motion]")) paused.delete(root);
			playing = true;
			for (const entry of instances.values()) entry.cleanup?.();
			instances.clear();
		}
		queue();
	};
	scope.addEventListener("rdct:motion:play", replay);
	const reset = (event) => {
		const root = event?.detail?.root;
		if (!preview || !current || !root?.isConnected || ![...scope.querySelectorAll("[data-rdct-motion]")].includes(root)) return;
		paused.set(root, root.getAttribute("data-rdct-motion"));
		replayRoots.delete(root);
		instances.get(root)?.cleanup?.();
		instances.delete(root);
	};
	if (preview) scope.addEventListener("rdct:motion:reset", reset);
	scan();
	const cleanup = () => {
		if (!current) return;
		current = false;
		observer.disconnect();
		query.removeEventListener("change", queue);
		scope.removeEventListener("rdct:motion:play", replay);
		scope.removeEventListener("rdct:motion:reset", reset);
		view?.removeEventListener("resize", queue);
		for (const entry of instances.values()) entry.cleanup?.();
		instances.clear();
		changed.clear();
		replayRoots.clear();
		if (observers.get(scope) === cleanup) observers.delete(scope);
	};
	observers.set(scope, cleanup);
	return cleanup;
}
//#endregion
export { observeContainerMotion as n, container_motion_manager_exports as t };
