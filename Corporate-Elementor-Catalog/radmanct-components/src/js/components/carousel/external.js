const documents = new WeakMap();
const actionable = 'button,a,[role="button"]';
const events = 'slideChange lock unlock update resize enable disable';
const validClass = (value) => typeof value === 'string' && /^[a-zA-Z][a-zA-Z0-9_-]{0,63}$/.test(value);

function blocked({slider, action}) {
  return slider.destroyed || slider.enabled === false || slider.isLocked ||
    (action === 'previous' ? slider.allowSlidePrev === false : slider.allowSlideNext === false) ||
    (!slider.params.rewind && (action === 'previous' ? slider.isBeginning : slider.isEnd));
}

function createContext(doc) {
  const entries = new Set();
  const originals = new Map();
  let targets = new Map();
  const restore = (node, value) => value === null ? node.removeAttribute('aria-disabled') : node.setAttribute('aria-disabled', value);
  const refresh = () => {
    targets = new Map();
    for (const entry of entries) {
      if (entry.slider.destroyed) continue;
      for (const [action, name] of entry.classes) {
        for (const wrapper of doc.querySelectorAll(`.${name}`)) {
          const nodes = wrapper.matches(actionable) ? [wrapper] : wrapper.querySelectorAll(actionable);
          for (const node of nodes) {
            if (!targets.has(node)) targets.set(node, []);
            const matches = targets.get(node);
            if (!matches.some((match) => match.slider === entry.slider && match.action === action)) matches.push({...entry, action});
          }
        }
      }
    }
    for (const [node, original] of originals) if (!targets.has(node)) { restore(node, original); originals.delete(node); }
    for (const [node, matches] of targets) {
      if (!originals.has(node)) originals.set(node, node.getAttribute('aria-disabled'));
      node.setAttribute('aria-disabled', String(Boolean(originals.get(node) === 'true' || node.disabled || matches.length !== 1 || blocked(matches[0]))));
    }
  };
  const activate = (event) => {
    const node = event.target?.closest?.(actionable);
    if (!node || event.defaultPrevented) return;
    if (event.type === 'keydown') {
      if (event.repeat || node.tagName === 'BUTTON' || (event.key === 'Enter' && node.tagName === 'A') || !['Enter',' '].includes(event.key)) return;
    } else if (event.button !== undefined && event.button !== 0) return;
    if (![...entries].some((entry) => entry.classes.some(([,name]) => node.closest(`.${name}`)))) return;
    refresh();
    const matches = targets.get(node);
    if (!matches) return;
    event.preventDefault();
    if (node.getAttribute('aria-disabled') === 'true') return;
    const entry = matches[0];
    entry.interact();
    if (entry.action === 'previous') entry.slider.slidePrev(); else entry.slider.slideNext();
    refresh();
  };
  doc.addEventListener('click', activate);
  doc.addEventListener('keydown', activate);
  const mutations = (records) => {
    const names = new Set([...entries].flatMap((entry) => entry.classes.map(([,name]) => name)));
    if (records.some((record) => record.type === 'childList' ||
      [...(record.oldValue ?? '').split(/\s+/), ...(record.target.getAttribute('class') ?? '').split(/\s+/)].some((name) => names.has(name)))) refresh();
  };
  const observer = doc.defaultView?.MutationObserver ? new doc.defaultView.MutationObserver(mutations) : null;
  if (doc.documentElement) observer?.observe(doc.documentElement, {childList:true, subtree:true, attributes:true, attributeFilter:['class'], attributeOldValue:true});
  return {entries, refresh, destroy() {
    observer?.disconnect();
    doc.removeEventListener('click', activate);
    doc.removeEventListener('keydown', activate);
    for (const [node, original] of originals) restore(node, original);
    originals.clear(); targets.clear();
  }};
}

export function registerCarouselExternal(root, slider, options, interact = () => {}) {
  const classes = [['previous', options.external_previous_class], ['next', options.external_next_class]].filter(([,name]) => validClass(name));
  if (!classes.length || options.mode === 'marquee') return () => {};
  const doc = root.ownerDocument;
  if (!documents.has(doc)) documents.set(doc, createContext(doc));
  const context = documents.get(doc);
  const entry = {slider, classes, interact};
  context.entries.add(entry);
  slider.on(events, context.refresh);
  context.refresh();
  let alive = true;
  return () => {
    if (!alive) return;
    alive = false;
    slider.off(events, context.refresh);
    context.entries.delete(entry);
    if (context.entries.size) context.refresh();
    else { context.destroy(); documents.delete(doc); }
  };
}
