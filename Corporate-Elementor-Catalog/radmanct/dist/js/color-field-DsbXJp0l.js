const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["../css/color-picker-DPV2Huqa.css"])))=>i.map(i=>d[i]);
import { t as __vitePreload } from "./preload-helper-CRqqVDM6.js";
import { t as ButtonControl } from "./controls-DqGpx29l.js";
//#region shared/admin/color-field.js
var { createElement: h, useState, useEffect, useRef } = window.wp.element;
var colors = [
	"#000000",
	"#ffffff",
	"#c83252",
	"#f97316",
	"#eab308",
	"#16a34a",
	"#0891b2",
	"#3155ff",
	"#9333ea"
];
var vendor;
var loadPicker = () => vendor ?? (vendor = __vitePreload(() => import("./color-picker-4v6oZUVS.js"), __vite__mapDeps([0]), import.meta.url).catch((error) => {
	vendor = void 0;
	throw error;
}));
function ColorField({ label, value = "", fallback = "#182522", resetLabel, onChange, id, showLabel = true }) {
	const button = useRef(null);
	const picker = useRef(null);
	const pending = useRef(false);
	const active = useRef(true);
	const latest = useRef({
		value,
		fallback,
		onChange
	});
	latest.current = {
		value,
		fallback,
		onChange
	};
	const [open, setOpen] = useState(false);
	const [failed, setFailed] = useState(false);
	useEffect(() => {
		active.current = true;
		return () => {
			active.current = false;
			picker.current?.destroy();
			picker.current = null;
		};
	}, []);
	useEffect(() => {
		picker.current?.setColor(value || fallback);
	}, [value, fallback]);
	const show = async () => {
		if (picker.current) {
			picker.current.show();
			return;
		}
		if (pending.current) return;
		pending.current = true;
		const target = button.current;
		try {
			const { createColorPicker } = await loadPicker();
			if (!active.current || button.current !== target || !target?.isConnected) return;
			picker.current = createColorPicker(target, {
				value: latest.current.value || latest.current.fallback,
				colors,
				label,
				resetLabel,
				onChange: (next) => {
					if (active.current && /^#[a-f\d]{6}$/i.test(next)) latest.current.onChange(next);
				},
				onOpen: () => active.current && setOpen(true),
				onClose: () => active.current && setOpen(false)
			});
			picker.current.show();
		} catch {
			if (active.current) setFailed(true);
		} finally {
			pending.current = false;
		}
	};
	return h("div", { className: "rdct-clr-field" }, showLabel && h("span", null, label), h("div", null, h("button", {
		ref: button,
		type: "button",
		id,
		"aria-label": label,
		title: label,
		"aria-haspopup": "dialog",
		"aria-expanded": open,
		onClick: show,
		className: "rdct-btn rdct-clr-field__trigger",
		style: { "--rdct-picker-color": value || fallback }
	}, h("span", {
		"aria-hidden": true,
		className: "rdct-clr-field__swatch"
	})), failed && h("input", {
		type: "color",
		value: value || fallback,
		"aria-label": label,
		onChange: (event) => onChange(event.target.value)
	}), h(ButtonControl, {
		icon: "image-rotate",
		iconOnly: true,
		label: resetLabel,
		disabled: !value,
		onClick: () => onChange("")
	})));
}
//#endregion
export { ColorField as t };
