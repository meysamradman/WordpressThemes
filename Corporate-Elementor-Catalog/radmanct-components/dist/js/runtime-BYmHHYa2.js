import { t as createPageLifecycle } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/runtime.js
var lifecycle = createPageLifecycle(window);
var borders;
function observePageBorders() {
	if (borders) return;
	borders = lifecycle.mount(() => __vitePreload(() => import("./hover-border-8KECeY1z.js").then((n) => n.t), [], import.meta.url), ({ observeBorders }) => observeBorders(document), "hover-border");
}
//#endregion
export { observePageBorders as n, lifecycle as t };
