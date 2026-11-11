const documents = new WeakMap();
const validId = (value) => typeof value === 'string' && /^[a-zA-Z][a-zA-Z0-9_-]{0,63}$/.test(value);

export function registerCarouselSync(root, slider, options) {
  const id = validId(options.controller_id) ? options.controller_id : '';
  const targets = typeof options.controlled_ids === 'string'
    ? [...new Set(options.controlled_ids.split(',').map((value) => value.trim().replace(/^#/, '')).filter(validId))].slice(0, 16) : [];
  if ((!id && !targets.length) || options.mode === 'marquee') return () => {};
  const doc = root.ownerDocument;
  let context = documents.get(doc);
  if (!context) { context = {entries: new Set(), publishing: false}; documents.set(doc, context); }
  const entry = {id, targets, slider, progress: options.sync_mode === 'progress', duration: 0};
  context.entries.add(entry);
  const publish = (source) => {
    if (context.publishing || !context.entries.has(source) || source.slider.destroyed) return;
    const index = source.slider.activeIndex;
    const progress = Math.max(0, Math.min(1, source.slider.progress));
    if (source.progress ? !Number.isFinite(progress) : !Number.isInteger(index) || index < 0) return;
    context.publishing = true;
    try {
      const visited = new Set([source]); const queue = [source];
      while (queue.length) {
        const current = queue.shift();
        for (const targetId of current.targets) {
          const matches = [...context.entries].filter((candidate) => candidate.id === targetId && !candidate.slider.destroyed);
          if (matches.length !== 1) continue;
          const target = matches[0];
          if (visited.has(target)) continue;
          visited.add(target); queue.push(target);
          const count = target.slider.slides?.length ?? 0;
          if (!count) continue;
          if (source.progress) {
            if (typeof target.slider.setProgress === 'function') {
              target.slider.setProgress(progress, target.slider.params.speed === 0 ? 0 : source.duration);
              if (target.slider.params.autoHeight) target.slider.updateAutoHeight?.(source.duration);
            }
          } else {
            const next = Math.min(index, count - 1);
            if (target.slider.activeIndex !== next) target.slider.slideTo(next, target.slider.params.speed, true);
          }
        }
      }
    } finally { context.publishing = false; }
  };
  const change = () => { if (!entry.progress) publish(entry); };
  const translate = () => { if (entry.progress) publish(entry); };
  const transition = (_, duration) => { entry.duration = Number.isFinite(duration) && duration >= 0 ? duration : 0; };
  slider.on('slideChange', change);
  if (entry.progress) { slider.on('setTranslate', translate); slider.on('setTransition', transition); }
  const incoming = [...context.entries].find((source) => source !== entry && id && source.targets.includes(id));
  if (incoming) publish(incoming); else publish(entry);
  let alive = true;
  return () => {
    if (!alive) return;
    alive = false; slider.off('slideChange', change); context.entries.delete(entry);
    if (entry.progress) { slider.off('setTranslate', translate); slider.off('setTransition', transition); }
    if (!context.entries.size) documents.delete(doc);
  };
}
