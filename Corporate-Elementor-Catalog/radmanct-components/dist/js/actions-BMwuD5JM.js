import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/components/button/actions.js
function initializeActions(root) {
	const doc = root.ownerDocument;
	const view = doc.defaultView;
	let alive = true;
	let modal;
	let images;
	let cancelScroll;
	let video;
	const contentDialog = async () => {
		const module = await __vitePreload(() => import("./content-dialog-zZJMWlDC.js"), [], import.meta.url);
		if (!alive || !root.isConnected) return null;
		return modal ?? (modal = module.createContentDialog(doc));
	};
	const click = async (event) => {
		if (event.defaultPrevented || event.button != null && event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
		const action = root.dataset.action;
		if (action === "link") return;
		event.preventDefault();
		if (action === "modal") {
			const target = doc.getElementById(root.dataset.anchor || "");
			if (!target) return;
			(await contentDialog())?.open(target, root, root.dataset.closeLabel);
			return;
		}
		if (action === "scroll" || action === "scroll-next") {
			const target = action === "scroll" ? doc.getElementById(root.dataset.anchor || "") : root.closest("section")?.nextElementSibling;
			if (!target) return;
			const module = await __vitePreload(() => import("./scroll-BJRCjJAy.js"), [], import.meta.url);
			if (!alive || !root.isConnected) return;
			cancelScroll?.();
			cancelScroll = await module.scrollToTarget(root, target);
			if (!alive) cancelScroll?.();
			return;
		}
		const url = root.dataset.actionUrl || root.getAttribute("href");
		let source;
		try {
			source = new URL(url, doc.baseURI);
		} catch {
			return;
		}
		if (source.origin !== view.location.origin) return;
		if (/\.(mp4|webm|ogv)$/i.test(source.pathname)) {
			const dialog = await contentDialog();
			if (!dialog) return;
			if (!video) {
				video = doc.createElement("video");
				video.controls = true;
				video.playsInline = true;
				video.preload = "metadata";
				doc.body.append(video);
				video.hidden = true;
			}
			video.src = source.href;
			dialog.open(video, root, root.dataset.closeLabel);
			return;
		}
		const module = await __vitePreload(() => import("./image-dialog-ZuSH4J42.js"), [], import.meta.url);
		if (!alive || !root.isConnected) return;
		images ?? (images = module.createImageDialog(doc, "rdct-im-dialog"));
		images.open(root, [root], {
			close: root.dataset.closeLabel,
			previous: root.dataset.previousLabel,
			next: root.dataset.nextLabel,
			label: root.dataset.caption || root.querySelector(".rdct-button__label")?.textContent || root.textContent.trim()
		});
	};
	const listener = (event) => {
		click(event).catch((error) => reportRuntimeError("button-action", error));
	};
	root.addEventListener("click", listener);
	return () => {
		alive = false;
		root.removeEventListener("click", listener);
		cancelScroll?.();
		modal?.destroy();
		images?.destroy();
		if (video) {
			video.pause();
			video.removeAttribute("src");
			video.remove();
		}
	};
}
//#endregion
export { initializeActions };
