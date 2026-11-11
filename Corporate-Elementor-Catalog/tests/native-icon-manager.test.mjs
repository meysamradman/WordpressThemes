import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';
import {ensureLocalIconLibrary, nativeIconManagerView, prepareNativeIconLibraries} from '../radmanct-components/src/js/admin/native-icon-values.js';

const source = await readFile(new URL('../themes/elementor/assets/js/editor.js',import.meta.url),'utf8');
function nativeFunction(marker, offset = 0) {
  const start = source.indexOf(marker,offset);
  assert.ok(start >= 0,`Native method missing: ${marker}`);
  const begin = source.indexOf('function',start);
  let end = source.indexOf('{',begin); let depth = 1;
  while (depth && ++end < source.length) {
    if (source[end] === '{') depth++;
    if (source[end] === '}') depth--;
  }
  return source.slice(begin,end + 1);
}

test('real supplied native picker methods reproduce missing-library failures and accept the repaired local tab', () => {
  const names = ['phone','check'];
  const elementor = {config:{icons:{libraries:[{name:'all',native:true}]}},iconManager:{library:{initIconType(tab,callback) {
    if (Array.isArray(tab.icons)) tab.icons = Object.fromEntries(tab.icons.map((name) => [name,{name,displayPrefix:tab.displayPrefix}]));
    callback(tab);
  }}}};
  const view = nativeIconManagerView('phone',()=>{},names);
  let rendered;
  const context = {elementor,renderIconManager:(data) => {rendered=data;return {unmount() {}};},_toConsumableArray:Array.from};
  const open = vm.runInNewContext(`(${nativeFunction('value: function onPickerShow()',source.indexOf('key: "loadIconLibraries"'))})`,context);
  const manager = {getSettings:() => view,setSettings() {}};
  assert.throws(() => open.call(manager),/displayPrefix/);
  const recommended = vm.runInNewContext(`(${nativeFunction('"handleRecommendedList", function()')})`,{
    ...context,_this:{props:{icons:{'rdct-local':names}},getLibrary:(name) => elementor.config.icons.libraries.filter((tab) => tab.name === name),getIconsOfType:(_,icons) => Object.values(icons)},
  });
  assert.throws(() => recommended(),/icons/);
  assert.equal(ensureLocalIconLibrary(elementor,{name:'rdct-local',label:'Radman CT',icons:names,prefix:'rdct-icon-',displayPrefix:'rdct-library',url:false}),true);
  open.call(manager);
  assert.equal(rendered.selected.library,'rdct-local');
  assert.equal(recommended().length,2);
  open.call(manager);
  assert.equal(recommended().length,2);
});

test('real native loader recovers a normalized library after its loaded flag is reset', async () => {
  const saved = new Map(); let fetches = 0;
  const tab = {name:'rdct-local',prefix:'rdct-icon-',displayPrefix:'rdct-library',ver:'1.0.0',icons:{phone:{name:'Phone',selector:'rdct-icon-phone'}}};
  const store = {getKey:(library) => library.name,get:(key) => saved.get(key),isValid:(library) => Boolean(saved.get(library.name)?.icons?.length),getIcons:(library) => saved.get(library.name)?.icons,save:(library) => saved.set(library.name,library)};
  const library = {loaded:{},runCallback(value) {this.notifyCallback?.(value);}};
  const context = {elementor:{iconManager:{store}},_this:library,_typeof:(value) => typeof value,jQuery:{each:(values,callback) => Object.entries(values).forEach(([key,value]) => callback(key,value))},elementorCommon:{helpers:{upperCaseWords:(value) => value}},fetch:async (url) => {fetches++;assert.equal(url,'/wp-admin/admin-ajax.php?action=rdct_icon_library');return {json:async () => ({icons:['phone','client-factory']})};}};
  library.normalizeIconList = vm.runInNewContext(`(${nativeFunction('value: function normalizeIconList(')})`,context);
  library.fetchIcons = vm.runInNewContext(`(${nativeFunction('"fetchIcons", function(')})`,context);
  const initialize = vm.runInNewContext(`(${nativeFunction('"initIconType", function(')})`,context);
  let received;
  initialize(tab,(value) => {received=value;});
  assert.equal(received,undefined); assert.equal(fetches,0);
  tab.fetchJson = '/wp-admin/admin-ajax.php?action=rdct_icon_library';
  initialize(tab,(value) => {received=value;});
  await new Promise((resolve) => setImmediate(resolve));
  assert.equal(fetches,1); assert.equal(library.loaded['rdct-local'],true);
  assert.deepEqual(Object.keys(received.icons),['phone','client-factory']);
  assert.equal(received.icons.phone.selector,'rdct-icon-phone');
});

test('serialized preparation prevents the shared native callback from replacing the local tab', async () => {
  const definition = {name:'rdct-local',icons:['phone']};
  const fixture = () => {
    const libs = [{name:'fa-solid',fetchJson:'/solid.json'},{...definition}]; const loaded = new Set(); let active = 0; let maximum = 0; let notify;
    const library = {initIconType(tab,callback) {
      notify = callback;
      const finish = () => {loaded.add(tab.name);tab.icons = {phone:{name:'Phone'}};notify?.(tab);};
      if (loaded.has(tab.name) || !tab.fetchJson) finish();
      else {active++;maximum=Math.max(maximum,active);setTimeout(() => {active--;finish();},1);}
    }};
    return {editor:{config:{icons:{libraries:libs}},iconManager:{library}},libs,maximum:() => maximum};
  };
  const broken = fixture();
  broken.libs.forEach((tab,index) => broken.editor.iconManager.library.initIconType(tab,(loaded) => {broken.libs[index] = loaded;}));
  await new Promise((resolve) => setTimeout(resolve,5));
  assert.equal(broken.libs.some((tab) => tab.name === 'rdct-local'),false);
  const fixed = fixture();
  const first = prepareNativeIconLibraries(fixed.editor,definition);
  assert.equal(prepareNativeIconLibraries(fixed.editor,definition),first);
  await first;
  fixed.libs.forEach((tab,index) => fixed.editor.iconManager.library.initIconType(tab,(loaded) => {fixed.libs[index] = loaded;}));
  assert.deepEqual(fixed.libs.map((tab) => tab.name),['fa-solid','rdct-local']);
  assert.equal(fixed.maximum(),1);
});

test('preparation has a bounded failure and a later open can retry', async () => {
  const definition = {name:'rdct-local',icons:['phone']}; let ready = false;
  const editor = {config:{icons:{libraries:[definition]}},iconManager:{library:{initIconType(tab,callback) {if (ready) callback(tab);}}}};
  await assert.rejects(prepareNativeIconLibraries(editor,definition,5),/timed out/);
  ready = true; await prepareNativeIconLibraries(editor,definition);
});

test('Classic adapter waits for native libraries and does not open a destroyed control', async () => {
  const adapterSource = (await readFile(new URL('../radmanct-components/src/js/admin/icon-controls.js',import.meta.url),'utf8')).replace(/^import .*;$/gm,'');
  const helpers = await import('../radmanct-components/src/js/admin/native-icon-values.js');
  let adapter; let ready; let opened = 0;
  function NativeIcons() {}
  NativeIcons.prototype.setDefaultIconLibraryLabel = () => {};
  NativeIcons.prototype.openPicker = () => {opened++;};
  NativeIcons.extend = (value) => value;
  const definition = {name:'rdct-local',icons:['phone']};
  const editor = {config:{icons:{libraries:[definition]}},getControlView:() => NativeIcons,modules:{controls:{BaseData:function() {}}},addControlView:(_,value) => {adapter=value;},iconManager:{library:{initIconType(tab,callback) {ready=() => callback(tab);}}}};
  const window = {wp:{element:{}},rdctIconControls:{library:definition},elementor:editor,addEventListener(){}};
  vm.runInNewContext(adapterSource,{window,...helpers});
  const first = adapter.openPicker.call({isDestroyed:false});
  assert.equal(opened,0); ready(); await first; assert.equal(opened,1);
  const view = {isDestroyed:false}; const second = adapter.openPicker.call(view);
  view.isDestroyed = true; ready(); await second; assert.equal(opened,1);
});
