//#region shared/admin/icon-picker.js
var { createElement: h$1, createPortal, useState, useRef, useEffect } = window.wp.element;
function iconMatches(icons, search) {
	const words = search.trim().toLocaleLowerCase().split(/\s+/u).filter(Boolean);
	return Object.entries(icons).filter(([name, icon]) => words.every((word) => `${name} ${icon.label}`.toLocaleLowerCase().includes(word)));
}
function IconPicker({ value = "", icons, onChange, labels, disabled = false, renderTrigger }) {
	const [open, setOpen] = useState(false);
	const [search, setSearch] = useState("");
	const [limit, setLimit] = useState(160);
	const dialog = useRef(null);
	const input = useRef(null);
	const opener = useRef(null);
	const selected = icons[value];
	const glyph = (icon) => icon && h$1("span", {
		className: "rdct-ico-picker__glyph",
		"aria-hidden": true,
		style: {
			maskImage: `url("${icon.src}")`,
			WebkitMaskImage: `url("${icon.src}")`
		}
	});
	useEffect(() => {
		if (!open) return;
		const node = dialog.current;
		node.showModal();
		input.current?.focus();
		return () => {
			node.close();
			if (opener.current?.isConnected) opener.current.focus();
		};
	}, [open]);
	useEffect(() => {
		if (disabled) setOpen(false);
	}, [disabled]);
	const choose = (name) => {
		if (disabled) return;
		onChange(name);
		setOpen(false);
	};
	const show = (event) => {
		opener.current = event.currentTarget;
		setSearch("");
		setLimit(160);
		setOpen(true);
	};
	const matches = open ? iconMatches(icons, search) : [];
	const triggerProps = {
		type: "button",
		disabled,
		onClick: show,
		"aria-haspopup": "dialog",
		"aria-expanded": open
	};
	const triggerContent = h$1("span", { className: "rdct-ico-picker__selection" }, glyph(selected), selected?.label || (value ? `${labels.unavailable} (${value})` : labels.choose));
	return h$1("div", { className: "rdct-ico-picker" }, renderTrigger ? renderTrigger(triggerProps, triggerContent) : h$1("button", {
		...triggerProps,
		className: "rdct-ico-picker__trigger"
	}, triggerContent), open && createPortal(h$1("dialog", {
		ref: dialog,
		className: "rdct-ico-picker__dialog",
		"aria-label": labels.title,
		onClose: () => setOpen(false),
		onCancel: () => setOpen(false),
		onClick: (event) => {
			if (event.target === event.currentTarget) setOpen(false);
		}
	}, h$1("div", { className: "rdct-ico-picker__body" }, h$1("div", { className: "rdct-ico-picker__heading" }, h$1("strong", null, labels.title), h$1("button", {
		type: "button",
		onClick: () => setOpen(false),
		"aria-label": labels.close
	}, "×")), h$1("input", {
		ref: input,
		type: "search",
		value: search,
		placeholder: labels.search,
		"aria-label": labels.search,
		onChange: (event) => {
			setSearch(event.target.value);
			setLimit(160);
		}
	}), h$1("div", { className: "rdct-ico-picker__grid" }, h$1("button", {
		type: "button",
		disabled,
		"aria-pressed": !value,
		onClick: () => choose("")
	}, h$1("span", { "aria-hidden": true }, "∅"), h$1("span", null, labels.none)), ...matches.slice(0, limit).map(([name, icon]) => h$1("button", {
		key: name,
		type: "button",
		disabled,
		title: `${icon.label} (${name})`,
		"aria-pressed": value === name,
		onClick: () => choose(name)
	}, glyph(icon), h$1("span", null, icon.label), h$1("small", { dir: "ltr" }, name)))), !matches.length && h$1("p", { role: "status" }, labels.empty), matches.length > limit && h$1("button", {
		type: "button",
		onClick: () => setLimit((count) => count + 160)
	}, labels.more))), document.body));
}
//#endregion
//#region radmanct/src/js/admin/builder-icon-picker.js
var { createElement: h } = window.wp.element;
function BuilderIconPicker({ value, onChange, config, labels }) {
	return h(IconPicker, {
		value,
		onChange,
		icons: config.iconChoices,
		labels: {
			title: labels.chooseIcon,
			choose: labels.chooseIcon,
			search: labels.searchIcons,
			none: labels.noIcon,
			close: labels.closeIconPicker,
			empty: labels.noMatchingIcons,
			unavailable: labels.unavailableIcon,
			more: labels.moreIcons
		}
	});
}
//#endregion
export { BuilderIconPicker as t };
