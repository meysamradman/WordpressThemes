import { observePreviewMarkers, previewStyle } from './atomic-preview-observer.js';

export function observeRenderedPreviews(doc, config, type, initialize = () => () => {}) {
  return observePreviewMarkers(doc, `[data-rdct-atomic-${type}]`, (marker) => {
    let alive = true; let cleanup; const releases = []; const controller = new AbortController();
    const timer = setTimeout(async () => {
      try {
        const body = new URLSearchParams({ action: 'rdct_component_render', component: type, _ajax_nonce: config.renderNonce, settings: marker.dataset.options || '{}' });
        const response = await fetch(config.renderUrl, { method: 'POST', body, credentials: 'same-origin', signal: controller.signal });
        const result = await response.json();
        if (!alive) return;
        if (!response.ok || !result.success) throw new Error('Preview failed');
        const parsed = new doc.defaultView.DOMParser().parseFromString(result.data.html, 'text/html');
        parsed.querySelectorAll('script, iframe, object, embed').forEach((node) => node.remove());
        marker.replaceChildren(...[...parsed.head.querySelectorAll('style'), ...parsed.body.childNodes].map((node) => doc.importNode(node, true)));
        for (const style of result.data.styles || []) releases.push(previewStyle(doc, style));
        cleanup = initialize(marker);
      } catch (error) { if (alive && error.name !== 'AbortError') marker.textContent = config.renderError; }
    }, 200);
    return () => { alive = false; clearTimeout(timer); controller.abort(); cleanup?.(); releases.forEach((release) => release()); };
  });
}
