import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';
import { nativeIconValue, localIconName, nativeIconManagerView, ensureLocalIconLibrary } from '../radmanct-components/src/js/admin/native-icon-values.js';

const h = (type, props, ...children) => ({ type, props:{...props,children} });
const element = {createElement:h,useState:() => ['',() => {}],useEffect() {},useRef:() => ({current:null}),createRoot() {}};
const ui = Object.fromEntries(['Box','Stack','Typography','Button','TextField'].map((name) => [name,`native.${name}`]));
const nodes = (node) => Array.isArray(node) ? node.flatMap(nodes) : node?.props ? [node,...nodes(node.props.children)] : [];
const definition = {name:'rdct-local',prefix:'rdct-icon-',displayPrefix:'rdct-library',icons:['phone','check'],url:false};

test('Atomic icon adapter uses the native manager, keeps string bindings, and respects disabled/unmount', async () => {
  let component; let selected; let shown; let loads = 0; let disabled = true; const cleanup = [];
  const window = {wp:{element:{...element,useEffect:(callback) => cleanup.push(callback())}},addEventListener() {},
    rdctIconControls:{library:definition,names:['phone','check'],labels:{phone:'Phone'},choose:'Choose',none:'None',localOnly:'Local icons'},
    elementor:{config:{icons:{libraries:[]}},iconManager:{loadIconLibraries() {++loads;},show:({view}) => {shown=view;}}},
    elementorV2:{ui,editorProps:{stringPropTypeUtil:{}},editorControls:{useBoundProp:() => ({value:'phone',disabled,setValue:(value) => {selected = value;}})},
      editorEditingPanel:{controlsRegistry:{get:() => false,register:(key,control) => {component=control;}}}}};
  const source=(await readFile(new URL('../radmanct-components/src/js/admin/icon-controls.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'');
  vm.runInNewContext(source,{window,nativeIconValue,localIconName,nativeIconManagerView,ensureLocalIconLibrary});
  let tree=component(); let buttons=nodes(tree).filter((node) => node.type===ui.Button);
  assert.ok(buttons.every((button) => button.props.disabled));
  buttons[0].props.onClick(); buttons[1].props.onClick(); assert.equal(loads,0); assert.equal(selected,undefined);
  disabled=false; tree=component(); buttons=nodes(tree).filter((node) => node.type===ui.Button);
  buttons[0].props.onClick(); assert.equal(loads,1);
  assert.deepEqual(shown.getControlValue(),nativeIconValue('phone'));
  shown.setValue(nativeIconValue('check')); assert.equal(selected,'check');
  shown.setValue({value:'fas fa-star',library:'fa-solid'}); assert.equal(selected,'check');
  buttons[1].props.onClick(); assert.equal(selected,'');
  cleanup.at(-1)(); shown.setValue(nativeIconValue('phone')); assert.equal(selected,'');
});

test('Classic icon adapter extends native icons and stores only catalog names without losing history options', async () => {
  let adapter; let raw='phone'; let savedOptions; let warnings=0;
  function BaseData() {}
  BaseData.prototype.getControlValue=function() {return raw;};
  BaseData.prototype.setValue=function(value,options) {raw=value;savedOptions=options;};
  const NativeIcons={prototype:{setDefaultIconLibraryLabel() {}},extend:(methods) => methods};
  const window={wp:{element},addEventListener() {},rdctIconControls:{names:['phone','check'],localOnly:'Local icons'},
    elementor:{getControlView:() => NativeIcons,modules:{controls:{BaseData}},addControlView:(key,view) => {adapter=view;},notifications:{showToast:() => ++warnings}}};
  const source=(await readFile(new URL('../radmanct-components/src/js/admin/icon-controls.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'');
  vm.runInNewContext(source,{window,nativeIconValue,localIconName,nativeIconManagerView,ensureLocalIconLibrary});
  assert.deepEqual(adapter.getControlValue(),nativeIconValue('phone'));
  const options={history:true}; adapter.setValue(nativeIconValue('check'),options);
  assert.equal(raw,'check');assert.equal(savedOptions,options);
  assert.equal(adapter.getCleanControlValue.call({model:{get:() => 'phone'}}),'check');
  raw='phone'; assert.equal(adapter.getCleanControlValue.call({model:{get:() => 'phone'}}),undefined); raw='check';
  adapter.setValue({library:'svg',value:{id:3}});assert.equal(raw,'check');assert.equal(warnings,1);
  adapter.setValue({library:'',value:''});assert.equal(raw,'');
});

test('Native icon value bridge rejects unsupported selections and keeps project and unavailable legacy identifiers', () => {
  assert.deepEqual(nativeIconValue('client-logo'),{library:'rdct-local',value:'rdct-library rdct-icon-client-logo'});
  assert.equal(localIconName(nativeIconValue('client-logo'),['client-logo']),'client-logo');
  for(const value of [{library:'svg',value:{id:2}},{library:'rdct-local',value:'rdct-library rdct-icon-check onclick=bad'},nativeIconValue('missing')]) {
    assert.equal(localIconName(value,['check']),undefined);
  }
  assert.equal(nativeIconValue('removed-icon').value,'rdct-library rdct-icon-removed-icon');
  assert.equal(nativeIconValue('<script>').value,'');
});

test('Native picker repairs a missing local tab before recommendations and preserves existing libraries', () => {
  const stock = {name:'fa-solid',icons:['check']};
  const libraries = [stock];
  const editor = {config:{icons:{libraries}}};
  const names = ['phone','check'];
  assert.equal(ensureLocalIconLibrary(editor,definition),true);
  const tab = libraries.find((item) => item.name === 'rdct-local');
  assert.equal(tab.displayPrefix,'rdct-library');
  assert.equal(tab.prefix,'rdct-icon-');
  assert.deepEqual(tab.icons,names);
  assert.notEqual(tab.icons,definition.icons);
  const recommended = nativeIconManagerView('phone',()=>{},names).model.get('recommended');
  assert.ok(Object.keys(recommended).every((name) => libraries.some((item) => item.name === name)));
  tab.icons = {phone:{name:'Phone'}};
  ensureLocalIconLibrary(editor,definition);
  assert.equal(libraries.length,2);
  assert.equal(libraries[0],stock);
  assert.equal(libraries[1],tab);
  assert.equal(ensureLocalIconLibrary({},definition),false);
});

test('Atomic table uses native text fields while cell edits retain adjacent data', async () => {
  globalThis.window = {wp:{element}};
  const {TableGrid} = await import('../radmanct-components/src/js/admin/table-grid.js');
  let next;
  const labels = {table:'Table',row:'Row',column:'Column'};
  const tree = TableGrid({value:{columns:['A','B'],rows:[{cells:['one','two']}]},labels,ui,disabled:true,onChange:(value) => {next = value;}});
  const fields = nodes(tree).filter((node) => node.type === ui.TextField);
  assert.equal(fields.length,4);
  assert.ok(fields.every((node) => node.props.disabled === true));
  const cell = fields.find((node) => node.props.value === 'one');
  assert.equal(cell.props.inputProps.maxLength,3000);
  cell.props.onChange({target:{value:'edited'}});
  assert.deepEqual(next.rows[0].cells,['edited','two']);
  assert.ok(!nodes(tree).some((node) => node.type === 'textarea' || node.type === 'input'));
});
