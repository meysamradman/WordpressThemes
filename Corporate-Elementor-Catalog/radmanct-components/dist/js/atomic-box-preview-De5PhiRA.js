import { t as observeRenderedPreviews } from "./elementor-atomic-components-COuKAf0K.js";
import { initializeBox } from "./box-CKwSNMNy.js";
//#region radmanct-components/src/js/integrations/atomic-box-preview.js
function observeBoxPreviews(doc, config) {
	return observeRenderedPreviews(doc, config, "box", (marker) => {
		const root = marker.querySelector("[data-rdct-box-motion]");
		return root ? initializeBox(root) : () => {};
	});
}
//#endregion
export { observeBoxPreviews };
