export async function saveSettings(config, values, request = fetch) {
  const body = new URLSearchParams({ action: 'rdct_save_settings', nonce: config.save.nonce, values: JSON.stringify(values) });
  const response = await request(config.save.url, { method: 'POST', credentials: 'same-origin', cache: 'no-store', body });
  const result = await response.json().catch(() => null);
  const saved = result?.data?.values;
  if (!response.ok || result?.success !== true || !saved || typeof saved !== 'object' || Array.isArray(saved)
    || Object.keys(config.defaults).some(key => !Object.hasOwn(saved, key))) {
    throw new Error(typeof result?.data?.message === 'string' ? result.data.message : config.labels.saveError);
  }
  return saved;
}

export function mergeSaved(current, submitted, saved) {
  return Object.fromEntries(Object.entries(saved).map(([key, value]) => [key, current[key] === submitted[key] ? value : current[key]]));
}
