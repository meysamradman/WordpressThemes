import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/animation.js
var pending;
var gsapPending;
function loadGsap() {
	if (!gsapPending) {
		const source = window.rdctComponents?.gsapUrl;
		gsapPending = (source ? __vitePreload(() => import(
			/* @vite-ignore */
			source
), [], import.meta.url) : __vitePreload(() => import("./gsap-vendor-aFPgKSyf.js"), [], import.meta.url)).catch((error) => {
			gsapPending = void 0;
			throw error;
		});
	}
	return gsapPending;
}
function loadAnimation() {
	if (!pending) {
		const source = window.rdctComponents?.animationUrl;
		pending = (source ? __vitePreload(() => import(
			/* @vite-ignore */
			source
), [], import.meta.url) : __vitePreload(() => import("./animation-vendor-DkG9lecj.js"), [], import.meta.url)).catch((error) => {
			pending = void 0;
			throw error;
		});
	}
	return pending;
}
//#endregion
export { loadAnimation, loadGsap };
