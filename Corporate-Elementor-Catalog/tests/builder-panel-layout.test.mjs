import test from 'node:test';
import assert from 'node:assert/strict';
let activeGroup;
globalThis.window={rdctBuilder:{labels:{},settingsUrl:'/settings'},wp:{element:{
  createElement:(type,props,...children)=>({type,props:props || {},children}),
  useState:initial=>[activeGroup ?? initial,()=>{}],useEffect(){},useRef:initial=>({current:initial}),
}}};
const {SettingsPanel}=await import('../radmanct/src/js/admin/builder-settings-panel.js');
const {ButtonFields}=await import('../radmanct/src/js/admin/builder-button-fields.js');
const walk=value=>Array.isArray(value)?value.flatMap(walk):value && typeof value==='object'?[value,...walk(value.children || [])]:[];
const labels=new Proxy({},{get:(_,key)=>key});

test('builder-wide settings use explicit tabs and keep header behavior separate from footer settings',()=>{
  const props={stored:{},layout:{sticky:'desktop'},labels};
  const header=walk(SettingsPanel({...props,kind:'header'}));
  assert.ok(header.some(n=>n.type?.name==='FieldGroup' && n.props.title==='headerBehavior'));
  assert.ok(header.some(n=>n.props.value==='desktop'));
  const tabs=header.find(n=>n.type?.name==='TabList');
  assert.deepEqual(tabs.props.tabs.map(tab=>tab.id),['general','layouts','backups','translations']);
  assert.equal(header.some(n=>n.type==='details' || n.type==='summary'),false);
  const footer=walk(SettingsPanel({...props,kind:'footer'}));
  assert.equal(footer.some(n=>n.type?.name==='FieldGroup' && n.props.title==='headerBehavior'),false);
  assert.equal(footer.some(n=>n.props.value==='desktop'),false);
});

test('button style tabs retain all groups and color changes preserve other values',()=>{
  activeGroup='normal';let patch;
  const tree=walk(ButtonFields({item:{id:'button',buttonOptions:{color:'#ffffff',radius:12}},update:value=>{patch=value;},labels,
    config:{buttonGroups:{content:'Content',appearance:'Appearance',normal:'Normal',hover:'Hover'},buttonFields:{background:{label:'Background',group:'normal',color:true,default:''}}}}));
  const tabs=tree.find(n=>n.type?.name==='TabList');
  assert.deepEqual(tabs.props.tabs.map(tab=>tab.id),['appearance','typography','normal','hover']);
  assert.equal(tree.some(n=>n.type==='details' || n.type==='summary'),false);
  tree.find(n=>n.type?.name==='ColorField').props.onChange('#123456');
  assert.deepEqual(patch,{buttonOptions:{color:'#ffffff',radius:12,background:'#123456'}});
});

test('typography remains editable through its visible tab without an accordion',()=>{
  activeGroup='typography';let patch;
  const tree=walk(ButtonFields({item:{id:'button',buttonOptions:{radius:8}},update:value=>{patch=value;},labels,
    config:{buttonGroups:{appearance:'Appearance'},buttonFields:{font_size:{label:'Font size',group:'appearance',default:16,min:0,max:100}}}}));
  assert.equal(tree.some(n=>n.type==='details' || n.type==='summary'),false);
  tree.find(n=>n.type?.name==='RangeControl').props.onChange(20);
  assert.deepEqual(patch,{buttonOptions:{radius:8,font_size:20}});
});
