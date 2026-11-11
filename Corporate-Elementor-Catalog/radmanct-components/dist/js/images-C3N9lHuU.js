import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { loadAnimation } from "./animation-C6xwqvoz.js";
//#region radmanct-components/src/js/components/curtain/images.js
function createImagePanelMotion(root, items, load = loadAnimation) {
	const view = root.ownerDocument.defaultView;
	const preference = view.matchMedia("(prefers-reduced-motion: no-preference)");
	const ratio = Math.max(1, Math.min(8, Number(root.dataset.ratio) || 3));
	const duration = Math.max(100, Math.min(1500, Number(root.dataset.duration) || 650)) / 1e3;
	let active = 0;
	let wide = false;
	let alive = true;
	let pending = false;
	let context;
	let setters = [];
	const reset = () => {
		context?.revert();
		context = void 0;
		setters = [];
	};
	const synchronize = async () => {
		if (!alive || !wide || !preference.matches) {
			reset();
			return;
		}
		if (context) {
			setters.forEach((set, index) => set(index === active ? ratio : 1));
			return;
		}
		if (pending) return;
		pending = true;
		try {
			const { gsap } = await load();
			if (!alive || !wide || !preference.matches || !root.isConnected) return;
			context = gsap.context(() => {}, root);
			context.add(() => {
				gsap.set(items, { flexGrow: (index) => index === active ? ratio : 1 });
				setters = items.map((item) => gsap.quickTo(item, "flexGrow", {
					duration,
					ease: "power2.inOut"
				}));
			});
		} catch (error) {
			reset();
			reportRuntimeError("image-panels-motion", error, view);
		} finally {
			pending = false;
		}
	};
	preference.addEventListener("change", synchronize);
	return {
		update(index, isWide) {
			active = index;
			wide = isWide;
			synchronize();
		},
		destroy() {
			if (!alive) return;
			alive = false;
			preference.removeEventListener("change", synchronize);
			reset();
		}
	};
}
//#endregion
export { createImagePanelMotion };
