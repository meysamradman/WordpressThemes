import {compileCustomCss} from './custom-css-compiler.js';

export function connectCustomCssPreview(document, editor) {
  const subscriptions = new Map(); let frame = 0; let active = true;
  const scan = () => {
    frame = 0;
    if (!active) return;
    for (const [root, subscription] of subscriptions) if (!root.isConnected) { subscription.detach(); subscriptions.delete(root); }
    for (const root of document.querySelectorAll('.elementor-element[data-id], [data-element_type][data-id]')) {
      const id = root.getAttribute('data-id'); let settings;
      try { settings = editor?.getContainer?.(id)?.settings; } catch { continue; }
      if (!settings?.get || !settings.on || !settings.off || subscriptions.get(root)?.settings === settings) continue;
      subscriptions.get(root)?.detach();
      let style;
      const sync = () => {
        if (!active || !root.isConnected) return;
        const result = compileCustomCss(settings.get('rdct_custom_css'),id);
        for (const name of [...root.classList]) if (name.startsWith(`rdct-css-${id}-`)) root.classList.remove(name);
        if (!result) { style?.remove(); style = null; return; }
        root.classList.add(result.class);
        if (!style) { style = document.createElement('style'); document.head.append(style); }
        style.textContent = result.css;
      };
      settings.on('change:rdct_custom_css',sync);
      subscriptions.set(root,{settings,detach:() => {settings.off('change:rdct_custom_css',sync);style?.remove();}});
      sync();
    }
  };
  const queue = () => { if (active && !frame) frame = requestAnimationFrame(scan); };
  const observer = new MutationObserver(queue);
  observer.observe(document.body,{childList:true,subtree:true,attributes:true,attributeFilter:['data-id']});
  editor?.on?.('document:loaded',queue);
  window.addEventListener('load',queue);
  scan();
  return () => { active = false; observer.disconnect(); if (frame) cancelAnimationFrame(frame); for (const subscription of subscriptions.values()) subscription.detach(); subscriptions.clear(); editor?.off?.('document:loaded',queue); window.removeEventListener('load',queue); };
}

if (typeof window !== 'undefined' && window.parent !== window) {
  const cleanup = connectCustomCssPreview(document,window.parent.elementor);
  window.addEventListener('pagehide',cleanup,{once:true});
}
