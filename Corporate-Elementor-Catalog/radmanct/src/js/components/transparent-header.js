const instances = new WeakMap();

export function initializeTransparentHeader(root) {
  instances.get(root)?.();
  const desktop = matchMedia('(min-width: 48rem)');
  const mode = root.dataset.stickyMode;
  const previous = root.style.getPropertyValue('--rdct-header-height');
  const sibling = root.nextElementSibling;
  const titleArea = sibling?.matches?.('[data-rdct-title-area]') ? sibling : null;
  const titleOffset = titleArea?.style.getPropertyValue('--rdct-title-header-offset') || '';
  let active = true; let frame = 0; let measure = true; let height = 0;
  const update = () => {
    frame = 0;
    if (!active || !root.isConnected) return;
	if (root.dataset.rdctTransparentDevices === 'desktop' && !desktop.matches) {
	  root.classList.remove('is-ready', 'is-scrolled');
      titleArea?.style.setProperty('--rdct-title-header-offset', '0px');
	  return;
	}
    if (measure) { height = root.getBoundingClientRect().height; measure = false; }
    if (!Number.isFinite(height) || height < 0) return;
    root.style.setProperty('--rdct-header-height', `${height}px`);
    titleArea?.style.setProperty('--rdct-title-header-offset', `${height}px`);
    root.classList.add('is-ready');
    const stuck=root.dataset.rdctStickyStart==='after-first-section' ? root.classList.contains('is-sticky-active') : window.scrollY>1;
    root.classList.toggle('is-scrolled', (mode === 'all' || (mode === 'desktop' && desktop.matches)) && stuck);
  };
  const queue = (refresh = false) => { measure ||= refresh; if (active && !frame) frame = requestAnimationFrame(update); };
  const resize = () => queue(true);
  const scroll = () => queue();
  const observer = typeof ResizeObserver === 'function' ? new ResizeObserver(resize) : null;
  observer?.observe(root);
  window.addEventListener('resize', resize, { passive: true });
  if (mode !== 'off') window.addEventListener('scroll', scroll, { passive: true });
  desktop.addEventListener('change', resize);
  root.addEventListener('rdct:sticky-change',scroll);
  update();
  const cleanup = () => {
    if (!active) return;
    active = false; if (frame) cancelAnimationFrame(frame); observer?.disconnect();
    window.removeEventListener('resize', resize); window.removeEventListener('scroll', scroll); desktop.removeEventListener('change', resize);
    root.removeEventListener('rdct:sticky-change',scroll);root.classList.remove('is-ready', 'is-scrolled');
    if (previous) root.style.setProperty('--rdct-header-height', previous); else root.style.removeProperty('--rdct-header-height');
    if (titleArea) { if (titleOffset) titleArea.style.setProperty('--rdct-title-header-offset', titleOffset); else titleArea.style.removeProperty('--rdct-title-header-offset'); }
    if (instances.get(root) === cleanup) instances.delete(root);
  };
  instances.set(root, cleanup);
  return cleanup;
}
