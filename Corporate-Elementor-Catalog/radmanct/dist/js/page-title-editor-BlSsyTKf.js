import { t as PageTitleFields } from "./page-title-fields-DHhcVJML.js";
//#region radmanct/src/js/admin/page-title-editor.js
var { createElement: h, createRoot, useState } = window.wp.element;
function Editor({ host }) {
	const [value, setValue] = useState(JSON.parse(host.dataset.value));
	const labels = JSON.parse(host.dataset.labels);
	return h(PageTitleFields, {
		value,
		labels,
		change: (next) => {
			setValue(next);
			document.getElementById("rdct-page-title-value").value = JSON.stringify(next);
		}
	});
}
var host = document.getElementById("rdct-page-title-editor");
if (host) createRoot(host).render(h(Editor, { host }));
//#endregion
