import test from 'node:test';
import assert from 'node:assert/strict';

let values = [], cursor = 0;
globalThis.window = {rdctBuilder:{libraryUrl:'/list'},wp:{element:{
  createElement:(type,props,...children)=>({type,props:{...props,children}}),
  useState:initial=>[values[cursor++] ?? initial,()=>{}],
  useRef:current=>({current}),useEffect:()=>{},
}}};
const {BuilderToolbar} = await import('../radmanct/src/js/admin/builder-toolbar.js');
const {BuilderSiteSettings} = await import('../radmanct/src/js/admin/builder-site-settings.js');
const {LoadingStatus} = await import('../shared/admin/controls.js');
const nodes = value => Array.isArray(value) ? value.flatMap(nodes) : value?.props ? [value,...nodes(value.props.children)] : [];
const labels = new Proxy({}, {get:(_,key)=>key});

test('builder has one visible operation status and save stays disabled until the operation ends',()=>{
  const all=nodes(BuilderToolbar({labels,kind:'header',width:1440,busy:true,status:'Saving',dirty:true,canSave:true}));
  const pending=all.filter(node=>node.props.className?.split(' ').includes('rdct-loading'));
  assert.equal(pending.length,1);
  assert.deepEqual(pending[0].props.children,['Saving']);
  assert.equal(pending[0].props.role,'status');
  const submit=all.find(node=>node.props.type==='submit');
  assert.equal(submit.props.disabled,true);
  assert.equal(submit.props['aria-busy'],true);
});

test('simultaneous header/footer loading has one status, retained until both settle and hidden during global saving',()=>{
  const render=(loading,saving=false)=>{
    cursor=0;values=['all',loading];
    return BuilderSiteSettings({config:{languages:[],labels},onDirty(){},registerSave(){},saving});
  };
  for(const pending of [{header:true,footer:true},{header:false,footer:true}]) {
    const tree=render(pending);
    assert.equal(nodes(tree).filter(node=>node.type===LoadingStatus).length,1);
  }
  assert.equal(nodes(render({header:false,footer:false})).filter(node=>node.type===LoadingStatus).length,0);
  assert.equal(nodes(render({header:true,footer:true},true)).filter(node=>node.type===LoadingStatus).length,0);
});
