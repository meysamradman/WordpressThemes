//#region shared/frontend/pointer-tilt.js
function initializePointerTilt(root, target, { amount, xProperty, yProperty }) {
	const view = root.ownerDocument.defaultView;
	const query = view.matchMedia("(prefers-reduced-motion: no-preference) and (hover: hover) and (pointer: fine)");
	let alive = true;
	let frame = 0;
	let rect;
	let point;
	const reset = () => {
		if (frame) view.cancelAnimationFrame(frame);
		frame = 0;
		rect = null;
		point = null;
		target.style.removeProperty(xProperty);
		target.style.removeProperty(yProperty);
	};
	const move = (event) => {
		if (!alive || !query.matches || event.pointerType !== "mouse") return;
		point = [event.clientX, event.clientY];
		if (frame) return;
		frame = view.requestAnimationFrame(() => {
			frame = 0;
			if (!alive || !query.matches || !point) return;
			rect || (rect = target.getBoundingClientRect());
			const strength = Number(amount());
			if (!rect.width || !rect.height || !Number.isFinite(strength)) return;
			const clamp = (value) => Math.max(-1, Math.min(1, value));
			target.style.setProperty(xProperty, `${clamp((.5 - (point[1] - rect.top) / rect.height) * 2) * strength}deg`);
			target.style.setProperty(yProperty, `${clamp(((point[0] - rect.left) / rect.width - .5) * 2) * strength}deg`);
		});
	};
	root.addEventListener("pointermove", move);
	root.addEventListener("pointerleave", reset);
	root.addEventListener("focusin", reset);
	query.addEventListener("change", reset);
	view.addEventListener("resize", reset);
	view.addEventListener("scroll", reset, { passive: true });
	return () => {
		if (!alive) return;
		alive = false;
		reset();
		root.removeEventListener("pointermove", move);
		root.removeEventListener("pointerleave", reset);
		root.removeEventListener("focusin", reset);
		query.removeEventListener("change", reset);
		view.removeEventListener("resize", reset);
		view.removeEventListener("scroll", reset);
	};
}
//#endregion
export { initializePointerTilt as t };
