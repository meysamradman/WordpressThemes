import { t as revealTween } from "./reveal-settings-DP2UsI3z.js";
//#region radmanct-components/src/js/components/text/reveal.js
var instances = /* @__PURE__ */ new WeakMap();
function initializeTextReveal(root, { gsap, ScrollTrigger }) {
	if (!root || !["scroll", "fill"].includes(root.dataset.rdctTextReveal)) return () => {};
	if (instances.has(root)) return instances.get(root);
	const target = root.querySelector?.(":scope > a") || root;
	const text = target.textContent;
	const parts = text.split(/(\s+)/u);
	if (parts.filter((part) => part && !/^\s+$/u.test(part)).length > 400) return () => {};
	gsap.registerPlugin(ScrollTrigger);
	const media = gsap.matchMedia();
	media.add("(prefers-reduced-motion: no-preference)", (context) => {
		let active = true;
		let changed = false;
		context.add("setup", () => {
			if (!active || !root.isConnected || changed) return;
			const visual = document.createElement("span");
			visual.setAttribute("aria-hidden", "true");
			const accessible = document.createElement("span");
			accessible.className = "rdct-tr__accessible";
			accessible.textContent = text;
			const words = [];
			parts.forEach((part) => {
				if (!part || /^\s+$/u.test(part)) {
					visual.append(document.createTextNode(part));
					return;
				}
				const word = document.createElement("span");
				word.textContent = part;
				visual.append(word);
				words.push(word);
			});
			if (!words.length) return;
			changed = true;
			target.replaceChildren(accessible, visual);
			if (root.dataset.rdctTextReveal === "fill") words.forEach((word) => {
				word.className = "rdct-text__fill-word";
			});
			const { from, to } = revealTween(root);
			gsap.fromTo(words, from, to);
		});
		Promise.resolve(document.fonts?.ready).then(() => context.setup(), () => context.setup());
		return () => {
			active = false;
			if (changed) target.textContent = text;
		};
	}, root);
	let destroyed = false;
	const destroy = () => {
		if (destroyed) return;
		destroyed = true;
		media.revert();
		instances.delete(root);
	};
	instances.set(root, destroy);
	return destroy;
}
function initializeTextReveals(scope = document, vendor) {
	const cleanup = Array.from(scope.querySelectorAll("[data-rdct-text-reveal=\"scroll\"]"), (root) => initializeTextReveal(root, vendor));
	return () => cleanup.forEach((destroy) => destroy());
}
//#endregion
export { initializeTextReveal, initializeTextReveals };
