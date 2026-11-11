import test from 'node:test';
import assert from 'node:assert/strict';
import {resolveProfile} from '../radmanct/src/js/admin/typography-profile.js';
import {resetSettings} from '../radmanct/src/js/admin/settings-reset.js';
import {saveSettings} from '../radmanct/src/js/admin/settings-save.js';

const roles = ['headings', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'link', 'span'];
const base = {persian_font:'dana', latin_font:'manrope', headings_persian_font:'hafez', h1_persian_font:'hafez', body_persian_size:'17'};

test('empty content profile preserves global settings', () => {
  assert.deepEqual(resolveProfile(base, {}, roles), base);
  assert.deepEqual(resolveProfile(base, {persian_font:''}, roles), base);
});

test('content profiles use existing save and section reset without changing other fonts', async () => {
  const defaults = {persian_font:'dana',blog_typography:[],portfolio_typography:[],catalog_typography:[]};
  const values = {...defaults,blog_typography:{persian_font:'peyda',h1_persian_font:'hafez'},portfolio_typography:{persian_font:'dana'}};
  const reset = resetSettings(values, defaults, 'blog_typography',['blog_typography']);
  assert.deepEqual(reset.blog_typography, []);
  assert.deepEqual(reset.portfolio_typography, values.portfolio_typography);
  assert.equal(reset.persian_font, 'dana');
  const config = {defaults,save:{url:'/admin-ajax.php',nonce:'test'},labels:{saveError:'Failed'}};
  await saveSettings(config,values,async(url,request)=>{
    assert.equal(request.body.get('action'),'rdct_save_settings');
    assert.deepEqual(JSON.parse(request.body.get('values')),values);
    return {ok:true,json:async()=>({success:true,data:{values}})};
  });
});

test('body choice cascades and explicit title exception remains independent', () => {
  const view = resolveProfile(base, {persian_font:'peyda', h1_persian_font:'hafez'}, roles);
  assert.equal(view.persian_font, 'peyda');
  assert.equal(view.headings_persian_font, '');
  assert.equal(view.h4_persian_font, '');
  assert.equal(view.h1_persian_font, 'hafez');
  assert.equal(view.latin_font, 'manrope');
  assert.equal(view.body_persian_size, '17');
});

test('view inheritance uses current general values; resetting restores its parent', () => {
  const general = resolveProfile(base, {h1_persian_font:'hafez'}, roles);
  const view = resolveProfile(general, {persian_font:'peyda'}, roles);
  assert.equal(view.h1_persian_font, '');
  assert.deepEqual(resolveProfile(general, {}, roles), general);
  const changed = resolveProfile(base, {persian_font:'iransansx'}, roles);
  assert.equal(resolveProfile(changed, {}, roles).persian_font, 'iransansx');
});
