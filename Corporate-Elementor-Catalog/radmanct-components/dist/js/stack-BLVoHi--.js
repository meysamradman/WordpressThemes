import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { t as responsiveBreakpoints } from "./responsive-ByPCCZxR.js";
import { initializeCarousel as initializeCarousel$1 } from "./engine-COsTlRhc.js";
//#region radmanct-components/src/js/components/carousel/stack.js
function initializeCarousel(root, load = () => __vitePreload(() => import("./stack-effect-B-s96hyN.js"), [], import.meta.url), featureModules = []) {
	let options;
	try {
		options = JSON.parse(root.dataset.options);
	} catch {
		return () => {};
	}
	const view = root.ownerDocument.defaultView;
	const { tablet } = responsiveBreakpoints(options);
	const media = view.matchMedia(`(min-width: ${tablet}px) and (prefers-reduced-motion: no-preference)`);
	let cleanup, state, effect, loading, generation = 0, alive = true;
	const mount = () => {
		if (!alive) return;
		const current = ++generation;
		if (cleanup) {
			state = cleanup.snapshot?.();
			cleanup();
			cleanup = null;
		}
		if (!media.matches) {
			cleanup = initializeCarousel$1(root, [], state, featureModules);
			return;
		}
		if (effect) {
			cleanup = initializeCarousel$1(root, [effect], state, featureModules);
			return;
		}
		loading || (loading = load());
		loading.then((module) => {
			effect = module.EffectCreative;
			if (alive && root.isConnected !== false && generation === current && media.matches) cleanup = initializeCarousel$1(root, [effect], state, featureModules);
		}).catch((error) => {
			loading = null;
			if (!alive || root.isConnected === false || generation !== current) return;
			reportRuntimeError("carousel-stack", error);
			cleanup = initializeCarousel$1(root, [], state, featureModules);
		});
	};
	media.addEventListener("change", mount);
	mount();
	return () => {
		if (!alive) return;
		alive = false;
		media.removeEventListener("change", mount);
		cleanup?.();
	};
}
//#endregion
export { initializeCarousel };
