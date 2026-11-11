import test from 'node:test';
import assert from 'node:assert/strict';
import { pickImage } from '../shared/admin/image-picker.js';
import { appendImageItems, registerItemRepeater } from '../radmanct-components/src/js/admin/atomic-repeater-control.js';

function mediaFixture(selected) {
  const handlers = {}; let removed = 0; let opened = 0; let options; let initial;
  const selection = { toJSON: () => selected, first: () => ({ toJSON: () => selected[0] }), add: (value) => { initial = value; } };
  const frame = { once: (name, fn) => { handlers[name] = fn; }, state: () => ({ get: () => selection }), open: () => { ++opened; handlers.open?.(); }, close: () => handlers.close?.(), remove: () => { ++removed; } };
  const media = (value) => { options = value; return frame; }; media.attachment = (id) => ({ id });
  return { media, handlers, options: () => options, initial: () => initial, removed: () => removed, opened: () => opened };
}

test('native batch media selection retains order, filters invalid items, caps count and releases the frame', () => {
  const fixture = mediaFixture([{ id: 3, type: 'image' }, { id: '2', type: 'image' }, { id: 3, type: 'image' }, { id: 8, type: 'video' }, { id: 0 }, { id: 9 }]);
  const updates = [];
  const cancel = pickImage({ media: fixture.media, title: 'Add images', multiple: true, maximum: 2, onSelect: (ids) => updates.push(ids) });
  assert.equal(fixture.options().multiple, true); assert.deepEqual(fixture.options().library, { type: 'image' });
  fixture.handlers.select(); assert.deepEqual(updates, [[3, 2]]);
  fixture.handlers.close(); cancel(); fixture.handlers.select();
  assert.equal(fixture.removed(), 1); assert.equal(updates.length, 1);
});

test('single-image picker preserves its numeric result and cancellation blocks late selections', () => {
  const fixture = mediaFixture([{ id: '7' }]); const updates = [];
  const cancel = pickImage({ media: fixture.media, title: 'Image', id: 4, onSelect: (id) => updates.push(id) });
  assert.equal(fixture.options().multiple, false); assert.deepEqual(fixture.initial(), { id: 4 });
  fixture.handlers.select(); assert.deepEqual(updates, [7]);
  cancel(); cancel(); fixture.handlers.select();
  assert.equal(fixture.removed(), 1); assert.deepEqual(updates, [7]);
});

test('native WordPress close-before-select keeps the image selection until the toolbar event completes', async () => {
  for (const multiple of [false,true]) {
    const fixture=mediaFixture([{id:17,type:'image'}]); const updates=[];
    pickImage({media:fixture.media,title:'Logo',multiple,onSelect:value=>updates.push(value)});
    fixture.handlers.close(); fixture.handlers.select();
    assert.deepEqual(updates,multiple?[[17]]:[17]);
    await Promise.resolve(); assert.equal(fixture.removed(),1);
    fixture.handlers.select();assert.equal(updates.length,1);
  }
});

test('closing without selection releases the frame and explicit cancellation blocks a queued select', async () => {
  const fixture=mediaFixture([{id:17}]);const updates=[];
  const cancel=pickImage({media:fixture.media,title:'Logo',onSelect:value=>updates.push(value)});
  fixture.handlers.close();cancel();fixture.handlers.select();
  await Promise.resolve();assert.deepEqual(updates,[]);assert.equal(fixture.removed(),1);
});

test('batch import appends native image props without replacing existing content or exceeding the slide limit', () => {
  const fields = { title: { default: '' }, content_type: { default: 'content' }, image_id: { default: 0, media: true }, content: { default: '', rich: true } };
  const existing = { $$type: 'rdct-carousel-item', value: { title: { $$type: 'string', value: 'Existing template' } } };
  const original = [existing];
  const next = appendImageItems(original, [3, 3, -1, 2, 9], fields, 3);
  assert.equal(next.length, 3); assert.equal(next[0], existing); assert.equal(original.length, 1);
  assert.equal(next[1].value.image_id.$$type, 'image');
  assert.equal(next[1].value.image_id.value.src.value.id.value, 3);
  assert.equal(next[2].value.image_id.value.src.value.id.value, 2);
  assert.equal(next[1].value.content.$$type, 'escaped-html');
  assert.equal(next[1].value.title.value, '');
});

test('Atomic batch selection uses current rows, respects disabled state and ignores selection after unmount', () => {
  const media = mediaFixture([{ id: 7, type: 'image' }]); let component; let refs = []; let index = 0; let cleanup; let commits = 0;
  const fields = { image_id: { default: 0, media: true }, title: { default: '' }, content_type: { default: 'content' } };
  const bound = { value: [], disabled: false, propType: { item_prop_type: {} }, setValue: (next) => { ++commits; bound.value = next; } };
  const element = { createElement: (type, props, ...children) => ({ type, props: { ...props, children } }), createContext: () => ({ Provider: 'Provider' }), useContext() {}, useState() {}, useRef: (initial) => refs[index++] ?? (refs[index - 1] = { current: initial }), useEffect: (effect) => { cleanup ??= effect(); } };
  const controls = Object.fromEntries(['Repeater', 'PropProvider', 'PropKeyProvider', 'TextControl', 'TextAreaControl', 'ColorControl'].map((key) => [key, key]));
  controls.useBoundProp = () => bound;
  registerItemRepeater({ get() {}, register: (_, control) => { component = control; } }, controls, { controlKey: 'rdct-carousel-items', itemType: 'rdct-carousel-item', batchImages: true, fields, labels: { item: 'Slide', addImages: 'Add images' } }, element, { Stack: 'Stack', Button: 'NativeButton' }, { stringPropTypeUtil: {} }, media.media);
  const renderButton = () => {
    const node = component().props.children[0].props.children[0]; index = 0; return node.type(node.props);
  };
  renderButton().props.onClick();
  const changed = { $$type: 'rdct-carousel-item', value: { title: { $$type: 'string', value: 'Edited while media is open' } } };
  bound.value = [changed]; renderButton(); media.handlers.select();
  assert.equal(bound.value[0], changed); assert.equal(bound.value.length, 2); assert.equal(commits, 1);
  bound.disabled = true; renderButton(); media.handlers.select(); assert.equal(commits, 1);
  bound.disabled = false; renderButton().props.onClick(); cleanup(); media.handlers.select(); assert.equal(commits, 1);
});
