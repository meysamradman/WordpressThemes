import { test } from 'node:test';
import assert from 'node:assert/strict';
import { resolveSelection, pickPreviewTarget, layoutAssignment, previewContentHeight } from '../radmanct/src/js/admin/builder-selection.js';

test('preview height follows actual content and can shrink after deleting tall rows', () => {
  const content = (height, offsetTop = 0) => ({ getBoundingClientRect: () => ({ height }), offsetTop });
  assert.equal(previewContentHeight(content(640, 8)), 648);
  assert.equal(previewContentHeight(content(79.2)), 80);
  assert.equal(previewContentHeight(content(0)), 48);
  assert.equal(previewContentHeight(null), 48);
});

test('assignment status follows saved enablement and language to site inheritance', () => {
  const stored = { id: 'default', data: { layout: { enabled: true } }, defaults: {} };
  assert.equal(layoutAssignment(stored).status, 'layoutAssigned');
  stored.id = 'custom';
  assert.equal(layoutAssignment(stored).status, 'layoutNotAssigned');
  stored.defaults.all = 'custom';
  assert.equal(layoutAssignment(stored, 'fa').status, 'layoutAssigned');
  assert.equal(layoutAssignment(stored, 'fa').direct, undefined);
  stored.defaults.fa = 'other';
  assert.equal(layoutAssignment(stored, 'fa').status, 'layoutNotAssigned');
  stored.data.layout.enabled = false;
  assert.equal(layoutAssignment(stored).status, 'layoutDisabled');
});

test('inspector follows a moved element and falls back safely after deletion or import', () => {
  const selection = { rowId: 'first', itemId: 'button' };
  const moved = { rows: [{ id: 'first', items: [] }, { id: 'second', items: [{ id: 'button' }] }] };
  assert.equal(resolveSelection(moved, selection).row.id, 'second');
  assert.equal(resolveSelection(moved, selection).item.id, 'button');
  const deleted = { rows: [{ id: 'first', items: [] }] };
  assert.deepEqual(resolveSelection(deleted, selection), { row: deleted.rows[0], item: null });
  assert.deepEqual(resolveSelection({ rows: [] }, selection), { row: null, item: null });
  assert.equal(resolveSelection(moved, { rowId: 'missing' }).row.id, 'first');
});

test('preview picking prefers elements over rows and ignores hidden or outside targets', () => {
  const node = (id, rect) => ({ getAttribute: () => id, getBoundingClientRect: () => rect });
  const row = node('row', { left: 0, top: 0, right: 500, bottom: 100, width: 500, height: 100 });
  const item = node('item', { left: 10, top: 10, right: 100, bottom: 50, width: 90, height: 40 });
  const hidden = node('hidden', { left: 0, top: 0, right: 0, bottom: 0, width: 0, height: 0 });
  const doc = { querySelectorAll: (selector) => selector.includes('item') ? [hidden, item] : [row] };
  assert.deepEqual(pickPreviewTarget(doc, 20, 20), { id: 'item', item: true });
  assert.deepEqual(pickPreviewTarget(doc, 300, 80), { id: 'row', item: false });
  assert.equal(pickPreviewTarget(doc, -10, -10), null);
});
