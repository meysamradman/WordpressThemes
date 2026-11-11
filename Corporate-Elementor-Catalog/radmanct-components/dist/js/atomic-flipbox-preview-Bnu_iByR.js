import { t as observeRenderedPreviews } from "./elementor-atomic-components-COuKAf0K.js";
import { initializeFlipBox } from "./flipbox-DADV-p-2.js";
//#region radmanct-components/src/js/integrations/atomic-flipbox-preview.js
function observeFlipBoxPreviews(doc, config) {
	return observeRenderedPreviews(doc, config, "flipbox", (marker) => initializeFlipBox(marker.querySelector("[data-rdct-flipbox]")));
}
//#endregion
export { observeFlipBoxPreviews };
