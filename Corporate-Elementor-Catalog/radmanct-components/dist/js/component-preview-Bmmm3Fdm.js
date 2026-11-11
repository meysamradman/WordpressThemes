import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/admin/component-preview.js
if (document.querySelector("[data-rdct-particles]")) __vitePreload(async () => {
	const { initializeParticleGroups } = await import("./particles-Dn3QTOLy.js");
	return { initializeParticleGroups };
}, [], import.meta.url).then(({ initializeParticleGroups }) => {
	const cleanup = initializeParticleGroups(document);
	window.addEventListener("pagehide", cleanup, { once: true });
});
if (document.querySelector("[data-rdct-text-reveal=scroll], [data-rdct-text-reveal=fill], [data-rdct-text-reveal=rotate], [data-rdct-text-reveal=marquee]")) __vitePreload(async () => {
	const { initializeTexts } = await import("./text-fUUJkCIq.js");
	return { initializeTexts };
}, [], import.meta.url).then(({ initializeTexts }) => {
	const cleanup = initializeTexts();
	window.addEventListener("pagehide", cleanup, { once: true });
});
if (document.querySelector("[data-rdct-curtain]")) __vitePreload(async () => {
	const { initializeCurtains } = await import("./curtain-DY-gfKdC.js");
	return { initializeCurtains };
}, [], import.meta.url).then(({ initializeCurtains }) => {
	const cleanup = initializeCurtains();
	window.addEventListener("pagehide", cleanup, { once: true });
});
if (document.querySelector("[data-rdct-flipbox]")) __vitePreload(async () => {
	const { initializeFlipBox } = await import("./flipbox-DADV-p-2.js");
	return { initializeFlipBox };
}, [], import.meta.url).then(({ initializeFlipBox }) => {
	const cleanup = initializeFlipBox(document.querySelector("[data-rdct-flipbox]"));
	window.addEventListener("pagehide", cleanup, { once: true });
});
if (document.querySelector("[data-rdct-image]")) __vitePreload(async () => {
	const { initializeImages } = await import("./image-BSHnXy1F.js");
	return { initializeImages };
}, [], import.meta.url).then(({ initializeImages }) => {
	const cleanup = initializeImages();
	window.addEventListener("pagehide", cleanup, { once: true });
});
if (document.querySelector("[data-rdct-carousel]")) __vitePreload(async () => {
	const { initializeCarousels } = await import("./carousel-entry-CCr7WeWc.js");
	return { initializeCarousels };
}, [], import.meta.url).then(({ initializeCarousels }) => {
	const cleanup = initializeCarousels();
	window.addEventListener("pagehide", cleanup, { once: true });
});
if (document.querySelector("[data-rdct-sticky-sections]")) __vitePreload(async () => {
	const { initializeStickySectionGroups } = await import("./sticky-sections-yNk-FbDt.js");
	return { initializeStickySectionGroups };
}, [], import.meta.url).then(({ initializeStickySectionGroups }) => {
	const cleanup = initializeStickySectionGroups();
	window.addEventListener("pagehide", cleanup, { once: true });
});
if (document.querySelector("[data-rdct-button]")) __vitePreload(async () => {
	const { initializeButtons } = await import("./button-DNJYImMI.js");
	return { initializeButtons };
}, [], import.meta.url).then(({ initializeButtons }) => {
	const cleanup = initializeButtons(document);
	window.addEventListener("pagehide", cleanup, { once: true });
});
//#endregion
