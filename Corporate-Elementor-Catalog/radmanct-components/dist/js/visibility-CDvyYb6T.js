//#region radmanct-components/src/js/components/image/visibility.js
function initializeImageVisibility(root, reveal = null) {
	const view = root.ownerDocument.defaultView;
	const query = view.matchMedia("(prefers-reduced-motion: no-preference)");
	let observer;
	const sync = () => {
		observer?.disconnect();
		root.classList.remove("is-enhanced", "is-in-view");
		reveal?.reset();
		if (!query.matches || !view.IntersectionObserver) return;
		root.classList.add("is-enhanced");
		observer = new view.IntersectionObserver((entries) => {
			for (const entry of entries) {
				root.classList.toggle("is-in-view", entry.isIntersecting);
				if (entry.isIntersecting && !root.classList.contains("is-seen")) {
					root.classList.add("is-seen");
					reveal?.enter();
				}
			}
		}, { threshold: 0 });
		observer.observe(root);
	};
	query.addEventListener("change", sync);
	sync();
	return () => {
		observer?.disconnect();
		query.removeEventListener("change", sync);
		root.classList.remove("is-enhanced", "is-in-view", "is-seen");
		reveal?.destroy();
	};
}
//#endregion
export { initializeImageVisibility };
