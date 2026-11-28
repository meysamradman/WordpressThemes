(() => {
  const pager = document.querySelector('[data-rds-pager="more"]');
  const items = document.querySelector('[data-rds-items]');
  if (!pager || !items) return;
  const status = pager.querySelector('[role="status"]');
  let busy = false;
  let retry = false;
  let controller;
  const observer = 'IntersectionObserver' in window
    ? new IntersectionObserver(entries => { if (entries.some(entry => entry.isIntersecting) && !retry) load(); }, {rootMargin:'120px'})
    : null;
  async function load() {
    const link = pager.querySelector('a[rel="next"]');
    if (busy || !link) return;
    const url = new URL(link.href, location.href);
    if (url.origin !== location.origin) return;
    busy = true;
    pager.setAttribute('aria-busy', 'true');
    status.textContent = 'در حال بارگذاری…';
    try {
      controller = new AbortController();
      const response = await fetch(url, {credentials:'same-origin',signal:controller.signal});
      if (!response.ok || new URL(response.url).origin !== location.origin) throw new Error('response');
      const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
      const incoming = doc.querySelector('[data-rds-items]');
      if (!incoming || !incoming.children.length) throw new Error('empty');
      const fragment = document.createDocumentFragment();
      for (const child of Array.from(incoming.children)) fragment.append(document.importNode(child, true));
      items.append(fragment);
      const next = doc.querySelector('[data-rds-pager="more"] a[rel="next"]');
      if (next) { link.href = next.href; } else { link.remove(); observer?.disconnect(); }
      retry = false;
      status.textContent = next ? 'موارد بعدی نمایش داده شد.' : 'همهٔ موارد نمایش داده شد.';
    } catch {
      retry = true;
      status.textContent = 'بارگذاری انجام نشد؛ برای تلاش دوباره نمایش بیشتر را بزنید.';
    } finally {
      busy = false;
      pager.removeAttribute('aria-busy');
    }
  }
  pager.addEventListener('click', event => {
    if (!event.target.closest('a[rel="next"]')) return;
    event.preventDefault();
    retry = false;
    load();
  });
  observer?.observe(pager);
  window.addEventListener('pagehide', () => { observer?.disconnect(); controller?.abort(); }, {once:true});
})();
