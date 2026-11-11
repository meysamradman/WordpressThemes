import test from 'node:test';
import assert from 'node:assert/strict';

let selectedGroup = 'persian';
let selectedDevice = '';
globalThis.window = { wp: { element: {
  createElement: (type, props, ...children) => ({ type, props: { ...props, children } }),
  Fragment: Symbol('Fragment'), useState: initial => [initial === '' ? selectedDevice : selectedGroup, () => {}],
} } };
const { TypographyFields } = await import('../radmanct/src/js/admin/typography-fields.js');
const { FontField, SelectField } = await import('../radmanct/src/js/admin/fields.js');
function nodes(node) {
  if (!node || typeof node !== 'object') return [];
  return [node, ...(node.props?.children || []).flat(Infinity).flatMap(nodes)];
}

test('role weight controls follow the effective local family and preserve the other language on submit', () => {
  const config = { labels: { fontWeightLabels: { 400: 'Regular', 700: 'Bold' } }, primaryFontGroup: 'persian', fontRoles: ['headings', 'h1'],
    fonts: {}, fontChoices: { persian: [], latin: [] }, samples: {}, fontWeights: { dana: ['400', '700'], hafez: ['400'], manrope: ['400', '700'] } };
  const values = { persian_font: 'dana', latin_font: 'manrope', headings_persian_font: '', h1_persian_font: '', body_persian_weight: '700', h1_persian_weight: '700', h1_latin_weight: '400' };
  const changes = [];
  let tree = nodes(TypographyFields({ config, values, change: (...args) => changes.push(args) }));
  const weight = tree.find(node => node.type === SelectField && node.props.name === 'h1_persian_weight');
  assert.deepEqual(weight.props.options.map(([value]) => value), ['', '400', '700']);
  assert.ok(tree.some(node => node.type === 'input' && node.props.name === 'radman_settings[h1_latin_weight]' && node.props.value === '400'));
  tree.find(node => node.type === FontField && node.props.name === 'persian_font').props.onChange('persian_font', 'hafez');
  assert.deepEqual(changes, [['persian_font', 'hafez'], ['body_persian_weight', ''], ['h1_persian_weight', '']]);
  selectedGroup = 'latin';
  tree = nodes(TypographyFields({ config, values, change: () => {} }));
  assert.ok(tree.some(node => node.type === 'input' && node.props.name === 'radman_settings[h1_persian_weight]' && node.props.value === '700'));
});

test('size controls preserve inactive device and language values without duplicate form keys', () => {
  selectedGroup = 'persian'; selectedDevice = '_mobile';
  const config = { labels: { fontWeightLabels: {} }, primaryFontGroup: 'persian', fontRoles: ['headings', 'h1'], fonts: {}, fontChoices: { persian: [], latin: [] }, samples: {}, fontWeights: {} };
  const values = { persian_font: 'dana', latin_font: 'manrope', h1_persian_size: '64', h1_persian_mobile_size: '32', h1_latin_tablet_size: '40' };
  const tree = nodes(TypographyFields({ config, values, change: () => {} }));
  const inputs = tree.filter(node => node.props?.name?.startsWith('radman_settings[') && node.props.name.endsWith('_size]'));
  assert.equal(new Set(inputs.map(node => node.props.name)).size, inputs.length);
  assert.ok(inputs.some(node => node.props.type === 'hidden' && node.props.name === 'radman_settings[h1_persian_mobile_size]' && node.props.value === '32'));
  const size = tree.find(node => node.type?.name === 'FontSizeControl' && node.props.id === 'rdct-h1_persian_mobile_size');
  assert.ok(size);
  assert.equal(size.props.name, undefined);
  assert.ok(inputs.some(node => node.props.type === 'hidden' && node.props.name === 'radman_settings[h1_persian_size]' && node.props.value === '64'));
  assert.ok(inputs.some(node => node.props.type === 'hidden' && node.props.name === 'radman_settings[h1_latin_tablet_size]' && node.props.value === '40'));
});

test('rem entry round-trips existing pixel values and retains empty inheritance', async () => {
  const {toRem, fromRem} = await import('../radmanct/src/js/admin/typography-size.js');
  assert.equal(toRem('17'), '1.0625');
  assert.equal(toRem('70'), '4.375');
  assert.equal(fromRem('4.375'), '70');
  assert.equal(fromRem('2'), '32');
  assert.equal(toRem(''), '');
  assert.equal(fromRem(''), '');
  for (const value of ['8', '8.01', '17', '32.5', '70', '199.99', '200']) assert.equal(fromRem(toRem(value)), value);
});
