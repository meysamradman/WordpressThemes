import test from 'node:test';
import assert from 'node:assert/strict';

let current;
globalThis.window = { document: { body: {} }, wp: { element: {
  createElement: (type, props, ...children) => {
    if (props?.ref) props.ref.current = type === 'dialog' ? current.dialog : current.input;
    return { type, props: props || {}, children: children.flat(Infinity).filter((child) => child != null && child !== false) };
  },
  createPortal: (node) => node,
  useState: (initial) => {
    const index = current.index++; const owner = current;
    owner.hooks[index] ??= { value: initial };
    return [owner.hooks[index].value, (value) => { owner.hooks[index].value = typeof value === 'function' ? value(owner.hooks[index].value) : value; }];
  },
  useRef: (initial) => { const index = current.index++; return current.hooks[index] ??= { current: initial }; },
  useEffect: (effect, deps) => {
    const index = current.index++; const owner = current; const previous = owner.hooks[index];
    if (!previous || deps.some((value, i) => value !== previous.deps[i])) owner.effects.push(() => { previous?.cleanup?.(); owner.hooks[index] = { deps, cleanup: effect() }; });
  },
} } };
globalThis.document = window.document;
const { IconPicker, iconMatches } = await import('../shared/admin/icon-picker.js');
const icons = { phone: { label: '\u062a\u0644\u0641\u0646', src: 'data:image/svg+xml,phone' }, 'arrow-right': { label: 'Arrow right', src: 'data:image/svg+xml,arrow' }, 'client-factory': { label: 'Factory', src: 'data:image/svg+xml,factory' } };
const labels = { title: 'Choose icon', choose: 'Choose icon', search: 'Search icons', none: 'None', close: 'Close', empty: 'No matches', unavailable: 'Unavailable', more: 'More' };
const nodes = (node, type) => [...(node?.type === type ? [node] : []), ...(node?.children || []).flatMap((child) => typeof child === 'object' ? nodes(child, type) : [])];
function fixture(value = 'phone') {
  const changes = []; let shown = 0; let closed = 0; let focused = 0; let inputFocused = 0;
  const owner = { hooks: [], effects: [], index: 0, dialog: { showModal: () => ++shown, close: () => ++closed }, input: { focus: () => ++inputFocused } };
  const opener = { isConnected: true, focus: () => ++focused };
  const render = () => {
    current = owner; owner.index = 0;
    const tree = IconPicker({ value, icons, labels, onChange: (next) => changes.push(next) });
    owner.effects.splice(0).forEach((effect) => effect()); return tree;
  };
  const open = () => { nodes(render(), 'button')[0].props.onClick({ currentTarget: opener }); return render(); };
  return { render, open, changes, counts: () => ({ shown, closed, focused, inputFocused }), destroy: () => owner.hooks.forEach((hook) => hook.cleanup?.()) };
}

test('icon search matches localized labels, stable names, multiple words and project additions locally', () => {
  assert.deepEqual(iconMatches(icons, 'RIGHT arrow').map(([name]) => name), ['arrow-right']);
  assert.deepEqual(iconMatches(icons, '\u062a\u0644\u0641\u0646').map(([name]) => name), ['phone']);
  assert.deepEqual(iconMatches(icons, 'client factory').map(([name]) => name), ['client-factory']);
  assert.equal(iconMatches(icons, 'missing').length, 0);
});

test('visual dialog shows names, commits one stable key and restores focus without a request', () => {
  const page = fixture(); const tree = page.open();
  assert.equal(nodes(tree, 'dialog').length, 1); assert.equal(page.counts().inputFocused, 1);
  const choice = nodes(tree, 'button').find((node) => node.props.title === 'Factory (client-factory)');
  assert.ok(choice); assert.equal(nodes(choice, 'small')[0].children[0], 'client-factory');
  choice.props.onClick(); page.render();
  assert.deepEqual(page.changes, ['client-factory']);
  assert.deepEqual(page.counts(), { shown: 1, closed: 1, focused: 1, inputFocused: 1 }); page.destroy();
});

test('cancel preserves an unavailable saved key and reopening resets local search', () => {
  const page = fixture('old-client-icon'); let tree = page.open();
  nodes(tree, 'input')[0].props.onChange({ target: { value: 'missing' } }); tree = page.render();
  assert.equal(nodes(tree, 'p').find((node) => node.props.role === 'status').children[0], labels.empty);
  nodes(tree, 'dialog')[0].props.onCancel(); page.render(); assert.deepEqual(page.changes, []);
  tree = page.open(); assert.equal(nodes(tree, 'input')[0].props.value, '');
  assert.ok(nodes(tree, 'button').some((node) => node.props.title === 'Factory (client-factory)'));
  page.destroy();
});
