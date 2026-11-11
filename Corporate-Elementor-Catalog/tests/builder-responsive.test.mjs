import test from 'node:test';
import assert from 'node:assert/strict';
import { devices, deviceAt, deviceKey, hiddenDevices } from '../radmanct/src/js/admin/builder-devices.js';
import { findPreviewDrop } from '../radmanct/src/js/admin/builder-drag.js';

let device = 'tablet';
globalThis.window = { wp: { element: {
  createElement: (type, props, ...children) => ({ type, props: props || {}, children }),
  useState: () => [device, () => {}],
} } };
const { AppearanceControls } = await import('../radmanct/src/js/admin/builder-controls.js');
const { LayoutFields } = await import('../radmanct/src/js/admin/builder-layout-fields.js');
const { VisualFields } = await import('../radmanct/src/js/admin/builder-visual-fields.js');
const nodes = (node) => Array.isArray(node) ? node.flatMap(nodes) : !node || typeof node !== 'object' ? [] : [node, ...(node.children || []).flatMap(nodes)];
const render = (value, row = false, section = 'all') => {
  let result;
  const tree = AppearanceControls({ value, row, section, labels: new Proxy({}, { get: (_, key) => key }), update: (next) => { result = next; } });
  return { nodes: nodes(tree), result: () => result };
};

test('device boundaries and legacy hiding preserve the stored intent', () => {
  assert.deepEqual([767, 768, 1023, 1024, 1439, 1440].map(deviceAt), ['mobile', 'tablet', 'tablet', 'laptop', 'laptop', 'desktop']);
  assert.deepEqual(devices.map(({ width }) => deviceAt(width)), devices.map(({ id }) => id));
  assert.equal(deviceKey('tablet', 'fontSize'), 'tabletFontSize');
  assert.deepEqual(hiddenDevices({ visibility: 'mobile', hiddenDevices: ['tablet'] }).sort(), ['desktop', 'laptop', 'tablet']);
});

test('tablet controls persist inherited columns, zero gaps and independent widths', () => {
  device = 'tablet';
  const grid = render({ rowLayout: 'grid', columns: 4 }, true, 'layout');
  const count = grid.nodes.find((node) => (node.props.type === 'number' || node.type?.name === 'RangeControl'));
  assert.equal(count.props.value, 4); assert.equal(count.props.disabled, true);
  const inherit = grid.nodes.find((node) => node.props.checked === true);
  inherit.props.onChange(false); assert.equal(grid.result().tabletColumns, 4);
  count.props.onChange(2); assert.equal(grid.result().tabletColumns, 2);
  const spacing = render({ gap: 20 }, true, 'layout');
  spacing.nodes.find((node) => (node.props.type === 'number' || node.type?.name === 'RangeControl') && node.props.max === 64).props.onChange(0);
  assert.equal(spacing.result().tabletGap, 0);
  const item = render({ width: 'full' }, false, 'layout');
  item.nodes.find((node) => node.props.options?.some(([key]) => key === 'full')).props.onChange('auto');
  assert.equal(item.result().tabletWidth, 'auto'); assert.equal(item.result().width, 'full');
});

test('container direction persists per device and vertical drag follows the visual flow', () => {
  device = 'tablet';
  const controls = render({direction:'row'},true,'layout');
  const direction = controls.nodes.find((node)=>node.props.options?.some(([key])=>key==='column'));
  direction.props.onChange('column');
  assert.equal(controls.result().tabletDirection,'column'); assert.equal(controls.result().direction,'row');
  const layout = {rows:[{id:'row',appearance:{direction:'column'},items:[{id:'item',appearance:{}}]}]};
  const targets = [{rowId:'row',id:'item',item:true,rect:{left:0,top:0,right:100,bottom:100,width:100,height:100}}];
  assert.equal(findPreviewDrop(targets,10,90,layout,false,false,'desktop').index,1);
  assert.equal(findPreviewDrop(targets,90,10,layout,false,true,'desktop').index,0);
});

test('hiding one device converts legacy visibility without losing other device choices', () => {
  const view = render({ visibility: 'mobile' }, false, 'advanced');
  const checkboxes = view.nodes.filter((node) => Object.hasOwn(node.props, 'checked'));
  assert.deepEqual(checkboxes.map((node) => node.props.checked), [true, true, true, false]);
  checkboxes[1].props.onChange(false);
  assert.equal(view.result().visibility, 'all');
  assert.deepEqual(view.result().hiddenDevices, ['desktop', 'tablet']);
});

test('tablet grid drag follows its override and inherited full width can be reset', () => {
  const row = { id: 'r', appearance: { rowLayout: 'grid', columns: 4, tabletColumns: 1 }, items: [{ id: 'i', appearance: { width: 'full', tabletWidth: 'auto' } }] };
  const targets = [{ id: 'i', rowId: 'r', item: true, rect: { left: 0, top: 0, right: 100, bottom: 60, width: 100, height: 60 } }];
  assert.equal(findPreviewDrop(targets, 75, 5, { rows: [row] }, false, false, 'tablet').index, 0);
  row.appearance.tabletColumns = 2;
  assert.equal(findPreviewDrop(targets, 75, 5, { rows: [row] }, false, false, 'tablet').index, 1);
});

test('logo and layout controls wire all four devices without replacing unrelated values', () => {
  const labels = new Proxy({}, { get: (_, key) => key });
  let changed;
  const layout = { width: 'contained', gutter: 24, laptopGutter: 18, tabletGutter: 12, mobileGutter: 8 };
  const gutters = nodes(LayoutFields({ layout, labels, change: (next) => { changed = next; } })).filter((node) => (node.props.type === 'number' || node.type?.name === 'RangeControl'));
  assert.deepEqual(gutters.map((node) => node.props.value), [24, 18, 12, 8]);
  gutters[1].props.onChange('0'); assert.equal(changed.laptopGutter, 0); assert.equal(changed.tabletGutter, 12);
  gutters[2].props.onChange(''); assert.equal(changed.tabletGutter, null);
  const logo = nodes(VisualFields({ item: { type: 'logo', logoWidth: 200, tabletLogoWidth: 120 }, labels, update: (patch) => { changed = patch; } })).filter((node) => (node.props.type === 'number' || node.type?.name === 'RangeControl'));
  assert.equal(logo.length, 8);
  logo[4].props.onChange(100); assert.deepEqual(changed, { tabletLogoWidth: 100 });
  logo[3].props.onChange(50); assert.deepEqual(changed, { laptopLogoHeight: 50 });
  logo[6].props.onChange(0); assert.deepEqual(changed, { mobileLogoWidth: 0 });
});

test('device alignment and distribution overrides preserve desktop choices', () => {
  device = 'tablet';
  const row = render({ align: 'start', justify: 'start' }, true, 'layout');
  row.nodes.find((node) => node.props.options?.some(([key]) => key === 'stretch')).props.onChange('center');
  assert.equal(row.result().tabletAlign, 'center'); assert.equal(row.result().align, 'start');
  row.nodes.find((node) => node.props.options?.some(([key]) => key === 'space-between')).props.onChange('end');
  assert.equal(row.result().tabletJustify, 'end'); assert.equal(row.result().justify, 'start');
  row.nodes.find((node) => node.props.options?.some(([key]) => key === 'column')).props.onChange('column');
  assert.equal(row.result().tabletDirection, 'column');
  assert.equal(row.result().tabletLayout, 'inline');
  assert.equal(row.nodes.some((node)=>node.props.options?.some(([key])=>key==='stack')),false);
});

test('one direction control reflects legacy stacked layouts and clears the conflicting override on edit',()=>{
  device='tablet';
  const row=render({tabletLayout:'stack'},true,'layout');
  const direction=row.nodes.find((node)=>node.props.options?.some(([key])=>key==='column'));
  assert.equal(direction.props.value,'column');
  direction.props.onChange('row');
  assert.equal(row.result().tabletDirection,'row');
  assert.equal(row.result().tabletLayout,'inline');
});

test('button content precedes direct layout controls without a disclosure and URL target is not duplicated',async()=>{
  const {Inspector}=await import('../radmanct/src/js/admin/builder-inspector.js');
  const labels=new Proxy({},{get:(_,key)=>key});
  const tree=Inspector({item:{id:'button',type:'button',text:'Action',url:'',appearance:{}},row:{appearance:{}},update(){},labels,config:{},actions:[],device:'desktop'});
  const tabs=nodes(tree).find((node)=>node.props.tabs)?.props.tabs;
  const content=nodes(tabs.find((tab)=>tab.id==='content').content);
  const text=content.findIndex((node)=>node.type?.name==='InputControl' && node.props.value==='Action');
  assert.ok(text>=0 && text<content.findIndex((node)=>node.props.className==='rdct-bld-inspector__layout'));
  assert.equal(content.some((node)=>node.type==='details' || node.type==='summary'),false);
  assert.equal(content.filter((node)=>node.type==='label' && node.children.includes('newTab')).length,1);
  const style=VisualFields({item:{type:'button'},labels,update(){}});
  assert.equal(nodes(style).filter((node)=>node.type==='label' && node.children.includes('newTab')).length,0);
});

test('compact item layout uses two icon groups and reveals the numeric width only for custom sizing',async()=>{
  const {itemLayoutControls}=await import('../radmanct/src/js/admin/builder-item-layout.js');
  const labels=new Proxy({},{get:(_,key)=>key});let changed;
  const make=(width,direction='rtl')=>nodes(itemLayoutControls({values:{align:'start',width,customWidth:40},device:'desktop',labels,direction,change:(key,value)=>{changed=[key,value];}}));
  const groups=make('auto').filter((node)=>node.props.iconOnly);
  assert.equal(groups.length,2);
  assert.equal(groups[0].props.options[0][3],'editor-alignright');
  assert.equal(groups[1].props.options.length,4);
  groups[1].props.onChange('custom');assert.deepEqual(changed,['width','custom']);
  assert.equal(make('auto').some((node)=>node.type?.name==='RangeControl'),false);
  const width=make('custom').find((node)=>node.type?.name==='RangeControl');
  assert.equal(width.props.value,40);assert.equal(width.props.unit,'%');
  assert.equal(make('auto','ltr').find((node)=>node.props.iconOnly).props.options[0][3],'editor-alignleft');
});

test('a shared device overrides stale local state and hides duplicate device controls', () => {
  device = 'tablet'; let result;
  const labels = new Proxy({}, { get: (_, key) => key });
  const controls = nodes(AppearanceControls({ device: 'laptop', showDeviceControl: false, value: {}, section: 'layout', labels, update: (next) => { result = next; } }));
  assert.equal(controls.some((node) => node.props.options?.some(([key]) => key === 'desktop')), false);
  controls.find((node) => node.props.options?.some(([key]) => key === 'full')).props.onChange('full');
  assert.equal(result.laptopWidth, 'full'); assert.equal(result.tabletWidth, 'inherit');
  const row = { id: 'r', appearance: { rowLayout: 'flex', tabletLayout: 'stack' }, items: [{ id: 'i' }] };
  const targets = [{ id: 'i', rowId: 'r', item: true, rect: { left: 0, top: 0, right: 100, bottom: 60, width: 100, height: 60 } }];
  assert.equal(findPreviewDrop(targets, 75, 5, { rows: [row] }, false, false, 'tablet').index, 0);
});
