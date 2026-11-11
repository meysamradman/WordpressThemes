import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { n as observeMenus } from "./manager-BevQ0Tlw.js";
//#region radmanct/src/js/integrations/elementor-navigation.js
var stopMenus = observeMenus();
var alive = true;
var previewCleanup;
if (window.rdctNavigationPreview) __vitePreload(async () => {
	const { initializeNavigationPreview } = await import("./elementor-navigation-preview-FIr6R2Tv.js");
	return { initializeNavigationPreview };
}, [], import.meta.url).then(({ initializeNavigationPreview }) => {
	if (alive) previewCleanup = initializeNavigationPreview(window.rdctNavigationPreview);
}).catch((error) => reportRuntimeError("navigation-preview", error));
window.addEventListener("pagehide", (event) => {
	if (!event.persisted) {
		alive = false;
		previewCleanup?.();
		stopMenus();
	}
});
//#endregion
