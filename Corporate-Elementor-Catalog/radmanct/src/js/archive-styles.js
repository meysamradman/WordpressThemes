const pending = new Map();

function loadStyle(href, doc) {
  if (pending.has(href)) return pending.get(href);
  const existing = [...doc.querySelectorAll('link[rel="stylesheet"]')].find(link => link.href === href);
  if (existing?.sheet) return Promise.resolve();
  const link = existing || doc.createElement('link');
  const task = new Promise((resolve, reject) => {
    const cleanup = () => { link.removeEventListener('load', ready); link.removeEventListener('error', failed); };
    const ready = () => { cleanup(); resolve(); };
    const failed = () => { cleanup(); if (!existing) link.remove(); pending.delete(href); reject(new Error('Archive stylesheet failed')); };
    link.addEventListener('load', ready); link.addEventListener('error', failed);
    if (!existing) { link.rel = 'stylesheet'; link.href = href; doc.head.append(link); }
  });
  pending.set(href, task);
  return task;
}

export async function prepareArchiveStyles(data, signal, doc = document) {
  const result = {...data}; const tasks = [];
  for (const key of ['intro','html','pageTitle']) {
    if (typeof result[key] !== 'string') continue;
    const template = doc.createElement('template'); template.innerHTML = result[key];
    for (const link of template.content.querySelectorAll('link[rel="stylesheet"][href]')) {
      const url = new URL(link.getAttribute('href'), doc.baseURI);
      if (url.origin !== new URL(doc.baseURI).origin) throw new Error('Archive stylesheet must be local');
      tasks.push(loadStyle(url.href, doc)); link.remove();
    }
    for (const preview of template.content.querySelectorAll('[data-rdct-preview-runtime]')) {
      const url = new URL(preview.getAttribute('data-rdct-preview-runtime'), doc.baseURI);
      if (url.origin !== new URL(doc.baseURI).origin || !/\/dist\/js\/card-preview[^/]*\.min\.js$/.test(url.pathname)) throw new Error('Archive media runtime must be local');
      tasks.push(import(/* @vite-ignore */ url.href));
    }
    result[key] = template.innerHTML;
  }
  if (signal.aborted) throw new DOMException('Aborted', 'AbortError');
  let abort;
  const cancelled = new Promise((_, reject) => { abort = () => reject(new DOMException('Aborted', 'AbortError')); signal.addEventListener('abort', abort, {once:true}); });
  try { await Promise.race([Promise.all(tasks), cancelled]); }
  finally { signal.removeEventListener('abort', abort); }
  return result;
}
