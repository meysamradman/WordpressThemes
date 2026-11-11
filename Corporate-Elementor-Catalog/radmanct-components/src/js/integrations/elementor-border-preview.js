import {observeBorders} from '../components/hover-border.js';

export function connectBorderPreview(doc, editor, fields) {
  const subscriptions = new Map(); let frame = 0; let active = true;
  const scan = () => {
    frame = 0; if (!active) return;
    for (const [root, entry] of subscriptions) if (!root.isConnected) {entry.detach(); subscriptions.delete(root);}
    for (const root of doc.querySelectorAll('.elementor-element[data-id]')) {
      const settings = editor?.getContainer?.(root.dataset.id)?.settings;
      if (!settings?.get || !settings.on || !settings.off || subscriptions.get(root)?.settings === settings) continue;
      subscriptions.get(root)?.detach();
      const sync = () => {
        const values = Object.fromEntries(fields.map((key) => { const raw = settings.get('rdct_border_'+key); return [key, raw && typeof raw === 'object' && '$$type' in raw ? raw.disabled ? undefined : raw.value : raw]; }));
        if (values.enabled === true || values.enabled === 'yes') { const value = JSON.stringify(values); if (root.getAttribute('data-rdct-border') !== value) root.setAttribute('data-rdct-border',value); }
        else root.removeAttribute('data-rdct-border');
      };
      settings.on('change',sync); subscriptions.set(root,{settings,detach: () => settings.off('change',sync)}); sync();
    }
  };
  const queue = () => {if (active && !frame) frame = requestAnimationFrame(scan);};
  const observer = new doc.defaultView.MutationObserver(queue); observer.observe(doc.documentElement,{subtree:true,childList:true,attributes:true,attributeFilter:['data-id']});
  doc.defaultView.addEventListener('elementor/frontend/init',queue); doc.defaultView.addEventListener('load',queue); scan();
  return () => {active = false; observer.disconnect(); if (frame) cancelAnimationFrame(frame); doc.defaultView.removeEventListener('elementor/frontend/init',queue); doc.defaultView.removeEventListener('load',queue); for (const entry of subscriptions.values()) entry.detach(); subscriptions.clear();};
}

if (window.parent !== window && window.rdctBorderFields) {
  const detach = connectBorderPreview(document,window.parent.elementor,window.rdctBorderFields); const cleanup = observeBorders(document);
  window.addEventListener('pagehide',() => {detach(); cleanup();},{once:true});
}
