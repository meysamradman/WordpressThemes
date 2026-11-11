import { observeContainerMotion } from '../container-motion-manager.js';
import { layouts, motionElements } from '../components/container-motion-options.js';
import { motionValue, hasNativeMotion } from './elementor-motion-values.js';

export function connectMotionPreview(document, editor, config) {
  const subscriptions = new Map();
  let current = true; let frame = 0;
  const actionElement = (action) => (source) => {
    if (!current) return;
    let id;
    try { id = source?.container?.id ?? source?.id; } catch { return; }
    if (typeof id !== 'string' || !/^[a-zA-Z0-9_-]{1,80}$/.test(id)) return;
    for (const [root, subscription] of subscriptions) {
      if (!root.isConnected || root.getAttribute('data-id') !== id) continue;
      subscription.sync();
      if (root.getAttribute('data-rdct-motion')) document.dispatchEvent(new CustomEvent(`rdct:motion:${action}`, { detail: { root } }));
    }
  };
  const playElement = actionElement('play'); const resetElement = actionElement('reset');
  const scan = () => {
    frame = 0;
    if (!current) return;
    for (const [root, subscription] of subscriptions) {
      if (!root.isConnected) { subscription.detach(); subscriptions.delete(root); }
    }
    for (const root of document.querySelectorAll(motionElements)) {
      const id = root.getAttribute('data-id');
      const container = id ? editor?.getContainer?.(id) : null;
      const settings = container?.settings;
      const existing = subscriptions.get(root);
      if (existing?.settings === settings) continue;
      existing?.detach();
      subscriptions.delete(root);
      if (!settings?.get || !settings?.on || !settings?.off) {
        if (existing) { root.removeAttribute('data-rdct-motion'); root.removeAttribute('data-rdct-native-motion'); }
        continue;
      }
      const sync = () => {
        if (!current || !root.isConnected || root.getAttribute('data-id') !== id) return;
        const values = {};
        for (const key of config.fields) {
          const raw = settings.get(`rm_motion_${key}`);
          values[key] = motionValue(raw, config.types?.[key]);
          if ((config.sliders ?? ['duration','stagger','delay']).includes(key)) {
            const slider = settings.get(`rm_motion_${key}_slider`)?.size;
            if (typeof slider === 'number' || typeof slider === 'string' && slider.trim() !== '' && Number.isFinite(Number(slider))) values[key] = slider;
          }
          if (config.units?.[key]) {
            const slider = settings.get(`rm_motion_${config.units[key].source}_slider`);
            if ((typeof slider?.size === 'number' || typeof slider?.size === 'string' && slider.size.trim() !== '' && Number.isFinite(Number(slider.size))) && config.units[key].values.includes(slider.unit)) values[key] = slider.unit;
          }
        }
        if (!root.matches(layouts)) { values.targets = 'self'; values.horizontal = false; values.reveal = false; }
        const native = hasNativeMotion(settings, container.model?.get('interactions'));
        if (native) root.setAttribute('data-rdct-native-motion', 'true');
        else root.removeAttribute('data-rdct-native-motion');
        if (!native && [values.animate, values.parallax, values.reveal, values.horizontal].some((value) => value === true || value === 'yes')) {
          const value = JSON.stringify(values);
          if (root.getAttribute('data-rdct-motion') !== value) root.setAttribute('data-rdct-motion', value);
        } else root.removeAttribute('data-rdct-motion');
      };
      settings.on('change', sync); container.model?.on('change:interactions', sync);
      const detach = () => { settings.off('change', sync); container.model?.off('change:interactions', sync); };
      subscriptions.set(root, { settings, sync, detach }); sync();
    }
  };
  const queue = () => { if (!frame && current) frame = requestAnimationFrame(scan); };
  const observer = new MutationObserver(queue);
  observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-id'] });
  editor?.channels?.editor?.on('rm_motion_play', playElement);
  editor?.channels?.editor?.on('rm_motion_reset', resetElement);
  window.addEventListener('elementor/frontend/init', queue);
  window.addEventListener('load', queue);
  scan();
  return () => {
    if (!current) return;
    current = false; observer.disconnect(); if (frame) cancelAnimationFrame(frame);
    for (const { detach } of subscriptions.values()) detach();
    subscriptions.clear(); editor?.channels?.editor?.off('rm_motion_play', playElement);
    editor?.channels?.editor?.off('rm_motion_reset', resetElement);
    window.removeEventListener('elementor/frontend/init', queue); window.removeEventListener('load', queue);
  };
}

if (window.rdctMotion && window.parent !== window) {
  const cleanupControls = connectMotionPreview(document, window.parent.elementor, window.rdctMotion);
  const cleanupMotion = observeContainerMotion(document, true);
  window.addEventListener('pagehide', () => { cleanupControls(); cleanupMotion(); }, { once: true });
}
