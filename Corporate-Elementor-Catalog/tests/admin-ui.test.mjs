import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';

test('builder preview opts out of Vue Devtools injection while keeping scripts sandboxed', async () => {
  const source = await readFile(new URL('../radmanct/src/js/admin/builder-preview.js', import.meta.url), 'utf8');
  assert.match(source, /'data-vue-devtools-ignore': ''/);
  assert.match(source, /sandbox: 'allow-same-origin'/);
  assert.doesNotMatch(source, /allow-scripts/);
});

globalThis.window = { wp: { element: { createElement: (type, props, ...children) => ({ type, props: { ...props, children } }) } } };
const { SelectControl, InputControl, CheckboxControl, SwitchControl, ButtonControl, RangeControl, ChoiceControl } = await import('../shared/admin/controls.js');
const { SelectField, InputField, ColorSetting } = await import('../radmanct/src/js/admin/fields.js');
const render = (node) => typeof node.type === 'function' ? render(node.type(node.props)) : node;

test('shared switches preserve checkbox submission and native keyboard semantics; icon actions have names and never submit accidentally', () => {
  let checked;
  const control = render(SwitchControl({ id: 'enabled', name: 'enabled', checked: true, disabled: true, 'aria-label': 'Enable layout', onChange: (value) => { checked = value; } }));
  assert.equal(control.type, 'input'); assert.equal(control.props.type, 'checkbox'); assert.equal(control.props.role, 'switch');
  assert.equal(control.props.checked, true); assert.equal(control.props.disabled, true); assert.equal(control.props.name, 'enabled');
  control.props.onChange({ target: { checked: false } }); assert.equal(checked, false);
  const action = ButtonControl({ label: 'Remove row', icon: 'trash', iconOnly: true, variant: 'danger', disabled: true });
  assert.equal(action.props.type, 'button'); assert.equal(action.props['aria-label'], 'Remove row'); assert.equal(action.props.title, 'Remove row');
  assert.equal(action.props.disabled, true); assert.match(action.props.className, /rdct-btn--danger/);
  assert.equal(ButtonControl({ label: 'Save', type: 'submit' }).props.type, 'submit');
});

test('builder toolbar keeps four viewports and returns to layouts without manual language direction', async () => {
  const { BuilderToolbar } = await import('../radmanct/src/js/admin/builder-toolbar.js');
  const nodes = (value) => Array.isArray(value) ? value.flatMap(nodes) : !value?.props ? [] : typeof value.type === 'function' ? nodes(value.type(value.props)) : [value, ...nodes(value.props.children)];
  const changes = []; const labels = new Proxy({}, { get: (_, key) => key });
  window.rdctBuilder = { libraryUrl: '/admin.php?page=rdct-footer' };
  const tree = BuilderToolbar({ labels, kind: 'footer', enabled: true, width: 1440, direction: 'rtl', dirty: true, canSave: true,
    onEnable: (value) => changes.push(['enabled', value]), onWidthChange: (value) => changes.push(['width', value]), onDirectionChange: (value) => changes.push(['direction', value]) });
  const all = nodes(tree);
  assert.equal(all.filter((node) => node.props.type === 'submit').length, 1);
  const device = all.filter((node) => node.type === 'button' && ['desktop', 'laptop', 'tablet', 'mobile'].includes(node.props['aria-label']));
  assert.equal(device.length, 4); device[2].props.onClick();
  const direction = all.find((node) => node.type === 'button' && node.props.children.flat().includes('LTR'));
  assert.equal(direction, undefined);
  assert.ok(all.some(node => node.type === 'a' && node.props.href === '/admin.php?page=rdct-footer'));
  assert.equal(all.some((node) => node.props.role === 'switch'), false);
  assert.equal(all.some((node) => node.props.type === 'range' || node.props.type === 'number'), false);
  assert.deepEqual(changes, [['width', 900]]);
  assert.equal(all.filter((node) => node.props.name).length, 0);
});

test('paired numeric controls submit once and preserve empty inheritance; choices never submit forms', () => {
  let value;
  const pair = RangeControl({ id: 'width', name: 'settings[width]', value: '', label: 'Width', min: 0, max: 100, onChange: (next) => { value = next; } });
  const slider = pair.props.children[0];
  const number = pair.props.children[1].props.children[0];
  assert.equal(slider.props.name, undefined);
  assert.equal(number.props.name, 'settings[width]');
  assert.equal(number.props.id, 'width');
  assert.equal(number.props.value, '');
  slider.props.onChange({ target: { value: '0' } }); assert.equal(value, '0');
  number.props.onChange({ target: { value: '' } }); assert.equal(value, '');
  const choices = ChoiceControl({ label: 'Align', value: 'center', options: [['start', 'Start'], ['center', 'Center']], onChange: (next) => { value = next; } });
  assert.ok(choices.props.children.every((button) => button.props.type === 'button'));
  assert.equal(choices.props.children[1].props['aria-pressed'], true);
  choices.props.children[0].props.onClick(); assert.equal(value, 'start');
});

test('shared selects retain native semantics, disabled options, numeric zero and RTL attributes', () => {
  let value;
  const select = SelectControl({ id: 'choice', name: 'settings[choice]', value: 0, numeric: true, dir: 'rtl', 'aria-describedby': 'choice-hint', options: [[0, 'Inherit'], [10, 'Unavailable', true]], onChange: (next) => { value = next; } });
  assert.equal(select.type, 'select'); assert.equal(select.props.value, 0);
  assert.equal(select.props.name, 'settings[choice]'); assert.equal(select.props.dir, 'rtl');
  assert.equal(select.props['aria-describedby'], 'choice-hint');
  assert.equal(select.props.className, undefined); assert.equal(select.props.numeric, undefined);
  assert.equal(select.props.children[1].props.disabled, true);
  select.props.onChange({ target: { value: '0' } }); assert.equal(value, 0);
});

test('shared inputs preserve empty settings and explicit builder conversion without altering checkbox submission', () => {
  let value;
  InputControl({ value: '', type: 'number', min: 1, onChange: (next) => { value = next; } }).props.onChange({ target: { value: '' } });
  assert.equal(value, '');
  InputControl({ value: 0, numeric: true, onChange: (next) => { value = next; } }).props.onChange({ target: { value: '12' } });
  assert.equal(value, 12);
  InputControl({ value: 12, numeric: true, onChange: (next) => { value = next; } }).props.onChange({ target: { value: '' } });
  assert.equal(value, '');
  const checkbox = CheckboxControl({ value: '1', checked: false, onChange: (next) => { value = next; } });
  assert.equal(checkbox.props.value, '1'); assert.equal(checkbox.props.checked, false);
  checkbox.props.onChange({ target: { checked: true } }); assert.equal(value, true);
});

test('settings adapters keep label associations, hint references, names and disabled state', () => {
  let changed;
  const field = render(SelectField({ name: 'catalog_archive_renderer', label: 'Archive', description: 'Hint', value: 'custom', disabled: true, options: [['custom', 'Custom']], onChange: (...args) => { changed = args; } }));
  assert.equal(field.props.children[0].props.htmlFor, 'rdct-catalog_archive_renderer');
  const body = field.props.children[1];
  const control = render(body.props.children.flat(Infinity)[0]);
  assert.equal(control.props.id, 'rdct-catalog_archive_renderer');
  assert.equal(control.props.name, 'radman_settings[catalog_archive_renderer]');
  assert.equal(control.props.disabled, true);
  assert.equal(control.props['aria-describedby'], 'rdct-catalog_archive_renderer-hint');
  control.props.onChange({ target: { value: 'elementor' } });
  assert.deepEqual(changed, ['catalog_archive_renderer', 'elementor']);
  const input = render(InputField({ name: 'brand_color', value: '#123456', label: 'Color', type: 'color', onChange: () => {} }));
  assert.equal(render(input.props.children[1].props.children.flat(Infinity)[0]).props.type, 'color');
  const color = render(ColorSetting({ name: 'preloader_background_secondary', label: 'Back color', value: '', fallback: '#123456', resetLabel: 'Inherit', onChange: () => {} }));
  const colorControls = color.props.children[1].props.children.flat(Infinity);
  const hidden = render(colorControls[0]);
  assert.equal(hidden.props.name, 'radman_settings[preloader_background_secondary]');
  assert.equal(hidden.props.value, '');
  assert.equal(colorControls[1].props.id, color.props.children[0].props.htmlFor);
  assert.equal(colorControls[1].props.showLabel, false);
});

test('admin palette stays in management assets and page styles use the shared source', async () => {
  for (const file of ['radmanct/src/css/admin/settings.css', 'radmanct/src/css/admin/builder.css', 'radmanct/src/css/admin/requirements.css', 'radmanct-components/src/css/admin/components.css', 'radmanct-content/src/css/admin/settings.css', 'radmanct-content/src/css/admin/content.css']) {
    const css = await readFile(new URL(`../${file}`, import.meta.url), 'utf8');
    assert.match(css, /@import.*shared\/admin\/tokens\.css/);
    assert.doesNotMatch(css, /#[0-9a-f]{3,8}\b/i);
    assert.doesNotMatch(css, /(?:padding|margin|border)-(?:left|right)\s*:/);
  }
  const manifest = JSON.parse(await readFile(new URL('../radmanct/dist/manifest.json', import.meta.url), 'utf8'));
  const css = await readFile(new URL(`../radmanct/dist/${manifest['src/css/theme.css'].file}`, import.meta.url), 'utf8');
  assert.doesNotMatch(css, /--rdct-admin-/);
});

test('shared Persian admin typography covers headings and labels while preserving font samples', async () => {
  const css = await readFile(new URL('../radmanct/src/css/admin/typography.css', import.meta.url), 'utf8');
  assert.match(css, /--rdct-admin-font:\s*"Radman CT Admin Peyda"/);
  assert.match(css, /h1, h2, h3, h4, h5, h6, label, legend/);
  assert.match(css, /:not\(\.rdct-stg__font-preview\)/);
  assert.doesNotMatch(css, /!important|\.dashicons|\.eicons/);
});


test('page title breadcrumbs stay independently available with inherited appearance across all views', async () => {
  const {PageTitleFields}=await import('../radmanct/src/js/admin/page-title-fields.js');
  const nodes=value=>Array.isArray(value)?value.flatMap(nodes):!value?.props?[]:typeof value.type==='function'?nodes(value.type(value.props)):[value,...nodes(value.props.children)];
  const labels=new Proxy({}, {get:(_,key)=>key});
  for(const scope of ['blog_archive','blog_single','catalog_archive','catalog_single','portfolio_archive','portfolio_single']) {
    let changed;
    const tree=nodes(PageTitleFields({value:{mode:'inherit',custom:false,breadcrumbMode:'inherit'},idPrefix:scope,labels,change:next=>changed=next}));
    const breadcrumb=tree.find(n=>n.props.id===`${scope}-breadcrumbs`);
    assert.equal(breadcrumb.type,'select');
    assert.deepEqual(breadcrumb.props.children.map(n=>n.props.value),['inherit','show','hide']);
    assert.ok(tree.some(n=>n.type==='label' && n.props.htmlFor===breadcrumb.props.id));
    breadcrumb.props.onChange({target:{value:'hide'}});
    assert.equal(changed.breadcrumbMode,'hide'); assert.equal(changed.custom,false); assert.equal(changed.mode,'inherit');
    assert.ok(!tree.some(n=>n.props.className==='rdct-background-fields'));
  }
});
