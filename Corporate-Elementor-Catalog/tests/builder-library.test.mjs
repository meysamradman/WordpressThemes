import test from 'node:test';
import assert from 'node:assert/strict';

let state; let cursor; let refs; let cancelled; let focused;
const labels = new Proxy({}, { get: (_, key) => String(key) });
globalThis.window = { rdctBuilder: { labels, kind: 'header', origin: 'https://local.test', languages: [] }, wp: { element: {
  createElement: (type, props, ...children) => ({ type, props: { ...props, children: children.flat(Infinity).filter((child) => child !== undefined && child !== null && child !== false) } }),
  useRef: () => refs[cursor++], useState: () => [state, () => {}], useEffect() {},
} } };
const { Library } = await import('../radmanct/src/js/admin/builder-library.js');
const nodes = (node) => Array.isArray(node) ? node.flatMap(nodes) : !node?.props ? [] : typeof node.type === 'function' ? nodes(node.type(node.props)) : [node, ...nodes(node.props.children)];
const stored = { id: 'default', data: { revision: 2 }, layouts: [{ id: 'default', title: 'Default' }] };
function render(phase, { revision = 2, busy = false, error = '' } = {}) {
  state = { phase, id: 'default', revision, error }; cursor = 0; cancelled = 0; focused = 0;
  refs = [{ current: null }, { current: { focus: () => ++focused } }, { current: { cancel: (notify) => { assert.equal(notify, true); ++cancelled; } } }, { current: null }];
  return nodes(Library({ stored, layout: { rows: [] }, busy, dirty: false, run() {}, change() {}, mode:'backups' }));
}
test('restore status has a keyboard cancellation path and stale or busy contexts hide it', () => {
  const tree = render('validating');
  assert.equal(tree.find((node) => node.props.role === 'status').props.children[0], 'restore_validating');
  const cancel = tree.find((node) => node.type === 'button' && node.props.children[0] === 'cancelImport');
  cancel.props.onClick(); assert.equal(cancelled, 1); assert.equal(focused, 1);
  assert.equal(tree.find((node) => node.type === 'button' && node.props.children[0] === 'exportLayout').props.disabled, true);
  for (const options of [{ revision: 1 }, { busy: true }]) {
    const stale = render('validating', options);
    assert.equal(stale.some((node) => node.type === 'button' && node.props.children[0] === 'cancelImport'), false);
    assert.equal(stale.find((node) => node.props.role === 'status').props.children[0], '');
  }
});
test('errors stay in the backup panel and are replaced by successful restore feedback', () => {
  const failed = render('error', { error: 'Session expired' });
  assert.equal(failed.find((node) => node.props.role === 'alert').props.children[0], 'Session expired');
  const success = render('applied');
  assert.equal(success.some((node) => node.props.role === 'alert'), false);
  assert.equal(success.find((node) => node.props.role === 'status').props.children[0], 'restore_applied');
});
