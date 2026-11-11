import { n as reportRuntimeError } from "./lifecycle-CHQMXjJa.js";
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { n as normalizePickerColor } from "./atomic-color-control-DA5gYLoc.js";
//#region radmanct-components/src/js/integrations/atomic-component-preview.js
function normalizeAtomicComponent(type, input, fields) {
	const values = {};
	for (const [key, field] of Object.entries(fields[type] || {})) {
		const value = input[key];
		if (typeof field.default === "boolean") values[key] = value === true || value === "true" ? true : value === false || value === "false" ? false : field.default;
		else if (typeof field.default === "number") values[key] = !["number", "string"].includes(typeof value) || typeof value === "string" && !value.trim() || !Number.isFinite(Number(value)) ? field.default : Math.min(field.max, Math.max(field.min, Number(value)));
		else values[key] = typeof value === "string" ? value : field.default;
		if (field.options && !Object.hasOwn(field.options, values[key])) values[key] = field.default;
	}
	return values;
}
function renderAtomicComponent(marker, config, initialize) {
	const doc = marker.ownerDocument;
	const type = marker.dataset.rdctAtomicComponent;
	const settings = normalizeAtomicComponent(type, marker.dataset, config.fields);
	let active = true;
	let cleanup;
	marker.replaceChildren();
	if (type === "icon") {
		const svgText = config.icons[settings.name];
		if (!svgText) return () => {};
		const parsed = new doc.defaultView.DOMParser().parseFromString(svgText, "image/svg+xml");
		const svg = doc.importNode(parsed.documentElement, true);
		if (svg.localName !== "svg") return () => {};
		svg.setAttribute("width", settings.size);
		svg.setAttribute("height", settings.size);
		svg.setAttribute("stroke-width", settings.stroke);
		if (!settings.mirror) svg.classList.remove("rdct-ico--directional");
		const label = settings.label.replace(/<[^>]*>/g, "").replace(/\s+/g, " ").trim();
		if (label) {
			svg.removeAttribute("aria-hidden");
			svg.setAttribute("role", "img");
			svg.setAttribute("aria-label", label);
		}
		marker.append(svg);
	} else if (type === "text-reveal" && settings.text.trim() && new TextEncoder().encode(settings.text).length <= 16e3) {
		const root = doc.createElement(settings.tag);
		root.className = "rdct-tr";
		root.textContent = settings.text;
		const mode = settings.mode === "scroll" && settings.text.split(/\s+/u).filter(Boolean).length > 400 ? "static" : settings.mode;
		root.dataset.rdctTextReveal = mode;
		for (const [key, name] of Object.entries({
			opacity: "initialOpacity",
			final_opacity: "finalOpacity",
			start: "revealStart",
			end: "revealEnd",
			from: "revealFrom",
			scrub: "revealScrub",
			stagger: "revealStagger"
		})) root.dataset[name] = settings[key];
		for (const key of ["idle", "active"]) root.style.setProperty(`--rdct-text-${key}`, normalizePickerColor(settings[key]) ?? config.fields[type][key].default);
		if (settings.direction !== "inherit") root.setAttribute("dir", settings.direction);
		if (mode === "hover") root.tabIndex = 0;
		marker.append(root);
		if (mode === "scroll") initialize().then(({ initializeTextReveal }) => {
			if (active && marker.isConnected) cleanup = initializeTextReveal(root);
		}).catch((error) => reportRuntimeError("atomic-text-preview", error));
	}
	return () => {
		active = false;
		cleanup?.();
	};
}
function observeAtomicComponents(doc, config, initialize, schedule = requestAnimationFrame, cancel = cancelAnimationFrame) {
	const instances = /* @__PURE__ */ new Map();
	let frame = 0;
	let active = true;
	const update = () => {
		frame = 0;
		for (const [marker, entry] of instances) if (!marker.isConnected) {
			entry.cleanup();
			instances.delete(marker);
		}
		for (const marker of doc.querySelectorAll("[data-rdct-atomic-component]")) {
			const signature = JSON.stringify(marker.dataset);
			if (instances.get(marker)?.signature === signature) continue;
			instances.get(marker)?.cleanup();
			instances.set(marker, {
				signature,
				cleanup: renderAtomicComponent(marker, config, initialize)
			});
		}
	};
	const observer = new doc.defaultView.MutationObserver(() => {
		if (active && !frame) frame = schedule(update);
	});
	observer.observe(doc.documentElement, {
		childList: true,
		subtree: true,
		attributes: true,
		attributeFilter: ["data-rdct-atomic-component", ...new Set(Object.values(config.fields).flatMap((fields) => Object.keys(fields).map((key) => `data-${key}`)))]
	});
	update();
	return () => {
		if (!active) return;
		active = false;
		observer.disconnect();
		if (frame) cancel(frame);
		for (const entry of instances.values()) entry.cleanup();
		instances.clear();
	};
}
//#endregion
//#region radmanct-components/src/js/integrations/atomic-preview-observer.js
var observationPools = /* @__PURE__ */ new WeakMap();
function subscribe(doc, update) {
	let pool = observationPools.get(doc);
	if (!pool) {
		const callbacks = /* @__PURE__ */ new Set();
		pool = {
			callbacks,
			frame: 0,
			observer: null
		};
		pool.observer = new doc.defaultView.MutationObserver(() => {
			if (pool.frame || !callbacks.size) return;
			pool.frame = doc.defaultView.requestAnimationFrame(() => {
				pool.frame = 0;
				for (const callback of [...callbacks]) if (callbacks.has(callback)) try {
					callback();
				} catch (error) {
					reportRuntimeError("atomic-preview", error, doc.defaultView);
				}
			});
		});
		pool.observer.observe(doc.documentElement, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: ["data-items", "data-options"]
		});
		observationPools.set(doc, pool);
	}
	pool.callbacks.add(update);
	return () => {
		pool.callbacks.delete(update);
		if (pool.callbacks.size) return;
		pool.observer.disconnect();
		if (pool.frame) doc.defaultView.cancelAnimationFrame(pool.frame);
		observationPools.delete(doc);
	};
}
function observePreviewMarkers(doc, selector, render) {
	const instances = /* @__PURE__ */ new Map();
	let alive = true;
	const update = () => {
		if (!alive) return;
		const markers = new Set(doc.querySelectorAll(selector));
		for (const [marker, entry] of instances) if (!marker.isConnected || !markers.has(marker)) {
			entry.cleanup();
			instances.delete(marker);
		}
		markers.forEach((marker) => {
			const signature = JSON.stringify(marker.dataset);
			if (instances.get(marker)?.signature === signature) return;
			instances.get(marker)?.cleanup();
			instances.set(marker, {
				signature,
				cleanup: render(marker)
			});
		});
	};
	const unsubscribe = subscribe(doc, update);
	update();
	return () => {
		if (!alive) return;
		alive = false;
		unsubscribe();
		instances.forEach(({ cleanup }) => cleanup());
		instances.clear();
	};
}
var stylePools = /* @__PURE__ */ new WeakMap();
function previewStyle(doc, url) {
	if (!url) return () => {};
	if (!stylePools.has(doc)) stylePools.set(doc, /* @__PURE__ */ new Map());
	const pool = stylePools.get(doc);
	if (!pool.has(url)) {
		let link = [...doc.querySelectorAll("link[rel=\"stylesheet\"]")].find((entry) => entry.href === url);
		const owned = !link;
		if (!link) {
			link = doc.createElement("link");
			link.rel = "stylesheet";
			link.href = url;
			doc.head.append(link);
		}
		pool.set(url, {
			link,
			owned,
			count: 0
		});
	}
	const entry = pool.get(url);
	++entry.count;
	let active = true;
	return () => {
		if (!active) return;
		active = false;
		if (--entry.count === 0) {
			if (entry.owned) entry.link.remove();
			pool.delete(url);
		}
	};
}
//#endregion
//#region radmanct-components/src/js/integrations/atomic-render-preview.js
function observeRenderedPreviews(doc, config, type, initialize = () => () => {}) {
	return observePreviewMarkers(doc, `[data-rdct-atomic-${type}]`, (marker) => {
		let alive = true;
		let cleanup;
		const releases = [];
		const controller = new AbortController();
		const timer = setTimeout(async () => {
			try {
				const body = new URLSearchParams({
					action: "rdct_component_render",
					component: type,
					_ajax_nonce: config.renderNonce,
					settings: marker.dataset.options || "{}"
				});
				const response = await fetch(config.renderUrl, {
					method: "POST",
					body,
					credentials: "same-origin",
					signal: controller.signal
				});
				const result = await response.json();
				if (!alive) return;
				if (!response.ok || !result.success) throw new Error("Preview failed");
				const parsed = new doc.defaultView.DOMParser().parseFromString(result.data.html, "text/html");
				parsed.querySelectorAll("script, iframe, object, embed").forEach((node) => node.remove());
				marker.replaceChildren(...[...parsed.head.querySelectorAll("style"), ...parsed.body.childNodes].map((node) => doc.importNode(node, true)));
				for (const style of result.data.styles || []) releases.push(previewStyle(doc, style));
				cleanup = initialize(marker);
			} catch (error) {
				if (alive && error.name !== "AbortError") marker.textContent = config.renderError;
			}
		}, 200);
		return () => {
			alive = false;
			clearTimeout(timer);
			controller.abort();
			cleanup?.();
			releases.forEach((release) => release());
		};
	});
}
//#endregion
//#region radmanct-components/src/js/integrations/atomic-sticky-sections-preview.js
function observeStickySectionPreviews(doc, config) {
	return observeRenderedPreviews(doc, config, "sticky-sections");
}
//#endregion
//#region radmanct-components/src/js/integrations/elementor-atomic-components.js
var config = window.rdctAtomicComponents;
if (config?.fields && config?.icons) {
	const stickyCleanup = observeStickySectionPreviews(document, config);
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) stickyCleanup();
	});
	const buttonCleanup = observeRenderedPreviews(document, config, "button", (marker) => {
		let alive = true;
		let cleanup;
		__vitePreload(async () => {
			const { initializeButtons } = await import("./button-DNJYImMI.js");
			return { initializeButtons };
		}, [], import.meta.url).then(({ initializeButtons }) => {
			if (alive) cleanup = initializeButtons(marker);
		}).catch((error) => reportRuntimeError("button-preview", error));
		return () => {
			alive = false;
			cleanup?.();
		};
	});
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) buttonCleanup();
	});
	for (const type of ["taxonomy-list", "archive-search"]) {
		const cleanup = observeRenderedPreviews(document, config, type);
		window.addEventListener("pagehide", (event) => {
			if (!event.persisted) cleanup();
		});
	}
	const accordionCleanup = observeRenderedPreviews(document, config, "accordion");
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) accordionCleanup();
	});
	const timelineCleanup = observeRenderedPreviews(document, config, "timeline");
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) timelineCleanup();
	});
	const testimonialCleanup = observeRenderedPreviews(document, config, "testimonial");
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) testimonialCleanup();
	});
	const particlesCleanup = observeRenderedPreviews(document, config, "particles", (marker) => {
		let active = true;
		let cleanup;
		__vitePreload(async () => {
			const { initializeParticleGroups } = await import("./particles-Dn3QTOLy.js");
			return { initializeParticleGroups };
		}, [], import.meta.url).then(({ initializeParticleGroups }) => {
			if (active) cleanup = initializeParticleGroups(marker);
		}).catch((error) => reportRuntimeError("atomic-preview", error));
		return () => {
			active = false;
			cleanup?.();
		};
	});
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) particlesCleanup();
	});
	const carouselCleanup = observeRenderedPreviews(document, config, "carousel", (marker) => {
		let active = true;
		let cleanup;
		__vitePreload(async () => {
			const { initializeCarousels } = await import("./carousel-entry-CCr7WeWc.js");
			return { initializeCarousels };
		}, [], import.meta.url).then(({ initializeCarousels }) => {
			if (active) cleanup = initializeCarousels(marker);
		}).catch((error) => reportRuntimeError("atomic-preview", error));
		return () => {
			active = false;
			cleanup?.();
		};
	});
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) carouselCleanup();
	});
	const textCleanup = observeRenderedPreviews(document, config, "text-reveal", (marker) => {
		let active = true;
		let cleanup;
		__vitePreload(async () => {
			const { initializeTexts } = await import("./text-fUUJkCIq.js");
			return { initializeTexts };
		}, [], import.meta.url).then(({ initializeTexts }) => {
			if (active) cleanup = initializeTexts(marker);
		}).catch((error) => reportRuntimeError("atomic-preview", error));
		return () => {
			active = false;
			cleanup?.();
		};
	});
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) textCleanup();
	});
	const iconCleanup = observeRenderedPreviews(document, config, "icon");
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) iconCleanup();
	});
	const tableCleanup = observeRenderedPreviews(document, config, "table");
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) tableCleanup();
	});
	const cleanup = observeAtomicComponents(document, config, () => __vitePreload(async () => {
		const { loadTextReveal } = await import("./text-reveal-entry-BwiRBWHI.js");
		return { loadTextReveal };
	}, [], import.meta.url).then(({ loadTextReveal }) => loadTextReveal()));
	let imageAlive = true;
	let imageCleanup;
	let imagePending = false;
	const loadImage = () => {
		if (imagePending || !document.querySelector("[data-rdct-atomic-image]")) return;
		imagePending = true;
		__vitePreload(async () => {
			const { observeImagePreviews } = await import("./atomic-image-preview-o17Ki5IV.js");
			return { observeImagePreviews };
		}, [], import.meta.url).then(({ observeImagePreviews }) => {
			if (imageAlive) {
				imageWatcher.disconnect();
				imageCleanup = observeImagePreviews(document, config);
			}
		}).catch((error) => {
			imagePending = false;
			reportRuntimeError("atomic-preview", error);
		});
	};
	const imageWatcher = new MutationObserver(loadImage);
	imageWatcher.observe(document.documentElement, {
		childList: true,
		subtree: true
	});
	loadImage();
	let iconBoxAlive = true;
	let iconBoxCleanup;
	let iconBoxPending = false;
	const loadIconBox = () => {
		if (iconBoxPending || !document.querySelector("[data-rdct-atomic-icon-box]")) return;
		iconBoxPending = true;
		__vitePreload(async () => {
			const { observeIconBoxPreviews } = await import("./atomic-icon-box-preview-C_RacVve.js");
			return { observeIconBoxPreviews };
		}, [], import.meta.url).then(({ observeIconBoxPreviews }) => {
			if (iconBoxAlive) {
				iconBoxWatcher.disconnect();
				iconBoxCleanup = observeIconBoxPreviews(document, config);
			}
		}).catch((error) => {
			iconBoxPending = false;
			reportRuntimeError("atomic-preview", error);
		});
	};
	const iconBoxWatcher = new MutationObserver(loadIconBox);
	iconBoxWatcher.observe(document.documentElement, {
		childList: true,
		subtree: true
	});
	loadIconBox();
	let flipAlive = true;
	let flipCleanup;
	let flipPending = false;
	const loadFlip = () => {
		if (flipPending || !document.querySelector("[data-rdct-atomic-flipbox]")) return;
		flipPending = true;
		__vitePreload(async () => {
			const { observeFlipBoxPreviews } = await import("./atomic-flipbox-preview-Bnu_iByR.js");
			return { observeFlipBoxPreviews };
		}, [], import.meta.url).then(({ observeFlipBoxPreviews }) => {
			if (flipAlive) {
				flipWatcher.disconnect();
				flipCleanup = observeFlipBoxPreviews(document, config);
			}
		}).catch((error) => {
			flipPending = false;
			reportRuntimeError("atomic-preview", error);
		});
	};
	const flipWatcher = new MutationObserver(loadFlip);
	flipWatcher.observe(document.documentElement, {
		childList: true,
		subtree: true
	});
	loadFlip();
	let boxAlive = true;
	let boxCleanup;
	let boxPending = false;
	const loadBox = () => {
		if (boxPending || !document.querySelector("[data-rdct-atomic-box]")) return;
		boxPending = true;
		__vitePreload(async () => {
			const { observeBoxPreviews } = await import("./atomic-box-preview-De5PhiRA.js");
			return { observeBoxPreviews };
		}, [], import.meta.url).then(({ observeBoxPreviews }) => {
			if (boxAlive) {
				boxWatcher.disconnect();
				boxCleanup = observeBoxPreviews(document, config);
			}
		}).catch((error) => {
			boxPending = false;
			reportRuntimeError("atomic-preview", error);
		});
	};
	const boxWatcher = new MutationObserver(loadBox);
	boxWatcher.observe(document.documentElement, {
		childList: true,
		subtree: true
	});
	loadBox();
	let alive = true;
	let curtainCleanup;
	let pending = false;
	const loadCurtain = () => {
		if (pending || !document.querySelector("[data-rdct-atomic-curtain]")) return;
		pending = true;
		__vitePreload(async () => {
			const { observeCurtainPreviews } = await import("./atomic-curtain-preview-g2eVN1Xa.js");
			return { observeCurtainPreviews };
		}, [], import.meta.url).then(({ observeCurtainPreviews }) => {
			if (alive) {
				watcher.disconnect();
				curtainCleanup = observeCurtainPreviews(document, config);
			}
		}).catch((error) => {
			pending = false;
			reportRuntimeError("atomic-preview", error);
		});
	};
	const watcher = new MutationObserver(loadCurtain);
	watcher.observe(document.documentElement, {
		childList: true,
		subtree: true
	});
	loadCurtain();
	window.addEventListener("pagehide", (event) => {
		if (!event.persisted) {
			alive = false;
			imageAlive = false;
			imageWatcher.disconnect();
			imageCleanup?.();
			boxAlive = false;
			iconBoxAlive = false;
			iconBoxWatcher.disconnect();
			iconBoxCleanup?.();
			flipAlive = false;
			flipWatcher.disconnect();
			flipCleanup?.();
			boxWatcher.disconnect();
			watcher.disconnect();
			cleanup();
			curtainCleanup?.();
			boxCleanup?.();
		}
	});
}
//#endregion
export { observeRenderedPreviews as t };
