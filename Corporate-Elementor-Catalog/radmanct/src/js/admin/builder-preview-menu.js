export function prepareMenuPreview(doc) {
  for (const root of doc.querySelectorAll('[data-rdct-menu]')) {
    const button = root.querySelector(':scope > .rdct-menu-toggle');
    const body = root.querySelector(':scope > .rdct-nav, :scope > [data-rdct-panel-body]');
    if (!button || !body) continue;
    root.dataset.enhanced = 'preview';
    button.hidden = false;
  }
}
