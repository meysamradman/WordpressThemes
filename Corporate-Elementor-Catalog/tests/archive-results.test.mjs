import test from 'node:test';
import assert from 'node:assert/strict';
import { appendArchiveResults } from '../radmanct/src/js/archive-results.js';

function fixture({ ids = ['post-2', 'post-3'], valid = true, focused = true } = {}) {
  let focus = false; let removed = false; let replaced;
  const children = ids.map(id => ({ id, querySelector: () => ({ focus: () => { focus = true; } }) }));
  const items = { children: [{ id: 'post-1' }, { id: 'post-2' }], append(...nodes) { this.children.push(...nodes); } };
  const next = { querySelector: () => null, querySelectorAll: () => [{ remove: () => { removed = true; } }] };
  const pagination = { contains: () => focused, replaceWith: value => { replaced = value; } };
  const selectors = { '[data-rdct-archive-items]': items, '[data-rdct-archive-pagination]': pagination };
  const incoming = { children };
  const view = {
    querySelector: selector => selectors[selector],
    ownerDocument: { createElement: () => ({ content: { querySelector: selector => valid ? (selector.includes('items') ? incoming : next) : null } }) },
  };
  return { view, items, next, state: () => ({ focus, removed, replaced }) };
}

test('load more retains existing cards, skips duplicate IDs, advances navigation and restores keyboard focus', () => {
  const f = fixture();
  assert.equal(appendArchiveResults(f.view, '<page>'), 1);
  assert.deepEqual(f.items.children.map(item => item.id), ['post-1', 'post-2', 'post-3']);
  assert.deepEqual(f.state(), { focus: true, removed: true, replaced: f.next });
  assert.equal(f.next.hidden, true);
});

test('malformed, empty or repeated pages preserve existing results and navigation', () => {
  for (const options of [{ valid: false }, { ids: [] }, { ids: ['post-1', 'post-2'] }]) {
    const f = fixture(options);
    assert.throws(() => appendArchiveResults(f.view, '<invalid>'));
    assert.equal(f.items.children.length, 2);
    assert.equal(f.state().replaced, undefined);
  }
});

test('mouse loading does not move focus from outside the pagination', () => {
  const f = fixture({ focused: false });
  appendArchiveResults(f.view, '<page>');
  assert.equal(f.state().focus, false);
});
