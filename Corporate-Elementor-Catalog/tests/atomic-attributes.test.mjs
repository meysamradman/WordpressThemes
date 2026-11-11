import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';

test('client Twig-compatible attribute pattern rejects executable and oversized strings', async () => {
  const { pattern } = JSON.parse(await readFile(new URL('../shared/elementor/attributes.json', import.meta.url), 'utf8'));
  const cases = JSON.parse(await readFile(new URL('./fixtures/atomic-attributes.json', import.meta.url), 'utf8'));
  const regex = new RegExp(pattern.slice(1, -1));
  for (const value of cases.valid) assert.equal(regex.test(value), true, value);
  for (const value of cases.invalid) assert.equal(regex.test(value), false, value);
  assert.equal(regex.test(`data-value="${'a'.repeat(16384)}"`), false);
  const files = await Promise.all(['radmanct', 'radmanct-components'].map((name) => readFile(new URL(`../${name}/inc/integrations/elementor/safe-attributes.html.twig`, import.meta.url), 'utf8')));
  assert.equal(files[0], files[1]); assert.ok(files[0].includes(JSON.stringify(pattern)));
});
