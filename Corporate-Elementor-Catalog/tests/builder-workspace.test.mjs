import { test } from 'node:test';
import assert from 'node:assert/strict';
import { findPreviewDrop, dropIndex } from '../radmanct/src/js/admin/builder-drag.js';
import { moveRow, moveItem, duplicateRow } from '../radmanct/src/js/admin/builder-mutations.js';
import { commit, undo, redo } from '../radmanct/src/js/admin/builder-history.js';

test('moving between rows preserves IDs, content, capacity and reversible history', () => {
  const original = { enabled: true, rows: [{ id: 'first', items: [{ id: 'social', links: [{ id: 'profile', text: 'Profile' }] }] }, { id: 'second', items: [] }] };
  const before = structuredClone(original);
  const moved = moveItem(original, 'first', 'social', 'second', 6);
  assert.deepEqual(original, before);
  assert.equal(moved.rows[0].items.length, 0);
  assert.equal(moved.rows[1].items[0], original.rows[0].items[0]);
  const history = commit({ past: [], present: original, future: [] }, moved);
  assert.deepEqual(undo(history).present, original);
  assert.deepEqual(redo(undo(history)).present, moved);
  const full = { rows: [original.rows[0], { id: 'full', items: Array.from({ length: 6 }, (_, i) => ({ id: `item-${i}` })) }] };
  assert.equal(moveItem(full, 'first', 'social', 'full', 0), full);
  assert.equal(moveItem(original, 'first', 'missing', 'second', 0), original);
  assert.equal(moveRow(original, 0, -1), original);
  assert.equal(moveRow(original, 0, 1).rows[1], original.rows[0]);
});

test('row duplication preserves layout and gives nested translations unique identities', () => {
  const original = { rows: [{ id: 'first', appearance: { gap: 0 }, items: [{ id: 'social', type: 'social', links: [{ id: 'profile', text: 'Profile' }] }] }] };
  const copy = duplicateRow(original, 'first');
  assert.equal(original.rows.length, 1);
  assert.equal(copy.rows.length, 2);
  assert.equal(copy.rows[1].appearance.gap, 0);
  assert.equal(new Set([original.rows[0].id, original.rows[0].items[0].id, 'profile', copy.rows[1].id, copy.rows[1].items[0].id, copy.rows[1].items[0].links[0].id]).size, 6);
  copy.rows[1].items[0].links[0].text = 'Changed';
  assert.equal(original.rows[0].items[0].links[0].text, 'Profile');
  assert.equal(duplicateRow(original, 'missing'), original);
  const full = { rows: Array.from({ length: 8 }, (_, i) => ({ id: `row-${i}`, items: [] })) };
  assert.equal(duplicateRow(full, 'row-0'), full);
});

const layout = { rows: [{ id: 'row', items: [{ id: 'a' }, { id: 'b' }, { id: 'c' }] }] };
const rect = (left, top, width, height) => ({ left, top, width, height, right: left + width, bottom: top + height });
const targets = [{ id: 'row', rowId: 'row', item: false, rect: rect(0, 0, 600, 200) }, { id: 'b', rowId: 'row', item: true, rect: rect(200, 20, 100, 100) }];

test('canvas drop chooses the insertion side in LTR, RTL and stacked mobile rows', () => {
  assert.equal(findPreviewDrop(targets, 210, 30, layout).index, 1);
  assert.equal(findPreviewDrop(targets, 290, 30, layout).index, 2);
  assert.equal(findPreviewDrop(targets, 290, 30, layout, false, true).index, 1);
  assert.equal(findPreviewDrop(targets, 210, 30, layout, false, true).index, 2);
  assert.equal(findPreviewDrop(targets, 210, 110, layout, true, true).index, 2);
  assert.equal(findPreviewDrop(targets, 210, 30, layout, true, true).index, 1);
  assert.equal(findPreviewDrop(targets, 500, 160, layout).index, 3);
  assert.equal(findPreviewDrop(targets, -1, -1, layout), null);
  assert.equal(findPreviewDrop([{ ...targets[1], rect: rect(0, 0, 0, 0) }], 0, 0, layout), null);
});

test('same-row removal adjusts insertion gaps without shifting cross-row or new items', () => {
  assert.equal(dropIndex(layout, { rowId: 'row', itemId: 'a' }, 'row', 2), 1);
  assert.equal(dropIndex(layout, { rowId: 'row', itemId: 'b' }, 'row', 1), 1);
  assert.equal(dropIndex(layout, { rowId: 'other', itemId: 'a' }, 'row', 2), 2);
  assert.equal(dropIndex(layout, { type: 'text' }, 'row', 2), 2);
  assert.equal(layout.rows[0].items.map((item) => item.id).join(''), 'abc');
});

globalThis.window = { wp: { element: {
  createElement: (type, props, ...children) => ({ type, props: { ...props, children } }),
  useState: (value) => [value, () => {}], useEffect: () => {},
} } };
const { PanelTabs } = await import('../shared/admin/panel-tabs.js');
const { ColorField } = await import('../shared/admin/color-field.js');

test('shared inspector tabs support RTL keyboard navigation and hide inactive panels', () => {
  let selected; let focused;
  const tabs = [{ id: 'content', label: 'Content' }, { id: 'style', label: 'Style' }, { id: 'advanced', label: 'Advanced' }];
  const tree = PanelTabs({ id: 'test', value: 'style', onChange: (next) => { selected = next; }, tabs });
  const listNode = tree.props.children[0];
  const list = listNode.type(listNode.props);
  globalThis.getComputedStyle = () => ({ direction: 'rtl' });
  const currentTarget = { querySelectorAll: () => tabs.map((tab) => ({ focus: () => { focused = tab.id; } })) };
  list.props.onKeyDown({ key: 'ArrowRight', currentTarget, preventDefault() {} });
  assert.equal(selected, 'content'); assert.equal(focused, 'content');
  list.props.onKeyDown({ key: 'End', currentTarget, preventDefault() {} });
  assert.equal(selected, 'advanced');
  assert.deepEqual(tree.props.children.slice(1).map((panel) => panel.props.hidden), [true, false, true]);
  assert.equal(list.props.children[1].props['aria-controls'], 'test-style-panel');
});

test('shared color fields commit only complete safe colors or inherited values', () => {
  const values = [];
  const tree = ColorField({ label: 'Color', value: '#123456', resetLabel: 'Inherit', onChange: (next) => values.push(next) });
  const controls = tree.props.children[1].props.children;
  const text = controls[1];
  text.props.onChange({ target: { value: '#12' } });
  text.props.onChange({ target: { value: '#fff;xx' } });
  assert.equal(values.length, 0);
  text.props.onChange({ target: { value: '#abcdef' } });
  controls[2].props.onClick();
  assert.deepEqual(values, ['#abcdef', '']);
  assert.equal(text.props.dir, 'ltr');
});
