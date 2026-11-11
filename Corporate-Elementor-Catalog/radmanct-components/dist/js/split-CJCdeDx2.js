import { n as pinWithinGroup, t as createStickyScene } from "./scene-IbrJ3j2M.js";
//#region radmanct-components/src/js/components/sticky-sections/split.js
function mount(root, { gsap, ScrollTrigger }) {
	return createStickyScene(root, gsap, ScrollTrigger, ({ top, available }) => {
		const fixed = root.querySelector(":scope > .rdct-ss__aside > .rdct-ss__fixed");
		const list = root.querySelector(":scope > .rdct-ss__list");
		if (!fixed || fixed.offsetHeight > available) return false;
		if (list.offsetHeight <= fixed.offsetHeight) return false;
		pinWithinGroup(ScrollTrigger, root, root, fixed, top);
	});
}
//#endregion
export { mount };
