//#region radmanct-components/src/js/components/flipbox.js
var instances = /* @__PURE__ */ new WeakMap();
function initializeFlipBox(root) {
	if (!root) return () => {};
	if (instances.has(root)) return instances.get(root);
	const button = root.querySelector("[data-rdct-fb-toggle]");
	const faces = [...root.querySelectorAll(":scope > .rdct-fb__wrap > [data-rdct-fb-face]")];
	if (!button || faces.length !== 2) return () => {};
	let flipped = false;
	let alive = true;
	const pointer = root.ownerDocument.defaultView.matchMedia("(hover: hover) and (pointer: fine)");
	const set = (value) => {
		if (!alive) return;
		flipped = value;
		root.classList.toggle("is-flipped", value);
		button.setAttribute("aria-expanded", String(value));
		for (const face of faces) {
			const inactive = face.dataset.rdctFbFace === "back" !== value;
			face.inert = inactive;
			face.setAttribute("aria-hidden", String(inactive));
		}
	};
	const click = () => set(!flipped);
	const enter = (event) => {
		if (event.pointerType === "mouse" && pointer.matches && !faces[0].contains(root.ownerDocument.activeElement)) set(true);
	};
	const leave = () => {
		if (!faces[1].contains(root.ownerDocument.activeElement)) set(false);
	};
	const key = (event) => {
		if (event.key === "Escape" && flipped) {
			event.preventDefault();
			button.focus();
			set(false);
		}
	};
	const blur = () => {
		queueMicrotask(() => {
			if (alive && !root.contains(root.ownerDocument.activeElement)) set(false);
		});
	};
	const preference = () => {
		if (!pointer.matches && !faces[1].contains(root.ownerDocument.activeElement)) set(false);
	};
	button.hidden = false;
	root.classList.add("is-enhanced");
	set(false);
	button.addEventListener("click", click);
	root.addEventListener("pointerenter", enter);
	root.addEventListener("pointerleave", leave);
	root.addEventListener("keydown", key);
	root.addEventListener("focusout", blur);
	pointer.addEventListener("change", preference);
	const cleanup = () => {
		if (!alive) return;
		alive = false;
		pointer.removeEventListener("change", preference);
		button.removeEventListener("click", click);
		root.removeEventListener("pointerenter", enter);
		root.removeEventListener("pointerleave", leave);
		root.removeEventListener("keydown", key);
		root.removeEventListener("focusout", blur);
		button.hidden = true;
		button.removeAttribute("aria-expanded");
		root.classList.remove("is-enhanced", "is-flipped");
		for (const face of faces) {
			face.inert = false;
			face.removeAttribute("aria-hidden");
		}
		instances.delete(root);
	};
	instances.set(root, cleanup);
	return cleanup;
}
//#endregion
export { initializeFlipBox };
