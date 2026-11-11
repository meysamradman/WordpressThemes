import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { n as observePageBorders, t as lifecycle } from "./runtime-BYmHHYa2.js";
//#region radmanct-components/src/js/integrations/elementor-components.js
var registered = false;
function register() {
	const Base = window.elementorModules?.frontend?.handlers?.Base;
	if (registered || !window.elementorFrontend?.hooks || !Base) return;
	registered = true;
	window.elementorFrontend.hooks.addAction("frontend/element_ready/global", ($element) => {
		const root = $element?.[0];
		if (root && (root.matches("[data-rdct-border]") || root.querySelector("[data-rdct-border]"))) observePageBorders();
	});
	const features = [
		[
			"button",
			["rdct-button", "rdct-atomic-button"],
			"[data-rdct-button]",
			() => __vitePreload(() => import("./button-DNJYImMI.js"), [], import.meta.url),
			"initializeButton"
		],
		[
			"sticky-sections",
			[
				"rdct-sticky-sections",
				"rdct-atomic-sticky-sections",
				...[
					"stack",
					"overlap",
					"services",
					"zoom",
					"split"
				].flatMap((mode) => [`rdct-sticky-${mode}`, `rdct-atomic-sticky-${mode}`])
			],
			"[data-rdct-sticky-sections]",
			() => __vitePreload(() => import("./sticky-sections-yNk-FbDt.js"), [], import.meta.url),
			"initializeStickySections"
		],
		[
			"particles",
			["rdct-particles", "rdct-atomic-particles"],
			"[data-rdct-particles]",
			() => __vitePreload(() => import("./particles-Dn3QTOLy.js"), [], import.meta.url),
			"initializeParticles"
		],
		[
			"text",
			["radman-text-reveal", "rdct-atomic-text-reveal"],
			"[data-rdct-text-reveal]",
			() => __vitePreload(() => import("./text-fUUJkCIq.js"), [], import.meta.url),
			"initializeText"
		],
		[
			"carousel",
			[
				"rdct-carousel",
				"rdct-atomic-carousel",
				...[
					"cards",
					"logos",
					"slider",
					"marquee",
					"stack",
					"testimonials"
				].flatMap((profile) => [`rdct-carousel-${profile}`, `rdct-atomic-carousel-${profile}`])
			],
			"[data-rdct-carousel]",
			() => __vitePreload(() => import("./carousel-entry-CCr7WeWc.js"), [], import.meta.url),
			"initializeCarousel"
		],
		[
			"image",
			["rdct-image", "rdct-atomic-image"],
			"[data-rdct-image]",
			() => __vitePreload(() => import("./image-BSHnXy1F.js"), [], import.meta.url),
			"initializeImage"
		],
		[
			"curtain",
			["rdct-curtain", "rdct-atomic-curtain"],
			"[data-rdct-curtain]",
			() => __vitePreload(() => import("./curtain-DY-gfKdC.js"), [], import.meta.url),
			"initializeCurtain"
		],
		[
			"box",
			["rdct-box", "rdct-atomic-box"],
			"[data-rdct-box-motion]",
			() => __vitePreload(() => import("./box-CKwSNMNy.js"), [], import.meta.url),
			"initializeBox"
		],
		[
			"flipbox",
			["rdct-flipbox", "rdct-atomic-flipbox"],
			"[data-rdct-flipbox]",
			() => __vitePreload(() => import("./flipbox-DADV-p-2.js"), [], import.meta.url),
			"initializeFlipBox"
		]
	];
	for (const [key, names, selector, load, method] of features) {
		class Handler extends Base {
			onInit() {
				super.onInit();
				this.release?.();
				const root = this.$element[0].querySelector(selector);
				if (root) this.release = lifecycle.mountRoot(root, key, load, (module, node) => module[method](node));
			}
			onDestroy() {
				this.release?.();
				super.onDestroy();
			}
		}
		for (const name of names) window.elementorFrontend.hooks.addAction(`frontend/element_ready/${name}.default`, ($element) => window.elementorFrontend.elementsHandler.addHandler(Handler, { $element }));
	}
}
register();
window.addEventListener("elementor/frontend/init", register, { once: true });
//#endregion
