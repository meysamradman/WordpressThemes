const registry = Symbol.for('radmanct.header-tone-managers');

export function observeHeaderTone(header, callback) {
  const doc = header.ownerDocument, view = doc.defaultView;
  if (view.elementorFrontend?.isEditMode?.()) return () => {};
  if (!doc[registry]) Object.defineProperty(doc, registry, { value: new WeakMap() });
  const managers = doc[registry];
  let manager = managers.get(header);
  if (!manager) {
    const callbacks = new Set();
    const sentinel = doc.createElement('span');
    sentinel.setAttribute('aria-hidden', 'true');
    sentinel.style.cssText = 'display:block;height:0;width:0;margin:0;padding:0;pointer-events:none';
    header.before(sentinel);
    let frame = 0;
    const update = () => {
      frame = 0;
      if (header.isConnected === false) return;
      const rect = header.getBoundingClientRect(), style = view.getComputedStyle(header);
      const sticky = header.classList.contains('is-sticky-active') || (['sticky', 'fixed'].includes(style.position) && sentinel.getBoundingClientRect().top < rect.top - 1);
      const x = Math.max(0, Math.min(view.innerWidth - 1, rect.left + rect.width / 2));
      const y = Math.max(0, Math.min(view.innerHeight - 1, rect.bottom + 1));
      const section = doc.elementsFromPoint(x, y).find(node => !header.contains(node) && node.closest('[data-rdct-header-tone]'))?.closest('[data-rdct-header-tone]');
      let tone = section?.dataset.rdctHeaderTone || '';
      if (tone === 'auto') tone = doc.documentElement.dataset.rdctScheme || 'light';
      if (!['light', 'dark'].includes(tone)) tone = '';
      for (const subscriber of callbacks) subscriber({ tone, sticky, style });
    };
    const schedule = () => { if (!frame) frame = view.requestAnimationFrame(update); };
    const events = [[view, 'scroll'], [view, 'resize'], [header, 'rdct:sticky-change'], [doc, 'rdct:color-scheme'], [doc, 'rdct:smooth-update'], [doc, 'load']];
    for (const [target, type] of events) target.addEventListener(type, schedule, { passive: true, capture: type === 'load' });
    const observer = view.ResizeObserver ? new view.ResizeObserver(schedule) : null;
    observer?.observe(header);
    manager = { callbacks, schedule, destroy() {
      for (const [target, type] of events) target.removeEventListener(type, schedule, type === 'load');
      observer?.disconnect();
      if (frame) view.cancelAnimationFrame(frame);
      sentinel.remove(); managers.delete(header);
    } };
    managers.set(header, manager);
  }
  manager.callbacks.add(callback); manager.schedule();
  let active = true;
  return () => {
    if (!active) return;
    active = false; manager.callbacks.delete(callback);
    if (!manager.callbacks.size) manager.destroy();
  };
}
