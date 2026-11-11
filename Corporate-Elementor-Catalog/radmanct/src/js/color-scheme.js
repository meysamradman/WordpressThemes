export function initializeColorScheme(doc = document, view = window) {
  const root = doc.documentElement;
  if (!root.dataset.rdctScheme) return () => {};
  const media = view.matchMedia('(prefers-color-scheme: dark)');
  const mode = root.dataset.rdctSchemeMode || 'light';
  let preference;
  try { preference = view.localStorage.getItem('rdct-color-scheme'); } catch {}
  const sync = () => doc.querySelectorAll('[data-rdct-theme-toggle]').forEach(button => button.setAttribute('aria-pressed', String(root.dataset.rdctScheme === 'dark')));
  const set = (scheme) => {
    root.dataset.rdctScheme = scheme;
    sync();
    doc.dispatchEvent(new view.CustomEvent('rdct:color-scheme', {detail: {scheme}}));
  };
  const click = event => {
    if (!event.target.closest?.('[data-rdct-theme-toggle]')) return;
    preference = root.dataset.rdctScheme === 'dark' ? 'light' : 'dark';
    try { view.localStorage.setItem('rdct-color-scheme', preference); } catch {}
    set(preference);
  };
  const system = () => { if (!['light', 'dark'].includes(preference) && root.dataset.rdctSchemeMode === 'system') set(media.matches ? 'dark' : 'light'); };
  const storage = event => {
    if (event.key !== null && event.key !== 'rdct-color-scheme') return;
    preference = event.newValue;
    if (['light', 'dark'].includes(preference)) set(preference);
    else set(mode === 'system' ? (media.matches ? 'dark' : 'light') : mode);
  };
  doc.addEventListener('click', click);
  const focus = event => { if (event.target.closest?.('[data-rdct-theme-toggle]')) sync(); };
  doc.addEventListener('focusin', focus);
  media.addEventListener('change', system);
  view.addEventListener('storage', storage);
  sync();
  const pagehide = event => { if (!event.persisted) cleanup(); };
  const cleanup = () => { doc.removeEventListener('click', click); doc.removeEventListener('focusin', focus); media.removeEventListener('change', system); view.removeEventListener('storage', storage); view.removeEventListener('pagehide', pagehide); };
  view.addEventListener('pagehide', pagehide);
  return cleanup;
}

if (typeof document !== 'undefined') initializeColorScheme();
