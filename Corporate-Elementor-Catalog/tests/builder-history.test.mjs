import { test } from 'node:test';
import assert from 'node:assert/strict';
import { commit, undo, redo } from '../radmanct/src/js/admin/builder-history.js';

test('builder undo/redo restores layouts and discards redo after a new edit', () => {
  const first = { rows: [{ id: 'a', items: [] }] };
  let state = { past: [], present: first, future: [] };
  state = commit(state, (layout) => ({ ...layout, rows: [...layout.rows, { id: 'b', items: [] }] }));
  const second = state.present;
  state = undo(state);
  assert.deepEqual(state.present, first);
  state = redo(state);
  assert.deepEqual(state.present, second);
  state = commit(undo(state), { rows: [] });
  assert.equal(state.future.length, 0);
  assert.equal(redo(state), state);
  for (let index = 0; index < 50; index++) state = commit(state, { rows: [], index });
  assert.equal(state.past.length, 20);
});
