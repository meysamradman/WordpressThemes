import { t as observeRenderedPreviews } from "./elementor-atomic-components-COuKAf0K.js";
import { initializeImages } from "./image-BSHnXy1F.js";
//#region radmanct-components/src/js/integrations/atomic-image-preview.js
function observeImagePreviews(doc, config) {
	return observeRenderedPreviews(doc, config, "image", initializeImages);
}
//#endregion
export { observeImagePreviews };
