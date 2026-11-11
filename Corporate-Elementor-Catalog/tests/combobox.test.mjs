import test from 'node:test';
import assert from 'node:assert/strict';
import { bindCombobox, normalizeSearch, observeComboboxes } from '../radmanct-components/src/js/controls/combobox.js';
import { readFileSync } from 'node:fs';

test('Tom Select stays pinned and outside the initial frontend import graph', () => {
  const read = (path) => JSON.parse(readFileSync(new URL(path, import.meta.url), 'utf8'));
  assert.equal(read('../package.json').dependencies['tom-select'], '2.6.2');
  assert.equal(read('../package-lock.json').packages['node_modules/tom-select'].version, '2.6.2');
  const manifest = read('../radmanct-components/dist/manifest.json');
  const theme = read('../radmanct/dist/manifest.json');
  assert.equal(Object.keys(theme).some((key) => key.startsWith('src/css/controls/')), false);
  const seen = new Set();
  const visit = (key) => { if (seen.has(key)) return; seen.add(key); for (const dependency of manifest[key]?.imports || []) visit(dependency); };
  visit('src/js/frontend.js');
  assert.equal([...seen].some((key) => /tom-select|combobox/.test(key)), false);
  const combo = manifest['src/js/controls/combobox.js'];
  assert.ok(combo.dynamicImports.some((key) => key.endsWith('/tom-select.js')));
  assert.ok(combo.dynamicImports.some((key) => key.includes('/dropdown_input/')));
  assert.ok(combo.dynamicImports.some((key) => key.includes('/remove_button/')));
  assert.ok(manifest['src/css/controls/combobox.css']);
});

function element(attributes = {}) {
  const attrs = new Map(Object.entries(attributes));
  const listeners = new Map();
  return {
    setAttribute: (key, value) => attrs.set(key, value), getAttribute: (key) => attrs.get(key) ?? null,
    removeAttribute: (key) => attrs.delete(key), addEventListener: (key, fn) => listeners.set(key, fn),
    removeEventListener: (key) => listeners.delete(key), dispatch: (key, event) => listeners.get(key)?.(event),
    listeners,
  };
}

function fixture() {
  const document = element();
  const label = element({ for: 'feature' });
  const form = {};
  const select = Object.assign(element({ 'aria-describedby': 'hint' }), {
    labels: [label], dataset: { searchLabel: 'Search features', emptyLabel: '<No results>', removeLabel: 'Remove' },
    multiple: false, isConnected: true, options: [{ value: '', selected: false }, { value: 'zero', selected: true }],
    innerHTML: '<option>original</option>', form, ownerDocument: document,
  });
  Object.defineProperty(select, 'selectedOptions', { get: () => select.options.filter((option) => option.selected) });
  let observerCallback;
  const view = { document, getComputedStyle: () => ({ direction: 'rtl' }), queueMicrotask,
    MutationObserver: class { constructor(fn) { observerCallback = fn; } observe() {} disconnect() { this.disconnected = true; } } };
  document.defaultView = view;
  document.documentElement = {};
  document.createElement = () => element();
  document.querySelectorAll = () => select.isConnected ? [select] : [];
  document.getElementById = () => null;
  class TomSelect {
    static instances = [];
    static define() {}
    constructor(input, settings) {
      this.settings = settings; this.wrapper = {}; this.control_input = element(); this.focus_node = element();
      this.syncCalls = 0; this.destroyed = false; input.tomselect = this;
      label.setAttribute('for', 'generated-control');
      TomSelect.instances.push(this);
    }
    getScoreFunction(query) { return (item) => item.text.includes(query) ? 1 : 0; }
    sync() { this.syncCalls++; }
    destroy() { this.destroyed = true; delete select.tomselect; select.options[0].selected = true; select.options[1].selected = false; }
  }
  return { view, document, label, select, TomSelect, notify: () => observerCallback([{ addedNodes: [] }]) };
}

test('Persian search handles Arabic letter variants, marks, spacing and Latin accents', () => {
  assert.equal(normalizeSearch('\u0643\u064a\u0641\u064a\u062a'), normalizeSearch('\u06a9\u06cc\u0641\u06cc\u062a'));
  assert.equal(normalizeSearch('caf\u00e9'), 'cafe');
  assert.equal(normalizeSearch('a\u200cb  c'), 'a b c');
});

test('adapter preserves field state, translated text and selection on cleanup', () => {
  const f = fixture();
  const binding = bindCombobox(f.select, f.TomSelect, () => {});
  const control = f.TomSelect.instances[0];
  assert.equal(control.wrapper.dir, 'rtl');
  assert.equal(control.control_input.getAttribute('aria-describedby'), 'hint');
  assert.equal(control.settings.allowEmptyOption, true);
  assert.equal(control.settings.create, false);
  assert.equal(control.settings.render.no_results().textContent, '<No results>');
  assert.equal(control.settings.score.call(control, '\u0643\u064a\u0641')({ text: '\u06a9\u06cc\u0641\u06cc\u062a' }), 1);
  f.select.dispatch('change'); assert.equal(control.syncCalls, 2);
  binding.destroy();
  assert.equal(control.destroyed, true);
  assert.equal(f.label.getAttribute('for'), 'feature');
  assert.equal(f.select.selectedOptions[0].value, 'zero');
  assert.equal(f.select.listeners.size, 0);
});

test('archive updates, reset, detached nodes and late imports share one lifecycle', async () => {
  const f = fixture();
  let loads = 0;
  const dispose = observeComboboxes(f.view, async () => { loads++; return { TomSelect: f.TomSelect, plugin() {} }; });
  await new Promise(setImmediate);
  assert.equal(loads, 1);
  f.document.dispatch('rdct:archive-updated');
  assert.equal(loads, 1);
  const control = f.TomSelect.instances[0];
  const before = control.syncCalls;
  f.document.dispatch('reset', { target: f.select.form });
  await new Promise(setImmediate);
  assert.equal(control.syncCalls, before + 1);
  f.select.isConnected = false; f.notify();
  assert.equal(control.destroyed, true);
  dispose();
  assert.equal(f.document.listeners.size, 0);

  const pending = fixture(); let resolve;
  const stop = observeComboboxes(pending.view, () => new Promise((done) => { resolve = done; }));
  await new Promise(setImmediate); stop();
  resolve({ TomSelect: pending.TomSelect, plugin() {} });
  await new Promise(setImmediate);
  assert.equal(pending.TomSelect.instances.length, 0);
});
