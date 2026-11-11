const initialized = new WeakSet();

export function initializeContentTables(root = document) {
  const regions = [];
  const label = document.body.dataset.tableLabel || '';
  root.querySelectorAll('.rdct-cnt table').forEach((table) => {
	if (table.closest('.rdct-tb')) return;
    if (table.parentElement?.closest('table')) return;
    let region = table.closest('.rdct-tbl-scroll, .wp-block-table');
    if (!region) {
      region = document.createElement('div');
      table.before(region);
      region.append(table);
      for (const name of ['alignwide', 'alignfull']) if (table.classList.contains(name)) region.classList.add(name);
    }
    if (initialized.has(region)) return;
    initialized.add(region);
    region.classList.add('rdct-tbl-scroll');
    const attributes = Object.fromEntries(['tabindex', 'role', 'aria-label'].map((name) => [name, region.getAttribute(name)]));
    regions.push({ region, table, attributes });
  });
  if (!regions.length) return () => {};
  const restore = ({ region, attributes }) => {
    for (const [name, value] of Object.entries(attributes)) {
      if (value === null) region.removeAttribute(name);
      else region.setAttribute(name, value);
    }
  };
  let frame;
  let active = true;
  const measure = () => {
    frame = null;
    for (const entry of regions) {
      const { region, table, attributes } = entry;
      if (region.scrollWidth <= region.clientWidth + 1) { restore(entry); continue; }
      if (attributes.tabindex === null) region.setAttribute('tabindex', '0');
      if (attributes.role === null && attributes['aria-label'] === null && !region.getAttribute('aria-labelledby')) {
        const name = table.querySelector('caption')?.textContent.trim() || region.querySelector('figcaption')?.textContent.trim() || label;
        if (name) { region.setAttribute('role', 'region'); region.setAttribute('aria-label', name); }
      }
    }
  };
  const schedule = () => { if (active && frame == null) frame = requestAnimationFrame(measure); };
  const observer = typeof ResizeObserver === 'function' ? new ResizeObserver(schedule) : null;
  regions.forEach(({ region, table }) => { observer?.observe(region); observer?.observe(table); });
  window.addEventListener('resize', schedule);
  document.fonts?.ready.then(schedule);
  schedule();
  return () => {
    active = false; observer?.disconnect(); cancelAnimationFrame(frame);
    window.removeEventListener('resize', schedule);
    regions.forEach((entry) => { restore(entry); initialized.delete(entry.region); });
  };
}
