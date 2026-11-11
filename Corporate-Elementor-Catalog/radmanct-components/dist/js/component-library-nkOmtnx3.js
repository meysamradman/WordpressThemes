import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct-components/src/js/admin/component-models.js
function createModelLoader({ url, nonce }, fetchModel = fetch, origin = window.location.origin) {
	const endpoint = new URL(url, origin);
	if (endpoint.origin !== origin) throw new Error("Invalid library origin");
	const cache = /* @__PURE__ */ new Map();
	return async ({ id, language, mode, documentId, signal }) => {
		if (!/^[a-z0-9-]{1,80}$/.test(id) || !["fa", "en"].includes(language) || !["classic", "atomic"].includes(mode) || !Number.isInteger(Number(documentId)) || Number(documentId) <= 0) throw new Error("Invalid sample");
		const key = JSON.stringify([
			documentId,
			id,
			language,
			mode
		]);
		if (signal?.aborted) throw new Error("Sample cancelled");
		if (cache.has(key)) return cache.get(key);
		const body = new URLSearchParams({
			action: "radman_component_library",
			view: "model",
			_ajax_nonce: nonce,
			post_id: documentId,
			sample: id,
			language,
			mode
		});
		const response = await fetchModel(endpoint.href, {
			method: "POST",
			credentials: "same-origin",
			body,
			signal
		});
		const result = await response.json();
		if (!response.ok || !result.success || !result.data?.model?.elements?.length || signal?.aborted) throw new Error("Sample unavailable");
		const model = result.data.model;
		cache.set(key, model);
		if (cache.size > 32) cache.delete(cache.keys().next().value);
		return model;
	};
}
//#endregion
//#region radmanct-components/src/js/admin/components.js
function normalizeLibrarySearch(value) {
	return value.toLocaleLowerCase().replace(/\u064a/g, "ی").replace(/\u0643/g, "ک").replace(/[\u200c\u200e\u200f]/g, "").trim();
}
function initializeLibrary(root) {
	if (root.dataset.libraryInitialized) return;
	root.dataset.libraryInitialized = "true";
	const grid = root.querySelector("[data-library-grid]");
	const overflow = root.querySelector("[data-library-overflow]");
	const cards = [...grid.children, ...overflow ? [...overflow.content.children] : []];
	overflow?.remove();
	const indexed = cards.map((card) => ({
		card,
		text: normalizeLibrarySearch(card.dataset.librarySearchText || ""),
		variants: JSON.parse(card.dataset.libraryVariants)
	}));
	const search = root.querySelector("[data-library-search]");
	const categories = [...root.querySelectorAll("button[data-library-category]")];
	const language = root.querySelector("[data-library-language]");
	const panel = root.querySelector("[data-library-preview-panel]");
	const designs = root.querySelector("[data-library-designs]");
	const designGrid = designs.querySelector("[data-library-design-grid]");
	const frame = panel.querySelector("iframe");
	const variant = panel.querySelector("[data-library-variant]");
	const previewStatus = panel.querySelector("[data-library-preview-status]");
	const status = panel.querySelector("[data-library-status]");
	const insert = panel.querySelector("[data-library-insert]");
	const previous = root.querySelector("[data-library-page=\"previous\"]");
	const next = root.querySelector("[data-library-page=\"next\"]");
	const number = new Intl.NumberFormat(language.value === "fa" ? "fa" : "en");
	const params = new URL(window.location.href).searchParams;
	const editor = root.dataset.editor === "true";
	let page = editor ? 1 : Math.max(1, Number(params.get("library_page")) || 1);
	let category = editor ? "" : params.get("library_category") || "";
	if (!categories.some((button) => button.dataset.libraryCategory === category)) category = "";
	if (!editor) search.value = params.get("library_search") || "";
	let selected;
	let opener;
	let familyEntry;
	let familyOpener;
	let hiddenFamilies = [];
	let hiddenGallery = [];
	let filtering = 0;
	let busy = false;
	let generation = 0;
	let modelRequest;
	const loadModel = root.dataset.libraryModelUrl ? createModelLoader({
		url: root.dataset.libraryModelUrl,
		nonce: root.dataset.libraryModelNonce
	}) : null;
	const syncUrl = () => {
		if (editor) return;
		const url = new URL(window.location.href);
		for (const [name, value] of [
			["library_search", search.value],
			["library_category", category],
			["library_page", page > 1 ? String(page) : ""]
		]) if (value) url.searchParams.set(name, value);
		else url.searchParams.delete(name);
		window.history.replaceState(window.history.state, "", url);
	};
	const filter = () => {
		filtering = 0;
		const query = normalizeLibrarySearch(search.value);
		const matches = indexed.filter((entry) => entry.text.includes(query) && (!category || entry.card.dataset.libraryCategory === category));
		const pages = Math.max(1, Math.ceil(matches.length / 24));
		page = Math.max(1, Math.min(pages, page));
		grid.replaceChildren(...matches.slice((page - 1) * 24, page * 24).map((entry) => entry.card));
		categories.forEach((button) => button.setAttribute("aria-pressed", String(button.dataset.libraryCategory === category)));
		root.querySelector("[data-library-empty]").hidden = matches.length > 0;
		root.querySelector("[data-library-results]").textContent = `${number.format(matches.length)} / ${number.format(cards.length)}`;
		root.querySelector("[data-library-page-label]").textContent = `${number.format(page)} / ${number.format(pages)}`;
		previous.disabled = page === 1;
		next.disabled = page === pages;
		previous.parentElement.hidden = pages === 1;
		syncUrl();
	};
	const closePreview = (restoreFocus = true) => {
		if (!selected) return;
		selected = null;
		generation++;
		modelRequest?.abort();
		frame.removeAttribute("src");
		panel.removeAttribute("aria-busy");
		previewStatus.textContent = "";
		status.textContent = "";
		if (panel.tagName === "DIALOG") {
			if (panel.open) panel.close();
		} else panel.hidden = true;
		for (const [node, hidden] of hiddenGallery) node.hidden = hidden;
		hiddenGallery = [];
		opener?.setAttribute("aria-expanded", "false");
		if (restoreFocus && opener?.isConnected) opener.focus({ preventScroll: true });
	};
	const loadPreview = (reload = false) => {
		if (!selected) return;
		const item = selected.variants.find((item) => item.id === variant.value);
		if (!item) return;
		const url = new URL(item.url, window.location.href);
		if (url.origin !== window.location.origin) return;
		url.searchParams.set("language", language.value);
		if (!reload && frame.getAttribute("src") === url.href) return;
		if (reload) frame.removeAttribute("src");
		panel.setAttribute("aria-busy", "true");
		previewStatus.textContent = previewStatus.dataset.loading;
		if (insert) insert.dataset.libraryInsert = item.id;
		frame.src = url.href;
	};
	const openPreview = (button) => {
		const entry = familyEntry;
		if (!entry) return;
		const card = entry.card;
		opener = button;
		selected = entry;
		panel.querySelector("[data-library-preview-title]").textContent = card.dataset.libraryName;
		panel.querySelector("[data-library-preview-description]").textContent = card.dataset.libraryDescription;
		variant.replaceChildren(...entry.variants.map((item) => {
			const option = document.createElement("option");
			option.value = item.id;
			option.textContent = item.label;
			return option;
		}));
		variant.value = button.dataset.libraryPreview;
		button.setAttribute("aria-expanded", "true");
		if (panel.tagName === "DIALOG") panel.showModal();
		else {
			hiddenGallery = [...root.children].filter((node) => node !== panel).map((node) => [node, node.hidden]);
			hiddenGallery.forEach(([node]) => {
				node.hidden = true;
			});
			panel.hidden = false;
		}
		variant.focus({ preventScroll: true });
		loadPreview();
	};
	const closeFamily = (restoreFocus = true) => {
		closePreview(false);
		designs.hidden = true;
		designGrid.replaceChildren();
		for (const [node, hidden] of hiddenFamilies) node.hidden = hidden;
		hiddenFamilies = [];
		familyEntry = null;
		familyOpener?.setAttribute("aria-expanded", "false");
		if (restoreFocus && familyOpener?.isConnected) familyOpener.focus({ preventScroll: true });
	};
	const openFamily = (button) => {
		const entry = indexed.find((entry) => entry.card.dataset.libraryCard === button.dataset.libraryFamily);
		if (!entry) return;
		familyEntry = entry;
		familyOpener = button;
		designs.querySelector("[data-library-family-title]").textContent = entry.card.dataset.libraryName;
		designs.querySelector("[data-library-family-description]").textContent = entry.card.dataset.libraryDescription;
		designGrid.replaceChildren(...entry.variants.map((item) => {
			const card = document.createElement("article");
			card.className = "rdct-lib__card";
			const action = document.createElement("button");
			action.type = "button";
			action.className = `rdct-lib__family ${editor ? "e-btn e-btn-txt-border" : "button"}`;
			action.dataset.libraryPreview = item.id;
			action.setAttribute("aria-haspopup", "dialog");
			action.setAttribute("aria-expanded", "false");
			if (item.image) {
				const image = document.createElement("img");
				image.src = item.image;
				image.alt = "";
				image.width = 960;
				image.height = 600;
				image.loading = "lazy";
				image.decoding = "async";
				action.append(image);
			} else action.append(entry.card.querySelector("[data-library-icon]").cloneNode(true));
			const title = document.createElement("strong");
			title.textContent = item.label;
			const description = document.createElement("span");
			description.className = "rdct-lib__description";
			description.textContent = item.description;
			action.append(title, description);
			card.append(action);
			return card;
		}));
		hiddenFamilies = [...root.children].filter((node) => node !== designs && node !== panel).map((node) => [node, node.hidden]);
		hiddenFamilies.forEach(([node]) => {
			node.hidden = true;
		});
		designs.hidden = false;
		button.setAttribute("aria-expanded", "true");
		designs.querySelector("[data-library-family-title]").focus({ preventScroll: true });
	};
	frame.addEventListener("load", () => {
		if (!selected || !frame.getAttribute("src")) return;
		let ready = false;
		try {
			ready = frame.contentDocument?.documentElement?.dataset.rdctPreview === "true";
		} catch {}
		panel.removeAttribute("aria-busy");
		previewStatus.textContent = ready ? previewStatus.dataset.ready : previewStatus.dataset.error;
	});
	frame.addEventListener("error", () => {
		panel.removeAttribute("aria-busy");
		previewStatus.textContent = previewStatus.dataset.error;
	});
	panel.addEventListener("close", () => closePreview());
	panel.addEventListener("click", (event) => {
		if (panel.tagName !== "DIALOG" || event.target !== panel) return;
		const box = panel.getBoundingClientRect();
		if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) closePreview();
	});
	variant.addEventListener("change", () => loadPreview());
	language.addEventListener("change", () => {
		if (selected) loadPreview();
	});
	search.addEventListener("input", () => {
		page = 1;
		if (!filtering) filtering = requestAnimationFrame(filter);
	});
	root.addEventListener("click", async (event) => {
		const button = event.target.closest("button");
		if (!button || !root.contains(button)) return;
		if (button.hasAttribute("data-library-family")) {
			openFamily(button);
			return;
		}
		if (button.hasAttribute("data-library-back")) {
			closeFamily();
			return;
		}
		if (button.hasAttribute("data-library-preview")) {
			openPreview(button);
			return;
		}
		if (button.hasAttribute("data-library-category")) {
			category = button.dataset.libraryCategory;
			page = 1;
			filter();
			return;
		}
		if (button.hasAttribute("data-library-page")) {
			page += button.dataset.libraryPage === "next" ? 1 : -1;
			filter();
			grid.scrollIntoView({ block: "nearest" });
			return;
		}
		if (button.hasAttribute("data-library-close-preview")) {
			closePreview();
			return;
		}
		if (button.hasAttribute("data-library-retry")) {
			loadPreview(true);
			return;
		}
		if (button.hasAttribute("data-library-width")) {
			panel.dataset.width = button.dataset.libraryWidth;
			panel.querySelectorAll("[data-library-width]").forEach((item) => item.setAttribute("aria-pressed", String(item === button)));
			return;
		}
		if (!button.hasAttribute("data-library-insert") || busy || !loadModel || !selected) return;
		busy = true;
		button.disabled = true;
		status.textContent = "";
		const ticket = ++generation;
		const documentId = window.elementor?.documents?.getCurrentId?.();
		modelRequest = new AbortController();
		try {
			const mode = panel.querySelector("[data-library-mode]")?.value || "classic";
			const [{ insertSample }, model] = await Promise.all([__vitePreload(() => import("./component-insert-BsFzsEgT.js"), [], import.meta.url), loadModel({
				id: button.dataset.libraryInsert,
				language: language.value,
				mode,
				documentId,
				signal: modelRequest.signal
			})]);
			if (ticket !== generation) return;
			await insertSample(model, window.elementor, window.$e, documentId);
			if (ticket === generation) status.textContent = status.dataset.inserted;
		} catch {
			if (ticket === generation) status.textContent = status.dataset.error;
		} finally {
			busy = false;
			button.disabled = false;
		}
	});
	const cancel = () => {
		closeFamily(false);
		generation++;
		modelRequest?.abort();
		cancelAnimationFrame(filtering);
	};
	root.addEventListener("rdct:library-close", cancel);
	window.addEventListener("pagehide", cancel);
	filter();
}
if (typeof document !== "undefined") document.querySelectorAll("[data-rdct-library]").forEach(initializeLibrary);
//#endregion
export { initializeLibrary, normalizeLibrarySearch };
