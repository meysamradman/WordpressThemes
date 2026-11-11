import test from 'node:test';
import assert from 'node:assert/strict';
import { createArchiveRequest, formUrl } from '../radmanct/src/js/archive-request.js';

const response = (scope = 'catalog') => ({ ok: true, json: async () => ({ success: true, data: { scope, html: '<p>Results</p>', title: 'Archive' } }) });

test('archive metadata accepts string filter values and rejects malformed header or filter state', async () => {
  for (const metadata of [{ header: '<header>Title</header>', filters: { rm_search: 'needle' } }, { header: {} }, { filters: [] }, { filters: { rm_search: [] } }]) {
    let received = false; let failed = false;
    const request = createArchiveRequest('catalog', () => { received = true; }, () => { failed = true; }, async () => ({
      ok: true, json: async () => ({ success: true, data: { scope: 'catalog', html: '', title: '', ...metadata } }),
    }));
    await request.load(new URL('https://local.test/products/'));
    const valid = typeof metadata.header === 'string';
    assert.equal(received, valid); assert.equal(failed, !valid);
  }
});

test('archive waits for required styles and rejects a stale result even if preparation finishes late', async () => {
  const prepared = [], received = [], errors = [];
  const request = createArchiveRequest('blog', data => received.push(data), error => errors.push(error), async () => response('blog'), data => new Promise(resolve => prepared.push(() => resolve(data))));
  const first = request.load(new URL('https://local.test/blog/?page=1'));
  await new Promise(setImmediate);
  assert.equal(received.length, 0);
  const second = request.load(new URL('https://local.test/blog/?page=2'));
  await new Promise(setImmediate);
  prepared[1](); await second;
  prepared[0](); await first;
  assert.equal(received.length, 1);
  assert.equal(errors.length, 0);
});

test('a stylesheet failure preserves the previous results and uses the existing retry path', async () => {
  let received = false; const errors = [];
  const request = createArchiveRequest('blog', () => { received = true; }, error => errors.push(error), async () => response('blog'), async () => { throw new Error('Stylesheet failed'); });
  await request.load(new URL('https://local.test/blog/'));
  assert.equal(received, false);
  assert.equal(errors[0].message, 'Stylesheet failed');
});

test('latest archive request wins even when the server ignores cancellation', async () => {
  const pending = [], received = [], errors = [];
  const request = createArchiveRequest('catalog', (...args) => received.push(args), (...args) => errors.push(args), (url, options) => new Promise((resolve) => pending.push({ url, options, resolve })));
  const first = request.load(new URL('https://local.test/products/?rm_search=old'));
  const second = request.load(new URL('https://local.test/products/?rm_search=new'));
  assert.equal(pending[0].options.signal.aborted, true);
  assert.match(pending[1].url, /_rdct_fragment=catalog/);
  pending[1].resolve(response()); await second;
  pending[0].resolve(response()); await first;
  assert.equal(received.length, 1);
  assert.equal(received[0][1].searchParams.get('rm_search'), 'new');
  assert.equal(received[0][1].searchParams.has('_rdct_fragment'), false);
  assert.deepEqual(errors, []);
});

test('cancellation during debounce suppresses late results and invalid scopes fail safely', async () => {
  let resolve;
  const received = [], errors = [];
  const request = createArchiveRequest('blog', (...args) => received.push(args), (...args) => errors.push(args), () => new Promise((done) => { resolve = done; }));
  const first = request.load(new URL('https://local.test/blog/'));
  request.cancel(); resolve(response('blog')); await first;
  assert.equal(received.length, 0);
  const next = request.load(new URL('https://local.test/blog/'));
  resolve(response('catalog')); await next;
  assert.equal(received.length, 0); assert.equal(errors.length, 1);
});

test('GET archive URLs reset pagination and preserve plain permalink and language fields', () => {
  const Original = globalThis.FormData;
  globalThis.FormData = class { constructor(form) { return form.entries; } };
  try {
    const url = formUrl({ action: 'https://local.test/?post_type=radman_product&paged=4', entries: [['post_type', 'radman_product'], ['lang', 'fa'], ['rm_search', 'new text'], ['rm_brand', ''], ['paged', '4']] }, 'https://local.test/');
    assert.equal(url.searchParams.get('post_type'), 'radman_product');
    assert.equal(url.searchParams.get('lang'), 'fa');
    assert.equal(url.searchParams.get('rm_search'), 'new text');
    assert.equal(url.searchParams.has('paged'), false);
    assert.equal(url.searchParams.has('rm_brand'), false);
  } finally { globalThis.FormData = Original; }
});
