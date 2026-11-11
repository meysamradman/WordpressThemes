import test from 'node:test';
import assert from 'node:assert/strict';

globalThis.window={wp:{element:{createElement:(type,props,...children)=>({type,props:props || {},children}),useState:initial=>[initial,()=>{}],useEffect(){}}}};
const {MenuFields}=await import('../radmanct/src/js/admin/builder-menu-fields.js');
const {MenuPanelFields}=await import('../radmanct/src/js/admin/builder-menu-panel-fields.js');
const {Inspector}=await import('../radmanct/src/js/admin/builder-inspector.js');
const {BuilderIconPicker}=await import('../radmanct/src/js/admin/builder-icon-picker.js');
const labels=new Proxy({},{get:(_,key)=>key});
const nodes=(value)=>Array.isArray(value)?value.flatMap(nodes):!value || typeof value!=='object'?[]:[value,...(value.children || []).flatMap(nodes)];
const config={menus:[{id:11,title:'Primary'},{id:22,title:'Mobile'}],menuSkins:{plain:'Plain',soft:'Soft'},iconChoices:[{id:'menu'}]};

test('menu item gap edits only the selected device and reset preserves explicit zero',()=>{
  const item={type:'menu',menuGap:32,mobileMenuGap:0}; let patch;
  const tree=nodes(MenuFields({item,config,labels:{...labels,menuItemGap:'Menu item gap'},tab:'style',device:'mobile',update:value=>{patch=value;}}));
  const range=tree.find(node=>node.type?.name==='RangeControl' && node.props.label==='Menu item gap');
  assert.equal(range.props.value,0); assert.equal(range.props.fallback,32);
  range.props.onChange(18); assert.deepEqual(patch,{mobileMenuGap:18});
  const reset=tree.find(node=>node.type==='button' && node.props.onClick);
  reset.props.onClick(); assert.deepEqual(patch,{mobileMenuGap:null});
  assert.equal(item.menuGap,32);
});

test('menu fields follow the selected device and changing panel visibility preserves other devices',()=>{
  const item={type:'menu',menuId:11,mobileMenuId:22,drawerDevices:['mobile','desktop'],mobileDisplay:'drawer'};
  let patch;
  const make=(device,tab='content')=>nodes(MenuFields({item,config,labels,device,tab,update:(value)=>{patch=value;}}));
  const desktop=make('desktop');const mobile=make('mobile');const tablet=make('tablet');
  const selects=(list)=>list.filter((node)=>node.type?.name==='SelectControl');
  assert.ok(selects(desktop).some((node)=>node.props.value===11));
  assert.equal(selects(desktop).some((node)=>node.props.value===22 || node.props.value==='drawer'),false);
  assert.ok(selects(mobile).some((node)=>node.props.value===22));
  assert.equal(selects(mobile).some((node)=>node.props.value===11),false);
  assert.equal(tablet.some((node)=>node.type?.name==='MenuPanelFields'),false);
  tablet.find((node)=>node.type?.name==='SwitchControl').props.onChange(true);
  assert.deepEqual(patch.drawerDevices,['mobile','desktop','tablet']);
  desktop.find((node)=>node.type?.name==='SwitchControl').props.onChange(false);
  assert.deepEqual(patch.drawerDevices,['mobile']);
  assert.equal(make('tablet','style').some((node)=>node.type?.name==='MenuPanelFields'),false);
});

test('sidebar-only dimensions and panel typography are shown only where they apply',()=>{
  const fields={panelWidth:{label:'Sidebar width',default:420,min:280,max:960,group:'style',condition:{panelMode:['sidebar']}},panelMode:{default:'sidebar'},panelFontSize:{label:'Link size',default:48,min:16,max:128,group:'style'},panelMobileFontSize:{label:'Mobile link size',default:30,min:16,max:80,group:'style'}};
  const make=(mode,device)=>nodes(MenuPanelFields({item:{panelMode:mode},config:{navigationPanels:fields},labels,group:'style',device,update(){}}));
  const legends=(list)=>list.filter((node)=>node.type==='legend').flatMap((node)=>node.children);
  assert.deepEqual(legends(make('sidebar','desktop')),['Sidebar width','Link size']);
  assert.deepEqual(legends(make('fullscreen','mobile')),['Mobile link size']);
});

test('image style omits text fields and icon-only panel style has no ineffective font-size control',()=>{
  const tabs=(item)=>nodes(Inspector({item,row:{appearance:{}},update(){},labels,config,actions:[]})).find((node)=>node.props.tabs).props.tabs;
  const image=nodes(tabs({type:'image'}).find((tab)=>tab.id==='style').content);
  assert.equal(image.some((node)=>node.type?.name==='AppearanceControls'),false);
  const panel=nodes(tabs({type:'panel',triggerLabel:''}).find((tab)=>tab.id==='style').content).find((node)=>node.type?.name==='AppearanceControls');
  assert.equal(panel.props.showFontSize,false);assert.equal(panel.props.colorLabel,'panelTriggerColor');
  const textPanel=nodes(tabs({type:'panel',triggerLabel:'Open'}).find((tab)=>tab.id==='style').content).find((node)=>node.type?.name==='AppearanceControls');
  assert.equal(textPanel.props.showFontSize,true);
});

test('all builder icon fields use one picker adapter and keep the shared catalog and localized labels',()=>{
  const result=BuilderIconPicker({value:'menu',onChange(){},config,labels});
  assert.equal(result.props.icons,config.iconChoices);
  assert.equal(result.props.labels.search,'searchIcons');
  assert.equal(result.props.labels.close,'closeIconPicker');
});
