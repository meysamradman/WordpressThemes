//#region radmanct-components/src/js/admin/component-insert.js
function libraryModel(models, id, language, mode = "classic") {
	if (!["classic", "atomic"].includes(mode) || !["fa", "en"].includes(language)) throw new Error("Unknown sample variant");
	const model = models?.[id]?.[`${language}${mode === "atomic" ? "-atomic" : ""}`];
	if (!model) throw new Error("Sample unavailable");
	return model;
}
async function insertSample(model, editor = window.elementor, commands = window.$e, expectedDocument) {
	const container = editor?.getPreviewContainer?.();
	if (!container || typeof commands?.run !== "function" || !["container", "e-flexbox"].includes(model?.elType) || !Array.isArray(model.elements) || model.elements.length !== 1) throw new Error("Editor unavailable");
	if (container.isEditable?.() === false || expectedDocument !== void 0 && editor.documents?.getCurrentId?.() !== expectedDocument) throw new Error("Document unavailable");
	const child = model.elements[0];
	if (![
		"rdct-testimonial",
		"rdct-atomic-testimonial",
		"rdct-carousel-cards",
		"rdct-carousel-logos",
		"rdct-carousel-slider",
		"rdct-carousel-marquee",
		"rdct-carousel-stack",
		"rdct-carousel-testimonials",
		"rdct-atomic-carousel-cards",
		"rdct-atomic-carousel-logos",
		"rdct-atomic-carousel-slider",
		"rdct-atomic-carousel-marquee",
		"rdct-atomic-carousel-stack",
		"rdct-atomic-carousel-testimonials",
		"rdct-carousel",
		"rdct-atomic-carousel",
		"rdct-table",
		"rdct-atomic-table",
		"radman-text-reveal",
		"radman-icon",
		"rdct-curtain",
		"rdct-box",
		"rdct-atomic-text-reveal",
		"rdct-atomic-icon",
		"rdct-atomic-curtain",
		"rdct-atomic-box",
		"rdct-flipbox",
		"rdct-atomic-flipbox",
		"rdct-icon-box",
		"rdct-atomic-icon-box",
		"rdct-image",
		"rdct-atomic-image"
	].includes(child.widgetType) || child.elType !== "widget") throw new Error("Unknown sample");
	const atomic = child.widgetType.startsWith("rdct-atomic-");
	if (model.elType === "e-flexbox" !== atomic) throw new Error("Mismatched sample variant");
	const result = await commands.run("document/elements/create", {
		container,
		model: structuredClone(model),
		options: {
			clone: true,
			edit: true
		}
	});
	const created = Array.isArray(result) ? result : [result];
	if (!created.length || created.some((item) => !item || typeof item.id !== "string" || !item.id)) throw new Error("Insertion failed");
	return result;
}
//#endregion
export { insertSample, libraryModel };
