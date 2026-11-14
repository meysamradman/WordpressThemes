import { t as createPageLifecycle } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct/src/js/frontend.js
var page = createPageLifecycle(window);
var features = [
	[
		"header-contrast",
		"[data-rdct-header-colors]",
		() => __vitePreload(() => import("./header-contrast-B6JPwngV.js"), [], import.meta.url),
		"initializeHeaderContrasts"
	],
	[
		"sticky-header",
		"[data-rdct-sticky-start]",
		() => __vitePreload(() => import("./sticky-header-C3sclY0F.js"), [], import.meta.url),
		"initializeStickyHeaders"
	],
	[
		"menus",
		"[data-rdct-menu]",
		() => __vitePreload(() => import("./manager-BevQ0Tlw.js").then((n) => n.t), [], import.meta.url),
		"observeMenus"
	],
	[
		"gallery",
		"[data-rdct-gallery]",
		() => __vitePreload(() => import("./gallery-DK_T7PPc.js"), [], import.meta.url),
		"initializeGalleries"
	],
	[
		"scroll-progress",
		"#rdct-scroll-progress",
		() => __vitePreload(() => import("./scroll-progress-B6-dEVTI.js"), [], import.meta.url),
		"initializeScrollProgress"
	]
];
for (const [key, selector, load, method] of features) if (document.querySelector(selector)) page.mount(load, (module) => module[method](), key);
if ([...document.querySelectorAll(".rdct-cnt table")].some((table) => !table.closest(".rdct-tb"))) page.mount(() => __vitePreload(() => import("./content-tables-CCmOBX0m.js"), [], import.meta.url), ({ initializeContentTables }) => initializeContentTables(), "content-tables");
for (const root of document.querySelectorAll("[data-rdct-transparent-header]")) page.mountRoot(root, "transparent-header", () => __vitePreload(() => import("./transparent-header-BinIZKuv.js"), [], import.meta.url), ({ initializeTransparentHeader }, node) => initializeTransparentHeader(node));
var reveal = !matchMedia("(prefers-reduced-motion: reduce)").matches && document.body.classList.contains("rdct-reveal-enabled") && document.querySelector("[data-rdct-reveal]");
var smooth = ![...document.querySelectorAll("[data-rdct-motion]")].some((node) => {
	try {
		const options = JSON.parse(node.dataset.rdctMotion);
		return options.horizontal === true || options.horizontal === "yes";
	} catch {
		return false;
	}
}) && document.body.classList.contains("rdct-smooth-scroll-enabled") && document.getElementById("rdct-scroll-wrapper");
var containerMotion = document.querySelector("[data-rdct-motion]");
async function initializeEffects() {
	if (smooth) {
		let smoothTask;
		await page.mount(() => ({}), () => {
			const query = matchMedia("(min-width:48rem) and (hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)");
			const load = () => {
				if (query.matches && !smoothTask) smoothTask = page.mount(() => __vitePreload(() => import("./smooth-scroll-B1iumRLk.js"), [], import.meta.url), ({ initializeSmoothScroll }) => initializeSmoothScroll(), "smooth-scroll");
			};
			query.addEventListener("change", load);
			load();
			return () => query.removeEventListener("change", load);
		}, "smooth-preferences").ready;
		if (smoothTask) await smoothTask.ready;
	}
	if (containerMotion) page.mount(() => __vitePreload(() => import("./container-motion-manager-kSyiToON.js").then((n) => n.t), [], import.meta.url), ({ observeContainerMotion }) => observeContainerMotion(), "container-motion");
	if (reveal) page.mount(() => __vitePreload(() => import("./motion-D0UfLi9k.js"), [], import.meta.url), ({ initializeMotion }) => initializeMotion(), "reveal");
}
if (smooth || reveal || containerMotion) initializeEffects();
if (document.getElementById("rdct-cursor")) page.mount(() => ({}), () => {
	const query = matchMedia("(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)");
	let pending = false;
	let loaded = false;
	const load = () => {
		if (!query.matches || pending || loaded) return;
		pending = true;
		page.mount(() => __vitePreload(() => import("./cursor-DQpPGiqp.js"), [], import.meta.url), ({ initializeCursor }) => {
			initializeCursor();
			loaded = true;
		}, "cursor").ready?.then(() => {
			pending = false;
		});
	};
	query.addEventListener("change", load);
	load();
	return () => query.removeEventListener("change", load);
}, "cursor-preferences");
//#endregion
