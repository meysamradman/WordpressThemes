export function motionValue(raw, type) {
  if (!raw || typeof raw !== 'object' || !('$$type' in raw)) return raw;
  if (raw.disabled === true || raw.$$type !== type) return undefined;
  return raw.value ?? undefined;
}
export function hasNativeMotion(settings, interactions) {
  for (const key of ['_animation','_animation_tablet','_animation_mobile','animation','animation_tablet','animation_mobile']) {
    const value = motionValue(settings.get(key), 'string');
    if (typeof value === 'string' && value !== '' && value !== 'none') return true;
  }
  if (['motion_fx_motion_fx_scrolling','motion_fx_motion_fx_mouse'].some((key) => { const raw = settings.get(key); const value = motionValue(raw, 'boolean') ?? motionValue(raw, 'string'); return value === true || value === 'yes'; })) return true;
  if (typeof interactions === 'string') {
    try { interactions = JSON.parse(interactions); } catch { return false; }
  }
  const items = interactions?.value?.items ?? interactions?.items;
  return !interactions?.disabled && Array.isArray(items) && items.length > 0;
}
