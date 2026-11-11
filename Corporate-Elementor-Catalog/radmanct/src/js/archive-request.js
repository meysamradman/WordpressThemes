export function formUrl(form, base = location.href) {
  const url = new URL(form.action, base);
  url.search = '';
  for (const [key, value] of new FormData(form)) if (typeof value === 'string' && value !== '') url.searchParams.append(key, value);
  url.searchParams.delete('_rdct_fragment');
  url.searchParams.delete('paged');
  url.searchParams.delete('page');
  url.hash = '';
  return url;
}

export function createArchiveRequest(scope, receive, fail, fetcher = fetch, prepare = data => data) {
  let controller;
  let serial = 0;
  const cancel = () => { serial++; controller?.abort(); };
  return {
    cancel,
    async load(url, options = {}) {
      cancel();
      const current = serial;
      controller = new AbortController();
      const activeController = controller;
      const request = new URL(url);
      request.searchParams.set('_rdct_fragment', scope);
      const timeout = setTimeout(() => activeController.abort(), 15000);
      try {
        const response = await fetcher(request.href, { signal: activeController.signal, credentials: 'same-origin', redirect: 'error', headers: { Accept: 'application/json' }, cache: 'no-store' });
        if (current !== serial) return;
        if (!response.ok) throw new Error('Archive request failed');
        const body = await response.json();
        if (current !== serial) return;
        if (!body.success || body.data?.scope !== scope || typeof body.data.html !== 'string' || typeof body.data.title !== 'string') throw new Error('Invalid archive response');
        if (body.data.filters !== undefined && (!body.data.filters || Array.isArray(body.data.filters) || typeof body.data.filters !== 'object' || Object.values(body.data.filters).some(value => typeof value !== 'string'))) throw new Error('Invalid archive filters');
        if (body.data.header !== undefined && typeof body.data.header !== 'string') throw new Error('Invalid archive header');
        if (body.data.intro !== undefined && typeof body.data.intro !== 'string') throw new Error('Invalid archive intro');
        if (body.data.pageTitle !== undefined && typeof body.data.pageTitle !== 'string') throw new Error('Invalid page title');
        const data = await prepare(body.data, activeController.signal);
        if (current !== serial) return;
        receive(data, new URL(url), options);
      } catch (error) { if (current === serial) fail(error, new URL(url), options); }
      finally { clearTimeout(timeout); }
    },
  };
}
