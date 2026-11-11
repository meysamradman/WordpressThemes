//#region radmanct/src/js/integrations/custom-css-compiler.js
function compileCustomCss(value, id) {
	if (value && typeof value === "object") value = value.$$type === "string" && !value.disabled ? value.value : "";
	if (typeof value !== "string" || new TextEncoder().encode(value).length > 16384 || !/^[a-zA-Z0-9_-]{1,80}$/.test(id) || /\0|<\/\s*(?:style|script)\b/i.test(value)) return null;
	const css = value.replace(/^[ \t\r\n\v\f]+|[ \t\r\n\v\f]+$/g, "");
	if (!css) return null;
	let output = "";
	const stack = [];
	for (let i = 0; i < css.length; i++) {
		const char = css[i];
		if (char === "/" && css[i + 1] === "*") {
			const end = css.indexOf("*/", i + 2);
			if (end === -1) return null;
			output += " ";
			i = end + 1;
			continue;
		}
		if (char === "\"" || char === "'") {
			const start = i;
			while (++i < css.length) {
				if (css[i] === "\\") {
					i++;
					continue;
				}
				if (css[i] === char) break;
			}
			if (i >= css.length) return null;
			output += css.slice(start, i + 1);
			continue;
		}
		if (char === "\\" || /^(?:(?:-webkit-)?image-set|src)\s*\(/i.test(css.slice(i))) return null;
		if (char === "@" && !/^@(media|supports|container|starting-style)\b/i.test(css.slice(i))) return null;
		if ("{([".includes(char)) stack.push(char);
		if ("})]".includes(char) && stack.pop() !== {
			"}": "{",
			")": "(",
			"]": "["
		}[char]) return null;
		if (char === "s" && !stack.includes("[") && !/[a-zA-Z0-9_.#-]/.test(css[i - 1] ?? "") && /^selector(?![a-zA-Z0-9_-]|\s*\()/.test(css.slice(i)) && /^[^{};]*\{/.test(css.slice(i + 8))) {
			output += "&";
			i += 7;
			continue;
		}
		output += char;
	}
	if (stack.length) return null;
	for (const match of output.matchAll(/url\(\s*(['"]?)(.*?)\1\s*\)/gis)) if (!/^(?:\/(?!\/)|\.{1,2}\/|[a-zA-Z0-9_-])[a-zA-Z0-9_./?%=&#+~-]*$/.test(match[2].trim())) return null;
	let hash = 2166136261;
	for (const byte of new TextEncoder().encode(css)) hash = Math.imul(hash ^ byte, 16777619) >>> 0;
	const className = `rdct-css-${id}-${hash.toString(16).padStart(8, "0")}`;
	return {
		class: className,
		css: `.${className}{${output}\n}`
	};
}
//#endregion
//#region radmanct/src/js/integrations/elementor-custom-css-preview.js
function connectCustomCssPreview(document, editor) {
	const subscriptions = /* @__PURE__ */ new Map();
	let frame = 0;
	let active = true;
	const scan = () => {
		frame = 0;
		if (!active) return;
		for (const [root, subscription] of subscriptions) if (!root.isConnected) {
			subscription.detach();
			subscriptions.delete(root);
		}
		for (const root of document.querySelectorAll(".elementor-element[data-id], [data-element_type][data-id]")) {
			const id = root.getAttribute("data-id");
			let settings;
			try {
				settings = editor?.getContainer?.(id)?.settings;
			} catch {
				continue;
			}
			if (!settings?.get || !settings.on || !settings.off || subscriptions.get(root)?.settings === settings) continue;
			subscriptions.get(root)?.detach();
			let style;
			const sync = () => {
				if (!active || !root.isConnected) return;
				const result = compileCustomCss(settings.get("rdct_custom_css"), id);
				for (const name of [...root.classList]) if (name.startsWith(`rdct-css-${id}-`)) root.classList.remove(name);
				if (!result) {
					style?.remove();
					style = null;
					return;
				}
				root.classList.add(result.class);
				if (!style) {
					style = document.createElement("style");
					document.head.append(style);
				}
				style.textContent = result.css;
			};
			settings.on("change:rdct_custom_css", sync);
			subscriptions.set(root, {
				settings,
				detach: () => {
					settings.off("change:rdct_custom_css", sync);
					style?.remove();
				}
			});
			sync();
		}
	};
	const queue = () => {
		if (active && !frame) frame = requestAnimationFrame(scan);
	};
	const observer = new MutationObserver(queue);
	observer.observe(document.body, {
		childList: true,
		subtree: true,
		attributes: true,
		attributeFilter: ["data-id"]
	});
	editor?.on?.("document:loaded", queue);
	window.addEventListener("load", queue);
	scan();
	return () => {
		active = false;
		observer.disconnect();
		if (frame) cancelAnimationFrame(frame);
		for (const subscription of subscriptions.values()) subscription.detach();
		subscriptions.clear();
		editor?.off?.("document:loaded", queue);
		window.removeEventListener("load", queue);
	};
}
if (typeof window !== "undefined" && window.parent !== window) {
	const cleanup = connectCustomCssPreview(document, window.parent.elementor);
	window.addEventListener("pagehide", cleanup, { once: true });
}
//#endregion
export { connectCustomCssPreview };
