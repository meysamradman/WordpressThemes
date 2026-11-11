//#region shared/frontend/lifecycle.js
function reportRuntimeError(feature, error, view = window) {
	if (view.rdctComponents?.debug === true || view.document?.getElementById("rdct-frontend-js-module")?.dataset.rdctDebug === "1") view.console?.error(`[Radman CT] ${feature}`, error);
}
function createPageLifecycle(view) {
	let active = true;
	let observer;
	const tasks = /* @__PURE__ */ new Set();
	const roots = /* @__PURE__ */ new Map();
	const report = (feature, error) => reportRuntimeError(feature, error, view);
	const mount = (load, initialize, feature) => {
		if (!active) return () => {};
		let alive = true;
		let cleanup;
		const release = () => {
			if (!alive) return;
			alive = false;
			tasks.delete(release);
			try {
				cleanup?.();
			} catch (error) {
				report(feature, error);
			}
		};
		tasks.add(release);
		release.ready = Promise.resolve().then(() => alive && active ? load() : void 0).then((module) => {
			if (!alive || !active) return;
			const mounted = initialize(module);
			if (alive && active) cleanup = mounted;
			else if (typeof mounted === "function") mounted();
		}).catch((error) => {
			if (alive && active) report(feature, error);
		});
		return release;
	};
	const discard = (root, key, entry) => {
		if (!entry.active) return;
		entry.active = false;
		const entries = roots.get(root);
		entries?.delete(key);
		if (!entries?.size) roots.delete(root);
		entry.release();
		if (!roots.size) {
			observer?.disconnect();
			observer = void 0;
		}
	};
	const mountRoot = (root, key, load, initialize) => {
		if (!active || !root.isConnected) return () => {};
		if (!roots.has(root)) roots.set(root, /* @__PURE__ */ new Map());
		const entries = roots.get(root);
		let entry = entries.get(key);
		if (!entry) {
			entry = {
				active: true,
				owners: 0
			};
			entries.set(key, entry);
			entry.release = mount(load, (module) => {
				if (!root.isConnected) {
					discard(root, key, entry);
					return;
				}
				return initialize(module, root);
			}, key);
			if (!observer && view.MutationObserver) {
				observer = new view.MutationObserver(() => {
					for (const [node, records] of roots) if (!node.isConnected) for (const [name, record] of records) discard(node, name, record);
				});
				observer.observe(view.document.documentElement, {
					childList: true,
					subtree: true
				});
			}
		}
		++entry.owners;
		let owned = true;
		return () => {
			if (!owned) return;
			owned = false;
			if (entry.active && --entry.owners === 0) discard(root, key, entry);
		};
	};
	const dispose = () => {
		if (!active) return;
		active = false;
		observer?.disconnect();
		observer = void 0;
		for (const entries of roots.values()) for (const entry of entries.values()) entry.active = false;
		roots.clear();
		for (const release of tasks) release();
		view.removeEventListener("pagehide", hide);
	};
	const hide = (event) => {
		if (!event.persisted) dispose();
	};
	view.addEventListener("pagehide", hide);
	return {
		mount,
		mountRoot,
		dispose
	};
}
//#endregion
export { reportRuntimeError as n, createPageLifecycle as t };
