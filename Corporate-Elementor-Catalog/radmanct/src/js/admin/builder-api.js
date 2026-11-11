const object = (value) => value !== null && typeof value === 'object' && !Array.isArray(value);
const layout = (value) => object(value) && value.version === 1 && typeof value.enabled === 'boolean' && Array.isArray(value.rows)
  && value.rows.every((row) => object(row) && typeof row.id === 'string' && Array.isArray(row.items) && row.items.every((item) => object(item) && typeof item.id === 'string' && typeof item.type === 'string'));

function validResponse(operation, data) {
  if (!object(data)) return false;
  if (operation === 'preview') return typeof data.html === 'string' && !!data.html.trim();
  if (operation === 'validate') return layout(data.layout);
  if (operation === 'assignments') return object(data.defaults) && Array.isArray(data.layouts) && data.layouts.every(entry => object(entry) && typeof entry.id === 'string' && typeof entry.title === 'string' && typeof entry.enabled === 'boolean' && (entry.language === null || typeof entry.language === 'string'));
  return typeof data.id === 'string' && !!data.id && object(data.data) && layout(data.data.layout)
    && Number.isSafeInteger(data.data.revision) && data.data.revision >= 0
    && Array.isArray(data.layouts) && data.layouts.every((entry) => object(entry) && typeof entry.id === 'string' && typeof entry.title === 'string')
    && data.defaults !== null && typeof data.defaults === 'object' && (data.warning === undefined || typeof data.warning === 'string');
}

export function createBuilderRequest(config) {
return async function request(operation, id, fields = {}, signal) {
  const body = new URLSearchParams({ action: 'radman_builder', nonce: config.nonce, kind: config.kind, id, operation });
  for (const [key, value] of Object.entries(fields)) body.set(key, typeof value === 'object' ? JSON.stringify(value) : String(value));
  const response = await fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', cache: 'no-store', body, signal });
  const result = await response.json().catch(() => null);
  if (!response.ok || result?.success !== true) throw new Error(typeof result?.data?.message === 'string' ? result.data.message : config.labels.requestError);
  if (!validResponse(operation, result.data)) throw new Error(config.labels.requestError);
  return result.data;
};
}

export const request = (...args) => createBuilderRequest(window.rdctBuilder)(...args);

export function builderSaveRequest(stored, layout, title = '') {
  return stored.creating
    ? {operation:'create', id:'default', fields:{layout,title:title.trim()}}
    : {operation:'save', id:stored.id, fields:{layout,revision:stored.data.revision,title:title.trim()}};
}
