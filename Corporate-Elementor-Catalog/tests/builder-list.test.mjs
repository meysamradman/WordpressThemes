import { test } from 'node:test';
import assert from 'node:assert/strict';

let click;
const status = { replaceChildren(value) { this.text = value.textContent; } };
const heading = { focus() { this.focused = true; } };
const updated = { querySelector(selector) { return selector === 'h1' ? heading : status; } };
const root = {
  querySelector() { return status; }, setAttribute() {}, removeAttribute() {},
  replaceWith(value) { this.replaced = value; },
};
const button = {
  dataset: { rdctDelete: 'saved-layout' }, disabled: false,
  closest(selector) { return selector === 'tr' ? { querySelector: () => ({ textContent: '<Home>' }) } : root; },
  focus() { this.focused = true; },
};
globalThis.document = {
  addEventListener(_type, callback) { click = callback; },
  createElement() { return {}; },
};
globalThis.DOMParser = class { parseFromString() { return { querySelector: () => updated }; } };
globalThis.window = {
  rdctBuilderList: { kind: 'header', ajaxUrl: '/ajax', nonce: 'nonce', labels: { confirmDelete: 'Confirm', requestError: 'Failed', deleted: 'Deleted' } },
  confirm: () => true,
  location: { href: '/admin.php?page=rdct-header&s=Home&status=enabled', reload() { this.reloaded = true; } },
};
await import('../radmanct/src/js/admin/builder-list.js');
const event = { target: { closest: () => button } };
const state = { id: 'default', data: { revision: 0, layout: { version: 1, enabled: false, rows: [] } }, layouts: [], defaults: [] };

test('cancel makes no delete request; failed authorization keeps the row and reports the server error', async () => {
  let calls = 0;
  window.confirm = () => false;
  globalThis.fetch = async () => { calls++; return { ok: false, json: async () => ({ success: false, data: { message: 'Denied' } }) }; };
  await click(event);
  assert.equal(calls, 0);
  window.confirm = () => true;
  await click(event);
  assert.equal(calls, 1);
  assert.equal(status.text, 'Denied');
  assert.equal(root.replaced, undefined);
  assert.equal(button.disabled, false);
});

test('one authenticated POST deletes; the same filtered list refreshes without navigating to a delete page', async () => {
  const calls = [];
  globalThis.fetch = async (url, options) => {
    calls.push({ url, options });
    return calls.length === 1 ? { ok: true, json: async () => ({ success: true, data: state }) } : { ok: true, text: async () => '<table></table>' };
  };
  const first = click(event);
  await click(event);
  await first;
  assert.equal(calls.length, 2);
  assert.equal(calls[0].options.method, 'POST');
  assert.equal(calls[0].options.body.get('operation'), 'delete');
  assert.equal(calls[0].options.body.get('nonce'), 'nonce');
  assert.equal(calls[0].options.body.get('id'), 'saved-layout');
  assert.equal(calls[1].url, window.location.href);
  assert.equal(root.replaced, updated);
  assert.equal(status.text, 'Deleted');
  assert.equal(heading.focused, true);
});

test('refresh failure after a successful deletion reloads the list instead of retrying deletion', async () => {
  let calls = 0;
  globalThis.fetch = async () => {
    if (++calls === 1) return { ok: true, json: async () => ({ success: true, data: state }) };
    throw new Error('Offline');
  };
  await click(event);
  assert.equal(calls, 2);
  assert.equal(window.location.reloaded, true);
});
