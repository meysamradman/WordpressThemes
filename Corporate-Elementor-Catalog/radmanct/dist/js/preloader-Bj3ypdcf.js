//#region radmanct/src/js/preloader.js
(() => {
	const root = document.getElementById("rdct-preloader");
	if (!root) return;
	if (document.readyState === "complete" || matchMedia("(prefers-reduced-motion: reduce)").matches || document.documentElement.classList.contains("elementor-editor-active") || document.body.classList.contains("elementor-editor-active")) {
		root.remove();
		return;
	}
	const events = new AbortController();
	const options = {
		once: true,
		signal: events.signal
	};
	let finished = false;
	let ready = false;
	let painted = false;
	let leaving = false;
	const duration = Number(root.dataset.exitDuration);
	const exitDuration = (Number.isFinite(duration) && duration >= 200 && duration <= 1200 ? duration : 350) + 60;
	let safety;
	let exit;
	let wait;
	let frame;
	const finish = () => {
		if (finished) return;
		finished = true;
		clearTimeout(safety);
		clearTimeout(exit);
		clearTimeout(wait);
		cancelAnimationFrame(frame);
		events.abort();
		root.remove();
	};
	const leave = () => {
		if (finished || leaving || !ready || !painted) return;
		leaving = true;
		clearTimeout(wait);
		root.classList.add("is-leaving");
		exit = setTimeout(finish, exitDuration);
	};
	window.addEventListener("load", () => {
		ready = true;
		leave();
	}, options);
	window.addEventListener("pageshow", (event) => {
		if (event.persisted) finish();
	}, options);
	window.addEventListener("pagehide", finish, options);
	document.addEventListener("pointerdown", finish, options);
	document.addEventListener("keydown", finish, options);
	root.classList.add("is-visible");
	root.hidden = false;
	safety = setTimeout(finish, 1800);
	wait = setTimeout(() => {
		ready = true;
		leave();
	}, 1800 - exitDuration);
	frame = requestAnimationFrame(() => {
		if (finished) return;
		frame = requestAnimationFrame(() => {
			if (!finished) {
				painted = true;
				leave();
			}
		});
	});
})();
//#endregion
