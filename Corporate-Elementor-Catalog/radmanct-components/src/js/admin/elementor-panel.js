export function prioritizeRadmanPanel(editor) {
  const panel = editor?.config?.document?.panel;
  const categories = panel?.elements_categories;
  if (categories && Object.hasOwn(categories, 'radmanct-components') && Object.keys(categories)[0] !== 'radmanct-components') {
    panel.elements_categories = { 'radmanct-components': categories['radmanct-components'], ...categories };
  }
  let collection;
  try { collection = editor?.getPanelView?.()?.getCurrentPageView?.()?.categoriesCollection; } catch { return; }
  if (!collection?.models || typeof collection.reset !== 'function') return;
  const index = collection.models.findIndex((model) => model.get('name') === 'radmanct-components');
  if (index > 0) collection.reset([collection.models[index], ...collection.models.filter((_, position) => position !== index)]);
}
