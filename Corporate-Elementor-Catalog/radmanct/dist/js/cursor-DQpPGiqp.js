//#region radmanct/src/js/cursor.js
var cursorFollowAlpha = (elapsed, duration) => 1 - Math.exp(-Math.max(0, elapsed) / (Math.max(.05, duration) * 1e3));
function initializeCursor() {
	const root = document.getElementById("rdct-cursor");
	if (!root || root.dataset.enhanced) return;
	const query = matchMedia("(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)");
	const dot = root.querySelector(".rdct-cursor__dot");
	const ring = root.querySelector(".rdct-cursor__ring");
	const label = root.querySelector(".rdct-cursor__label");
	if (!dot || !ring || !label) return;
	root.dataset.enhanced = "true";
	const events = new AbortController();
	const options = {
		signal: events.signal,
		passive: true
	};
	let frame = 0;
	let x = 0, y = 0, ringX = 0, ringY = 0;
	let activeTarget = null, bounds = null, previousTime = 0;
	const duration = Math.min(1, Math.max(.05, Number(root.dataset.followDuration) || .18));
	const hide = () => {
		root.hidden = true;
		cancelAnimationFrame(frame);
		frame = 0;
		activeTarget = null;
		bounds = null;
		previousTime = 0;
	};
	const position = (element, left, top) => {
		element.style.transform = `translate3d(${left}px,${top}px,0) translate(-50%,-50%)`;
	};
	const animate = (time) => {
		frame = 0;
		const alpha = cursorFollowAlpha(previousTime ? Math.min(64, time - previousTime) : 16.67, duration);
		previousTime = time;
		const magnetic = root.dataset.hoverEffect === "magnetic" && bounds;
		const targetX = magnetic ? bounds.left + bounds.width / 2 + (x - bounds.left - bounds.width / 2) * .25 : x;
		const targetY = magnetic ? bounds.top + bounds.height / 2 + (y - bounds.top - bounds.height / 2) * .25 : y;
		ringX += (targetX - ringX) * alpha;
		ringY += (targetY - ringY) * alpha;
		position(dot, x, y);
		position(ring, ringX, ringY);
		if (Math.abs(targetX - ringX) + Math.abs(targetY - ringY) > .15) frame = requestAnimationFrame(animate);
		else previousTime = 0;
	};
	const inspect = (target) => {
		const marked = target.closest("[data-rdct-cursor], [data-rdct-cursor-label]");
		const kind = marked?.getAttribute("data-rdct-cursor");
		const nextLabel = ((marked?.getAttribute("data-rdct-cursor-label") ?? (kind === "image" ? root.dataset.imageLabel : kind === "carousel" ? root.dataset.carouselLabel : "")) || "").slice(0, 80);
		if (label.textContent !== nextLabel) label.textContent = nextLabel;
		root.classList.toggle("is-labelled", Boolean(label.textContent));
		const interactive = target.closest("a[href], button:not(:disabled), [role=\"button\"], [data-rdct-cursor-target]");
		const nextTarget = interactive?.getAttribute("aria-disabled") === "true" ? null : interactive;
		if (nextTarget !== activeTarget) {
			activeTarget = nextTarget;
			bounds = nextTarget && root.dataset.hoverEffect === "magnetic" ? nextTarget.getBoundingClientRect() : null;
		}
		root.classList.toggle("is-interactive", Boolean(activeTarget));
	};
	document.addEventListener("pointermove", (event) => {
		const target = event.target;
		if (!query.matches || event.pointerType !== "mouse" || !(target instanceof Element) || target.closest("input, textarea, select, [contenteditable]:not([contenteditable=\"false\"]), iframe, #wpadminbar, [data-rdct-cursor=\"none\"]")) {
			hide();
			return;
		}
		x = event.clientX;
		y = event.clientY;
		if (root.hidden) {
			ringX = x;
			ringY = y;
		}
		root.hidden = false;
		inspect(target);
		if (!frame) frame = requestAnimationFrame(animate);
	}, options);
	document.addEventListener("pointerleave", hide, options);
	document.addEventListener("pointercancel", hide, options);
	document.addEventListener("visibilitychange", hide, options);
	document.addEventListener("scroll", hide, {
		...options,
		capture: true
	});
	window.addEventListener("blur", hide, options);
	query.addEventListener("change", hide, options);
	const cleanup = () => {
		hide();
		events.abort();
		delete root.dataset.enhanced;
	};
	window.addEventListener("resize", hide, options);
	window.addEventListener("pagehide", (event) => {
		hide();
		if (!event.persisted) cleanup();
	}, options);
	return cleanup;
}
//#endregion
export { cursorFollowAlpha, initializeCursor };
