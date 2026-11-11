import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/admin/elementor-panel.js
function prioritizeRadmanPanel(editor) {
	const panel = editor?.config?.document?.panel;
	const categories = panel?.elements_categories;
	if (categories && Object.hasOwn(categories, "radmanct-components") && Object.keys(categories)[0] !== "radmanct-components") panel.elements_categories = {
		"radmanct-components": categories["radmanct-components"],
		...categories
	};
	let collection;
	try {
		collection = editor?.getPanelView?.()?.getCurrentPageView?.()?.categoriesCollection;
	} catch {
		return;
	}
	if (!collection?.models || typeof collection.reset !== "function") return;
	const index = collection.models.findIndex((model) => model.get("name") === "radmanct-components");
	if (index > 0) collection.reset([collection.models[index], ...collection.models.filter((_, position) => position !== index)]);
}
//#endregion
//#region radmanct-components/src/js/admin/component-dialog.js
function createLibraryDialog(manager, { label, closeLabel, onHide }) {
	if (typeof manager?.createWidget !== "function") return null;
	let open = false;
	const modal = manager.createWidget("lightbox", {
		id: "rdct-elementor-library",
		className: "rdct-elementor-library",
		headerMessage: label,
		closeButton: true,
		closeButtonOptions: { attributes: { "aria-label": closeLabel } },
		preventScroll: true,
		hide: {
			onBackgroundClick: true,
			onOutsideClick: true,
			onEscKeyPress: true
		},
		onHide() {
			open = false;
			onHide?.();
		}
	});
	const content = modal.getElements("message")[0];
	const surface = modal.getElements("widgetContent")[0];
	surface.setAttribute("role", "dialog");
	surface.setAttribute("aria-modal", "true");
	surface.setAttribute("aria-label", label);
	surface.addEventListener("keydown", (event) => {
		if (event.key !== "Tab" || event.defaultPrevented) return;
		const nodes = [...surface.querySelectorAll("a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),iframe,[tabindex]:not([tabindex=\"-1\"])")].filter((node) => node.getClientRects().length);
		const active = surface.ownerDocument.activeElement;
		const last = nodes[nodes.length - 1];
		const target = event.shiftKey && active === nodes[0] ? last : !event.shiftKey && active === last ? nodes[0] : null;
		if (target) {
			event.preventDefault();
			target.focus({ preventScroll: true });
		}
	});
	content.setAttribute("data-library-content", "");
	return {
		content,
		get open() {
			return open;
		},
		show() {
			open = true;
			modal.show();
			modal.getElements("closeButton")[0]?.focus?.({ preventScroll: true });
		},
		hide() {
			if (open) modal.hide();
		},
		busy(value) {
			content.setAttribute("aria-busy", String(value));
		}
	};
}
//#endregion
//#region radmanct-components/src/js/admin/component-editor.js
function editorReady(editor, commands) {
	try {
		return !!(typeof commands?.run === "function" && editor?.$previewContents?.[0]?.readyState === "complete" && editor?.getPreviewContainer?.() && editor?.documents?.getCurrentId?.());
	} catch {
		return false;
	}
}
if (typeof window !== "undefined" && typeof document !== "undefined") {
	let attached;
	let initialized = false;
	let loading;
	let controller;
	let generation = 0;
	const linkedPreviews = /* @__PURE__ */ new WeakSet();
	const launcher = document.querySelector("[data-library-open]");
	const label = launcher?.textContent;
	let dialog;
	const closed = () => {
		generation++;
		controller?.abort();
		loading = null;
		dialog?.busy(false);
		if (launcher) {
			launcher.disabled = false;
			launcher.removeAttribute("aria-busy");
		}
		launcher?.setAttribute("aria-expanded", "false");
		dialog?.content.querySelector("[data-rdct-library]")?.dispatchEvent(new Event("rdct:library-close"));
		if (launcher?.rdctLibraryOpener?.isConnected) launcher.rdctLibraryOpener.focus({ preventScroll: true });
	};
	const modal = () => dialog || (dialog = createLibraryDialog(window.elementorCommon?.dialogsManager, {
		label,
		closeLabel: launcher?.dataset.libraryCloseLabel,
		onHide: closed
	}));
	launcher?.setAttribute("aria-haspopup", "dialog");
	launcher?.setAttribute("aria-expanded", "false");
	window.addEventListener("pagehide", () => {
		dialog?.hide();
		controller?.abort();
	});
	const shortcut = () => {
		const preview = window.elementor?.$previewContents?.[0];
		if (!preview || !launcher) return;
		const button = document.createElement("button");
		button.type = "button";
		button.className = "elementor-add-section-area-button";
		button.dataset.rdctLibraryShortcut = "true";
		button.style.cssText = "border-radius:50%;width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;padding:10px;border:0;background:#235a50;color:#fff;vertical-align:middle";
		button.innerHTML = "<svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" aria-hidden=\"true\" focusable=\"false\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.4\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M6 20V4h7a4.5 4.5 0 0 1 0 9H6m6 0 6 7\"/></svg>";
		button.title = label;
		button.setAttribute("aria-label", label);
		const template = document.getElementById("tmpl-elementor-add-section");
		if (template && !template.textContent.includes("data-rdct-library-shortcut")) template.textContent = template.textContent.replace("<div class=\"elementor-add-section-drag-title", `${button.outerHTML}<div class="elementor-add-section-drag-title`);
		preview.querySelectorAll(".elementor-add-section-drag-title").forEach((title) => {
			if (!title.parentElement.querySelector("[data-rdct-library-shortcut]")) title.before(button.cloneNode(true));
		});
		if (!linkedPreviews.has(preview)) {
			linkedPreviews.add(preview);
			preview.addEventListener("click", (event) => {
				const opener = event.target.closest?.("[data-rdct-library-shortcut]");
				if (opener) {
					event.preventDefault();
					event.stopPropagation();
					launcher.rdctLibraryOpener = opener;
					launcher.click();
				}
			}, true);
		}
	};
	const reveal = () => {
		prioritizeRadmanPanel(window.elementor);
		if (!editorReady(window.elementor, window.$e)) return;
		if (document.querySelector("[data-library-open]") && window.elementorCommon?.dialogsManager?.createWidget) shortcut();
	};
	launcher?.addEventListener("click", async (event) => {
		event.stopImmediatePropagation();
		if (!editorReady(window.elementor, window.$e)) return;
		if (!modal()) return;
		if (!dialog.open) dialog.show();
		launcher.setAttribute("aria-expanded", "true");
		if (initialized) {
			dialog.content.querySelector("[data-library-search]")?.focus({ preventScroll: true });
			return;
		}
		if (loading) return;
		const target = dialog.content;
		target.textContent = launcher.dataset.libraryLoading;
		dialog.busy(true);
		const requestController = new AbortController();
		controller = requestController;
		const ticket = ++generation;
		launcher.disabled = true;
		launcher.setAttribute("aria-busy", "true");
		loading = (async () => {
			const url = new URL(launcher.dataset.libraryUrl, window.location.href);
			if (url.origin !== window.location.origin) throw new Error("Invalid library origin");
			const documentId = window.elementor.documents.getCurrentId();
			const body = new URLSearchParams({
				action: "radman_component_library",
				_ajax_nonce: launcher.dataset.libraryNonce,
				post_id: documentId
			});
			const response = await fetch(url, {
				method: "POST",
				credentials: "same-origin",
				body,
				signal: requestController.signal
			});
			const result = await response.json();
			if (!response.ok || !result.success || typeof result.data?.html !== "string") throw new Error("Invalid library response");
			if (!editorReady(window.elementor, window.$e) || window.elementor.documents.getCurrentId() !== documentId) throw new Error("Document changed");
			if (requestController.signal.aborted || ticket !== generation || !dialog.open) throw new Error("Library closed");
			target.innerHTML = result.data.html;
			const { initializeLibrary } = await __vitePreload(async () => {
				const { initializeLibrary } = await import("./component-library-nkOmtnx3.js");
				return { initializeLibrary };
			}, [], import.meta.url);
			if (requestController.signal.aborted || ticket !== generation || !dialog.open) throw new Error("Library closed");
			const root = target.querySelector("[data-rdct-library][data-editor]");
			if (!root) throw new Error("Missing library");
			initializeLibrary(root);
			initialized = true;
			root.querySelector("[data-library-search]")?.focus({ preventScroll: true });
		})();
		try {
			await loading;
			if (ticket !== generation) return;
			launcher.textContent = label;
			if (launcher.rdctLibraryOpener?.isConnected) {
				launcher.rdctLibraryOpener.title = label;
				launcher.rdctLibraryOpener.setAttribute("aria-label", label);
			}
		} catch {
			if (!requestController.signal.aborted && ticket === generation) {
				target.textContent = launcher.dataset.libraryError;
				launcher.textContent = launcher.dataset.libraryError;
				if (launcher.rdctLibraryOpener?.isConnected) {
					launcher.rdctLibraryOpener.title = launcher.dataset.libraryError;
					launcher.rdctLibraryOpener.setAttribute("aria-label", launcher.dataset.libraryError);
				}
			}
		} finally {
			if (ticket === generation) {
				loading = null;
				launcher.disabled = false;
				launcher.removeAttribute("aria-busy");
				dialog.busy(false);
			}
		}
	});
	const attach = () => {
		const editor = window.elementor;
		if (!editor) return;
		if (attached !== editor) {
			attached = editor;
			editor.on?.("preview:loaded", reveal);
			editor.on?.("document:loaded", reveal);
		}
		reveal();
	};
	window.addEventListener("elementor/init", attach);
	window.addEventListener("elementor/loaded", attach);
	window.__elementorEditorV1LoadingPromise?.then(attach);
	attach();
}
//#endregion
export { editorReady };
