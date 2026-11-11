import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { t as lockPanelScroll } from "./scroll-lock-wif582GO.js";
//#region radmanct/src/js/navigation/drawer.js
async function loadDrawerMotion(container) {
	if (container.dataset.panelMotion === "curtain") return (await __vitePreload(async () => {
		const { createPanelMotion } = await import("./curtain-hkaAKJaW.js");
		return { createPanelMotion };
	}, [], import.meta.url)).createPanelMotion;
	if (container.dataset.panelMotion === "wave") return (await __vitePreload(async () => {
		const { createPanelMotion } = await import("./wave-CegW72nA.js");
		return { createPanelMotion };
	}, [], import.meta.url)).createPanelMotion;
	return null;
}
function createDrawer(container, navigation, button, onClose, motionFactory = null) {
	const doc = container.ownerDocument || document;
	const view = doc.defaultView || window;
	const dialog = doc.createElement("dialog");
	if (typeof dialog.showModal !== "function") return null;
	dialog.className = "rdct-mob-drawer";
	for (const name of container.classList || []) if (name.startsWith("rdct-menu--skin-")) dialog.classList.add(name);
	if (container.hasAttribute?.("data-panel-only")) dialog.setAttribute("data-panel-only", "");
	const originalHidden = navigation.hidden;
	dialog.dataset.panelMode = container.dataset.panelMode || "sidebar";
	dialog.dataset.panelLinkLayout = container.dataset.panelLinkLayout || "auto";
	dialog.dataset.panelMotion = motionFactory ? container.dataset.panelMotion : "standard";
	dialog.dataset.drawerSide = container.dataset.drawerSide || "start";
	if (motionFactory) dialog.style.setProperty("--rdct-panel-motion-color", container.dataset.panelMotionColor || "#390507");
	const content = container.querySelector(":scope > .rdct-menu-panel-content");
	const contentHidden = content?.hidden;
	const header = container.querySelector(":scope > .rdct-menu-panel-header");
	const headerHidden = header?.hidden;
	dialog.id = `${navigation.id}-drawer`;
	dialog.setAttribute("aria-label", navigation.getAttribute("aria-label"));
	const closeButton = doc.createElement("button");
	closeButton.type = "button";
	closeButton.className = "rdct-mob-drawer__close";
	closeButton.setAttribute("autofocus", "");
	const closeIcon = container.querySelector(":scope > template[data-rdct-panel-close]");
	if (closeIcon?.content) closeButton.append(closeIcon.content.cloneNode(true));
	else closeButton.textContent = "×";
	closeButton.setAttribute("aria-label", container.dataset.closeLabel);
	dialog.append(closeButton);
	container.append(dialog);
	const duration = Math.min(2e3, Math.max(200, Number(container.dataset.panelDuration) || 800));
	const motion = motionFactory?.(dialog, duration);
	let active = false;
	let closing = false;
	let releaseScroll;
	let destroyed = false;
	let generation = 0;
	let returnFocus = false;
	const fitViewport = () => {
		if (active && ["fullscreen", "split"].includes(dialog.dataset.panelMode) && Number.isFinite(view.innerWidth)) dialog.style.inlineSize = `${view.innerWidth}px`;
	};
	view.addEventListener("resize", fitViewport);
	const restore = () => {
		const x = view.scrollX;
		const y = view.scrollY;
		active = false;
		closing = false;
		if (dialog.open) dialog.close();
		container.append(dialog);
		container.insertBefore(navigation, dialog);
		navigation.hidden = originalHidden;
		if (content) {
			container.insertBefore(content, dialog);
			content.hidden = contentHidden;
		}
		if (header) {
			container.insertBefore(header, dialog);
			header.hidden = headerHidden;
		}
		button.setAttribute("aria-controls", navigation.id);
		button.removeAttribute("aria-haspopup");
		if (returnFocus && !destroyed) button.focus({ preventScroll: true });
		returnFocus = false;
		if (Number.isFinite(y) && (view.scrollY !== y || view.scrollX !== x)) view.scrollTo({
			left: x,
			top: y,
			behavior: "instant"
		});
		releaseScroll?.();
		releaseScroll = null;
	};
	const close = (immediate = false) => {
		if (!active || closing && !immediate) return;
		returnFocus || (returnFocus = Boolean(dialog.contains?.(doc.activeElement)));
		const current = ++generation;
		closing = true;
		motion?.cancel();
		if (!motion || immediate || !dialog.open) {
			restore();
			return;
		}
		let result;
		try {
			result = motion.play(false);
		} catch {
			motion.cancel();
			restore();
			return;
		}
		Promise.resolve(result).catch(() => {}).then(() => {
			if (!destroyed && generation === current) restore();
		});
	};
	const dismiss = () => {
		returnFocus = true;
		onClose();
	};
	closeButton.addEventListener("click", dismiss);
	dialog.addEventListener("cancel", (event) => {
		event.preventDefault();
		dismiss();
	});
	dialog.addEventListener("close", () => {
		if (active) {
			onClose();
			close(true);
		}
	});
	dialog.addEventListener("click", (event) => {
		const rect = dialog.getBoundingClientRect();
		if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) dismiss();
	});
	const pagehide = () => {
		onClose();
		close(true);
	};
	view.addEventListener("pagehide", pagehide);
	return {
		close,
		contains(target) {
			return Boolean(target && dialog.contains?.(target));
		},
		destroy() {
			if (destroyed) return;
			destroyed = true;
			++generation;
			motion?.cancel();
			if (active) restore();
			motion?.destroy();
			view.removeEventListener("pagehide", pagehide);
			dialog.remove();
			view.removeEventListener("resize", fitViewport);
		},
		open(scrollLock) {
			if (destroyed || active && !closing) {
				scrollLock?.();
				return;
			}
			++generation;
			motion?.cancel();
			closing = false;
			returnFocus = false;
			if (releaseScroll) scrollLock?.();
			else releaseScroll = scrollLock || lockPanelScroll(doc, dialog);
			if (!active) {
				const computed = view.getComputedStyle?.(container);
				if (computed) {
					for (let index = 0; index < computed.length; index++) {
						const key = computed[index];
						if (key.startsWith("--rdct-")) dialog.style.setProperty(key, computed.getPropertyValue(key));
					}
					for (const key of [
						"fontFamily",
						"fontSize",
						"fontWeight",
						"lineHeight",
						"color"
					]) dialog.style[key] = computed[key];
					dialog.setAttribute("dir", computed.direction);
				}
				doc.body.append(dialog);
				dialog.append(navigation);
				navigation.hidden = false;
				if (header) {
					header.hidden = false;
					dialog.append(header);
				}
				if (content) {
					content.hidden = false;
					dialog.append(content);
				}
			}
			active = true;
			fitViewport();
			button.setAttribute("aria-controls", dialog.id);
			button.setAttribute("aria-haspopup", "dialog");
			const x = view.scrollX;
			const y = view.scrollY;
			try {
				if (!dialog.open) dialog.showModal();
			} catch {
				restore();
				return;
			}
			closeButton.focus({ preventScroll: true });
			if (Number.isFinite(y) && (view.scrollY !== y || view.scrollX !== x)) view.scrollTo({
				left: x,
				top: y,
				behavior: "instant"
			});
			if (motion) try {
				Promise.resolve(motion.play(true)).catch(() => motion.cancel());
			} catch {
				motion.cancel();
			}
		}
	};
}
//#endregion
export { createDrawer, loadDrawerMotion };
