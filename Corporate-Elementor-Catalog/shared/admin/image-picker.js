export function pickMedia({ title, id = 0, onSelect, type = 'image', multiple = false, maximum = 50, media = window.wp?.media }) {
  if (!media) return;
  if (!['image', 'video', 'audio'].includes(type)) return;
  let active = true; let selectedOnce = false;
  const frame = media({ title, multiple, library: { type } });
  frame.once('open', () => {
    if (id > 0) frame.state().get('selection').add(media.attachment(id));
  });
  frame.once('select', () => {
    if (!active || selectedOnce) return;
    selectedOnce = true;
    if (multiple) {
      const ids = [...new Set(frame.state().get('selection').toJSON().filter((item) => !item.type || item.type === type).map((item) => Number(item.id)).filter((value) => Number.isSafeInteger(value) && value > 0))].slice(0, maximum);
      if (ids.length) onSelect(ids);
      return;
    }
    const item = frame.state().get('selection').first()?.toJSON();
    if (item?.type && item.type !== type) return;
    const selected = Number(item?.id);
    if (Number.isSafeInteger(selected) && selected > 0) onSelect(selected);
  });
  const release = () => { if (active) { active = false; frame.remove(); } };
  frame.once('close', () => queueMicrotask(release));
  frame.open();
  return () => { if (active) { frame.close(); release(); } };
}

export function pickImage(options) { return pickMedia({ ...options, type: 'image' }); }
