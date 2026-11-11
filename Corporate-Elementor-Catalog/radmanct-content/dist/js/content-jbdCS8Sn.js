const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["../css/product-fields-Czi8UuiS.css"])))=>i.map(i=>d[i]);
//#region shared/admin/controls.js
var { createElement: h$1 } = window.wp.element;
function SelectControl({ value, options, onChange, numeric = false, ...attributes }) {
	return h$1("select", {
		...attributes,
		value: value ?? "",
		onChange: (event) => onChange(numeric && event.target.value !== "" ? Number(event.target.value) : event.target.value)
	}, ...options.map(([key, label, disabled = false]) => h$1("option", {
		key,
		value: key,
		disabled
	}, label)));
}
function InputControl({ value, onChange, numeric = false, type = "text", ...attributes }) {
	return h$1("input", {
		...attributes,
		type,
		value: value ?? "",
		onChange: (event) => onChange(numeric && event.target.value !== "" ? Number(event.target.value) : event.target.value)
	});
}
function TextareaControl({ value, onChange, ...attributes }) {
	return h$1("textarea", {
		...attributes,
		value: value ?? "",
		onChange: (event) => onChange(event.target.value)
	});
}
//#endregion
//#region \0vite/preload-helper.js
var scriptRel = /* @__PURE__ */ (function detectScriptRel() {
	const relList = typeof document !== "undefined" && document.createElement("link").relList;
	return relList && relList.supports && relList.supports("modulepreload") ? "modulepreload" : "preload";
})();
var assetsURL = function(dep, importerUrl) {
	return new URL(dep, importerUrl).href;
};
var seen = {};
var isCssPreloadUrl = function isCssPreloadUrl(url) {
	return url.pathname.endsWith(".css");
};
var preloadOnce = function preloadOnce(seen, href, preload) {
	if (href in seen) return seen[href];
	const promise = preload();
	if (!promise) {
		seen[href] = void 0;
		return;
	}
	const preloadPromise = promise.then(() => {
		seen[href] = void 0;
	}, (err) => {
		seen[href] = void 0;
		throw err;
	});
	seen[href] = preloadPromise;
	return preloadPromise;
};
var __vitePreload = function preload(baseModule, deps, importerUrl) {
	let promise = Promise.resolve();
	if (deps && deps.length > 0) {
		let preloadedHrefs;
		const cspNonceMeta = document.querySelector("meta[property=csp-nonce]");
		const cspNonce = cspNonceMeta?.nonce || cspNonceMeta?.getAttribute("nonce");
		function allSettled(promises) {
			return Promise.all(promises.map((p) => Promise.resolve(p).then((value) => ({
				status: "fulfilled",
				value
			}), (reason) => ({
				status: "rejected",
				reason
			}))));
		}
		function importMetaResolve(specifier) {
			if (import.meta.resolve) return new URL(import.meta.resolve(specifier));
			return new URL(
				specifier,
				/** #__KEEP__ */
				import.meta.url
			);
		}
		promise = allSettled(deps.map((depString) => {
			depString = assetsURL(depString, importerUrl);
			const dep = importMetaResolve(depString);
			const isCss = isCssPreloadUrl(dep);
			return preloadOnce(seen, dep.href, () => {
				if (preloadedHrefs === void 0) {
					preloadedHrefs = {
						all: /* @__PURE__ */ new Set(),
						styles: /* @__PURE__ */ new Set()
					};
					const links = document.getElementsByTagName("link");
					for (let i = links.length - 1; i >= 0; i--) {
						const link = links[i];
						preloadedHrefs.all.add(link.href);
						if (link.rel === "stylesheet") preloadedHrefs.styles.add(link.href);
					}
				}
				if ((isCss ? preloadedHrefs.styles : preloadedHrefs.all).has(dep.href)) return;
				const link = document.createElement("link");
				link.rel = isCss ? "stylesheet" : scriptRel;
				if (!isCss) link.as = "script";
				link.crossOrigin = "";
				link.href = dep.href;
				if (cspNonce) link.setAttribute("nonce", cspNonce);
				document.head.appendChild(link);
				if (isCss) return new Promise((res, rej) => {
					link.addEventListener("load", res);
					link.addEventListener("error", () => rej(/* @__PURE__ */ new Error(`Unable to preload CSS for ${dep}`)));
				});
			});
		}).filter((p) => p !== void 0));
	}
	function handlePreloadError(err) {
		const e = new Event("vite:preloadError", { cancelable: true });
		e.payload = err;
		window.dispatchEvent(e);
		if (!e.defaultPrevented) throw err;
	}
	return promise.then((res) => {
		for (const item of res || []) {
			if (item.status !== "rejected") continue;
			handlePreloadError(item.reason);
		}
		return baseModule().catch(handlePreloadError);
	});
};
//#endregion
//#region radmanct-content/src/js/admin/content.js
var { createElement: h, createRoot, useState, useEffect, useRef } = window.wp.element;
var config = window.rdctContent;
var labels = config.labels;
var button = (text, onClick, disabled = false) => h("button", {
	type: "button",
	className: "button",
	onClick,
	disabled
}, text);
function App() {
	const [data, setData] = useState(config.data);
	const [images, setImages] = useState(config.images);
	const [ProductFields, setProductFields] = useState(null);
	const [specsError, setSpecsError] = useState(false);
	useEffect(() => {
		let current = true;
		if (config.product) __vitePreload(() => import("./product-fields-CBueDBF7.js").then((module) => {
			if (current) setProductFields(() => module.ProductFields);
		}), __vite__mapDeps([0]), import.meta.url).catch(() => {
			if (current) setSpecsError(true);
		});
		return () => {
			current = false;
		};
	}, []);
	const field = useRef(null);
	const frame = useRef(null);
	const currentData = useRef(data);
	currentData.current = data;
	const serialized = JSON.stringify(data);
	useEffect(() => {
		field.current?.dispatchEvent(new Event("change", { bubbles: true }));
	}, [serialized]);
	useEffect(() => () => {
		frame.current?.off();
		frame.current?.close();
	}, []);
	function selectImages() {
		if (frame.current) {
			frame.current.open();
			return;
		}
		const picker = window.wp.media({
			title: labels.gallery,
			library: { type: "image" },
			button: { text: labels.addImages },
			multiple: true
		});
		frame.current = picker;
		picker.on("open", () => {
			const selection = picker.state().get("selection");
			selection.reset();
			currentData.current.gallery.forEach((id) => selection.add(window.wp.media.attachment(id)));
		});
		picker.on("select", () => {
			const selected = picker.state().get("selection").toJSON().filter((image) => image.type === "image").slice(0, 20);
			setImages((current) => ({
				...current,
				...Object.fromEntries(selected.map((image) => [image.id, image.sizes?.thumbnail?.url || image.url]))
			}));
			setData((current) => ({
				...current,
				gallery: selected.map((image) => image.id)
			}));
		});
		picker.open();
	}
	function move(key, index, offset) {
		setData((current) => {
			const values = [...current[key]];
			if (index + offset < 0 || index + offset >= values.length) return current;
			values.splice(index + offset, 0, values.splice(index, 1)[0]);
			return {
				...current,
				[key]: values
			};
		});
	}
	return h("div", { className: "rdct-cnt-editor" }, h("input", {
		ref: field,
		type: "hidden",
		name: "radman_content_data",
		value: serialized
	}), config.product && h("section", { className: "rdct-cnt-editor__section" }, h("label", { className: "rdct-cnt-editor__field" }, labels.code, h(InputControl, {
		dir: "auto",
		value: data.code,
		maxLength: 40,
		onChange: (value) => setData({
			...data,
			code: value
		})
	}))), h("section", {
		className: "rdct-cnt-editor__section",
		"aria-labelledby": "rdct-gallery-title"
	}, h("div", { className: "rdct-cnt-editor__heading" }, h("h3", { id: "rdct-gallery-title" }, labels.gallery), button(labels.addImages, selectImages)), h("p", { className: "description" }, labels.galleryLimit), !data.gallery.length && h("p", null, labels.empty), h("ul", { className: "rdct-cnt-editor__gallery" }, ...data.gallery.map((id, index) => h("li", { key: id }, images[id] ? h("img", {
		src: images[id],
		alt: "",
		width: 120,
		height: 100
	}) : h("span", null, `#${id}`), h("div", { className: "rdct-cnt-editor__actions" }, button(labels.up, () => move("gallery", index, -1), index === 0), button(labels.down, () => move("gallery", index, 1), index === data.gallery.length - 1), button(labels.remove, () => setData((current) => ({
		...current,
		gallery: current.gallery.filter((value) => value !== id)
	})))))))), config.product && ProductFields && h(ProductFields, {
		data,
		setData,
		move,
		labels,
		contactAvailable: config.contactAvailable,
		contactSettings: config.contactSettings
	}), specsError && h("p", { role: "alert" }, labels.productError));
}
var root = document.getElementById("rdct-content-root");
if (root) createRoot(root).render(h(App));
//#endregion
export { SelectControl as n, TextareaControl as r, InputControl as t };
