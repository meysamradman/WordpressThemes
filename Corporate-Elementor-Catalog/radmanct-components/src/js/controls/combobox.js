import { reportRuntimeError } from '../../../../shared/frontend/lifecycle.js';

export function normalizeSearch(value) {
  return String(value).normalize('NFKD').replace(/\p{M}/gu, '').replace(/[\u064a\u0649]/g, '\u06cc')
    .replace(/\u0643/g, '\u06a9').replace(/\u0640/g, '').replace(/[\u200c\u200d]/g, ' ').replace(/\s+/g, ' ').trim().toLowerCase();
}

export function bindCombobox(select, TomSelect, plugin) {
  const label = select.labels?.[0];
  const labelFor = label?.getAttribute('for');
  const pluginName = select.multiple ? 'remove_button' : 'dropdown_input';
  TomSelect.define(pluginName, plugin);
  const control = new TomSelect(select, {
    plugins: select.multiple ? { remove_button: { title: select.dataset.removeLabel } } : ['dropdown_input'],
    create: false, allowEmptyOption: true, maxOptions: 200, maxItems: select.multiple ? null : 1,
    closeAfterSelect: !select.multiple, selectOnTab: false, sortField: '$order',
    wrapperClass: 'rdct-combobox', controlClass: 'rdct-combobox__control',
    dropdownClass: 'rdct-combobox__dropdown', dropdownContentClass: 'rdct-combobox__options',
    itemClass: 'rdct-combobox__item', optionClass: 'rdct-combobox__option',
    score(query) {
      const score = this.getScoreFunction(normalizeSearch(query));
      return (item) => score({ ...item, text: normalizeSearch(item.text) });
    },
    render: {
      no_results: () => {
        const message = select.ownerDocument.createElement('div');
        message.className = 'rdct-combobox__empty';
        message.textContent = select.dataset.emptyLabel;
        message.setAttribute('role', 'status');
        return message;
      },
    },
  });
  control.wrapper.dir = select.ownerDocument.defaultView.getComputedStyle(select).direction;
  control.control_input.placeholder = select.dataset.searchLabel || '';
  control.control_input.setAttribute('aria-label', select.dataset.searchLabel || select.getAttribute('aria-label') || '');
  const sync = () => {
    control.sync();
    for (const node of [control.focus_node, control.control_input]) {
      for (const attribute of ['aria-invalid', 'aria-describedby']) {
        const value = select.getAttribute(attribute);
        if (value === null) node.removeAttribute(attribute);
        else node.setAttribute(attribute, value);
      }
    }
  };
  select.addEventListener('change', sync);
  select.addEventListener('rdct:field-sync', sync);
  sync();
  return {
    sync,
    destroy() {
      select.removeEventListener('change', sync);
      select.removeEventListener('rdct:field-sync', sync);
      const html = select.innerHTML;
      const values = [...select.selectedOptions].map((option) => option.value);
      control.destroy();
      select.innerHTML = html;
      for (const option of select.options) option.selected = values.includes(option.value);
      if (label) {
        if (labelFor === null) label.removeAttribute('for');
        else label.setAttribute('for', labelFor);
      }
    },
  };
}

export function observeComboboxes(view = window, load = async (select) => {
  const [core, plugin] = await Promise.all([
    import('tom-select/base'),
    select.multiple ? import('tom-select/plugins/remove_button/plugin.js') : import('tom-select/plugins/dropdown_input/plugin.js'),
  ]);
  return { TomSelect: core.default, plugin: plugin.default };
}) {
  const document = view.document;
  const selector = 'select[data-rdct-combobox]';
  const entries = new Map();
  let active = true;
  const mount = (select) => {
    if (entries.has(select) || select.tomselect || !select.isConnected) return;
    const entry = { alive: true, control: null };
    entries.set(select, entry);
    Promise.resolve().then(() => load(select)).then(({ TomSelect, plugin }) => {
      if (active && entry.alive && select.isConnected) entry.control = bindCombobox(select, TomSelect, plugin);
    }).catch((error) => {
      if (entries.get(select) !== entry) return;
      if (select.tomselect) select.tomselect.destroy();
      entries.delete(select);
      reportRuntimeError('combobox', error, view);
    });
  };
  const collect = (root) => {
    if (root.matches?.(selector)) mount(root);
    for (const select of root.querySelectorAll?.(selector) || []) mount(select);
  };
  const prune = () => {
    for (const [select, entry] of entries) if (!select.isConnected) {
      entry.alive = false; entry.control?.destroy(); entries.delete(select);
    }
  };
  const synchronize = () => {
    prune(); collect(document);
    for (const entry of entries.values()) entry.control?.sync();
  };
  const reset = (event) => view.queueMicrotask(() => {
    if (!active || event.defaultPrevented) return;
    for (const [select, entry] of entries) if (select.form === event.target) entry.control?.sync();
  });
  const observer = new view.MutationObserver((records) => {
    prune();
    for (const record of records) for (const node of record.addedNodes) collect(node);
  });
  collect(document);
  observer.observe(document.documentElement, { childList: true, subtree: true });
  document.addEventListener('rdct:archive-updated', synchronize);
  document.addEventListener('reset', reset);
  return () => {
    active = false; observer.disconnect();
    document.removeEventListener('rdct:archive-updated', synchronize);
    document.removeEventListener('reset', reset);
    for (const entry of entries.values()) { entry.alive = false; entry.control?.destroy(); }
    entries.clear();
  };
}
