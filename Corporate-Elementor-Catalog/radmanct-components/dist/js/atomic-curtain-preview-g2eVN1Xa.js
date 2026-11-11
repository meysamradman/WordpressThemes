import { t as observeRenderedPreviews } from "./elementor-atomic-components-COuKAf0K.js";
import { initializeCurtains } from "./curtain-DY-gfKdC.js";
//#region radmanct-components/src/js/integrations/atomic-curtain-preview.js
function observeCurtainPreviews(doc, config) {
	return observeRenderedPreviews(doc, config, "curtain", (marker) => initializeCurtains(marker));
}
//#endregion
export { observeCurtainPreviews };
