import { reportRuntimeError } from '../../../../shared/frontend/lifecycle.js';

const observationPools = new WeakMap();

function subscribe(doc, update) {
  let pool = observationPools.get(doc);
  if (!pool) {
    const callbacks = new Set();
    pool = { callbacks, frame: 0, observer: null };
    pool.observer = new doc.defaultView.MutationObserver(() => {
      if (pool.frame || !callbacks.size) return;
      pool.frame = doc.defaultView.requestAnimationFrame(() => {
        pool.frame = 0;
        for (const callback of [...callbacks]) if (callbacks.has(callback)) {
          try { callback(); } catch (error) { reportRuntimeError('atomic-preview', error, doc.defaultView); }
        }
      });
    });
    pool.observer.observe(doc.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-items', 'data-options'] });
    observationPools.set(doc, pool);
  }
  pool.callbacks.add(update);
  return () => {
    pool.callbacks.delete(update);
    if (pool.callbacks.size) return;
    pool.observer.disconnect();
    if (pool.frame) doc.defaultView.cancelAnimationFrame(pool.frame);
    observationPools.delete(doc);
  };
}

export function observePreviewMarkers(doc, selector, render) {
  const instances = new Map(); let alive = true;
  const update = () => {
    if (!alive) return;
    const markers = new Set(doc.querySelectorAll(selector));
    for (const [marker, entry] of instances) if (!marker.isConnected || !markers.has(marker)) { entry.cleanup(); instances.delete(marker); }
    markers.forEach((marker) => {
      const signature = JSON.stringify(marker.dataset);
      if (instances.get(marker)?.signature === signature) return;
      instances.get(marker)?.cleanup();
      instances.set(marker, { signature, cleanup: render(marker) });
    });
  };
  const unsubscribe = subscribe(doc, update);
  update();
  return () => { if (!alive) return; alive = false; unsubscribe(); instances.forEach(({ cleanup }) => cleanup()); instances.clear(); };
}

const stylePools = new WeakMap();
export function previewStyle(doc, url) {
  if (!url) return () => {};
  if (!stylePools.has(doc)) stylePools.set(doc, new Map());
  const pool = stylePools.get(doc);
  if (!pool.has(url)) {
    let link = [...doc.querySelectorAll('link[rel="stylesheet"]')].find((entry) => entry.href === url);
    const owned = !link;
    if (!link) { link = doc.createElement('link'); link.rel = 'stylesheet'; link.href = url; doc.head.append(link); }
    pool.set(url, { link, owned, count: 0 });
  }
  const entry = pool.get(url); ++entry.count;
  let active = true;
  return () => { if (!active) return; active = false; if (--entry.count === 0) { if (entry.owned) entry.link.remove(); pool.delete(url); } };
}
