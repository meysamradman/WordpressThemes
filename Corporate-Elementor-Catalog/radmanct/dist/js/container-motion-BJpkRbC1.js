const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["../css/horizontal-scroll-BR9_fQbW.css"])))=>i.map(i=>d[i]);
import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { t as gsapWithCSS } from "./gsap-DTQ6fzR8.js";
import { t as ScrollTrigger } from "./ScrollTrigger-DWXBKx_c.js";
import { a as revealClip, i as motionTargets, r as motionOptions } from "./container-motion-options-BVS1oqbN.js";
//#region radmanct/src/js/components/container-motion.js
var instances = /* @__PURE__ */ new WeakMap();
function initializeContainerMotion(root, raw, preview = false, play = false) {
	instances.get(root)?.();
	if (!root.isConnected || root.hasAttribute("data-rdct-native-motion")) return () => {};
	const options = motionOptions(raw);
	gsapWithCSS.registerPlugin(ScrollTrigger);
	const media = gsapWithCSS.matchMedia();
	media.add("(prefers-reduced-motion: no-preference)", () => {
		let focus;
		let horizontalCleanup;
		let alive = true;
		if (options.horizontal && !preview) __vitePreload(async () => {
			const { initializeHorizontalScroll } = await import("./horizontal-scroll-CAS8f2bG.js");
			return { initializeHorizontalScroll };
		}, __vite__mapDeps([0]), import.meta.url).then(({ initializeHorizontalScroll }) => {
			if (alive && root.isConnected) horizontalCleanup = initializeHorizontalScroll(root, options, gsapWithCSS, ScrollTrigger);
		}).catch((error) => reportRuntimeError("horizontal-scroll", error));
		if (options.reveal && !options.horizontal && (!preview || play) && !root.matches(":focus-within")) {
			const style = root.ownerDocument.defaultView.getComputedStyle(root);
			const original = style.clipPath;
			const reveal = gsapWithCSS.fromTo(root, { clipPath: revealClip(options.reveal_direction, style.direction === "rtl") }, {
				clipPath: original === "none" ? "inset(0 0% 0 0%)" : original,
				ease: "none",
				duration: preview ? 1.2 : 1,
				...preview ? {} : { scrollTrigger: {
					trigger: root,
					start: `top ${options.reveal_start}%`,
					end: `top ${options.reveal_end}%`,
					scrub: options.reveal_scrub,
					invalidateOnRefresh: true
				} }
			});
			focus = () => {
				reveal.scrollTrigger?.kill();
				reveal.kill();
				gsapWithCSS.set(root, { clipPath: original });
			};
			root.addEventListener("focusin", focus);
		}
		if (options.animate && !options.horizontal && !(options.parallax && options.targets === "self")) {
			const targets = motionTargets(root, options);
			if (targets.length) gsapWithCSS.fromTo(targets, options.from, {
				...options.to,
				duration: options.duration,
				delay: options.delay,
				ease: options.ease,
				stagger: {
					each: options.stagger,
					from: options.order
				},
				...preview ? {} : { scrollTrigger: {
					trigger: root,
					start: "top 85%",
					once: true
				} }
			});
		}
		if (options.parallax && !options.horizontal) gsapWithCSS.fromTo(root, options.parallax_from, {
			...options.parallax_to,
			ease: options.parallax_ease,
			...preview && play ? { duration: options.duration } : { scrollTrigger: {
				trigger: root,
				start: options.parallax_start,
				end: options.parallax_end,
				scrub: options.scrub,
				invalidateOnRefresh: true
			} }
		});
		return () => {
			alive = false;
			horizontalCleanup?.();
			if (focus) root.removeEventListener("focusin", focus);
		};
	});
	let active = true;
	const cleanup = () => {
		if (!active) return;
		active = false;
		media.revert();
		if (instances.get(root) === cleanup) instances.delete(root);
	};
	instances.set(root, cleanup);
	return cleanup;
}
//#endregion
export { initializeContainerMotion };
