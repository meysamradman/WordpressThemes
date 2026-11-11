import { test } from 'node:test';
import assert from 'node:assert/strict';

globalThis.window = { rdctBuilder: { ajaxUrl: '/wp-admin/admin-ajax.php', nonce: 'test-nonce', kind: 'header', labels: { requestError: 'Your changes remain in the editor.' } } };
const { request, createBuilderRequest, builderSaveRequest } = await import('../radmanct/src/js/admin/builder-api.js');

test('theme settings share the authenticated builder transport without changing the editor configuration', async () => {
  const sent = [];
  globalThis.fetch = async (_url, options) => {sent.push(options.body);return {ok:true,json:async()=>({success:true,data:state})};};
  for (const kind of ['header','footer']) {
    const api = createBuilderRequest({ajaxUrl:'/wp-admin/admin-ajax.php',nonce:'settings-nonce',kind,labels:{requestError:'Failed'}});
    await api('save','default',{layout,revision:2});
  }
  assert.deepEqual(sent.map((body)=>body.get('kind')),['header','footer']);
  assert.ok(sent.every((body)=>body.get('nonce')==='settings-nonce' && body.get('revision')==='2'));
  assert.equal(window.rdctBuilder.nonce,'test-nonce');
});
const layout = { version: 1, enabled: true, rows: [{ id: 'row', items: [{ id: 'text', type: 'text', text: 'Original' }] }] };
const state = { id: 'default', data: { layout, revision: 2 }, layouts: [{ id: 'default', title: 'Main' }], defaults: [] };
const reply = (data) => { globalThis.fetch = async () => ({ ok: true, json: async () => ({ success: true, data }) }); };

test('first draft save creates a named layout and never saves over the original header', () => {
  const draft={creating:true,id:'default',data:{revision:0}};
  assert.deepEqual(builderSaveRequest(draft,layout,'  Home header  '),{operation:'create',id:'default',fields:{layout,title:'Home header'}});
  const saved={id:'l123',data:{revision:3}};
  assert.deepEqual(builderSaveRequest(saved,layout,' Renamed header '),{operation:'save',id:'l123',fields:{layout,revision:3,title:'Renamed header'}});
});

test('assignment catalog transport accepts only lightweight typed layout summaries', async () => {
  const summary={defaults:{all:'default'},layouts:[{id:'default',title:'Main header',enabled:true,language:null}]};
  reply(summary); assert.deepEqual(await request('assignments','default'),summary);
  for(const invalid of [{...summary,defaults:[]},{...summary,layouts:[{...summary.layouts[0],enabled:'yes'}]},{...summary,layouts:[null]}]) {
    reply(invalid); await assert.rejects(request('assignments','default'));
  }
});

test('builder transport rejects malformed success data before replacing editor state', async () => {
  for (const data of [null, [], {}, { ...state, data: null }, { ...state, layouts: [null] }, { ...state, data: { layout: { ...layout, rows: [null] }, revision: 2 } }, { ...state, data: { layout, revision: '2' } }]) {
    reply(data);
    await assert.rejects(request('save', 'default'), /Your changes remain/);
  }
  reply(state);
  assert.deepEqual(await request('save', 'default'), state);
  reply({ layout });
  assert.deepEqual(await request('validate', 'default'), { layout });
  reply({ html: '' });
  await assert.rejects(request('preview', 'default'), /Your changes remain/);
});

test('builder transport keeps native errors and sends authenticated POST with cancellation', async () => {
  const controller = new AbortController();
  globalThis.fetch = async (url, options) => {
    assert.equal(url, '/wp-admin/admin-ajax.php');
    assert.equal(options.method, 'POST');
    assert.equal(options.credentials, 'same-origin');
    assert.equal(options.signal, controller.signal);
    assert.equal(options.body.get('nonce'), 'test-nonce');
    assert.deepEqual(JSON.parse(options.body.get('layout')), layout);
    return { ok: false, json: async () => ({ success: false, data: { message: 'Layout changed in another tab.' } }) };
  };
  await assert.rejects(request('save', 'default', { layout }, controller.signal), /Layout changed in another tab/);
  globalThis.fetch = async () => ({ ok: false, json: async () => { throw new SyntaxError('Not JSON'); } });
  await assert.rejects(request('save', 'default'), /Your changes remain/);
});
