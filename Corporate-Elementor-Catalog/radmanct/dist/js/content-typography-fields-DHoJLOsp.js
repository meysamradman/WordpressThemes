import { t as ButtonControl } from "./controls-DqGpx29l.js";
import { r as FontFaces } from "./settings-BCrDiqNP.js";
import { n as TabList } from "./panel-tabs-BJ7bpkAa.js";
import { t as TypographyFields } from "./typography-fields-B70eGrZZ.js";
//#region radmanct/src/js/admin/typography-profile.js
function resolveProfile(base, raw, roles) {
	const values = { ...base };
	const overrides = Object.fromEntries(Object.entries(raw).filter(([, value]) => value !== ""));
	for (const group of ["persian", "latin"]) if (overrides[`${group}_font`]) for (const role of roles) values[`${role}_${group}_font`] = "";
	else if (overrides[`headings_${group}_font`]) for (let level = 1; level <= 6; level++) values[`h${level}_${group}_font`] = "";
	return {
		...values,
		...overrides
	};
}
//#endregion
//#region radmanct/src/js/admin/content-typography-fields.js
var { createElement: h, useState } = window.wp.element;
function ContentTypographyFields({ scope, config, values, change }) {
	const [group, setGroup] = useState(config.primaryFontGroup);
	const key = `${scope}_typography`;
	const raw = values[key] || {};
	const resolve = (draft) => resolveProfile(values, draft, config.fontRoles);
	return h("div", null, h("p", { className: "description" }, config.labels.contentTypographyHint), h(ButtonControl, { onClick: () => change(key, {}) }, config.labels.contentTypographyReset), h(TabList, {
		id: `rdct-fonts-${scope}`,
		value: group,
		onChange: setGroup,
		tabs: [{
			id: "persian",
			label: config.labels.arabicScript
		}, {
			id: "latin",
			label: config.labels.latinScript
		}]
	}), h("div", {
		id: `rdct-fonts-${scope}-${group}-panel`,
		role: "tabpanel",
		"aria-labelledby": `rdct-fonts-${scope}-${group}-tab`
	}, h(FontFaces, {
		config,
		values: resolve(raw),
		groups: [group]
	}), h(TypographyFields, {
		key: group,
		config,
		values: raw,
		group,
		resolveValues: resolve,
		inheritLabel: config.labels.inheritBodyFont,
		change: (name, value) => change(key, (current) => ({
			...current,
			[name]: value
		}))
	})));
}
//#endregion
export { ContentTypographyFields };
