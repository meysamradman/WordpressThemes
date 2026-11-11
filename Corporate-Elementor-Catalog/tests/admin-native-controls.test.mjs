import {test} from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import {normalizePickerColor,registerColorControl} from '../radmanct-components/src/js/admin/atomic-color-control.js';
import {registerResponsiveControls} from '../radmanct-components/src/js/admin/atomic-responsive-control.js';
import {curtainItem,registerCurtainControls} from '../radmanct-components/src/js/admin/atomic-curtain-controls.js';
import {nativeEditorRow,registerNativeRichText,createNativeTemplateSelect} from '../radmanct-components/src/js/admin/atomic-native-fields.js';
import {matchesFieldConditions} from '../radmanct-components/src/js/admin/atomic-repeater-control.js';

test('repeater conditions match native scalar, OR-list, negation and AND semantics',() => {
  const row={value:{mode:{$$type:'string',value:'image'},enabled:{$$type:'boolean',value:false}}};
  assert.equal(matchesFieldConditions(row,{}, {mode:['text','image'],enabled:false}),true);
  assert.equal(matchesFieldConditions(row,{}, {'mode!':['image','template']}),false);
  assert.equal(matchesFieldConditions(row,{style:{default:'normal'}},{style:'normal','mode!':'text'}),true);
  assert.equal(matchesFieldConditions(row,{}, {mode:['text'],enabled:false}),false);
});

test('native template Select displays existing numeric IDs and commits numeric IDs with history metadata',() => {
  let saved; let disabled=false;
  const controls={PropProvider:'native-provider',PropKeyProvider:'native-key',SelectControl:'native-select',useBoundProp:() => ({value:{$$type:'number',value:13},propType:{kind:'union'},isDisabled:() => disabled,setValue:(...args) => {saved=args;}})};
  const NativeSelect=createNativeTemplateSelect(controls,(type,props,...children) => ({type,props,children}));
  const tree=NativeSelect({options:[{value:0,label:'None'},{value:13,label:'Saved'}]});
  assert.equal(tree.props.value.selection.value,'13');
  assert.equal(tree.children[0].children[0].type,'native-select');
  const meta={action:{type:'change'}};
  tree.props.setValue({selection:{$$type:'string',value:'13'}},{},meta);
  assert.deepEqual(saved[0],{$$type:'number',value:13}); assert.equal(saved[2],meta);
  saved=undefined;
  tree.props.setValue({selection:{$$type:'string',value:'999'}},{},meta); assert.equal(saved,undefined);
  disabled=true;
  tree.props.setValue({selection:{$$type:'string',value:'13'}},{},meta); assert.equal(saved,undefined);
  disabled=false;
  tree.props.setValue({selection:{$$type:'string',value:'0'}},{},meta); assert.equal(saved[0].value,0);
});

test('legacy media and HTML adapt to native editor values without changing saved rows',() => {
  const row = {$$type:'rdct-curtain-item',value:{image_id:{$$type:'number',value:7},content:{$$type:'string',value:'<strong>Saved</strong>'},title:{$$type:'string',value:'Title'}}};
  const next = nativeEditorRow(row,{image_id:{media:true},content:{rich:true}});
  assert.equal(next.value.image_id.$$type,'image');
  assert.equal(next.value.image_id.value.src.value.id.value,7);
  assert.equal(next.value.content.$$type,'escaped-html');
  assert.equal(next.value.content.value,'<strong>Saved</strong>');
  assert.equal(next.value.title,row.value.title);
  assert.equal(row.value.image_id.$$type,'number');
  assert.equal(row.value.content.$$type,'string');
  const dynamic={$$type:'dynamic',value:{name:'existing-tag'}};
  const opaque=nativeEditorRow({value:{image_id:dynamic,content:dynamic}},{image_id:{media:true},content:{rich:true}});
  assert.equal(opaque.value.image_id,dynamic);
  assert.equal(opaque.value.content,dynamic);
});

test('rich content uses native InlineEditingControl with legacy content and native value commits',() => {
  let component; let committed;
  const bound = {value:{$$type:'string',value:'Existing'},propType:{kind:'union'},setValue:(...args) => {committed=args;},isDisabled:() => true};
  const controls = {useBoundProp:() => bound,InlineEditingControl:'native-inline-editor',PropProvider:'native-provider',PropKeyProvider:'native-key'};
  registerNativeRichText({get:() => null,register:(key,value) => {component=value;}},controls,{createElement:(type,props,...children) => ({type,props,children})},{escapedHtmlPropTypeUtil:{}});
  const context={elementId:'box-13'};
  const tree=component({context});
  assert.equal(tree.type,'native-provider');
  assert.equal(tree.props.value.content.value,'Existing');
  assert.equal(tree.props.isDisabled(),true);
  assert.equal(tree.children[0].children[0].type,'native-inline-editor');
  assert.equal(tree.children[0].children[0].props.context,context);
  const value={$$type:'escaped-html',value:'<strong>Edited</strong>'};
  tree.props.setValue({content:value},{history:'native'},{bind:'content'});
  assert.equal(committed[0],value);
  assert.deepEqual(committed[1],{history:'native'});
});

test('native picker values preserve hex, convert RGB/alpha and reject invalid CSS',() => {
  assert.equal(normalizePickerColor('#12345678'),'#12345678');
  assert.equal(normalizePickerColor('#abcd'),'#aabbccdd');
  assert.equal(normalizePickerColor('rgb(16, 32, 48)'),'#102030');
  assert.equal(normalizePickerColor('rgba(16, 32, 48, .5)'),'#10203080');
  assert.equal(normalizePickerColor('transparent'),'#00000000');
  assert.equal(normalizePickerColor(''),null);
  for (const invalid of ['#abcde','rgb(999,0,0)','rgba(1,2,3,)','red;display:none','var(--unknown)',{}]) assert.equal(normalizePickerColor(invalid),undefined);
});

function fixture() {
  const registered = new Map(); const saved = [];
  const h = (type,props,...children) => ({type,props:props ?? {},children});
  const ui = Object.fromEntries(['Stack','ToggleButtonGroup','ToggleButton','Slider','Button'].map((key) => [key,`native-${key}`]));
  const controls = {ColorControl:'native-color',PropProvider:'native-provider',PropKeyProvider:'native-key',NumberControl:'native-number',ToggleControl:'native-toggle',useBoundProp:() => ({value:{mobile:{$$type:'number',value:18}},setValue:(next) => saved.push(next),disabled:false,propType:{kind:'object'}})};
  globalThis.window = {wp:{element:{createElement:h,useState:(initial) => [initial,() => {}]}},elementorV2:{ui,editorProps:{stringPropTypeUtil:{key:'string'}}}};
  const registry = {get:(key) => registered.get(key),register:(key,component) => registered.set(key,component)};
  return {registered,saved,controls,registry};
}

test('color adapter renders Elementor ColorControl and keeps the existing string prop',() => {
  const f = fixture();
  registerColorControl(f.registry,f.controls,window.wp.element.createElement);
  const node = f.registered.get('rdct-color')();
  assert.equal(node.type,'native-color');
  assert.equal(node.props.propTypeUtil.key,'string');
  node.props.onChange('rgba(16,32,48,.5)');
  assert.equal(f.saved[0],'#10203080');
  node.props.onChange(null); assert.equal(f.saved[1],null);
});

test('responsive composition uses native controls and preserves overrides on other devices',() => {
  const f = fixture();
  const spec = {min:8,max:128,default:24,label:'Size'};
  registerResponsiveControls(f.registry,f.controls,{controls:{size:spec},labels:{desktop:'Desktop',tablet:'Tablet',mobile:'Mobile'}});
  const root = f.registered.get('size')();
  const fieldRow = root.children[1]; const slider = fieldRow.children[0]; const provider = fieldRow.children[1];
  assert.equal(slider.type,'native-Slider');
  assert.equal(provider.type,'native-provider');
  assert.equal(provider.children[0].type,'native-key');
  assert.equal(provider.children[0].children[0].type,'native-number');
  slider.props.onChange(null,500);
  assert.deepEqual(f.saved[0],{desktop:{$$type:'number',value:128},mobile:{$$type:'number',value:18}});
});

test('responsive sizes use the native SizeControl without a parallel numeric slider',() => {
  const f = fixture();
  f.controls.SizeControl = 'native-size';
  registerResponsiveControls(f.registry,f.controls,{controls:{size:{min:0,max:100,default:24,units:['px','rem'],label:'Size'}},labels:{}});
  const root = f.registered.get('size')();
  assert.equal(root.children[1].type,'native-provider');
  assert.equal(root.children[1].children[0].children[0].type,'native-size');
  assert.equal(root.children[1].children[0].children[0].props.disableCustom,true);
});

test('missing native button exports cannot register undefined React element types',() => {
  for (const key of ['Button','ToggleButton']) {
    const f = fixture();
    delete window.elementorV2.ui[key];
    registerResponsiveControls(f.registry,f.controls,{controls:{size:{min:0,max:100,default:24}},labels:{}});
    assert.equal(f.registered.size,0);
  }
});

test('responsive choices use native ToggleControl and missing API does not register a broken field',() => {
  const f = fixture();
  const config = {controls:{align:{options:{start:'Start',end:'End'},default:'start'}},labels:{}};
  registerResponsiveControls(f.registry,f.controls,config);
  const provider = f.registered.get('align')().children[1];
  assert.equal(provider.children[0].children[0].type,'native-toggle');
  for (const option of provider.children[0].children[0].props.options) {
    assert.equal(typeof option.renderContent,'function');
    assert.equal(option.renderContent({size:'tiny'}),option.label);
  }
  f.controls.NumberControl = null;
  registerResponsiveControls(f.registry,f.controls,{...config,controls:{missing:config.controls.align}});
  assert.equal(f.registered.has('missing'),false);
});

test('responsive unit choices use a compact native select and preserve other device overrides', () => {
  const f = fixture(); f.controls.SelectControl = 'native-select';
  registerResponsiveControls(f.registry, f.controls, {controls:{unit:{label:'Unit',options_ui:'select',options:{px:'px',em:'em',rem:'rem'},default:'px'}},labels:{}});
  const root = f.registered.get('unit')();
  const select = root.children[1].children[0].children[0];
  assert.equal(select.type, 'native-select');
  assert.equal(select.props.options.length, 3);
  assert.equal(select.props.ariaLabel, 'Unit');
  root.children[2].props.onClick();
  assert.deepEqual(f.saved[0], {mobile:{$$type:'number',value:18}});
});

function curtainFixture(count = 2,disabled = false) {
  const fields = {title:{default:'Title',label:'Title'},content:{default:'',label:'Content',multiline:true,condition:{content_type:'text'}},content_type:{default:'text',label:'Type',options:{text:'Text',template:'Template'}},template_id:{default:0,label:'Template',template:true},angle:{default:180,label:'Angle',min:0,max:360},color:{default:'#000000',label:'Color',color:true,group:'normal'}};
  const config = {fields,templates:{0:'None',13:'Saved template'},labels:{item:'Item',normal:'Normal',active:'Active'}};
  const rows = Array.from({length:count},(_,index) => curtainItem(fields,{title:`Item ${index}`}));
  const saved = []; const registered = new Map(); let context;
  const element = {createElement:(type,props,...children) => ({type,props:props ?? {},children}),createContext:() => ({Provider:'context'}),useContext:() => context,useState:(initial) => [initial,() => {}]};
  const ui = Object.fromEntries(['Stack','ToggleButtonGroup','ToggleButton','Select','MenuItem','Button'].map((key) => [key,`native-${key}`]));
  const controls = Object.fromEntries(['Repeater','PropProvider','PropKeyProvider','TextControl','TextAreaControl','ColorControl','SelectControl','NumberControl','ControlFormLabel'].map((key) => [key,`native-${key}`]));
  controls.useBoundProp = () => ({value:rows,disabled,propType:{kind:'array',item_prop_type:{kind:'union',prop_types:{'rdct-curtain-item':{kind:'object',shape:{}}}}},setValue:(...args) => saved.push(args)});
  const registry = {get:(key) => registered.get(key),register:(key,component) => registered.set(key,component)};
  registerCurtainControls(registry,controls,config,element,ui,{stringPropTypeUtil:{key:'string'}});
  const root = registered.get('rdct-curtain-items')(); context = root.props.value;
  return {rows,saved,root,repeater:root.children[0],context,fields};
}

test('Curtain uses native repeater and keeps typed defaults, bounds and action metadata',() => {
  const f = curtainFixture(12);
  assert.equal(f.repeater.type,'native-Repeater');
  assert.equal(f.repeater.props.disableAddItemButton,true);
  assert.equal(f.repeater.props.showDuplicate,false);
  assert.equal(f.repeater.props.showToggle,false);
  assert.equal(f.repeater.props.itemSettings.initialValues.value.angle.$$type,'number');
  f.repeater.props.setValues([...f.rows,curtainItem(f.fields)]);
  assert.equal(f.saved.length,0);
  const meta = {action:{type:'reorder'}};
  f.repeater.props.setValues([...f.rows].reverse(),{},meta);
  assert.equal(f.saved[0][2],meta);
  assert.equal(f.saved[0][0][0].value.title.value,'Item 11');
  const locked = curtainFixture(2,true); locked.repeater.props.setValues([]);
  assert.equal(locked.saved.length,0);
});

test('Curtain item edits preserve siblings and native controls bind to the existing fields',() => {
  const f = curtainFixture();
  const content = f.repeater.props.itemSettings.Content({value:f.rows[0],index:0});
  assert.equal(content.type,'native-PropProvider');
  assert.equal(content.children[0].children[0].props.propType.kind,'object');
  assert.equal(content.children[0].type,'native-PropKeyProvider');
  assert.equal(content.children[0].props.bind,'0');
  const renderedFields = content.children[0].children[0].children[0].children[1];
  const fields = Object.fromEntries(renderedFields.map((node) => [node.props.fieldKey,node.type(node.props)]));
  assert.equal(fields.title.children[1].type,'native-PropKeyProvider');
  assert.equal(fields.title.children[1].children[0].type,'native-TextControl');
  assert.equal(fields.content.children[1].children[0].type,'native-TextAreaControl');
  assert.equal(fields.content_type.children[1].children[0].type,'native-SelectControl');
  assert.equal(fields.angle.children[1].children[0].type,'native-NumberControl');
  assert.equal(typeof fields.template_id.children[1].children[0].type,'function');
  f.context.replace(0,{...f.rows[0],value:{...f.rows[0].value,template_id:{$$type:'number',value:13}}});
  assert.equal(f.saved[0][0][0].value.template_id.$$type,'number');
  assert.equal(f.saved[0][0][0].value.template_id.value,13);
  assert.equal(f.saved[0][0][1],f.rows[1]);
});

test('native PropKeyProvider accepts the resolved repeater item schema and rejects the serialized union',() => {
  const source=readFileSync(new URL('../themes/elementor/assets/js/packages/editor-controls/editor-controls.js',import.meta.url),'utf8');
  const declaration=source.match(/var PropKeyProvider = [\s\S]+?\n\t};/)[0];
  const build=new Function('usePropContext','react','ArrayPropKeyProvider','ObjectPropKeyProvider','MissingPropTypeError','UnsupportedParentError',`${declaration}; return PropKeyProvider;`);
  let schema={kind:'union'};
  const native=build(() => ({propType:schema}),{createElement:(type,props) => ({type,props})},'array','object',Error,Error);
  assert.throws(() => native({bind:'title'}));
  const f=curtainFixture();
  schema=f.context.itemType;
  assert.equal(native({bind:'title'}).type,'object');
});
