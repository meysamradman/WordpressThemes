export function appendArchiveResults(view, html) {
  const template = view.ownerDocument.createElement('template');
  template.innerHTML = html;
  const items = view.querySelector('[data-rdct-archive-items]');
  const incoming = template.content.querySelector('[data-rdct-archive-items]');
  const pagination = view.querySelector('[data-rdct-archive-pagination]');
  const next = template.content.querySelector('[data-rdct-archive-pagination]');
  if (!items || !incoming || !pagination || !next || !incoming.children.length) throw new Error('Invalid archive page');
  const ids = new Set([...items.children].map(item => item.id).filter(Boolean));
  const added = [...incoming.children].filter(item => !item.id || !ids.has(item.id));
  if (!added.length) throw new Error('Duplicate archive page');
  const focused = pagination.contains(view.ownerDocument.activeElement);
  items.append(...added);
  next.querySelectorAll('[data-rdct-archive-link]').forEach(link => link.remove());
  next.hidden = !next.querySelector('a');
  pagination.replaceWith(next);
  if (focused) {
    const title = added[0].querySelector(':is(h2,h3,h4,h5,h6) a') || added[0].querySelector('a:not([tabindex="-1"])');
    title?.focus({ preventScroll: true });
  }
  return added.length;
}
