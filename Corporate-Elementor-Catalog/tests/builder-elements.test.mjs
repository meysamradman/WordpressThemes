import test from 'node:test';
import assert from 'node:assert/strict';
import { createItem, cloneItem } from '../radmanct/src/js/admin/builder-elements.js';

test('duplicating social elements gives nested translations independent identities', () => {
  const original = { id: 'original', type: 'social', links: [{ id: 'profile', text: 'Profile', url: 'https://example.test/', icon: 'external-link' }] };
  const before = structuredClone(original);
  const first = cloneItem(original);
  const second = cloneItem(original);
  assert.deepEqual(original, before);
  assert.equal(new Set([original.id, original.links[0].id, first.id, first.links[0].id, second.id, second.links[0].id]).size, 6);
  assert.equal(first.links[0].url, original.links[0].url);
  first.links[0].text = 'Different';
  assert.equal(original.links[0].text, 'Profile');
});

test('element creation preserves existing defaults and safe empty linked elements', () => {
  const config = {};
  assert.equal(cloneItem({id:'legacy',type:'block',blockId:42}).blockId, 42);
  assert.equal(createItem('menu', config).menuId, 0);
  assert.deepEqual(createItem('social', config).links, []);
  assert.equal(createItem('contact', config).direction, 'auto');
  assert.equal(createItem('icon', config).size, 20);
  const button = createItem('button', {...config,labels:{button:'Localized button'}});
  assert.equal(button.text,'Localized button');
  assert.equal(button.url,'');
});
