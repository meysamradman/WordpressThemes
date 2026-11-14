//#region radmanct/src/js/admin/content-view-fields.js
var { createElement: h } = window.wp.element;
function ContentViewFields({ scope, config }) {
	const { labels } = config;
	const [content] = scope.split("_");
	return h("div", null, !config.contentModules[content] && h("p", { className: "description" }, labels.moduleDisabled), h("p", { className: "description" }, labels.rendererHint), !config.elementorPro && h("p", { className: "description" }, labels.proUnavailable), h("div", { className: "rdct-stg__actions" }, config.contentLinks?.items?.[content] && h("a", {
		className: "rdct-btn",
		href: config.contentLinks.items[content]
	}, labels.manageContentItems), config.contentLinks?.archives?.[content] && h("a", {
		className: "rdct-btn",
		href: config.contentLinks.archives[content],
		target: "_blank",
		rel: "noopener noreferrer"
	}, labels.viewContentArchive), config.elementorBuilderUrl && h("a", {
		className: "rdct-btn",
		href: config.elementorBuilderUrl
	}, labels.elementorDesign)));
}
//#endregion
export { ContentViewFields };
