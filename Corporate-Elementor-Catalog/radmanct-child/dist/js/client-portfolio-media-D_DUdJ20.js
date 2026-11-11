import { n as reportRuntimeError, t as createPageLifecycle } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region shared/frontend/media-parallax.js
function initializeMediaParallax(root, { frame, target, amount = 12, minWidth = 768, direction = "up" }, load) {
	const view = root.ownerDocument.defaultView;
	if (!frame || !target || view.elementorFrontend?.isEditMode?.()) return () => {};
	const bounded = (value, fallback, min, max) => Number.isFinite(Number(value)) ? Math.min(max, Math.max(min, Number(value))) : fallback;
	amount = bounded(amount, 12, 1, 30);
	const width = bounded(minWidth, 768, 0, 1920);
	const media = view.matchMedia(`(min-width: ${width}px) and (prefers-reduced-motion: no-preference)`);
	let active = true;
	let pending = false;
	let context;
	let trigger;
	let refreshFrame = 0;
	const reset = () => {
		context?.revert();
		context = null;
		trigger = null;
	};
	const update = async () => {
		if (!active) return;
		if (!media.matches) {
			reset();
			return;
		}
		if (context || pending) return;
		pending = true;
		try {
			const { gsap, ScrollTrigger } = await load();
			if (!active || !root.isConnected || !media.matches) return;
			gsap.registerPlugin(ScrollTrigger);
			const from = direction === "down" ? -amount : amount;
			const scale = 1 + amount * .02 + .02;
			context = gsap.context(() => {}, root);
			context.add(() => {
				trigger = gsap.fromTo(target, {
					yPercent: from,
					scale
				}, {
					yPercent: -from,
					scale,
					ease: "none",
					scrollTrigger: {
						trigger: frame,
						start: "top bottom",
						end: "bottom top",
						scrub: .5,
						invalidateOnRefresh: true
					}
				}).scrollTrigger;
			});
		} catch (error) {
			reset();
			reportRuntimeError("image-parallax", error, view);
		} finally {
			pending = false;
		}
	};
	const refresh = () => {
		if (!active || !context || refreshFrame) return;
		refreshFrame = view.requestAnimationFrame(() => {
			refreshFrame = 0;
			if (active && media.matches) trigger?.refresh();
		});
	};
	const observer = view.ResizeObserver ? new view.ResizeObserver(refresh) : null;
	observer?.observe(frame);
	media.addEventListener("change", update);
	view.addEventListener("resize", refresh);
	root.addEventListener("load", refresh, true);
	update();
	return () => {
		if (!active) return;
		active = false;
		if (refreshFrame) view.cancelAnimationFrame(refreshFrame);
		observer?.disconnect();
		media.removeEventListener("change", update);
		view.removeEventListener("resize", refresh);
		root.removeEventListener("load", refresh, true);
		reset();
	};
}
//#endregion
//#region radmanct-child/src/js/portfolio/media.js
function initializePortfolioMedia(archive, load) {
	const view = archive.ownerDocument.defaultView;
	const page = createPageLifecycle(view);
	const mounted = /* @__PURE__ */ new Map();
	let pending;
	const loadVendor = load || (() => {
		if (!pending) {
			const url = new URL(archive.dataset.animationSrc, view.location.href);
			if (url.origin !== view.location.origin || !/\/dist\/js\/animation-vendor-[^/]+\.min\.js$/.test(url.pathname)) return Promise.reject(/* @__PURE__ */ new Error("Invalid local animation source"));
			pending = __vitePreload(() => import(
				/* @vite-ignore */
				url.href
), [], import.meta.url).catch((error) => {
				pending = void 0;
				throw error;
			});
		}
		return pending;
	});
	const sync = () => {
		for (const [node, release] of mounted) if (!node.isConnected) {
			release();
			mounted.delete(node);
		}
		for (const node of archive.querySelectorAll("[data-rdct-project-motion]")) {
			if (mounted.has(node)) continue;
			mounted.set(node, page.mountRoot(node, "portfolio-media", () => ({ initializeMediaParallax }), (module, frame) => module.initializeMediaParallax(frame, {
				frame,
				target: frame.querySelector(".rdct-client-project__parallax"),
				amount: 6,
				minWidth: 768
			}, loadVendor)));
		}
	};
	archive.addEventListener("rdct:archive-updated", sync);
	sync();
	return () => {
		archive.removeEventListener("rdct:archive-updated", sync);
		page.dispose();
		mounted.clear();
	};
}
if (typeof document !== "undefined") {
	const page = createPageLifecycle(window);
	for (const archive of document.querySelectorAll(".rdct-client-portfolio-archive[data-animation-src]")) page.mountRoot(archive, "portfolio-media-manager", () => ({}), () => initializePortfolioMedia(archive));
}
//#endregion
export { initializePortfolioMedia };
