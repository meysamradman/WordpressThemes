import { n as pinWithinGroup, t as createStickyScene } from "./scene-IbrJ3j2M.js";
//#region radmanct-components/src/js/components/sticky-sections/panels.js
function mount(root, { gsap, ScrollTrigger }) {
	return createStickyScene(root, gsap, ScrollTrigger, ({ items, inners, top }) => {
		items.slice(0, -1).forEach((item, index) => {
			if (item.classList.contains("is-tall")) return;
			pinWithinGroup(ScrollTrigger, root, item, inners[index], top);
		});
	});
}
//#endregion
export { mount };
