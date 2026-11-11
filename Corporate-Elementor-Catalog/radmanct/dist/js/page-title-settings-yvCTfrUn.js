import { t as PageTitleFields } from "./page-title-fields-DHhcVJML.js";
import { ContentTypographyFields } from "./content-typography-fields-BLCp3KMw.js";
//#region radmanct/src/js/admin/page-title-settings.js
var { createElement: h } = window.wp.element;
function PageTitleSettings({ config, values, change }) {
	return h("div", null, h(PageTitleFields, {
		value: values.page_title,
		change: (next) => change("page_title", next),
		labels: config.labels,
		global: true
	}), h("details", null, h("summary", null, config.labels.page_title_typography), h(ContentTypographyFields, {
		scope: "page_title",
		config: {
			...config,
			typographyRoles: [
				"body",
				"h1",
				"link"
			]
		},
		values,
		change
	})));
}
//#endregion
export { PageTitleSettings };
