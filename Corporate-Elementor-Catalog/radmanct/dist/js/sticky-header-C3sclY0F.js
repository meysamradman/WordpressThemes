import { t as gsapWithCSS } from "./gsap-DTQ6fzR8.js";
//#region radmanct/src/js/components/sticky-header.js
var instances = /* @__PURE__ */ new WeakMap();
function firstSection(doc) {
	const title = doc.querySelector("[data-rdct-page-title]");
	if (title?.matches?.("[data-rdct-page-title]") && !title.hidden && title.getBoundingClientRect().height > 1) return title;
	const main = doc.querySelector("#main-content, main");
	const content = main?.querySelector("[data-elementor-type=\"wp-page\"]") ?? main;
	if (!content) return null;
	const selector = "section, .e-con, .e-container, .e-flexbox, .elementor-section";
	const visible = (node) => !node.hidden && (node.getBoundingClientRect?.().height ?? 1) > 1;
	return [...content.children].find((node) => node.matches(selector) && visible(node)) ?? [...content.querySelectorAll(selector)].find(visible) ?? null;
}
function initializeStickyHeader(root, options = {}) {
	if (instances.has(root)) return instances.get(root).cleanup;
	const vendor = options.vendor ?? gsapWithCSS;
	const doc = root.ownerDocument;
	const view = doc.defaultView;
	const section = firstSection(doc);
	if (!section) return () => {};
	const desktop = view.matchMedia("(min-width:48rem)");
	const reduced = view.matchMedia("(prefers-reduced-motion:reduce)");
	const overlay = () => root.classList.contains("rdct-bld--transparent") && (root.dataset.rdctTransparentDevices !== "desktop" || desktop.matches);
	const placeholder = doc.createElement("div");
	placeholder.setAttribute("aria-hidden", "true");
	placeholder.hidden = true;
	root.after(placeholder);
	let portal = !!options.portal;
	let moved = false;
	let frame = 0;
	let alive = true;
	let fixed = false;
	let target = false;
	let generation = 0;
	let context;
	let tween;
	let position;
	const restore = () => {
		tween?.kill();
		context?.revert();
		context = null;
		tween = null;
		root.classList.remove("is-sticky-active");
		root.dispatchEvent?.(new Event("rdct:sticky-change"));
		if (moved) {
			placeholder.before(root);
			for (const [name, value, priority] of position) value ? root.style.setProperty(name, value, priority) : root.style.removeProperty(name);
			moved = false;
		}
		placeholder.hidden = true;
		fixed = false;
	};
	const enter = (offset) => {
		const rect = root.getBoundingClientRect();
		if (!fixed) {
			placeholder.style.blockSize = overlay() ? "0px" : `${rect.height}px`;
			placeholder.hidden = false;
			if (portal) {
				position = [
					"position",
					"top",
					"left",
					"width"
				].map((name) => [
					name,
					root.style.getPropertyValue(name),
					root.style.getPropertyPriority(name)
				]);
				doc.body.append(root);
				moved = true;
				root.style.position = "fixed";
				root.style.top = `${offset}px`;
				root.style.left = `${rect.left}px`;
				root.style.width = `${rect.width}px`;
			}
			root.classList.add("is-sticky-active");
			fixed = true;
			root.dispatchEvent?.(new Event("rdct:sticky-change"));
			context = vendor.context(() => {}, root);
			if (!reduced.matches) context.add(() => vendor.set(root, {
				yPercent: -100,
				autoAlpha: 0
			}));
		}
		context.add(() => {
			tween = vendor.to(root, {
				yPercent: 0,
				autoAlpha: 1,
				duration: reduced.matches ? 0 : .32,
				ease: "power3.out",
				overwrite: "auto"
			});
		});
	};
	const update = () => {
		frame = 0;
		if (!alive || !root.isConnected) return;
		const enabled = root.dataset.stickyMode === "all" || desktop.matches;
		const offset = Number.parseFloat(view.getComputedStyle(root).getPropertyValue("--rdct-sticky-offset")) || 0;
		const desired = enabled && section.getBoundingClientRect().bottom <= offset + (target ? 2 : 0);
		if (moved) {
			const bounds = placeholder.getBoundingClientRect();
			const geometry = {
				top: `${offset}px`,
				width: `${bounds.width}px`,
				left: `${bounds.left}px`
			};
			for (const [name, value] of Object.entries(geometry)) if (root.style[name] !== value) root.style[name] = value;
		}
		if (desired === target) {
			if (reduced.matches && fixed && tween) {
				tween.kill();
				tween = null;
				if (desired) context.add(() => vendor.set(root, {
					yPercent: 0,
					autoAlpha: 1
				}));
				else restore();
			}
			return;
		}
		target = desired;
		const current = ++generation;
		tween?.kill();
		if (desired) enter(offset);
		else if (fixed) {
			if (reduced.matches) restore();
			else context.add(() => {
				tween = vendor.to(root, {
					yPercent: -100,
					autoAlpha: 0,
					duration: .22,
					ease: "power2.in",
					overwrite: "auto",
					onComplete: () => {
						if (alive && generation === current && !target) restore();
					}
				});
			});
		}
	};
	const queue = () => {
		if (alive && !frame) frame = view.requestAnimationFrame(update);
	};
	const configure = (next) => {
		if (portal === next) return;
		portal = next;
		++generation;
		target = false;
		if (fixed) restore();
		queue();
	};
	const observer = view.ResizeObserver ? new view.ResizeObserver(queue) : null;
	observer?.observe(root);
	observer?.observe(section);
	const breakpoint = () => {
		if (fixed) placeholder.style.blockSize = overlay() ? "0px" : `${root.getBoundingClientRect().height}px`;
		queue();
	};
	view.addEventListener("scroll", queue, { passive: true });
	view.addEventListener("resize", queue, { passive: true });
	desktop.addEventListener("change", breakpoint);
	reduced.addEventListener("change", queue);
	doc.addEventListener("rdct:smooth-update", queue);
	root.addEventListener("rdct:sticky-refresh", queue);
	doc.fonts?.ready.then(queue).catch(() => {});
	const cleanup = () => {
		if (!alive) return;
		alive = false;
		++generation;
		if (frame) view.cancelAnimationFrame(frame);
		observer?.disconnect();
		view.removeEventListener("scroll", queue);
		view.removeEventListener("resize", queue);
		desktop.removeEventListener("change", breakpoint);
		reduced.removeEventListener("change", queue);
		doc.removeEventListener("rdct:smooth-update", queue);
		root.removeEventListener("rdct:sticky-refresh", queue);
		restore();
		placeholder.remove();
		instances.delete(root);
	};
	instances.set(root, {
		cleanup,
		configure,
		get portal() {
			return portal;
		}
	});
	update();
	return cleanup;
}
function enableSmoothStickyHeader(root) {
	const current = instances.get(root);
	if (!current) return initializeStickyHeader(root, { portal: true });
	const previous = current.portal;
	current.configure(true);
	return () => current.configure(previous);
}
function initializeStickyHeaders(scope = document) {
	const releases = [...scope.querySelectorAll("[data-rdct-sticky-start]")].map((root) => initializeStickyHeader(root));
	return () => releases.forEach((release) => release());
}
//#endregion
export { enableSmoothStickyHeader, firstSection, initializeStickyHeader, initializeStickyHeaders };
