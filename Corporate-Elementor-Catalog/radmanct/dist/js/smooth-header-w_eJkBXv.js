import { enableSmoothStickyHeader } from "./sticky-header-C3sclY0F.js";
//#region radmanct/src/js/smooth-header.js
function pinSmoothHeaders(content, ScrollTrigger, offset) {
	const doc = content.ownerDocument;
	const records = [...content.querySelectorAll("header.rdct-bld--sticky-all, header.rdct-bld--sticky-desktop")].map((root) => {
		if (root.dataset.rdctStickyStart === "after-first-section") return enableSmoothStickyHeader(root);
		const wasActive = root.classList.contains("is-sticky-active");
		root.setAttribute("data-rdct-smooth-sticky", "");
		root.classList.remove("is-sticky-active");
		const trigger = ScrollTrigger.create({
			trigger: root,
			pin: root,
			pinType: "fixed",
			pinReparent: true,
			pinSpacing: false,
			start: () => `top top+=${offset()}`,
			end: "max",
			invalidateOnRefresh: true,
			onToggle: (self) => root.classList.toggle("is-sticky-active", self.isActive || self.progress === 1),
			onUpdate: (self) => {
				if (self.isActive || self.progress === 1) root.style.top = `${offset()}px`;
			}
		});
		return () => {
			trigger.kill(true);
			root.removeAttribute("data-rdct-smooth-sticky");
			root.classList.toggle("is-sticky-active", wasActive);
			root.dispatchEvent(new doc.defaultView.Event("rdct:sticky-refresh"));
		};
	});
	return () => records.reverse().forEach((release) => release());
}
//#endregion
export { pinSmoothHeaders };
