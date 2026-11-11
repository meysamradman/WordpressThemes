import { t as gsapWithCSS } from "./gsap-DTQ6fzR8.js";
//#region radmanct/src/js/navigation/motion/timeline.js
var ns = "http://www.w3.org/2000/svg";
function svgNode(doc, tag, attributes = {}) {
	const node = doc.createElementNS(ns, tag);
	for (const [key, value] of Object.entries(attributes)) node.setAttribute(key, value);
	return node;
}
function createPanelTimeline(dialog, vendor = gsapWithCSS) {
	const reduced = dialog.ownerDocument.defaultView.matchMedia("(prefers-reduced-motion: reduce)");
	let task;
	let context;
	let timeline;
	let destroyed = false;
	let locked = [];
	const overflow = dialog.style.overflow;
	const contentSelector = ":scope > .rdct-nav, :scope > [data-rdct-panel-body], :scope > .rdct-menu-panel-header, :scope > .rdct-menu-panel-content";
	const unlock = () => {
		for (const [node, inert] of locked) node.inert = inert;
		locked = [];
	};
	const reset = () => {
		context?.revert();
		context = null;
		timeline = null;
		unlock();
		dialog.style.overflow = overflow;
	};
	const settle = (completed) => {
		if (!task) return;
		const current = task;
		task = null;
		reset();
		if (completed) current.draw(1);
		current.resolve(completed);
	};
	const skip = () => {
		if (reduced.matches) settle(true);
	};
	reduced.addEventListener("change", skip);
	return {
		get reduced() {
			return reduced.matches;
		},
		get itemSpan() {
			return Math.min(12, Math.max(0, dialog.querySelectorAll(":scope > .rdct-nav > .rdct-menu > li, :scope > [data-rdct-panel-body], :scope > .rdct-menu-panel-header, :scope > .rdct-menu-panel-content").length - 1));
		},
		run(duration, draw) {
			settle(false);
			reset();
			if (destroyed) return Promise.resolve(false);
			if (reduced.matches) {
				draw(1);
				return Promise.resolve(true);
			}
			dialog.style.overflow = "hidden";
			locked = [...dialog.querySelectorAll(contentSelector)].map((node) => [node, node.inert]);
			locked.forEach(([node]) => {
				node.inert = true;
			});
			return new Promise((resolve) => {
				const state = { progress: 0 };
				task = {
					draw,
					resolve
				};
				draw(0);
				context = vendor.context(() => {
					timeline = vendor.timeline({
						paused: true,
						onComplete: () => settle(true)
					});
					timeline.to(state, {
						progress: 1,
						duration: duration / 1e3,
						ease: "none",
						onUpdate: () => {
							if (task?.draw === draw) draw(state.progress);
						}
					}, 0);
				}, dialog);
				timeline.play();
			});
		},
		items(open, { delay = 0, duration = 600, stagger = 50, distance = 60 } = {}) {
			if (destroyed || reduced.matches || !timeline || !context) return;
			const items = [...dialog.querySelectorAll(":scope > .rdct-nav > .rdct-menu > li")].slice(0, 50);
			const blocks = [...dialog.querySelectorAll(":scope > [data-rdct-panel-body], :scope > .rdct-menu-panel-header, :scope > .rdct-menu-panel-content")];
			context.add(() => {
				for (const [index, item] of [...items, ...blocks].entries()) {
					const order = Math.max(0, Math.min(open ? index : items.length - 1 - index, 12));
					timeline.fromTo(item, {
						opacity: open ? 0 : 1,
						y: open ? distance : 0
					}, {
						opacity: open ? 1 : 0,
						y: open ? 0 : distance,
						duration: duration / 1e3,
						ease: open ? "power4.out" : "power2.in"
					}, (delay + order * stagger) / 1e3);
				}
			});
		},
		cancel() {
			settle(false);
			reset();
		},
		destroy() {
			destroyed = true;
			settle(false);
			reset();
			reduced.removeEventListener("change", skip);
		}
	};
}
var easeOut = gsapWithCSS.parseEase("power4.out");
var easeIn = gsapWithCSS.parseEase("power4.in");
var curveIn = gsapWithCSS.parseEase("power2.in");
var curveOut = gsapWithCSS.parseEase("power2.out");
var mix = (start, end, progress) => start + (end - start) * progress;
//#endregion
export { easeOut as a, easeIn as i, curveIn as n, mix as o, curveOut as r, svgNode as s, createPanelTimeline as t };
