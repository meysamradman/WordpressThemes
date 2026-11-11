import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import {runInNewContext} from 'node:vm';

const source=(await readFile(new URL('../radmanct-components/src/js/components/button.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'').replace(/import\(/g,'importModule(').replace(/export function /g,'function ');
const tick=()=>new Promise(setImmediate);
function fixture(dataset,matches=true){
  let loads=0;let created=0;let destroyed=0;let finish;
  const vendor={gsap:{}};
  const context={loadGsap:()=>{loads++;return new Promise(resolve=>{finish=()=>resolve(vendor);});},reportRuntimeError:(_,error)=>{throw error;},importModule:async()=>({initializeLiquid:mount,initializeMagnetic:mount})};
  function mount(root,engine){assert.equal(engine,vendor);created++;return()=>destroyed++;}
  runInNewContext(source,context);
  const media={matches,addEventListener(){},removeEventListener(){}};
  const root={dataset,isConnected:true,ownerDocument:{defaultView:{matchMedia:()=>media}}};
  const release=context.initializeButton(root);
  return {release,finish:()=>finish?.(),counts:()=>[loads,created,destroyed]};
}

test('plain buttons and reduced motion do not request an animation engine',()=>{
  assert.deepEqual(fixture({}).counts(),[0,0,0]);
  assert.deepEqual(fixture({textEffect:'liquid-fill'},false).counts(),[0,0,0]);
});

test('liquid and magnetic effects consume the same shared vendor and clean up',async()=>{
  const page=fixture({textEffect:'liquid-fill',magnetic:'true'});
  page.finish();await tick();assert.deepEqual(page.counts(),[1,2,0]);
  page.release();assert.deepEqual(page.counts(),[1,2,2]);
});

test('late shared vendor cannot mount effects after cleanup',async()=>{
  const page=fixture({textEffect:'liquid-fill',magnetic:'true'});
  page.release();page.finish();await tick();assert.deepEqual(page.counts(),[1,0,0]);
});

test('button effect static graphs do not bypass the shared GSAP vendor',async()=>{
  const manifest=JSON.parse(await readFile(new URL('../radmanct-components/dist/manifest.json',import.meta.url),'utf8'));
  for(const entry of ['src/js/components/button.js','src/js/components/button/liquid.js','src/js/components/button/magnetic.js','src/js/components/button/scroll.js']){
    const visited=new Set();
    function visit(key){if(visited.has(key))return;visited.add(key);assert.ok(manifest[key],key);for(const dependency of manifest[key].imports||[])visit(dependency);}
    visit(entry);
    for(const key of visited){
      const code=await readFile(new URL(`../radmanct-components/dist/${manifest[key].file}`,import.meta.url),'utf8');
      assert.doesNotMatch(code,/GreenSock|GSAP [0-9]|gsap-core/,`${entry}: ${key}`);
    }
  }
});

