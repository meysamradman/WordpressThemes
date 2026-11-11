import { t as createPageLifecycle } from "./lifecycle-CHQMXjJa.js";
//#region radmanct-child/src/js/content/sidebar.js
function observeSidebarOffset(root) {
	const doc = root.ownerDocument, view = doc.defaultView;
	const header = doc.querySelector(".rdct-bld--header");
	const bar = doc.getElementById("wpadminbar");
	const desktop = view.matchMedia("(min-width:64.01rem)");
	const natural = root.hasAttribute("data-rdct-natural-sidebar");
	let frame = 0, previous = "", previousTop = "";
	const update = () => {
		frame = 0;
		const fixedBar = bar && view.getComputedStyle(bar).position === "fixed" ? bar.getBoundingClientRect().height : 0;
		const sticky = header && header.matches(".rdct-bld--sticky-all,.rdct-bld--sticky-desktop");
		const value = `${Math.ceil(fixedBar + (sticky ? header.getBoundingClientRect().height : 0))}px`;
		if (value !== previous) {
			previous = value;
			root.style.setProperty("--rdct-blog-sticky-offset", value);
			doc.dispatchEvent(new Event("rdct:sidebar-offset"));
		}
		if (natural) {
			const gap = parseFloat(view.getComputedStyle(doc.documentElement).fontSize) || 16;
			const top = `${Math.min(parseFloat(value) + gap, view.innerHeight - root.offsetHeight - gap)}px`;
			if (top !== previousTop) {
				previousTop = top;
				root.style.setProperty("--rdct-natural-sticky-top", top);
				doc.dispatchEvent(new Event("rdct:sidebar-offset"));
			}
		}
	};
	const queue = () => {
		if (desktop.matches && !frame) frame = view.requestAnimationFrame(update);
	};
	const observer = typeof view.ResizeObserver === "function" ? new view.ResizeObserver(queue) : null;
	if (header) observer?.observe(header);
	if (bar) observer?.observe(bar);
	if (natural) observer?.observe(root);
	header?.addEventListener("rdct:sticky-change", queue);
	desktop.addEventListener("change", queue);
	view.addEventListener("resize", queue);
	if (desktop.matches) update();
	return () => {
		observer?.disconnect();
		header?.removeEventListener("rdct:sticky-change", queue);
		desktop.removeEventListener("change", queue);
		view.removeEventListener("resize", queue);
		if (frame) view.cancelAnimationFrame(frame);
		root.style.removeProperty("--rdct-blog-sticky-offset");
		root.style.removeProperty("--rdct-natural-sticky-top");
	};
}
var page = createPageLifecycle(window);
for (const root of document.querySelectorAll("[data-rdct-sticky-sidebar]")) page.mountRoot(root, "blog-sidebar", () => ({}), () => observeSidebarOffset(root));
//#endregion
export { observeSidebarOffset as t };
