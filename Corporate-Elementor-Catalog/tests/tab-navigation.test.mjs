import test from 'node:test';
import assert from 'node:assert/strict';
import { navigateTabs } from '../shared/admin/tab-navigation.js';

function fixture(direction = 'ltr') {
  const list = { querySelectorAll: () => tabs };
  let focused;
  const tabs = ['first', 'disabled', 'last'].map((id) => ({
    dataset: { tabId: id }, disabled: id === 'disabled',
    closest: () => list, getAttribute: () => null, focus: () => { focused = id; },
  }));
  globalThis.getComputedStyle = () => ({ direction });
  return (key, index = 0, orientation) => {
    let changed; let prevented = false;
    const target = { closest: () => tabs[index] };
    navigateTabs({ key, target, currentTarget: list, preventDefault: () => { prevented = true; } }, (id) => { changed = id; }, orientation);
    return { changed, focused, prevented };
  };
}

test('horizontal tabs skip disabled items and wrap in both directions', () => {
  assert.deepEqual(fixture()('ArrowRight'), { changed: 'last', focused: 'last', prevented: true });
  assert.equal(fixture()('ArrowRight', 2).changed, 'first');
  assert.equal(fixture()('ArrowLeft').changed, 'last');
  assert.equal(fixture()('End').changed, 'last');
  assert.equal(fixture()('Home', 2).changed, 'first');
});

test('RTL follows visual direction while vertical tabs use Up/Down', () => {
  assert.equal(fixture('rtl')('ArrowLeft').changed, 'last');
  assert.equal(fixture('rtl')('ArrowDown', 0, 'vertical').changed, 'last');
  assert.equal(fixture('rtl')('ArrowUp', 2, 'vertical').changed, 'first');
  assert.equal(fixture()('ArrowRight', 0, 'vertical').prevented, false);
  assert.equal(fixture()('ArrowDown').prevented, false);
  assert.equal(fixture()('Tab').prevented, false);
});

test('nested tablists do not react to each other', () => {
  let called = false;
  const target = { closest: () => ({ closest: () => ({}) }) };
  navigateTabs({ target, currentTarget: {}, key: 'End' }, () => { called = true; });
  assert.equal(called, false);
});


globalThis.window = { wp: { element: { createElement: (type, props, ...children) => ({ type, props: { ...props, children } }) } } };
const { PanelTabs, TabList } = await import('../shared/admin/panel-tabs.js');
test('tab and panel selection agree when the stored tab is missing or disabled', () => {
  const tabs = [{ id: 'old', label: 'Old', disabled: true }, { id: 'available', label: 'Available' }];
  for (const value of ['old', 'removed']) {
    const tree = PanelTabs({ id: 'sample', value, tabs, onChange() {} });
    const [list, ...panels] = tree.props.children;
    assert.equal(list.props.value, 'available');
    assert.equal(panels[0].props.hidden, true);
    assert.equal(panels[1].props.hidden, false);
    const controls = TabList(list.props).props.children;
    assert.equal(controls[0].props.tabIndex, -1);
    assert.equal(controls[1].props.tabIndex, 0);
  }
});
