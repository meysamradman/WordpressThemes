export function initializeNavigationPreview(config) {
const pending = new Map();
let active = true; let queued = false; let sequence = 0;
const scan = () => {
  queued = false;
  if (!active || !config) return;
  for (const [root,entry] of pending) if (!root.isConnected) { entry.controller.abort(); pending.delete(root); }
  for (const root of document.querySelectorAll('[data-rdct-atomic-navigation]')) {
    const key = root.getAttribute('data-options');
    if (!key || key.length > 8192 || pending.get(root)?.key === key) continue;
    const instance = pending.get(root)?.instance ?? `preview-${++sequence}`;
    pending.get(root)?.controller.abort();
    const controller = new AbortController(); const entry = {key,controller,instance}; pending.set(root,entry);
    const body = new URLSearchParams({action:'rdct_navigation_preview',panel:root.hasAttribute('data-panel-preview')?'1':'0',nonce:config.nonce,postId:config.postId,instance,settings:key});
    fetch(config.url,{method:'POST',credentials:'same-origin',body,signal:controller.signal}).then((response) => {
      if (!response.ok) throw new Error('Preview request failed');
      return response.json();
    }).then((result) => {
      if (!active || !root.isConnected || pending.get(root) !== entry) return;
      if (!result.success || typeof result.data?.html !== 'string') throw new Error('Invalid preview');
      root.innerHTML = result.data.html;
    }).catch((error) => {if (error.name !== 'AbortError' && active && root.isConnected && pending.get(root) === entry) root.textContent = config.error;});
  }
};
const observer = config ? new MutationObserver(() => {if (!queued && active) {queued = true; queueMicrotask(scan);}}) : null;
observer?.observe(document.body,{childList:true,subtree:true,attributes:true,attributeFilter:['data-options']});
scan();
  return () => {
  active = false; observer?.disconnect();
  for (const entry of pending.values()) entry.controller.abort();
  pending.clear();
  };
}
