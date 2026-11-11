//#region node_modules/swiper/shared/classes-to-selector.mjs
function classesToSelector(classes = "") {
	return `.${classes.trim().replace(/([.:!+/()[\]#>~*^$|=,'"@{}\\])/g, "\\$1").replace(/ /g, ".")}`;
}
//#endregion
export { classesToSelector as t };
