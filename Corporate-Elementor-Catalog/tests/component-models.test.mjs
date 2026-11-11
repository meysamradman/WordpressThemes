import {test} from 'node:test';
import assert from 'node:assert/strict';
import {createModelLoader} from '../radmanct-components/src/js/admin/component-models.js';

const origin = 'https://site.test';
const request = {id:'table-general',language:'fa',mode:'atomic',documentId:13};
test('only the selected sample is fetched and cached independently by document/language/mode', async () => {
  const calls = [];
  const load = createModelLoader({url:'/wp-admin/admin-ajax.php',nonce:'nonce'},async (url,options) => {
    calls.push(options.body); return {ok:true,json:async () => ({success:true,data:{model:{elType:'e-flexbox',elements:[{}]}}})};
  },origin);
  await load(request); await load(request); assert.equal(calls.length,1);
  assert.equal(calls[0].get('sample'),'table-general'); assert.equal(calls[0].get('view'),'model');
  await load({...request,language:'en'}); await load({...request,mode:'classic'}); await load({...request,documentId:14});
  assert.equal(calls.length,4);
});
test('invalid origins/variants and failed/cancelled responses never populate the cache', async () => {
  assert.throws(() => createModelLoader({url:'https://evil.test/ajax'},()=>{},origin));
  let calls = 0;
  const load = createModelLoader({url:'/ajax',nonce:'x'},async () => { calls++; return {ok:false,json:async () => ({success:false})}; },origin);
  await assert.rejects(load({...request,mode:'invalid'})); assert.equal(calls,0);
  await assert.rejects(load(request)); await assert.rejects(load(request)); assert.equal(calls,2);
  const cancelled = createModelLoader({url:'/ajax'},async () => ({ok:true,json:async () => ({success:true,data:{model:{elements:[{}]}}})}),origin);
  const controller = new AbortController(); controller.abort();
  await assert.rejects(cancelled({...request,signal:controller.signal}));
});
