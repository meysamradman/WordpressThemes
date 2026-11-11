import { t as gsapWithCSS } from "./gsap-DTQ6fzR8.js";
import { t as ScrollTrigger } from "./ScrollTrigger-DWXBKx_c.js";
//#region radmanct/src/js/motion.js
function initializeMotion() {
	gsapWithCSS.registerPlugin(ScrollTrigger);
	const media = gsapWithCSS.matchMedia();
	media.add("(prefers-reduced-motion: no-preference)", () => {
		gsapWithCSS.utils.toArray("[data-rdct-reveal]").forEach((element) => {
			gsapWithCSS.from(element, {
				y: 20,
				opacity: 0,
				duration: .55,
				ease: "power2.out",
				scrollTrigger: {
					trigger: element,
					start: "top 92%",
					once: true
				}
			});
		});
	});
	return () => media.revert();
}
//#endregion
export { initializeMotion };
