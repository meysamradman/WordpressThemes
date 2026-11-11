import {initializeMenus} from './menu.js';

const managers = new WeakMap();
export function observeMenus(scope = document) {
  if (managers.has(scope)) return managers.get(scope);
  const instances = new Map();
  let active = true; let queued = false;
  const scan = () => {
    queued = false;
    if (!active) return;
    for (const [root,cleanup] of instances) if (!root.isConnected) { cleanup(); instances.delete(root); }
    for (const root of scope.querySelectorAll('[data-rdct-menu]')) if (!instances.has(root)) instances.set(root,initializeMenus(root));
  };
  const observer = new MutationObserver(() => {if (!queued && active) {queued = true; queueMicrotask(scan);}});
  observer.observe(scope.documentElement || scope,{childList:true,subtree:true});
  const cleanup = () => {
    if (!active) return;
    active = false; observer.disconnect();
    for (const dispose of instances.values()) dispose();
    instances.clear(); managers.delete(scope);
  };
  managers.set(scope,cleanup); scan();
  return cleanup;
}
