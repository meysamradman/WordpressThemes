import { reportRuntimeError } from '../../../../shared/frontend/lifecycle.js';
import { normalizePickerColor } from '../admin/atomic-color-control.js';

export function normalizeAtomicComponent(type, input, fields) {
  const values = {};
  for (const [key, field] of Object.entries(fields[type] || {})) {
    const value = input[key];
    if (typeof field.default === 'boolean') values[key] = value === true || value === 'true' ? true : value === false || value === 'false' ? false : field.default;
    else if (typeof field.default === 'number') values[key] = !['number', 'string'].includes(typeof value) || typeof value === 'string' && !value.trim() || !Number.isFinite(Number(value)) ? field.default : Math.min(field.max, Math.max(field.min, Number(value)));
    else values[key] = typeof value === 'string' ? value : field.default;
    if (field.options && !Object.hasOwn(field.options, values[key])) values[key] = field.default;
  }
  return values;
}

export function renderAtomicComponent(marker, config, initialize) {
  const doc = marker.ownerDocument;
  const type = marker.dataset.rdctAtomicComponent;
  const settings = normalizeAtomicComponent(type, marker.dataset, config.fields);
  let active = true; let cleanup;
  marker.replaceChildren();
  if (type === 'icon') {
    const svgText = config.icons[settings.name];
    if (!svgText) return () => {};
    const parsed = new doc.defaultView.DOMParser().parseFromString(svgText, 'image/svg+xml');
    const svg = doc.importNode(parsed.documentElement, true);
    if (svg.localName !== 'svg') return () => {};
    svg.setAttribute('width', settings.size); svg.setAttribute('height', settings.size); svg.setAttribute('stroke-width', settings.stroke);
    if (!settings.mirror) svg.classList.remove('rdct-ico--directional');
    const label = settings.label.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
    if (label) { svg.removeAttribute('aria-hidden'); svg.setAttribute('role', 'img'); svg.setAttribute('aria-label', label); }
    marker.append(svg);
  } else if (type === 'text-reveal' && settings.text.trim() && new TextEncoder().encode(settings.text).length <= 16000) {
    const root = doc.createElement(settings.tag);
    root.className = 'rdct-tr'; root.textContent = settings.text;
    const mode = settings.mode === 'scroll' && settings.text.split(/\s+/u).filter(Boolean).length > 400 ? 'static' : settings.mode;
    root.dataset.rdctTextReveal = mode;
    for (const [key, name] of Object.entries({ opacity: 'initialOpacity', final_opacity: 'finalOpacity', start: 'revealStart', end: 'revealEnd', from: 'revealFrom', scrub: 'revealScrub', stagger: 'revealStagger' })) root.dataset[name] = settings[key];
    for (const key of ['idle', 'active']) root.style.setProperty(`--rdct-text-${key}`, normalizePickerColor(settings[key]) ?? config.fields[type][key].default);
    if (settings.direction !== 'inherit') root.setAttribute('dir', settings.direction);
    if (mode === 'hover') root.tabIndex = 0;
    marker.append(root);
    if (mode === 'scroll') initialize().then(({ initializeTextReveal }) => { if (active && marker.isConnected) cleanup = initializeTextReveal(root); }).catch((error) => reportRuntimeError('atomic-text-preview', error));
  }
  return () => { active = false; cleanup?.(); };
}

export function observeAtomicComponents(doc, config, initialize, schedule = requestAnimationFrame, cancel = cancelAnimationFrame) {
  const instances = new Map(); let frame = 0; let active = true;
  const update = () => {
    frame = 0;
    for (const [marker, entry] of instances) if (!marker.isConnected) { entry.cleanup(); instances.delete(marker); }
    for (const marker of doc.querySelectorAll('[data-rdct-atomic-component]')) {
      const signature = JSON.stringify(marker.dataset);
      if (instances.get(marker)?.signature === signature) continue;
      instances.get(marker)?.cleanup();
      instances.set(marker, { signature, cleanup: renderAtomicComponent(marker, config, initialize) });
    }
  };
  const observer = new doc.defaultView.MutationObserver(() => { if (active && !frame) frame = schedule(update); });
  observer.observe(doc.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-rdct-atomic-component', ...new Set(Object.values(config.fields).flatMap((fields) => Object.keys(fields).map((key) => `data-${key}`)))] });
  update();
  return () => { if (!active) return; active = false; observer.disconnect(); if (frame) cancel(frame); for (const entry of instances.values()) entry.cleanup(); instances.clear(); };
}

