import { n as pickMedia } from "./image-picker-61mFi_WJ.js";
//#region radmanct/src/js/admin/card-media.js
for (const root of document.querySelectorAll("[data-rdct-card-media-editor]")) {
	const input = root.querySelector("[name=rdct_card_video_id]");
	const name = root.querySelector("[data-video-name]");
	const remove = root.querySelector("[data-video-remove]");
	const status = root.querySelector("[data-video-status]");
	let release;
	root.querySelector("[data-video-select]").addEventListener("click", () => {
		release?.();
		release = pickMedia({
			title: root.dataset.title,
			id: Number(input.value),
			type: "video",
			onSelect(id) {
				const item = window.wp.media.attachment(id).toJSON();
				if (![
					"video/mp4",
					"video/webm",
					"video/ogg"
				].includes(item.mime)) {
					status.textContent = root.dataset.error;
					return;
				}
				input.value = String(id);
				name.textContent = item.filename || item.title || String(id);
				remove.hidden = false;
				status.textContent = "";
			}
		});
	});
	remove.addEventListener("click", () => {
		input.value = "0";
		name.textContent = "";
		remove.hidden = true;
		status.textContent = "";
	});
	window.addEventListener("pagehide", () => release?.(), { once: true });
}
//#endregion
