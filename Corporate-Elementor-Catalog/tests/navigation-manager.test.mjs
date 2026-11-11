import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import {runInNewContext} from 'node:vm';

test('navigation lifecycle shares one manager and disposes removed Elementor instances',async () => {
  const source = (await readFile(new URL('../radmanct/src/js/navigation/manager.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/m,'').replace('export function','function');
  let callback; let starts = 0; let stops = 0; let disconnects = 0;
  const first = {isConnected:true}; const second = {isConnected:false};
  const scope = {documentElement:{},querySelectorAll:() => [first,second].filter((root) => root.isConnected)};
  const context = {queueMicrotask,initializeMenus:() => {starts++;return () => stops++;},MutationObserver:class {constructor(fn){callback = fn;}observe(){}disconnect(){disconnects++;}}};
  runInNewContext(`${source}\nthis.observe = observeMenus;`,context);
  const cleanup = context.observe(scope);
  assert.equal(context.observe(scope),cleanup); assert.equal(starts,1);
  first.isConnected = false; second.isConnected = true; callback();
  await Promise.resolve();
  assert.equal(starts,2); assert.equal(stops,1);
  cleanup(); cleanup();
  assert.equal(stops,2); assert.equal(disconnects,1);
});
