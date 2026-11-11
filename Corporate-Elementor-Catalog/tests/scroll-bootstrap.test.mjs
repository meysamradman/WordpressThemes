import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import {runInNewContext} from 'node:vm';
import {createPageLifecycle} from '../shared/frontend/lifecycle.js';

const source=(await readFile(new URL('../radmanct/src/js/frontend.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'').replaceAll('import(', 'importModule(');
const flush=()=>new Promise(setImmediate);
function fixture(eligible){
  const query=new EventTarget();query.matches=eligible;
  const calls=[];let resolveSmooth;
  const smooth=new Promise(resolve=>{resolveSmooth=()=>resolve({initializeSmoothScroll(){calls.push('smooth initialized');return ()=>calls.push('smooth disposed');}});});
  const document={body:{classList:{contains:key=>key==='rdct-smooth-scroll-enabled'}},getElementById:key=>key==='rdct-scroll-wrapper'?{}:null,
    querySelector:key=>key==='[data-rdct-motion]'?{}:null,querySelectorAll:key=>key==='[data-rdct-motion]'?[{dataset:{rdctMotion:'{"animate":true}'}}]:[]};
  const window=new EventTarget();window.document=document;
  const load=path=>{calls.push(path);return path==='./smooth-scroll.js'?smooth:Promise.resolve({observeContainerMotion(){calls.push('motion initialized');}});};
  runInNewContext(source,{window,document,createPageLifecycle,importModule:load,matchMedia:key=>key==='(prefers-reduced-motion: reduce)'?{matches:false}:query});
  return {calls,query,window,resolveSmooth};
}

test('container motion waits for the selected smooth scroller to initialize',async()=>{
  const page=fixture(true);await flush();
  assert.deepEqual(page.calls,['./smooth-scroll.js']);
  page.resolveSmooth();await flush();
  assert.deepEqual(page.calls,['./smooth-scroll.js','smooth initialized','./container-motion-manager.js','motion initialized']);
  page.window.dispatchEvent(new Event('pagehide'));
  assert.equal(page.calls.at(-1),'smooth disposed');
});

test('a page initially outside the smooth breakpoint loads it once when eligible and cleans up its preference listener',async()=>{
  const page=fixture(false);await flush();
  assert.deepEqual(page.calls,['./container-motion-manager.js','motion initialized']);
  page.query.matches=true;page.query.dispatchEvent(new Event('change'));await flush();
  page.query.dispatchEvent(new Event('change'));await flush();
  assert.equal(page.calls.filter(call=>call==='./smooth-scroll.js').length,1);
  page.resolveSmooth();await flush();page.window.dispatchEvent(new Event('pagehide'));
  const count=page.calls.length;page.query.dispatchEvent(new Event('change'));await flush();assert.equal(page.calls.length,count);
});


test('smooth scrolling aligns shell movement to whole pixels and releases its runtime',async()=>{
  const script=(await readFile(new URL('../radmanct/src/js/smooth-scroll.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'').replace('export function initializeSmoothScroll','function initializeSmoothScroll');
  const wrapper={dataset:{smoothDuration:'1.6'},style:{}};
  const content={style:{},querySelector:()=>null};
  const document=new EventTarget(); document.getElementById=id=>id==='rdct-scroll-wrapper'?wrapper:id==='rdct-scroll-content'?content:id==='rdct-scroll-progress'?{}:id==='wpadminbar'?{getBoundingClientRect:()=>({height:32})}:null;
  const window=new EventTarget(); let config,release,killed=0,reverted=0;
  const gsap={registerPlugin(){},killTweensOf(){},matchMedia:()=>({add(query,callback){assert.match(query,/prefers-reduced-motion: no-preference/);release=callback();},revert(){reverted++;release();}})};
  const ScrollTrigger={addEventListener(){},removeEventListener(){}};
  const ScrollSmoother={get:()=>null,create(options){config=options;return{kill(){killed++;}};}};
  const updates=[];document.addEventListener('rdct:smooth-update',event=>updates.push(event.detail.scrollTop));
  const CustomEvent=class extends Event{constructor(type,options){super(type);this.detail=options.detail;}};
  const context={document,window,gsap,ScrollTrigger,ScrollSmoother,getComputedStyle:()=>({paddingBottom:'0px'}),Event,CustomEvent};
  runInNewContext(script+';globalThis.dispose=initializeSmoothScroll();',context);
  assert.equal(config.wholePixels,true);assert.equal(config.smooth,1.6);assert.equal(config.effects,false);assert.equal(config.smoothTouch,0);
  assert.equal(wrapper.style.top,'32px');assert.equal(content.style.paddingBottom,'calc(0px + 32px)');
  config.onUpdate({scrollTop:()=>125});assert.equal(updates.at(-1),125);
  context.dispose();assert.equal(killed,1);assert.equal(reverted,1);assert.equal(updates.at(-1),null);
});
