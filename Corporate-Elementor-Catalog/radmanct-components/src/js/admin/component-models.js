export function createModelLoader({url, nonce}, fetchModel = fetch, origin = window.location.origin) {
  const endpoint = new URL(url, origin);
  if (endpoint.origin !== origin) throw new Error('Invalid library origin');
  const cache = new Map();
  return async ({id, language, mode, documentId, signal}) => {
    if (!/^[a-z0-9-]{1,80}$/.test(id) || !['fa','en'].includes(language) || !['classic','atomic'].includes(mode) || !Number.isInteger(Number(documentId)) || Number(documentId) <= 0) throw new Error('Invalid sample');
    const key = JSON.stringify([documentId,id,language,mode]);
    if (signal?.aborted) throw new Error('Sample cancelled');
    if (cache.has(key)) return cache.get(key);
    const body = new URLSearchParams({action:'radman_component_library', view:'model', _ajax_nonce:nonce, post_id:documentId, sample:id, language, mode});
    const response = await fetchModel(endpoint.href,{method:'POST',credentials:'same-origin',body,signal});
    const result = await response.json();
    if (!response.ok || !result.success || !result.data?.model?.elements?.length || signal?.aborted) throw new Error('Sample unavailable');
    const model = result.data.model;
    cache.set(key,model);
    if (cache.size > 32) cache.delete(cache.keys().next().value);
    return model;
  };
}
