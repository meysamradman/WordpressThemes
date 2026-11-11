export function createLibraryDialog(manager, {label, closeLabel, onHide}) {
  if (typeof manager?.createWidget !== 'function') return null;
  let open = false;
  const modal = manager.createWidget('lightbox', {
    id: 'rdct-elementor-library',
    className: 'rdct-elementor-library',
    headerMessage: label,
    closeButton: true,
    closeButtonOptions: {attributes: {'aria-label': closeLabel}},
    preventScroll: true,
    hide: {onBackgroundClick: true, onOutsideClick: true, onEscKeyPress: true},
    onHide() { open = false; onHide?.(); },
  });
  const content = modal.getElements('message')[0];
  const surface = modal.getElements('widgetContent')[0];
  surface.setAttribute('role', 'dialog');
  surface.setAttribute('aria-modal', 'true');
  surface.setAttribute('aria-label', label);
  surface.addEventListener('keydown', (event) => {
    if (event.key !== 'Tab' || event.defaultPrevented) return;
    const nodes = [...surface.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),iframe,[tabindex]:not([tabindex="-1"])')].filter(node => node.getClientRects().length);
    const active = surface.ownerDocument.activeElement;
    const last = nodes[nodes.length - 1];
    const target = event.shiftKey && active === nodes[0] ? last : (!event.shiftKey && active === last ? nodes[0] : null);
    if (target) { event.preventDefault(); target.focus({preventScroll:true}); }
  });
  content.setAttribute('data-library-content', '');
  return {
    content,
    get open() { return open; },
    show() { open = true; modal.show(); modal.getElements('closeButton')[0]?.focus?.({preventScroll:true}); },
    hide() { if (open) modal.hide(); },
    busy(value) { content.setAttribute('aria-busy', String(value)); },
  };
}
