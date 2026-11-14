import {observeMutations} from '../runtime/observers.js';

const managers = new WeakMap();

export function observeCardPreviews(doc = document) {
  if (managers.has(doc)) return managers.get(doc);
  const view = doc.defaultView;
  const preference = view.matchMedia('(prefers-reduced-motion: reduce)');
  const fine = view.matchMedia('(hover: hover) and (pointer: fine)');
  const connection = view.navigator?.connection;
  const entries = new Map();
  let alive = true;
  const updateState=(entry,playing)=>entry.root.classList.toggle('is-play',playing);
  const stop = (entry) => {
    if (entry.desired || entry.requested) {
      ++entry.token; entry.desired = false; entry.requested = false; entry.video.pause();
    }
    updateState(entry, false);
  };
  const failed = (entry) => { stop(entry); entry.blocked = true; };
  const start = (entry) => {
    entry.desired = true;
    if (entry.requested) return;
    entry.requested = true;
    const token = ++entry.token;
    try {
      entry.video.muted=true;entry.video.defaultMuted=true;entry.video.volume=0;entry.video.loop=true;entry.video.controls=false;entry.video.playsInline=true;
      if (!entry.video.getAttribute('src')) {
        const url = new URL(entry.video.dataset.src, view.location.href);
        if (url.origin !== view.location.origin || !entry.video.canPlayType(entry.video.dataset.mime)) { failed(entry); return; }
        entry.video.muted = true; entry.video.defaultMuted = true; entry.video.playsInline = true;
        const poster = entry.root.querySelector('img');
        if (poster?.currentSrc) entry.video.poster = poster.currentSrc;
        entry.video.src = url.href;
      }
      Promise.resolve(entry.video.play()).then(() => {
        if (!alive || !entry.root.isConnected || token !== entry.token || !entry.desired) return;
        updateState(entry, true);
      }).catch(() => { if (alive && token === entry.token) failed(entry); });
    } catch { failed(entry); }
  };
  function sync() {
    if (!alive) return;
    const automatic = Boolean(observer) && !preference.matches && !connection?.saveData;
    const candidates = [...entries.values()].filter(entry => entry.root.isConnected && !doc.hidden && entry.ratio >= .45 && !entry.blocked
      && automatic && (entry.root.dataset.mode !== 'hover' || entry.hover || entry.focus));
    candidates.sort((a, b) => Number(b.hover || b.focus) - Number(a.hover || a.focus) || b.ratio - a.ratio);
    const active = new Set(candidates.slice(0, 2));
    for (const entry of entries.values()) {
      if (active.has(entry)) start(entry);
      else {
        stop(entry);
        if ((doc.hidden || entry.ratio < .45) && entry.video.getAttribute('src')) { entry.video.removeAttribute('src'); entry.video.load(); }
      }
    }
  }
  const observer = view.IntersectionObserver ? new view.IntersectionObserver(records => {
    for (const record of records) {
      const entry = entries.get(record.target);
      if (entry) entry.ratio = record.isIntersecting ? record.intersectionRatio : 0;
    }
    sync();
  }, { threshold: [0, .45, .8, 1] }) : null;

  const add = (root) => {
    if (entries.has(root) || !root.isConnected) return;
    const video = root.querySelector('video[data-src]');
    const owner = root.closest('.rdc-card-pre-own');
    if (!video) return;
    const entry = { root, video, ratio: observer ? 0 : 1, hover: false, focus: false, desired: false, requested: false, blocked: false, token: 0, listeners: [] };
    const listen = (node, name, fn) => { node.addEventListener(name, fn); entry.listeners.push(() => node.removeEventListener(name, fn)); };
    const interaction = root.closest('a') || root;
    listen(interaction, 'pointerenter', () => { if (fine.matches) { entry.hover = true; sync(); } });
    listen(interaction, 'pointerleave', () => { entry.hover = false; sync(); });
    listen(owner, 'focusin', () => { entry.focus = true; sync(); });
    listen(owner, 'focusout', event => { if (!owner.contains(event.relatedTarget)) { entry.focus = false; sync(); } });
    listen(video, 'error', () => { failed(entry); video.removeAttribute('src'); video.load(); });
    listen(video, 'playing', () => { if (alive && entry.desired && root.isConnected) updateState(entry, true); else video.pause(); });
    updateState(entry, false); entries.set(root, entry); observer?.observe(root);
  };
  const discover = (node) => {
    if (node.matches?.('[data-rdc-card-pre]')) add(node);
    for (const root of node.querySelectorAll?.('[data-rdc-card-pre]') || []) add(root);
  };
  const remove = (entry) => {
    stop(entry); observer?.unobserve(entry.root); entry.listeners.forEach(release => release());
    if (entry.video.getAttribute('src')) { entry.video.removeAttribute('src'); entry.video.load(); }
    entries.delete(entry.root);
  };
  const releaseMutations = observeMutations(view,records => {
    for (const record of records) for (const node of record.addedNodes) discover(node);
    for (const entry of entries.values()) if (!entry.root.isConnected) remove(entry);
    sync();
  },doc);
  discover(doc);
  preference.addEventListener('change', sync); connection?.addEventListener?.('change', sync);
  doc.addEventListener('visibilitychange', sync);
  const hide = () => { for (const entry of entries.values()) stop(entry); };
  const show = () => sync();
  view.addEventListener('pagehide', hide); view.addEventListener('pageshow', show);
  sync();
  const cleanup = () => {
    if (!alive) return;
    alive = false; releaseMutations(); observer?.disconnect();
    for (const entry of [...entries.values()]) remove(entry);
    preference.removeEventListener('change', sync); connection?.removeEventListener?.('change', sync);
    doc.removeEventListener('visibilitychange', sync); view.removeEventListener('pagehide', hide); view.removeEventListener('pageshow', show);
    managers.delete(doc);
  };
  managers.set(doc, cleanup); return cleanup;
}
