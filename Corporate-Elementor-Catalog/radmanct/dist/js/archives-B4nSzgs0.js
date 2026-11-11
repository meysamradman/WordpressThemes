import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
//#region radmanct/src/js/archive-request.js
function formUrl(form, base = location.href) {
	const url = new URL(form.action, base);
	url.search = "";
	for (const [key, value] of new FormData(form)) if (typeof value === "string" && value !== "") url.searchParams.append(key, value);
	url.searchParams.delete("_rdct_fragment");
	url.searchParams.delete("paged");
	url.searchParams.delete("page");
	url.hash = "";
	return url;
}
function createArchiveRequest(scope, receive, fail, fetcher = fetch, prepare = (data) => data) {
	let controller;
	let serial = 0;
	const cancel = () => {
		serial++;
		controller?.abort();
	};
	return {
		cancel,
		async load(url, options = {}) {
			cancel();
			const current = serial;
			controller = new AbortController();
			const activeController = controller;
			const request = new URL(url);
			request.searchParams.set("_rdct_fragment", scope);
			const timeout = setTimeout(() => activeController.abort(), 15e3);
			try {
				const response = await fetcher(request.href, {
					signal: activeController.signal,
					credentials: "same-origin",
					redirect: "error",
					headers: { Accept: "application/json" },
					cache: "no-store"
				});
				if (current !== serial) return;
				if (!response.ok) throw new Error("Archive request failed");
				const body = await response.json();
				if (current !== serial) return;
				if (!body.success || body.data?.scope !== scope || typeof body.data.html !== "string" || typeof body.data.title !== "string") throw new Error("Invalid archive response");
				if (body.data.filters !== void 0 && (!body.data.filters || Array.isArray(body.data.filters) || typeof body.data.filters !== "object" || Object.values(body.data.filters).some((value) => typeof value !== "string"))) throw new Error("Invalid archive filters");
				if (body.data.header !== void 0 && typeof body.data.header !== "string") throw new Error("Invalid archive header");
				if (body.data.intro !== void 0 && typeof body.data.intro !== "string") throw new Error("Invalid archive intro");
				if (body.data.pageTitle !== void 0 && typeof body.data.pageTitle !== "string") throw new Error("Invalid page title");
				const data = await prepare(body.data, activeController.signal);
				if (current !== serial) return;
				receive(data, new URL(url), options);
			} catch (error) {
				if (current === serial) fail(error, new URL(url), options);
			} finally {
				clearTimeout(timeout);
			}
		}
	};
}
//#endregion
//#region radmanct/src/js/archive-styles.js
var pending = /* @__PURE__ */ new Map();
function loadStyle(href, doc) {
	if (pending.has(href)) return pending.get(href);
	const existing = [...doc.querySelectorAll("link[rel=\"stylesheet\"]")].find((link) => link.href === href);
	if (existing?.sheet) return Promise.resolve();
	const link = existing || doc.createElement("link");
	const task = new Promise((resolve, reject) => {
		const cleanup = () => {
			link.removeEventListener("load", ready);
			link.removeEventListener("error", failed);
		};
		const ready = () => {
			cleanup();
			resolve();
		};
		const failed = () => {
			cleanup();
			if (!existing) link.remove();
			pending.delete(href);
			reject(/* @__PURE__ */ new Error("Archive stylesheet failed"));
		};
		link.addEventListener("load", ready);
		link.addEventListener("error", failed);
		if (!existing) {
			link.rel = "stylesheet";
			link.href = href;
			doc.head.append(link);
		}
	});
	pending.set(href, task);
	return task;
}
async function prepareArchiveStyles(data, signal, doc = document) {
	const result = { ...data };
	const tasks = [];
	for (const key of [
		"intro",
		"html",
		"pageTitle"
	]) {
		if (typeof result[key] !== "string") continue;
		const template = doc.createElement("template");
		template.innerHTML = result[key];
		for (const link of template.content.querySelectorAll("link[rel=\"stylesheet\"][href]")) {
			const url = new URL(link.getAttribute("href"), doc.baseURI);
			if (url.origin !== new URL(doc.baseURI).origin) throw new Error("Archive stylesheet must be local");
			tasks.push(loadStyle(url.href, doc));
			link.remove();
		}
		for (const preview of template.content.querySelectorAll("[data-rdct-preview-runtime]")) {
			const url = new URL(preview.getAttribute("data-rdct-preview-runtime"), doc.baseURI);
			if (url.origin !== new URL(doc.baseURI).origin || !/\/dist\/js\/card-preview[^/]*\.min\.js$/.test(url.pathname)) throw new Error("Archive media runtime must be local");
			tasks.push(__vitePreload(() => import(
				/* @vite-ignore */
				url.href
), [], import.meta.url));
		}
		result[key] = template.innerHTML;
	}
	if (signal.aborted) throw new DOMException("Aborted", "AbortError");
	let abort;
	const cancelled = new Promise((_, reject) => {
		abort = () => reject(new DOMException("Aborted", "AbortError"));
		signal.addEventListener("abort", abort, { once: true });
	});
	try {
		await Promise.race([Promise.all(tasks), cancelled]);
	} finally {
		signal.removeEventListener("abort", abort);
	}
	return result;
}
//#endregion
//#region radmanct/src/js/archive-results.js
function appendArchiveResults(view, html) {
	const template = view.ownerDocument.createElement("template");
	template.innerHTML = html;
	const items = view.querySelector("[data-rdct-archive-items]");
	const incoming = template.content.querySelector("[data-rdct-archive-items]");
	const pagination = view.querySelector("[data-rdct-archive-pagination]");
	const next = template.content.querySelector("[data-rdct-archive-pagination]");
	if (!items || !incoming || !pagination || !next || !incoming.children.length) throw new Error("Invalid archive page");
	const ids = new Set([...items.children].map((item) => item.id).filter(Boolean));
	const added = [...incoming.children].filter((item) => !item.id || !ids.has(item.id));
	if (!added.length) throw new Error("Duplicate archive page");
	const focused = pagination.contains(view.ownerDocument.activeElement);
	items.append(...added);
	next.querySelectorAll("[data-rdct-archive-link]").forEach((link) => link.remove());
	next.hidden = !next.querySelector("a");
	pagination.replaceWith(next);
	if (focused) (added[0].querySelector(":is(h2,h3,h4,h5,h6) a") || added[0].querySelector("a:not([tabindex=\"-1\"])"))?.focus({ preventScroll: true });
	return added.length;
}
//#endregion
//#region radmanct/src/js/archives.js
function initializeArchives() {
	const root = document.querySelector("[data-rdct-archive]");
	if (!root || root.dataset.enhanced) return;
	const view = root.querySelector("[data-rdct-archive-view]");
	const status = root.querySelector("[data-rdct-archive-status]");
	const retry = root.querySelector("[data-rdct-archive-retry]");
	if (!view || !status || !retry) return;
	root.dataset.enhanced = "true";
	let timer;
	let failed;
	let composing = false;
	let pending = false;
	const busy = (value) => {
		pending = value;
		view.setAttribute("aria-busy", String(value));
		root.classList.toggle("is-loading", value);
	};
	const transport = createArchiveRequest(root.dataset.rdctArchive, (data, url, options) => {
		if (options.append) {
			appendArchiveResults(view, data.html);
			busy(false);
			retry.hidden = true;
			failed = null;
			status.textContent = root.dataset.moreLoaded || "";
			root.dispatchEvent(new CustomEvent("rdct:archive-updated", {
				bubbles: true,
				detail: {
					scope: root.dataset.rdctArchive,
					filters: data.filters
				}
			}));
			return;
		}
		const active = document.activeElement;
		const titleArea = document.querySelector("[data-rdct-title-area]");
		if (titleArea && typeof data.pageTitle === "string") titleArea.innerHTML = data.pageTitle;
		const field = view.contains(active) && active.name ? {
			name: active.name,
			start: active.selectionStart,
			end: active.selectionEnd
		} : null;
		const inside = view.contains(active);
		const intro = root.querySelector("[data-rdct-archive-intro]");
		if (intro) intro.innerHTML = data.intro || "";
		view.innerHTML = intro ? data.html : (data.intro || "") + data.html;
		document.title = data.title;
		const header = root.querySelector("[data-rdct-archive-header]");
		if (header && typeof data.header === "string") header.outerHTML = data.header;
		if (!options.pop && location.href !== url.href) history.pushState(history.state, "", url.href);
		busy(false);
		retry.hidden = true;
		failed = null;
		status.textContent = view.querySelector("[data-rdct-result-count]")?.textContent || "";
		if (field) {
			const replacement = [...view.querySelectorAll("input, select")].find((entry) => entry.name === field.name);
			replacement?.focus({ preventScroll: true });
			if (replacement?.type === "search" && field.start !== null) replacement.setSelectionRange(field.start, field.end);
		} else if (inside || options.scroll) view.focus({ preventScroll: true });
		if (options.scroll) view.scrollIntoView({
			block: "start",
			behavior: "instant"
		});
		root.dispatchEvent(new CustomEvent("rdct:archive-updated", {
			bubbles: true,
			detail: {
				scope: root.dataset.rdctArchive,
				filters: data.filters
			}
		}));
	}, (error, url, options) => {
		busy(false);
		status.textContent = root.dataset.error;
		retry.hidden = false;
		failed = {
			url,
			options
		};
	}, fetch, prepareArchiveStyles);
	function load(url, options = {}) {
		if (url.origin !== location.origin) return;
		clearTimeout(timer);
		transport.cancel();
		busy(true);
		retry.hidden = true;
		status.textContent = root.dataset.loading;
		transport.load(url, options);
	}
	const form = (target) => target.closest("form[data-rdct-archive-form]");
	root.addEventListener("submit", (event) => {
		const target = form(event.target);
		if (target && target.method.toLowerCase() === "get") {
			event.preventDefault();
			load(formUrl(target));
		}
	});
	root.addEventListener("change", (event) => {
		const target = form(event.target);
		if (target && event.target.tagName === "SELECT") load(formUrl(target));
	});
	root.addEventListener("input", (event) => {
		const target = form(event.target);
		if (!target || event.target.type !== "search") return;
		clearTimeout(timer);
		transport.cancel();
		busy(false);
		status.textContent = "";
		retry.hidden = true;
		if (!composing && !event.isComposing) timer = setTimeout(() => load(formUrl(target)), 350);
	});
	root.addEventListener("compositionstart", (event) => {
		if (event.target.type !== "search" || !form(event.target)) return;
		composing = true;
		clearTimeout(timer);
		transport.cancel();
	});
	root.addEventListener("compositionend", (event) => {
		if (event.target.type !== "search" || !form(event.target)) return;
		composing = false;
		const target = form(event.target);
		if (target) {
			clearTimeout(timer);
			timer = setTimeout(() => load(formUrl(target)), 350);
		}
	});
	root.addEventListener("click", (event) => {
		if (event.target.closest("[data-rdct-archive-retry]")) {
			if (failed) load(failed.url, failed.options);
			return;
		}
		const link = event.target.closest("a[data-rdct-archive-link], a[data-rdct-load-more], .pagination a");
		if (!link || !root.contains(link) || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === "_blank" || link.hasAttribute("download")) return;
		const url = new URL(link.href);
		if (url.origin !== location.origin) return;
		event.preventDefault();
		const append = link.hasAttribute("data-rdct-load-more");
		if (append && pending) return;
		load(url, {
			append,
			scroll: !append && !!link.closest(".pagination")
		});
	});
	window.addEventListener("popstate", () => load(new URL(location.href), { pop: true }));
	window.addEventListener("pagehide", () => {
		clearTimeout(timer);
		transport.cancel();
		busy(false);
		status.textContent = "";
	});
}
//#endregion
export { initializeArchives };
