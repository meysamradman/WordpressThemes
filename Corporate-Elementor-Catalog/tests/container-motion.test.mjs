import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { runInNewContext } from 'node:vm';
import { motionOptions, motionTargets, revealClip, scrollPosition } from '../radmanct/src/js/components/container-motion-options.js';
import { motionValue, hasNativeMotion } from '../radmanct/src/js/integrations/elementor-motion-values.js';

test('inactive typed Elementor motion never takes ownership away from Radman; valid native motion still does', async () => {
  const cases = JSON.parse(await readFile(new URL('./fixtures/native-motion-values.json',import.meta.url),'utf8'));
  for (const entry of cases) assert.equal(hasNativeMotion({get:(key)=>entry.settings[key]},entry.interactions),entry.expected,JSON.stringify(entry));
});

test('reference easings survive both motion modes and are valid in the pinned GSAP runtime', async () => {
  const easings=JSON.parse(await readFile(new URL('../radmanct/inc/components/motion-easings.json',import.meta.url),'utf8'));
  const {gsap}=await import('gsap');
  for(const ease of easings){
    assert.equal(motionOptions({ease}).ease,ease);
    assert.equal(motionOptions({parallax_ease:ease}).parallax_ease,ease);
    const curve=gsap.parseEase(ease);
    assert.equal(typeof curve,'function',ease);
    assert.ok(Number.isFinite(curve(.5)),ease);
  }
});

test('motion settings reject arbitrary JS and bound transforms, timing and opacity', () => {
  const options = motionOptions({ animate: 'yes', preset: 'custom', duration: 999, from_x: 'alert(1)', from_opacity: -8, to_scaleX: 20, ease: 'function(){}' });
  assert.equal(options.animate, true); assert.equal(options.duration, 10); assert.equal(options.from.x, 0);
  assert.equal(options.from.opacity, 0); assert.equal(options.to.scaleX, 5); assert.equal(options.ease, 'power4.out');
  assert.equal(motionOptions({ preset: 'fade-left' }).from.x, -150);
  assert.equal(motionOptions({ preset: 'fade-right' }).from.x, 150);
  assert.equal(motionOptions({parallax_perspective:600}).parallax_from.transformPerspective,600);
  assert.equal(motionOptions({parallax_perspective:99999}).parallax_from.transformPerspective,2000);
  assert.equal(Object.hasOwn(motionOptions().parallax_from,'transformPerspective'),false);
});

test('Hub custom defaults are neutral, zero duration and full stagger/scale ranges work, and explicit saved states stay intact', () => {
  const initial = motionOptions();
  assert.equal(initial.preset, 'custom');
  assert.equal(initial.from.y, 0); assert.equal(initial.from.opacity, 1);
  assert.equal(initial.parallax_from.y, 0); assert.equal(initial.parallax_to.y, 0);
  const explicit = motionOptions({preset:'custom',from_y:40,from_opacity:0,parallax_from_y:-40,parallax_to_y:40,duration:0,stagger:8,to_scaleX:5});
  assert.equal(explicit.from.y,40); assert.equal(explicit.from.opacity,0);
  assert.equal(explicit.parallax_from.y,-40); assert.equal(explicit.parallax_to.y,40);
  assert.equal(explicit.duration,0); assert.equal(explicit.stagger,8); assert.equal(explicit.to.scaleX,5);
  assert.equal(motionOptions({from_x:900}).from.x,900);
});

test('Hub preset transforms and custom parallax positions resolve without executing input', () => {
  assert.equal(motionOptions({preset:'flip-x'}).from.rotationX,-30);
  assert.equal(motionOptions({preset:'flip-x'}).from.y,150);
  assert.equal(motionOptions({preset:'flip-y'}).from.rotationY,30);
  assert.equal(motionOptions({preset:'flip-y'}).from.x,150);
  assert.equal(motionOptions({preset:'scale-up'}).from.scaleX,.75);
  assert.equal(motionOptions({preset:'scale-down'}).from.scaleY,1.25);
  for (const value of ['top bottom-=25%','50% 50%','bottom+=100px top','+=300','max']) assert.equal(scrollPosition(value,'fallback'),value);
  for (const value of ['()=>window.alert(1)','top bottom; color:red','<script>','top '.repeat(99),null]) assert.equal(scrollPosition(value,'fallback'),'fallback');
  const options=motionOptions({parallax_start:'custom',parallax_start_custom:'top bottom-=25%',parallax_end:'custom',parallax_end_custom:'+=300'});
  assert.equal(options.parallax_start,'top bottom-=25%'); assert.equal(options.parallax_end,'+=300');
  assert.equal(motionOptions({parallax_distance:150,parallax_distance_unit:'%'}).parallax_end,'bottom+=150% top');
  assert.equal(motionOptions({parallax_distance:-90}).parallax_end,'bottom-=90px top');
  assert.equal(motionOptions({parallax_end:'custom',parallax_end_custom:'bottom center',parallax_distance:150}).parallax_end,'bottom center');
});

test('parallax-only Play previews from/to without a scroll trigger while frontend remains scroll driven', async () => {
  const source = (await readFile(new URL('../radmanct/src/js/components/container-motion.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'').replace(/export /g,'');
  const calls=[]; const root={isConnected:true,hasAttribute:()=>false};
  const context={motionOptions,motionTargets:()=>[],ScrollTrigger:{},gsap:{registerPlugin(){},fromTo:(...args)=>calls.push(args),matchMedia:()=>({add:(_,fn)=>fn(),revert(){}})}};
  runInNewContext(`${source}\nthis.initialize=initializeContainerMotion;`,context);
  context.initialize(root,{parallax:true,duration:2},true,true);
  assert.equal(calls[0][2].scrollTrigger,undefined); assert.equal(calls[0][2].duration,2);
  context.initialize(root,{parallax:true},false,true);
  assert.equal(calls[1][2].scrollTrigger.scrub,1);
});

test('PHP and JavaScript consume the same motion value compatibility cases', async () => {
  const cases = JSON.parse(await readFile(new URL('./fixtures/motion-values.json', import.meta.url), 'utf8'));
  for (const { input, expected } of cases) {
    const actual = motionOptions(input);
    for (const [key, value] of Object.entries(expected)) assert.equal(actual[key], value, JSON.stringify(input));
  }
});

test('transform origins preserve inherited designs and validate independent mixed-unit states', () => {
  assert.equal(Object.hasOwn(motionOptions({preset:'custom'}).from,'transformOrigin'),false);
  const options=motionOptions({preset:'custom',from_origin:true,from_originX:0,from_originX_unit:'%',from_originY:-35.5,from_originY_unit:'px',from_originZ:-200,to_origin:true,to_originX:100,to_originY:0,parallax_to_origin:true,parallax_to_originX:50,parallax_to_originY:75});
  assert.equal(options.from.transformOrigin,'0% -35.5px -200px');
  assert.equal(options.to.transformOrigin,'100% 0% 0px');
  assert.equal(options.parallax_to.transformOrigin,'50% 75% 0px');
  const translated=motionOptions({preset:'custom',from_x:25,from_x_unit:'vw',to_y:-15.5,to_y_unit:'%',parallax_from_y:30,parallax_from_y_unit:'vh'});
  assert.equal(translated.from.x,'25vw'); assert.equal(translated.to.y,'-15.5%'); assert.equal(translated.parallax_from.y,'30vh');
  assert.equal(motionOptions({preset:'custom',from_x:500,from_x_unit:'vw'}).from.x,'100vw');
  assert.equal(motionOptions({preset:'custom',from_x:20,from_x_unit:'px;bad()'}).from.x,20);
  const unsafe=motionOptions({preset:'custom',from_origin:true,from_originX:9999,from_originX_unit:'px;display:none',from_originY:-9999,from_originY_unit:'px',from_originZ:'bad()'});
  assert.equal(unsafe.from.transformOrigin,'100% -500px 0px');
  assert.equal(Object.hasOwn(motionOptions({preset:'fade-up',from_origin:true}).from,'transformOrigin'),false);
});

test('classic boxed and Atomic containers target logical children and isolate nested owners', () => {
  const root = {}; const other = {};
  const item = (owner, container = false) => ({ matches: (selector) => selector.includes('data-element_type') ? container : false, closest: () => owner, contains: () => false, querySelector: () => null });
  const first = item(root); const second = item(root, true); const nested = item(other);
  const wrapper = { matches: (selector) => selector.includes('.e-con-inner'), children: [first, second, nested] };
  Object.assign(root, { children: [wrapper], querySelectorAll: () => [first, second, nested] });
  assert.deepEqual(motionTargets(root, { inner: false, targets: 'contents' }), [first, second]);
  assert.deepEqual(motionTargets(root, { targets: 'containers' }), [second]);
  assert.deepEqual(motionTargets(root, { inner: true, targets: 'contents' }), [first]);
});

test('GSAP instances revert on replay, preserve reading order and keep parallax separate', async () => {
  const source = (await readFile(new URL('../radmanct/src/js/components/container-motion.js', import.meta.url), 'utf8')).replace(/^import .*;\r?\n/gm, '').replace(/export /g, '');
  const calls = []; let reverted = 0; let reduced = false;
  const root = { isConnected: true, hasAttribute: () => false }; const child = {};
  const context = { motionOptions, revealClip, motionTargets: () => [child], ScrollTrigger: {}, gsap: {
    registerPlugin() {}, fromTo: (...args) => calls.push(args),
    matchMedia: () => ({ add: (_query, fn) => { if (!reduced) fn(); }, revert: () => { ++reverted; } }),
  } };
  runInNewContext(`${source}\nthis.initialize = initializeContainerMotion;`, context);
  const cleanup = context.initialize(root, { animate: true, parallax: true, order: 'end',preset:'custom',from_origin:true,from_originX:0,parallax_from_origin:true,parallax_from_originY:100 }, true);
  assert.equal(calls.length, 2); assert.equal(calls[0][2].stagger.from, 'end');
  assert.equal(calls[0][2].scrollTrigger, undefined); assert.equal(calls[1][0], root);
  assert.equal(calls[1][2].scrollTrigger.scrub, 1);
  assert.equal(calls[1][2].scrollTrigger.invalidateOnRefresh,true);
  assert.equal(calls[0][1].transformOrigin,'0% 50% 0px');
  assert.equal(calls[1][1].transformOrigin,'50% 100% 0px');
  context.initialize(root, { animate: true }); assert.equal(reverted, 1);
  cleanup(); reduced = true; const count = calls.length;
  context.initialize(root, { animate: true }); assert.equal(calls.length, count);
});

test('Atomic live settings unwrap typed values and detach after replacement or unmount', async () => {
  const frames = new Map(); let nextFrame = 0; let observer; let removed = false;
  globalThis.window = { addEventListener() {}, removeEventListener() {} };
  globalThis.requestAnimationFrame = (fn) => { frames.set(++nextFrame, fn); return nextFrame; };
  globalThis.cancelAnimationFrame = (id) => frames.delete(id);
  globalThis.MutationObserver = class { constructor(callback) { observer = callback; } observe() {} disconnect() {} };
  const { connectMotionPreview } = await import('../radmanct/src/js/integrations/elementor-motion-preview.js');
  const attributes = new Map([['data-id', 'atomic']]);
  const root = { isConnected: true, matches: () => true, getAttribute: (key) => attributes.get(key), setAttribute: (key, value) => attributes.set(key, value), removeAttribute: (key) => attributes.delete(key) };
  const values = { rm_motion_animate: { $$type: 'boolean', value: true }, rm_motion_duration: { $$type: 'number', value: 2.4 }, rm_motion_from_x:30, rm_motion_from_x_unit:'vw', rm_motion_from_x_slider:{size:0,unit:'px'} };
  const callbacks = new Set();
  const settings = { get: (key) => values[key], on: (_, fn) => callbacks.add(fn), off: (_, fn) => callbacks.delete(fn) };
  const events = []; const channel = new Map(); const clicks = new Map();
  globalThis.CustomEvent ??= class { constructor(type, options = {}) { this.type = type; this.detail = options.detail ?? null; } };
  const button = { addEventListener: (key,fn) => clicks.set(key,fn), removeEventListener: (key) => clicks.delete(key), remove: () => { removed = true; } };
  const document = { dispatchEvent: (event) => events.push(event), body: { append() {} }, createElement: () => button, querySelector: () => attributes.has('data-rdct-motion') ? root : null, querySelectorAll: () => root.isConnected ? [root] : [] };
  const cleanup = connectMotionPreview(document, { channels:{editor:{on:(key,fn) => channel.set(key,fn),off:(key) => channel.delete(key)}}, getContainer: (id) => id === 'atomic' ? { settings } : null }, { play: 'Play', fields: ['animate', 'duration','from_x','from_x_unit'], types: { animate: 'boolean', duration: 'number',from_x:'number',from_x_unit:'string' },sliders:['duration','from_x'],units:{from_x_unit:{source:'from_x',values:['px','%','vw','vh']}} });
  assert.equal(JSON.parse(attributes.get('data-rdct-motion')).duration, 2.4);
  assert.equal(JSON.parse(attributes.get('data-rdct-motion')).from_x,0);
  assert.equal(JSON.parse(attributes.get('data-rdct-motion')).from_x_unit,'px');
  values.rm_motion_from_x_slider.size=''; for(const fn of callbacks)fn();
  assert.equal(JSON.parse(attributes.get('data-rdct-motion')).from_x,30);
  assert.equal(JSON.parse(attributes.get('data-rdct-motion')).from_x_unit,'vw');
  assert.equal(clicks.size, 0); assert.equal(callbacks.size, 1);
  const selected = channel.get('rm_motion_play');
  selected({id:'atomic'}); assert.equal(events.at(-1).detail.root,root);
  selected({id:'control-view',container:{id:'atomic'}}); assert.equal(events.length,2);
  selected(); selected({id:'missing'}); selected({id:'[data-id]'}); selected({get container(){throw new Error('Detached control');}});
  assert.equal(events.length,2);
  values.rm_motion_animate = { $$type: 'boolean', value: true, disabled: true }; for (const fn of callbacks) fn();
  assert.equal(attributes.has('data-rdct-motion'), false);
  selected({id:'atomic'}); assert.equal(events.length,2);
  values.rm_motion_animate = { $$type: 'boolean', value: false }; for (const fn of callbacks) fn();
  assert.equal(attributes.has('data-rdct-motion'), false);
  values.rm_motion_animate = { $$type: 'boolean', value: true }; for (const fn of callbacks) fn();
  assert.equal(attributes.has('data-rdct-motion'), true);
  attributes.set('data-id', 'missing'); observer(); for (const fn of frames.values()) fn(); frames.clear();
  assert.equal(callbacks.size, 0);
  assert.equal(attributes.has('data-rdct-motion'), false);
  attributes.set('data-id', 'atomic'); observer(); for (const fn of frames.values()) fn(); frames.clear();
  assert.equal(callbacks.size, 1);
  root.isConnected = false; observer(); for (const fn of frames.values()) fn(); frames.clear();
  assert.equal(callbacks.size, 0);
  cleanup(); assert.equal(removed, false); assert.equal(callbacks.size, 0); assert.equal(channel.size,0);
  selected({id:'atomic'}); assert.equal(events.length,2);
});

test('Atomic panel Play sends the active element ID without changing motion settings', async () => {
  const {registerMotionControls} = await import('../radmanct/src/js/admin/elementor-motion-controls.js');
  let component; const sent = []; const settings = {rm_motion_animate:{$$type:'boolean',value:true}};
  globalThis.window.elementor = {channels:{editor:{trigger:(...args) => sent.push(args)}}};
  const panel = {SettingsField:'SettingsField',useElement:() => ({element:{id:'selected'},settings}),controlsRegistry:{get:() => null,register:(_,next) => {component = next;}}};
  const ui = {Popover:'Popover',Stack:'Stack',Typography:'Typography',Button:'Button',Divider:'Divider'};
  const react = {createElement:(type,props,...children) => ({type,props:{...props,children}}),useState:() => [null,() => {}]};
  const config = {fields:Object.fromEntries(['animate','parallax','preset','targets','inner'].map((key) => [key,{default:false}])),groups:{},labels:{play:'Play'}};
  registerMotionControls(panel,{SwitchControl:'Switch'},ui,react,{},config);
  const tree = component({group:'motion',layout:true});
  const nodes = (node) => Array.isArray(node) ? node.flatMap(nodes) : node?.props ? [node,...nodes(node.props.children)] : [];
  assert.ok(nodes(tree).some((node)=>node.props.name==='targets'));
  assert.deepEqual(nodes(tree).filter((node)=>['parallax','animate'].includes(node.props.name)).map((node)=>node.props.name),['parallax','animate']);
  nodes(tree).find((node) => node.type === 'Button').props.onClick();
  assert.deepEqual(sent,[['rm_motion_play',{id:'selected'}]]);
  assert.equal(settings.rm_motion_animate.value,true);
  settings.rm_motion_reveal = {$$type:'boolean',value:true};
  settings.rm_motion_horizontal = {$$type:'boolean',value:false};
  nodes(component({group:'scroll',layout:true})).find((node) => node.type === 'Button').props.onClick();
  assert.deepEqual(sent.at(-1),['rm_motion_play',{id:'selected'}]);
  settings.rm_motion_animate.value=false; settings.rm_motion_parallax={$$type:'boolean',value:true};
  const parallaxButtons=nodes(component({group:'motion',layout:true})).filter((node)=>node.type==='Button');
  assert.equal(parallaxButtons.length,2);
  parallaxButtons[0].props.onClick(); parallaxButtons[1].props.onClick();
  assert.deepEqual(sent.at(-2),['rm_motion_play',{id:'selected'}]);
  assert.deepEqual(sent.at(-1),['rm_motion_reset',{id:'selected'}]);
  assert.equal(settings.rm_motion_parallax.value,true);
});

test('Atomic envelopes respect disabled values and schema types before shared normalization', () => {
  assert.equal(motionValue('yes', 'boolean'), 'yes');
  assert.equal(motionValue({ $$type: 'boolean', value: false }, 'boolean'), false);
  assert.equal(motionValue({ $$type: 'number', value: 0 }, 'number'), 0);
  assert.equal(motionValue({ $$type: 'number', value: 8, disabled: true }, 'number'), undefined);
  assert.equal(motionValue({ $$type: 'string', value: true }, 'boolean'), undefined);
  assert.equal(motionValue({ $$type: 'boolean', value: true, disabled: false }, 'boolean'), true);
  const values = motionOptions({ animate: motionValue({ $$type: 'boolean', value: true, disabled: true }, 'boolean'), duration: motionValue({ $$type: 'string', value: 9 }, 'number'), inner: motionValue({ $$type: 'boolean', value: false, disabled: true }, 'boolean') });
  assert.equal(values.animate, false); assert.equal(values.duration, 1.6); assert.equal(values.inner, true);
});

test('Atomic origin controls use native fields, conditional rows and bounds for the chosen unit', async () => {
  const {registerMotionControls}=await import('../radmanct/src/js/admin/elementor-motion-controls.js');
  let component;
  let resets=0;
  const settings={rm_motion_animate:{$$type:'boolean',value:true},rm_motion_preset:{$$type:'string',value:'custom'},rm_motion_from_origin:{$$type:'boolean',value:false},rm_motion_from_originX_unit:{$$type:'string',value:'%'}};
  const fields={animate:{default:false},parallax:{default:false},preset:{default:'custom'},targets:{default:'self'},inner:{default:true},from_origin:{type:'boolean',default:false,group:'from',label:'Origin'},from_originX:{default:50,min:-500,max:500,unit_key:'from_originX_unit',group:'from',condition:{from_origin:true},label:'X'},from_originX_unit:{default:'%',options:{'%':'%',px:'px'},group:'from',condition:{from_origin:true},label:'Unit'}};
  const h=(type,props,...children)=>({type,props:{...props,children}});
  const nodes=(node)=>Array.isArray(node)?node.flatMap(nodes):node?.props?[node,...nodes(node.props.children)]:[];
  const panel={SettingsField:'SettingsField',useElement:()=>({element:{id:'test'},settings}),controlsRegistry:{get:()=>null,register:(_,next)=>{component=next;}}};
  const controls={SwitchControl:'Switch',ControlFormLabel:'Label',SelectControl:'NativeSelect',NumberControl:'NativeNumber',useBoundProp:()=>({value:50,disabled:false,resetValue:()=>++resets})};
  const ui=Object.fromEntries(['Popover','Stack','Typography','Button','IconButton','Box','Slider','Divider'].map(name=>[name,name]));
  registerMotionControls(panel,controls,ui,{createElement:h,useState:()=>[null,()=>{}]},{numberPropTypeUtil:{}},{fields,groups:{from:'From'},labels:{reset:'Reset'}});
  const popup=nodes(component({group:'motion',layout:false})).find(node=>node.props.group==='from');
  assert.equal(nodes(popup.type(popup.props)).some(node=>node.props.name==='from_originX'),false);
  settings.rm_motion_from_origin.value=true;
  const axis=nodes(popup.type(popup.props)).find(node=>node.props.name==='from_originX');
  const reset=nodes(axis.type(axis.props)).find(node=>node.type?.name==='Reset');
  reset.type(reset.props).props.onClick(); assert.equal(resets,1);
  const range=nodes(axis.type(axis.props)).find(node=>node.type?.name==='NumericRange');
  let slider=nodes(range.type(range.props)).find(node=>node.type==='Slider');
  assert.equal(slider.props.max,100); assert.equal(slider.props.step,.1);
  settings.rm_motion_from_originX_unit.value='px';
  slider=nodes(range.type(range.props)).find(node=>node.type==='Slider');
  assert.equal(slider.props.max,500); assert.equal(slider.props.step,1);
});


test('individual motion targets itself; native owners and nested target hierarchies remain isolated', () => {
  const root = {hasAttribute:() => false};
  assert.deepEqual(motionTargets(root,{targets:'self'}),[root]);
  root.hasAttribute = () => true;
  assert.deepEqual(motionTargets(root,{targets:'self'}),[]);
  const first = {matches:() => false,closest:() => root,contains:() => false,querySelector:() => null};
  const nested = {...first};
  const parent = {...first}; nested.parentElement = parent;
  const containsIndependent = {...first,querySelector:() => ({})};
  Object.assign(root,{children:[],querySelectorAll:() => [first,parent,nested,containsIndependent]});
  assert.deepEqual(motionTargets(root,{inner:true,targets:'contents'}),[first,parent]);
});

test('native entrance, Pro motion and Atomic interactions own their node without changing saved options', () => {
  for (const [key,value] of [['_animation','fadeIn'],['animation_mobile','bounce'],['motion_fx_motion_fx_mouse','yes']]) {
    assert.equal(hasNativeMotion({get:(name) => name === key ? value : undefined}),true);
  }
  assert.equal(hasNativeMotion({get:() => 'none'}),false);
  assert.equal(hasNativeMotion({get:() => undefined},JSON.stringify({items:[{id:'native'}]})),true);
  assert.equal(hasNativeMotion({get:() => undefined},{disabled:true,items:[{}]}),false);
  assert.equal(hasNativeMotion({get:() => undefined},'invalid'),false);
});

test('self animation and parallax never create competing transform tweens on the same node', async () => {
  const source = (await readFile(new URL('../radmanct/src/js/components/container-motion.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'').replace(/export /g,'');
  const calls = [];
  const root = {isConnected:true,hasAttribute:() => false};
  const context = {motionOptions,motionTargets:() => [root],ScrollTrigger:{},gsap:{registerPlugin(){},fromTo:(...args) => calls.push(args),matchMedia:() => ({add:(_,fn) => fn(),revert(){}})}};
  runInNewContext(`${source}\nthis.initialize = initializeContainerMotion;`,context);
  context.initialize(root,{animate:true,parallax:true,targets:'self'});
  assert.equal(calls.length,1);
  assert.equal(calls[0][2].scrollTrigger.scrub,1);
  root.hasAttribute = () => true;
  context.initialize(root,{animate:true,targets:'self'});
  assert.equal(calls.length,1);
});
