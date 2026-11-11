import test from 'node:test';
import assert from 'node:assert/strict';

globalThis.window = { wp: { element: { createElement: () => null } } };
const { buttonControlState } = await import('../radmanct/src/js/admin/builder-button-fields.js');
const field = { default: -1000, min: -1000, max: 200 };
const metadata = { unit_field: 'line_height_unit', default_unit: '', min: .1, max: 200, step: .1, range: { '': { min: .1, max: 10 }, px: { min: 1, max: 200 } } };
const fields = { line_height_unit: { default: '' } };

test('button timing follows the selected effect without replacing explicit durations', () => {
  const duration = { default: .4, min: .1, max: 2, effect_defaults: { 'switch-x': .85, 'liquid-x': .6, 'marquee-x': 2 } };
  const state = (current) => buttonControlState('duration', duration, undefined, current, {}, {}, 'desktop');
  assert.equal(state({ text_effect: 'switch-x' }).value, .85);
  assert.equal(state({ text_effect: 'liquid-x' }).value, .6);
  assert.equal(state({ text_effect: 'marquee-x' }).value, 2);
  assert.equal(state({ text_effect: 'switch-x', duration: .4 }).value, .4);
});

test('unset typography is blank and unitless line height has a useful range', () => {
  const state = buttonControlState('line_height', field, metadata, {}, {}, fields, 'desktop');
  assert.equal(state.value, '');
  assert.equal(state.unit, '');
  assert.equal(state.min, .1);
  assert.equal(state.max, 10);
  assert.equal(state.step, .1);
});

test('responsive typography preserves inheritance, zero and explicit unit selection', () => {
  const current = { line_height: -1000, line_height_unit: 'px', line_height_responsive: { tablet: 0, mobile: 1.6 }, line_height_unit_responsive: { mobile: '' } };
  assert.equal(buttonControlState('line_height', field, metadata, current, {}, fields, 'desktop').value, '');
  assert.equal(buttonControlState('line_height', field, metadata, current, {}, fields, 'tablet').value, 0);
  const mobile = buttonControlState('line_height', field, metadata, current, {}, fields, 'mobile');
  assert.equal(mobile.value, 1.6);
  assert.equal(mobile.unit, '');
  assert.equal(mobile.max, 10);
  assert.equal(buttonControlState('line_height', field, metadata, current, {}, fields, 'laptop').value, '');
});
