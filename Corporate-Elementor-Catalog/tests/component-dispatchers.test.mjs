import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';

const source = (await readFile(new URL('../radmanct-components/src/js/components/image.js',import.meta.url),'utf8')).replace(/^import .*;$/gm,'').replace(/export /g,'').replace(/\bimport\(/g,'load(');
const flush = () => new Promise(setImmediate);

function fixture({reveal='',tilt=0,classes=[],lightbox=false}={}) {
  const loads=[]; const listeners=new Set(); let mounts=0; let releases=0;
  const root={isConnected:true,dataset:{reveal,tilt},ownerDocument:{defaultView:{}},classList:{contains:(name)=>classes.includes(name)},querySelector:()=>lightbox?{}:null,addEventListener:(event)=>listeners.add(event),removeEventListener:(event)=>listeners.delete(event)};
  const initialize=()=>{++mounts;return ()=>++releases;};
  const module={initializeImageVisibility:initialize,initializeImageTilt:initialize,createImageReveal:()=>({})};
  const context=vm.createContext({load:(path)=>{loads.push(path);return Promise.resolve(module);},reportRuntimeError:(_scope,error)=>{throw error;}});
  vm.runInContext(source,context);
  return {root,loads,listeners,initialize:()=>context.initializeImage(root),mounts:()=>mounts,releases:()=>releases};
}

test('image dispatcher selects independent features and keeps plain/lightbox images free of motion',async()=>{
  for(const [options,expected,mounts] of [
    [{},[],0], [{lightbox:true},[],0],
    [{tilt:1},['./image/tilt.js'],1],
    [{classes:['has-float']},['./image/visibility.js'],1],
    [{classes:['has-shadow-motion']},['./image/visibility.js'],1],
    [{reveal:'start'},['./image/visibility.js','./image/reveal.js'],1],
  ]) {
    const page=fixture(options); const cleanup=page.initialize(); assert.equal(page.initialize(),cleanup);
    await flush(); assert.deepEqual(page.loads,expected); assert.equal(page.mounts(),mounts);
    assert.equal(page.listeners.has('click'),Boolean(options.lightbox));
    cleanup(); cleanup(); assert.equal(page.releases(),mounts); assert.equal(page.listeners.size,0);
  }
});

test('removing an image before its selected modules resolve prevents late listeners and animation',async()=>{
  const page=fixture({reveal:'end',tilt:2}); const cleanup=page.initialize(); cleanup();
  await flush(); assert.equal(page.mounts(),0); assert.equal(page.releases(),0);
  const fresh=page.initialize(); await flush(); assert.equal(page.mounts(),2); fresh(); assert.equal(page.releases(),2);
});
