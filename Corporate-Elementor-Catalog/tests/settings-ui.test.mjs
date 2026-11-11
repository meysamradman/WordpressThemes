import test from 'node:test';
import assert from 'node:assert/strict';

let state; let effects = []; let refs = []; let refIndex = 0;
const h = (type, props, ...children) => ({ type, props: { ...props, children } });
globalThis.window = { wp: { element: { createElement: h,
  useState: (initial) => { if (state === undefined) state = typeof initial === 'function' ? initial() : initial; return [state, (next) => { state = typeof next === 'function' ? next(state) : next; }]; },
  useEffect: (effect) => effects.push(effect), useRef: (initial) => refs[refIndex++] ?? (refs[refIndex - 1] = { current: initial }),
} } };
const { SectionNavigation } = await import('../shared/admin/section-navigation.js');
const { ColorField } = await import('../shared/admin/color-field.js');
const { FontFaces } = await import('../radmanct/src/js/admin/fields.js');
const { fontGroups } = await import('../radmanct/src/js/admin/settings-fonts.js');
const { CustomCodeFields, CodeEditor } = await import('../radmanct/src/js/admin/custom-code-fields.js');
const { TypographyFields } = await import('../radmanct/src/js/admin/typography-fields.js');
const { AnalyticsFields } = await import('../radmanct/src/js/admin/analytics-fields.js');
const walk = (node) => Array.isArray(node) ? node.flatMap(walk) : !node?.props ? [] : [node, ...walk(node.props.children)];

test('separate font pages preserve inactive selections without language tabs', () => {
  state = undefined; effects = [];
  const config = { primaryFontGroup: 'persian', multilingualFonts: false, labels: new Proxy({}, { get: (_, key) => key }), fontRoles: ['headings', 'h1'], fontChoices: { persian: [['fa', 'Persian']], latin: [['en', 'English']] }, samples: {}, fonts: {} };
  const values = { persian_font: 'fa', latin_font: 'en', headings_latin_font: 'en', font_language: 'auto', mixed_language_fonts: false };
  let group = 'persian';
  const render = () => walk(TypographyFields({ config, values, group, change: (name, value) => { values[name] = value; } }));
  const first = render();
  assert.equal(first.some(node => node.props.options?.some(([id]) => id === 'latin') && !node.props.name), false);
  assert.equal(first.find((node) => node.props.name === 'radman_settings[latin_font]').props.value, 'en');
  group = 'latin';
  const english = render();
  const field = english.find((node) => node.props.name === 'latin_font');
  assert.equal(field.props.value, 'en'); field.props.onChange('latin_font', 'new-en');
  assert.equal(english.find((node) => node.props.name === 'radman_settings[persian_font]').props.value, 'fa');
  assert.ok(english.some((node) => node.type === FontFaces && node.props.groups[0] === 'latin'));
  assert.equal(values.mixed_language_fonts, false);
  group = 'persian';
  assert.equal(render().find((node) => node.props.name === 'radman_settings[latin_font]').props.value, 'new-en');
});

test('Analytics accepts a bounded tag snippet through the existing settings form and stays disabled until explicitly enabled', () => {
  const changes = [];
  const nodes = walk(AnalyticsFields({ values: { analytics_enabled: false, analytics_measurement_id: '' }, labels: {}, change: (...args) => changes.push(args) }));
  const toggle = nodes.find((node) => node.props.name === 'analytics_enabled');
  assert.equal(toggle.props.value, false);
  const code = nodes.find((node) => node.props.name === 'radman_settings[analytics_measurement_id]');
  assert.equal(code.props.dir, 'ltr'); assert.equal(code.props.maxLength, 8192);
  code.props.onChange('G-ABC1234567');
  assert.deepEqual(changes, [['analytics_measurement_id', 'G-ABC1234567']]);
});

test('grouped settings menus disclose children without changing selection and keep keyboard navigation connected to panels', () => {
  state = undefined; effects = []; let selected; let focused;
  const groups = [{ id: 'general', label: 'General', items: [{ id: 'identity', label: 'Identity' }] }, { id: 'appearance', label: 'Appearance', items: [{ id: 'typography', label: 'Fonts' }, { id: 'layout', label: 'Layout' }] }];
  const render = () => SectionNavigation({ groups, value: 'typography', onChange: (value) => { selected = value; }, label: 'Settings' });
  const nodes = walk(render());
  assert.equal(nodes.find((node) => node.props.id === 'rdct-group-appearance').props.inert, undefined);
  assert.equal(nodes.find((node) => node.props.id === 'rdct-group-general').props.inert, '');
  const toggle = nodes.find((node) => node.props['aria-controls'] === 'rdct-group-appearance');
  toggle.props.onClick(); assert.equal(selected, undefined);
  assert.equal(walk(render()).find((node) => node.props.id === 'rdct-group-appearance').props.inert, '');
  toggle.props.onClick();
  nodes.find(node => node.props['aria-controls'] === 'rdct-group-general').props.onClick();
  assert.equal(walk(render()).find(node => node.props.id === 'rdct-group-appearance').props.inert, '');
  assert.equal(walk(render()).filter(node => node.props['data-open'] === true).length, 1);
  toggle.props.onClick();
  const children = walk(render()).find((node) => node.props.id === 'rdct-group-appearance');
  children.props.onKeyDown({ key: 'ArrowDown', target: { dataset: { section: 'typography' } }, preventDefault() {}, currentTarget: { querySelectorAll: () => [{ focus() {} }, { focus: () => { focused = 'layout'; } }] } });
  assert.equal(selected, 'layout'); assert.equal(focused, 'layout');
  assert.equal(nodes.find((node) => node.props.id === 'rdct-tab-typography').props['aria-controls'], 'rdct-panel-typography');
});

test('shared color trigger reopens its existing picker and destroys it on unmount', async () => {
  state = undefined; effects = []; refs = []; refIndex = 0;
  const changes = [];
  const tree = ColorField({ id: 'brand', label: 'Brand color', value: '#123456', onChange: (value) => changes.push(value), resetLabel: 'Reset' });
  const trigger = walk(tree).find(node => node.type === 'button' && node.props.id === 'brand');
  assert.equal(trigger.props['aria-haspopup'], 'dialog');
  let opened = 0; let destroyed = 0;
  refs[1].current = { show: () => opened++, destroy: () => destroyed++ };
  const cleanup = effects[0]();
  await trigger.props.onClick(); await trigger.props.onClick();
  assert.equal(opened, 2);
  walk(tree).find(node => node.props.label === 'Reset').props.onClick();
  assert.deepEqual(changes, ['']);
  cleanup();
  assert.equal(destroyed, 1); assert.equal(refs[1].current, null);
});

test('single-language admin font definitions exclude inactive groups and explicit multilingual controls retain both choices', () => {
  const config = { primaryFontGroup: 'persian', fonts: { fa: { family: 'Local Persian', files: [['persian/one.woff2', '400']] }, en: { family: 'Local Latin', files: [['english/one.woff2', '400']] } }, fontBase: '/fonts/' };
  const values = { font_language: 'auto', mixed_language_fonts: false, persian_font: 'fa', latin_font: 'en' };
  assert.deepEqual(fontGroups(config, values), ['persian']);
  const css = FontFaces({ config, values }).props.children[0];
  assert.match(css, /persian\/one/); assert.doesNotMatch(css, /english\/one/);
  assert.deepEqual(fontGroups({ ...config, multilingualFonts: true }, values), ['persian', 'latin']);
  assert.deepEqual(fontGroups(config, { ...values, mixed_language_fonts: true }), ['persian', 'latin']);
  const english = FontFaces({ config, values: { ...values, font_language: 'latin' } }).props.children[0];
  assert.match(english, /english\/one/); assert.doesNotMatch(english, /persian\/one/);
});

test('custom CSS device selection preserves sibling values and maps edits to the selected scope', () => {
  state = undefined; effects = []; const changes = [];
  const config = { canEditCode: true, codeEditor: { css: false }, labels: new Proxy({}, { get: (_, key) => key }) };
  const values = { custom_css: '.global{}', custom_css_mobile: '.mobile{}' };
  const render = () => CustomCodeFields({ kind: 'css', values, config, change: (...args) => changes.push(args) });
  const first = walk(render()); first.find((node) => node.props.options).props.onChange('mobile');
  const editor = walk(render()).find((node) => node.type === CodeEditor);
  assert.equal(editor.props.value, '.mobile{}'); editor.props.change('.changed{}');
  assert.deepEqual(changes, [['custom_css_mobile', '.changed{}']]); assert.equal(values.custom_css, '.global{}');
  assert.equal(walk(CustomCodeFields({ kind: 'js', values, config: { ...config, canEditCode: false } })).some((node) => node.type === CodeEditor), false);
});

test('native code editor synchronizes changes and removes listeners on unmount; disabled syntax highlighting keeps a plain textarea', () => {
  refs = []; refIndex = 0; effects = []; let listener; let removed = false; let restored = false; const changes = [];
  const cm = { getValue: () => '.start{}', setValue() {}, on(event, callback) { listener = callback; }, off(event, callback) { removed = callback === listener; }, toTextArea() { restored = true; } };
  window.wp.codeEditor = { initialize: () => ({ codemirror: cm }) };
  const tree = CodeEditor({ value: '.start{}', change: (value) => changes.push(value), settings: { codemirror: { mode: 'css' } }, label: 'CSS' });
  walk(tree).find((node) => node.type === 'textarea').props.ref.current = {};
  const cleanup = effects[0](); listener(); assert.deepEqual(changes, ['.start{}']); cleanup(); assert.ok(removed && restored);
  refs = []; refIndex = 0; effects = [];
  const plain = CodeEditor({ value: '', change: (value) => changes.push(value), settings: false, label: 'JS' });
  assert.equal(effects[0](), undefined);
  const textarea = walk(plain).find((node) => node.type === 'textarea');
  assert.equal(textarea.props.dir, 'ltr'); textarea.props.onChange({ target: { value: 'const ready = true;' } });
  assert.equal(changes.at(-1), 'const ready = true;');
  delete window.wp.codeEditor;
});
