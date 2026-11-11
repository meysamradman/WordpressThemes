import { createArchiveRequest, formUrl } from './archive-request.js';
import { prepareArchiveStyles } from './archive-styles.js';
import { appendArchiveResults } from './archive-results.js';

export function initializeArchives() {
  const root = document.querySelector('[data-rdct-archive]');
  if (!root || root.dataset.enhanced) return;
  const view = root.querySelector('[data-rdct-archive-view]');
  const status = root.querySelector('[data-rdct-archive-status]');
  const retry = root.querySelector('[data-rdct-archive-retry]');
  if (!view || !status || !retry) return;
  root.dataset.enhanced = 'true';
  let timer;
  let failed;
  let composing = false;
  let pending = false;
  const busy = (value) => { pending = value; view.setAttribute('aria-busy', String(value)); root.classList.toggle('is-loading', value); };
  const transport = createArchiveRequest(root.dataset.rdctArchive, (data, url, options) => {
    if (options.append) {
      appendArchiveResults(view, data.html);
      busy(false); retry.hidden = true; failed = null;
      status.textContent = root.dataset.moreLoaded || '';
      root.dispatchEvent(new CustomEvent('rdct:archive-updated', { bubbles: true, detail: { scope: root.dataset.rdctArchive, filters: data.filters } }));
      return;
    }
    const active = document.activeElement;
    const titleArea = document.querySelector('[data-rdct-title-area]');
    if (titleArea && typeof data.pageTitle === 'string') titleArea.innerHTML = data.pageTitle;
    const field = view.contains(active) && active.name ? { name: active.name, start: active.selectionStart, end: active.selectionEnd } : null;
    const inside = view.contains(active);
    const intro = root.querySelector('[data-rdct-archive-intro]');
    if (intro) intro.innerHTML = data.intro || '';
    view.innerHTML = intro ? data.html : (data.intro || '') + data.html;
    document.title = data.title;
    const header = root.querySelector('[data-rdct-archive-header]');
    if (header && typeof data.header === 'string') header.outerHTML = data.header;
    if (!options.pop && location.href !== url.href) history.pushState(history.state, '', url.href);
    busy(false); retry.hidden = true; failed = null;
    status.textContent = view.querySelector('[data-rdct-result-count]')?.textContent || '';
    if (field) {
      const replacement = [...view.querySelectorAll('input, select')].find((entry) => entry.name === field.name);
      replacement?.focus({ preventScroll: true });
      if (replacement?.type === 'search' && field.start !== null) replacement.setSelectionRange(field.start, field.end);
    } else if (inside || options.scroll) view.focus({ preventScroll: true });
    if (options.scroll) view.scrollIntoView({ block: 'start', behavior: 'instant' });
    root.dispatchEvent(new CustomEvent('rdct:archive-updated', { bubbles: true, detail: { scope: root.dataset.rdctArchive, filters: data.filters } }));
  }, (error, url, options) => {
    busy(false); status.textContent = root.dataset.error; retry.hidden = false; failed = { url, options };
  }, fetch, prepareArchiveStyles);
  function load(url, options = {}) {
    if (url.origin !== location.origin) return;
    clearTimeout(timer); transport.cancel();
    busy(true); retry.hidden = true; status.textContent = root.dataset.loading;
    transport.load(url, options);
  }
  const form = (target) => target.closest('form[data-rdct-archive-form]');
  root.addEventListener('submit', (event) => { const target = form(event.target); if (target && target.method.toLowerCase() === 'get') { event.preventDefault(); load(formUrl(target)); } });
  root.addEventListener('change', (event) => { const target = form(event.target); if (target && event.target.tagName === 'SELECT') load(formUrl(target)); });
  root.addEventListener('input', (event) => {
    const target = form(event.target);
    if (!target || event.target.type !== 'search') return;
    clearTimeout(timer); transport.cancel(); busy(false); status.textContent = ''; retry.hidden = true;
    if (!composing && !event.isComposing) timer = setTimeout(() => load(formUrl(target)), 350);
  });
  root.addEventListener('compositionstart', (event) => { if (event.target.type !== 'search' || !form(event.target)) return; composing = true; clearTimeout(timer); transport.cancel(); });
  root.addEventListener('compositionend', (event) => { if (event.target.type !== 'search' || !form(event.target)) return; composing = false; const target = form(event.target); if (target) { clearTimeout(timer); timer = setTimeout(() => load(formUrl(target)), 350); } });
  root.addEventListener('click', (event) => {
    if (event.target.closest('[data-rdct-archive-retry]')) { if (failed) load(failed.url, failed.options); return; }
    const link = event.target.closest('a[data-rdct-archive-link], a[data-rdct-load-more], .pagination a');
    if (!link || !root.contains(link) || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === '_blank' || link.hasAttribute('download')) return;
    const url = new URL(link.href);
    if (url.origin !== location.origin) return;
    event.preventDefault();
    const append = link.hasAttribute('data-rdct-load-more');
    if (append && pending) return;
    load(url, { append, scroll: !append && !!link.closest('.pagination') });
  });
  window.addEventListener('popstate', () => load(new URL(location.href), { pop: true }));
  window.addEventListener('pagehide', () => { clearTimeout(timer); transport.cancel(); busy(false); status.textContent = ''; });
}
