import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { loadAnimation } from "./animation-C6xwqvoz.js";
//#region radmanct-components/src/js/components/text-reveal-entry.js
async function loadTextReveal(root) {
	const lines = root?.dataset.rdctTextReveal === "scroll" && root.dataset.revealSplit === "lines";
	const [vendor, engine] = await Promise.all([loadAnimation(), lines ? __vitePreload(() => import("./lines-CLsRxS5N.js"), [], import.meta.url) : __vitePreload(() => import("./reveal-C0Q5XVqh.js"), [], import.meta.url)]);
	const initialize = lines ? engine.initializeTextLines : engine.initializeTextReveal;
	return { initializeTextReveal: (target) => initialize(target, vendor) };
}
//#endregion
export { loadTextReveal };
