import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { insertSample, libraryModel } from '../radmanct-components/src/js/admin/component-insert.js';

const sample = () => ({ elType: 'container', settings: {}, elements: [{ elType: 'widget', widgetType: 'radman-text-reveal', settings: { text: 'Example', tag: 'h2' }, elements: [] }] });

test('library insertion uses the document command and clones each independent sample', async () => {
  const parent = { id: 'page' }; const calls = []; const model = sample();
  const editor = { getPreviewContainer: () => parent };
  const commands = { run: (name, args) => { calls.push({ name, args }); args.model.elements[0].settings.text = 'Changed'; return { id: `item-${calls.length}` }; } };
  await insertSample(model, editor, commands); await insertSample(model, editor, commands);
  assert.equal(calls.length, 2); assert.equal(calls[0].name, 'document/elements/create');
  assert.equal(calls[0].args.container, parent); assert.equal(calls[0].args.options.clone, true);
  assert.equal(model.elements[0].settings.text, 'Example'); assert.notEqual(calls[0].args.model, calls[1].args.model);
});

test('unavailable editors, unsupported widgets and failed commands never report success', async () => {
  let calls = 0; const commands = { run: () => { calls++; return {}; } };
  await assert.rejects(insertSample(sample(), {}, commands));
  const unknown = sample(); unknown.elements[0].widgetType = 'unknown';
  await assert.rejects(insertSample(unknown, { getPreviewContainer: () => ({}) }, commands));
  assert.equal(calls, 0);
  await assert.rejects(insertSample(sample(), { getPreviewContainer: () => ({}) }, { run: () => null }));
});

test('empty or invalid command results are rejected and valid arrays are accepted', async () => {
  const editor = { getPreviewContainer: () => ({}) };
  for (const result of [[], {}, [null], [{ id: '' }]]) {
    await assert.rejects(insertSample(sample(), editor, { run: () => result }));
  }
  const result = [{ id: 'created' }];
  assert.equal(await insertSample(sample(), editor, { run: () => result }), result);
});

test('document switches and read-only containers prevent the insertion command', async () => {
  let called = false;
  const commands = { run: () => { called = true; return { id: 'created' }; } };
  const editor = { getPreviewContainer: () => ({}), documents: { getCurrentId: () => 20 } };
  await assert.rejects(insertSample(sample(), editor, commands, 10));
  editor.getPreviewContainer = () => ({ isEditable: () => false });
  await assert.rejects(insertSample(sample(), editor, commands, 20));
  assert.equal(called, false);
});

test('Atomic samples preserve typed props, use a native flexbox and remain independently cloneable', async () => {
  const model = { version: '0.0', elType: 'e-flexbox', settings: {}, styles: [], elements: [{ version: '0.0', elType: 'widget', widgetType: 'rdct-atomic-icon', settings: { mirror: { $$type: 'boolean', value: false }, size: { $$type: 'number', value: 48 } }, styles: [], elements: [] }] };
  let created;
  await insertSample(model, { getPreviewContainer: () => ({}) }, { run: (_, args) => { created = args.model; return { id: 'native' }; } });
  assert.deepEqual(created, model); assert.notEqual(created.elements[0].settings.mirror, model.elements[0].settings.mirror);
  created.elements[0].settings.size.value = 12; assert.equal(model.elements[0].settings.size.value, 48);
  const mixed = structuredClone(model); mixed.elType = 'container';
  await assert.rejects(insertSample(mixed, { getPreviewContainer: () => ({}) }, { run() { throw new Error('Must not execute'); } }));
});

test('language and renderer selection never fall back to a different sample variant', () => {
  const classic = sample(); const atomic = { elType: 'e-flexbox' };
  const models = { first: { fa: classic, 'fa-atomic': atomic } };
  assert.equal(libraryModel(models, 'first', 'fa'), classic);
  assert.equal(libraryModel(models, 'first', 'fa', 'atomic'), atomic);
  assert.throws(() => libraryModel(models, 'first', 'en', 'atomic'));
  assert.throws(() => libraryModel(models, 'first', 'fa', 'unknown'));
  assert.throws(() => libraryModel(models, 'first', 'unknown'));
});

test('gallery assets and preview dependencies stay outside public frontend entries', async () => {
  const manifest = JSON.parse(await readFile(new URL('../radmanct/dist/manifest.json', import.meta.url), 'utf8'));
  const seen = new Set();
  function visit(key) { if (seen.has(key)) return; seen.add(key); for (const next of [...(manifest[key].imports ?? []), ...(manifest[key].dynamicImports ?? [])]) visit(next); }
  visit('src/js/frontend.js');
  assert.equal(seen.has('src/js/admin/components.js'), false);
  assert.equal(seen.has('src/js/admin/component-insert.js'), false);
  assert.equal(seen.has('src/js/admin/component-preview.js'), false);
  const library = await readFile(new URL('../radmanct-components/src/js/admin/components.js', import.meta.url), 'utf8');
  assert.doesNotMatch(library, /gsap|ScrollTrigger|fetch\(/);
  assert.match(library, /removeAttribute\('src'\)/);
  const assets = JSON.parse(await readFile(new URL('../radmanct-components/dist/manifest.json', import.meta.url), 'utf8'));
  const editorCss = await readFile(new URL('../radmanct-components/dist/' + assets['src/css/admin/elementor-library.css'].file, import.meta.url), 'utf8');
  const adminCss = await readFile(new URL('../radmanct-components/dist/' + assets['src/css/admin/components.css'].file, import.meta.url), 'utf8');
  assert.doesNotMatch(editorCss, /--rdct-admin-|Admin Peyda|\.rdct-lib-dialog/);
  assert.match(editorCss, /--e-a-bg-default/);
  assert.match(adminCss, /--rdct-admin-/);
});
