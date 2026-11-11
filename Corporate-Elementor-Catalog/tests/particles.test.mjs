import {test} from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import {createParticleEngine} from '../radmanct-components/src/js/vendor/particles.js';
import {initializeParticles} from '../radmanct-components/src/js/components/particles.js';

class Target {
  events = new Map();
  addEventListener(type, callback) { if (!this.events.has(type)) this.events.set(type,new Set()); this.events.get(type).add(callback); }
  removeEventListener(type, callback) { this.events.get(type)?.delete(callback); }
  emit(type, event = {}) { for (const callback of [...(this.events.get(type) || [])]) callback(event); }
  get listeners() { return [...this.events.values()].reduce((sum,list) => sum + list.size,0); }
}
function node() {
  const target = new Target(), classes = new Set();
  return Object.assign(target,{style:{}, classList:{contains:(key) => classes.has(key),add:(...keys) => keys.forEach((key) => classes.add(key)),remove:(...keys) => keys.forEach((key) => classes.delete(key))}, children:[], offsetWidth:500, offsetHeight:300, scrollLeft:0, scrollTop:0,
    appendChild(child) { child.parentElement?.removeChild(child); this.children.push(child); child.parentElement = this; return child; },
    removeChild(child) { this.children = this.children.filter((item) => item !== child); child.parentElement = null; },
    remove() { this.parentElement?.removeChild(this); },
    getBoundingClientRect:() => ({left:20,top:10,width:500,height:300}),
    closest:() => null, matches:() => false, removeAttribute() {},
  });
}
function environment() {
  const frames = new Map(), timers = new Map(), coordinates = []; let id = 0;
  const win = Object.assign(new Target(),{devicePixelRatio:4,requestAnimationFrame:(callback) => { frames.set(++id,callback); return id; },cancelAnimationFrame:(key) => frames.delete(key),setTimeout:(callback) => { timers.set(++id,callback); return id; },clearTimeout:(key) => timers.delete(key)});
  const document = Object.assign(new Target(),{defaultView:win, hidden:false, createElement() { const element = node(); element.getContext = () => new Proxy({arc:(x,y) => coordinates.push([x,y])}, {get:(target,key) => target[key] ?? (() => {}),set:(target,key,value) => { target[key] = value; return true; }}); return element; }});
  const host = node(); host.ownerDocument = document;
  return {win,document,host,frames,timers,coordinates};
}
const config = (extra = {}) => ({particles:{number:{value:5,density:{enable:false}},line_linked:{enable:false},move:{enable:true},...extra},interactivity:{events:{onhover:{enable:true,mode:'grab'},onclick:{enable:true,mode:'push'},resize:true},modes:{push:{particles_nb:500}}},retina_detect:true});

test('local upstream engine caps push, retina and cleans only its own canvas/listeners/RAF', () => {
  const env = environment(), secondHost = node(); secondHost.ownerDocument = env.document;
  const first = createParticleEngine(env.host,config());
  const second = createParticleEngine(secondHost,config());
  assert.equal(first.count,5); assert.equal(env.host.children[0].width,1000);
  env.host.emit('mousemove',{clientX:40,clientY:30}); env.host.emit('click');
  assert.equal(first.count,200); assert.equal(second.count,5);
  first.resize(); first.destroy(); first.destroy();
  assert.equal(env.host.children.length,0); assert.equal(env.host.listeners,0);
  assert.ok(env.frames.size); assert.ok(env.win.listeners);
  second.destroy();
  assert.equal(env.frames.size,0); assert.equal(env.win.listeners,0);
  assert.equal(secondHost.listeners,0);
  assert.equal(Object.hasOwn(Object,'deepExtend'),false);
});

test('opacity and size animation continue when movement is off; click timers are cancelled', () => {
  const env = environment();
  const opts = config({move:{enable:false},opacity:{anim:{enable:true}}});
  opts.interactivity.events.onclick.mode = 'repulse';
  const engine = createParticleEngine(env.host,opts);
  assert.ok(env.frames.size);
  env.host.emit('click'); assert.ok(env.timers.size);
  engine.destroy(); assert.equal(env.timers.size,0); assert.equal(env.frames.size,0);
});

test('click without prior mousemove creates finite particles at local click coordinates', () => {
  const env = environment(), opts = config();
  opts.interactivity.modes.push.particles_nb = 1;
  const engine = createParticleEngine(env.host,opts);
  env.host.emit('click',{clientX:120,clientY:80});
  const callbacks = [...env.frames.values()]; env.frames.clear(); callbacks.forEach((callback) => callback());
  assert.equal(engine.count,6);
  assert.ok(env.coordinates.every(([x,y]) => Number.isFinite(x) && Number.isFinite(y)));
  assert.ok(env.coordinates.some(([x,y]) => Math.abs(x - 200) < 10 && Math.abs(y - 140) < 10));
  engine.destroy();
});

test('density and overlap remain bounded on crowded canvases', () => {
  const env = environment();
  const engine = createParticleEngine(env.host,config({number:{value:200,density:{enable:true,value_area:1}},size:{value:200},move:{enable:true,bounce:true}}));
  assert.ok(engine.count <= 200); engine.destroy();
});

function runtime() {
  const env = environment(), root = node(), layer = node(), media = Object.assign(new Target(),{matches:false});
  root.dataset = {options:JSON.stringify(config()),background:'0',hover:'0'};
  root.isConnected = true; root.querySelector = () => layer;
  root.appendChild(layer); env.host.appendChild(root);
  const previous = Object.fromEntries(['document','matchMedia','getComputedStyle','IntersectionObserver','ResizeObserver'].map((key) => [key,globalThis[key]]));
  let intersection;
  Object.assign(globalThis,{document:env.document,matchMedia:() => media,getComputedStyle:() => ({position:'static'}),IntersectionObserver:class {constructor(callback) { intersection = callback; } observe() {} disconnect() {}},ResizeObserver:undefined});
  return {...env,root,layer,media,visible:(value) => intersection([{isIntersecting:value}]),restore:() => Object.assign(globalThis,previous)};
}
const settle = () => new Promise((resolve) => setImmediate(resolve));
test('runtime defers imports offscreen, handles late import, hidden page and reduced motion', async () => {
  const env = runtime(); let loads = 0, starts = 0, stops = 0, resolveLoad;
  try {
    const cleanup = initializeParticles(env.root,() => { loads++; return new Promise((resolve) => { resolveLoad = resolve; }); });
    assert.equal(env.host.classList.contains('rdct-pt-host'),false);
    assert.equal(loads,0); env.visible(true); assert.equal(loads,1);
    env.document.hidden = true;
    resolveLoad({createParticleEngine:() => { starts++; return {destroy() { stops++; }}; }});
    await settle(); assert.equal(starts,0);
    env.document.hidden = false; env.document.emit('visibilitychange'); assert.equal(starts,1);
    env.media.matches = true; env.media.emit('change'); assert.equal(stops,1);
    env.media.matches = false; env.media.emit('change'); assert.equal(starts,2);
    env.visible(false); assert.equal(stops,2);
    cleanup(); cleanup(); assert.equal(env.document.listeners,0); assert.equal(env.media.listeners,0);
  } finally { env.restore(); }
});
test('destroy during import never starts a canvas and duplicate consumers share one instance', async () => {
  const env = runtime(); let starts = 0, loads = 0, resolveLoad;
  try {
    const load = () => { loads++; return new Promise((resolve) => { resolveLoad = resolve; }); };
    const first = initializeParticles(env.root,load), second = initializeParticles(env.root,load);
    env.visible(true); assert.equal(loads,1);
    first(); assert.ok(env.document.listeners); second();
    resolveLoad({createParticleEngine:() => { starts++; }}); await settle();
    assert.equal(starts,0); assert.equal(env.document.listeners,0);
    assert.equal(env.host.classList.contains('rdct-pt-host'),false);
  } finally { env.restore(); }
});
test('background hover follows parent focus, leaves content in place and restores the layer on cleanup', async () => {
  const env = runtime(); let focused = false, starts = 0, stops = 0;
  env.root.dataset.background = '1'; env.root.dataset.hover = '1';
  env.host.matches = () => focused;
  try {
    const cleanup = initializeParticles(env.root,async () => ({createParticleEngine:() => { starts++; return {destroy() { stops++; }}; }}));
    assert.equal(env.root.parentElement,env.host); assert.equal(env.layer.parentElement,env.host);
    env.visible(true); assert.equal(starts,0);
    focused = true; env.host.emit('focusin'); await settle(); assert.equal(starts,1);
    focused = false; env.host.emit('focusout'); await settle(); assert.equal(stops,1);
    cleanup(); assert.equal(env.layer.parentElement,env.root); assert.equal(env.host.listeners,0);
    assert.equal(env.host.classList.contains('rdct-pt-host'),false);
  } finally { env.restore(); }
});
test('Hub fields have explicit schema/adapter coverage and licensed engine has no global loader', async () => {
  const read = (path) => readFile(new URL(path,import.meta.url),'utf8');
  const [hub,schema,classic,atomic,engine] = await Promise.all([
    read('../themes/hub-elementor-addons/elementor/widgets/particles.php'),read('../radmanct-components/inc/components/particles.php'),read('../radmanct-components/inc/integrations/elementor/widgets/particles.php'),read('../radmanct-components/inc/integrations/elementor/atomic-components/particles.php'),read('../radmanct-components/src/js/vendor/particles.js')]);
  for (const [,key] of hub.matchAll(/\$this->add_control\(\s*'([^']+)'/g)) assert.ok((schema+classic+atomic).includes(`'${key}'`),`Missing Hub field ${key}`);
  assert.doesNotMatch(engine,/window\.particlesJS|window\.pJSDom|Object\.deepExtend\s*=/);
  assert.match(await read('../radmanct-components/licenses/particles.txt'),/MIT License/);
});

test('particle engine and stylesheet stay outside unrelated frontend and animation static graphs', async () => {
  const base = new URL('../radmanct-components/dist/',import.meta.url);
  const manifest = JSON.parse(await readFile(new URL('manifest.json',base),'utf8'));
  const walk = async (key, seen = new Set()) => {
    if (seen.has(key)) return ''; seen.add(key);
    const asset = manifest[key]; assert.ok(asset,key);
    return await readFile(new URL(asset.file,base),'utf8') + (await Promise.all((asset.imports || []).map((entry) => walk(entry,seen)))).join('');
  };
  for (const key of ['src/js/frontend.js','src/js/animation-vendor.js','src/js/components/carousel-entry.js','src/js/integrations/elementor-components.js']) assert.doesNotMatch(await walk(key),/retina_detect|opacity_min|particles-js-canvas-el/);
  assert.ok(manifest['src/css/components/particles.css']);
  assert.ok(manifest['src/js/vendor/particles.js']);
});
