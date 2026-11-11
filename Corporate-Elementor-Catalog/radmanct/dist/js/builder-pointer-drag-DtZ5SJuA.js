//#region radmanct/src/js/admin/builder-pointer-drag.js
function pointerView(event) {
	const element = event.currentTarget;
	const editor = element.closest?.(".rdct-bld-editor")?.getBoundingClientRect() || {
		left: 0,
		top: 0
	};
	const scroll = element.ownerDocument?.elementFromPoint(event.clientX, event.clientY)?.closest(".rdct-bld-canvas,.rdct-adm-scroll");
	if (scroll) {
		const rect = scroll.getBoundingClientRect();
		const amount = event.clientY < rect.top + 32 ? -12 : event.clientY > rect.bottom - 32 ? 12 : 0;
		if (amount) scroll.scrollBy(0, amount);
	}
	return {
		x: event.clientX - editor.left,
		y: event.clientY - editor.top
	};
}
function createPointerDrag({ start, move, end, cancel }, threshold = 5) {
	let active = null;
	const clear = () => {
		const previous = active;
		active = null;
		if (previous?.element.hasPointerCapture?.(previous.id)) previous.element.releasePointerCapture(previous.id);
		return previous;
	};
	const abort = () => {
		if (clear()) cancel();
	};
	return {
		cancel: abort,
		bind(data) {
			return {
				draggable: false,
				onDragStart: (event) => event.preventDefault(),
				onPointerDown(event) {
					if (event.isPrimary === false || event.button !== void 0 && event.button !== 0) return;
					abort();
					event.preventDefault();
					event.stopPropagation();
					const element = event.currentTarget;
					element.focus?.({ preventScroll: true });
					active = {
						id: event.pointerId,
						x: event.clientX,
						y: event.clientY,
						element,
						data,
						moved: false
					};
					element.setPointerCapture(event.pointerId);
					start(data);
				},
				onPointerMove(event) {
					if (!active || active.id !== event.pointerId) return;
					if (!active.moved && Math.hypot(event.clientX - active.x, event.clientY - active.y) < threshold) return;
					active.moved = true;
					event.preventDefault();
					move(event, active.data, pointerView(event));
				},
				onPointerUp(event) {
					if (!active || active.id !== event.pointerId) return;
					const previous = clear();
					if (previous.moved) {
						event.preventDefault();
						event.stopPropagation();
						end(event, previous.data);
					} else cancel();
				},
				onPointerCancel(event) {
					if (active?.id === event.pointerId) abort();
				},
				onLostPointerCapture(event) {
					if (active?.id === event.pointerId) abort();
				},
				onKeyDown(event) {
					if (event.key === "Escape") abort();
				}
			};
		}
	};
}
//#endregion
export { createPointerDrag };
