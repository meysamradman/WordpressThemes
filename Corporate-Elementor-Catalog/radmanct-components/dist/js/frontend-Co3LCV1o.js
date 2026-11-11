import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { n as observePageBorders, t as lifecycle } from "./runtime-BYmHHYa2.js";
//#region radmanct-components/src/js/frontend.js
var features = [
	[
		"button",
		"[data-rdct-button]",
		() => __vitePreload(() => import("./button-DNJYImMI.js"), [], import.meta.url),
		"initializeButton"
	],
	[
		"text-header",
		"[data-rdct-text-header]",
		() => __vitePreload(() => import("./header-state-D4GFfZU1.js"), [], import.meta.url),
		"initializeHeaderText"
	],
	[
		"sticky-sections",
		"[data-rdct-sticky-sections]",
		() => __vitePreload(() => import("./sticky-sections-yNk-FbDt.js"), [], import.meta.url),
		"initializeStickySections"
	],
	[
		"particles",
		"[data-rdct-particles]",
		() => __vitePreload(() => import("./particles-Dn3QTOLy.js"), [], import.meta.url),
		"initializeParticles"
	],
	[
		"text",
		"[data-rdct-text-reveal=scroll], [data-rdct-text-reveal=fill], [data-rdct-text-reveal=rotate], [data-rdct-text-reveal=marquee]",
		() => __vitePreload(() => import("./text-fUUJkCIq.js"), [], import.meta.url),
		"initializeText"
	],
	[
		"curtain",
		"[data-rdct-curtain]",
		() => __vitePreload(() => import("./curtain-DY-gfKdC.js"), [], import.meta.url),
		"initializeCurtain"
	],
	[
		"box",
		"[data-rdct-box-motion]",
		() => __vitePreload(() => import("./box-CKwSNMNy.js"), [], import.meta.url),
		"initializeBox"
	],
	[
		"flipbox",
		"[data-rdct-flipbox]",
		() => __vitePreload(() => import("./flipbox-DADV-p-2.js"), [], import.meta.url),
		"initializeFlipBox"
	],
	[
		"image",
		"[data-rdct-image]",
		() => __vitePreload(() => import("./image-BSHnXy1F.js"), [], import.meta.url),
		"initializeImage"
	],
	[
		"carousel",
		"[data-rdct-carousel]",
		() => __vitePreload(() => import("./carousel-entry-CCr7WeWc.js"), [], import.meta.url),
		"initializeCarousel"
	]
];
for (const [key, selector, load, method] of features) for (const root of document.querySelectorAll(selector)) lifecycle.mountRoot(root, key, load, (module, node) => module[method](node));
if (document.querySelector("[data-rdct-border]")) observePageBorders();
var comboboxesMounted = false;
var discoverComboboxes = () => {
	if (comboboxesMounted || !document.querySelector("select[data-rdct-combobox]")) return;
	comboboxesMounted = true;
	lifecycle.mount(() => __vitePreload(() => import("./combobox-BSNoFLm1.js"), [], import.meta.url), ({ observeComboboxes }) => observeComboboxes(), "comboboxes");
};
lifecycle.mount(() => ({}), () => {
	discoverComboboxes();
	document.addEventListener("rdct:archive-updated", discoverComboboxes);
	return () => document.removeEventListener("rdct:archive-updated", discoverComboboxes);
}, "combobox-discovery");
var cardPreviewsMounted = false;
var discoverCardPreviews = () => {
	if (cardPreviewsMounted || !document.querySelector("[data-rdct-card-preview]")) return;
	cardPreviewsMounted = true;
	lifecycle.mount(() => __vitePreload(() => import("./card-preview-runtime-0Pt5Fw2W.js"), [], import.meta.url), () => {}, "card-preview-discovery");
};
lifecycle.mount(() => ({}), () => {
	discoverCardPreviews();
	document.addEventListener("rdct:archive-updated", discoverCardPreviews);
	return () => document.removeEventListener("rdct:archive-updated", discoverCardPreviews);
}, "card-preview-discovery-listener");
//#endregion
