import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile, readdir } from 'node:fs/promises';

const root = new URL('../', import.meta.url);
const registry = JSON.parse(await readFile(new URL('tools/css-class-map.json', root), 'utf8'));
const roots = ['radmanct', 'radmanct-components', 'radmanct-content', 'radmanct-mcp', 'shared'];
async function* sources(directory) {
  for (const entry of await readdir(directory, { withFileTypes: true })) {
    if (['dist', 'vendor', 'node_modules'].includes(entry.name)) continue;
    const url = new URL(entry.name + (entry.isDirectory() ? '/' : ''), directory);
    if (entry.isDirectory()) yield* sources(url);
    else if (/\.(css|js|php|twig)$/.test(entry.name)) yield url;
  }
}
const legacy = (name) => Object.keys(registry.prefixes).some((prefix) => name === prefix || name.startsWith(prefix + '-') || name.startsWith(prefix + '__'));
const nativeIconIdentifier = (file, name) => /\/(?:shared\/icons|inc\/icons)\/elementor\.php$/.test(file.pathname) && ['rdct-library', 'rdct-icon-'].includes(name);

test('own CSS selectors and literal markup use stable short module names', async () => {
  assert.equal(new Set(Object.values(registry.classes)).size, Object.keys(registry.classes).length);
  const violations = [];
  for (const folder of roots) for await (const file of sources(new URL(folder + '/', root))) {
    const source = await readFile(file, 'utf8');
    const contexts = [...source.matchAll(/\.rdct-[\w-]+/g)].map((match) => match[0].slice(1));
    for (const match of source.matchAll(/(?:\bclass\s*=\s*|\bclassName\s*[:=]\s*)(['"`])([\s\S]*?)\1/g)) {
      contexts.push(...[...match[2].matchAll(/\brdct-[\w-]+/g)].map((token) => token[0]));
    }
    for (const name of contexts) if (legacy(name) && !nativeIconIdentifier(file, name)) violations.push(`${file.pathname}: ${name}`);
  }
  assert.deepEqual(violations, []);
});

test('stored Elementor widget and control identifiers retain their public names', async () => {
  for (const [path, name] of [
    ['widgets/carousel.php', 'rdct-carousel'], ['widgets/table.php', 'rdct-table'],
    ['widgets/icon-box.php', 'rdct-icon-box'], ['table-control.php', 'rdct-table-grid'],
    ['icon-picker-control.php', 'rdct-icon-picker'],
  ]) {
    const source = await readFile(new URL('radmanct-components/inc/integrations/elementor/' + path, root), 'utf8');
    assert.match(source, new RegExp(`get_(?:name|type)\\(\\)[^\\n]*return '${name}'`));
  }
  const backup = await readFile(new URL('radmanct/src/js/admin/builder-backup.js', root), 'utf8');
  assert.ok(backup.includes("format: 'rdct-builder'"));
});
