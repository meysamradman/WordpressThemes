import { r as elementChildren, t as createElement } from "./utils-rsb8sxsd.js";
//#region node_modules/swiper/shared/create-element-if-not-defined.mjs
function createElementIfNotDefined(swiper, originalParams, params, checkProps) {
	const target = params ?? {};
	const original = originalParams ?? {};
	if (swiper.params.createElements) Object.keys(checkProps).forEach((key) => {
		if (!target[key] && target.auto === true) {
			let element = elementChildren(swiper.el, `.${checkProps[key]}`)[0];
			if (!element) {
				element = createElement("div", checkProps[key]);
				element.className = checkProps[key];
				swiper.el.append(element);
			}
			target[key] = element;
			original[key] = element;
		}
	});
	return target;
}
//#endregion
export { createElementIfNotDefined as t };
