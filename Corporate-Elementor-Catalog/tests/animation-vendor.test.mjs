import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';

test('both production vendor APIs preserve named exports and share their GSAP instance',async()=>{
  for (const owner of ['radmanct','radmanct-components']) {
    const manifest=JSON.parse(await readFile(new URL(`../${owner}/dist/manifest.json`,import.meta.url),'utf8'));
    const prefix=owner==='radmanct'?'../radmanct-components/':'';
    const resolve=(name)=>new URL(`../${owner}/dist/${manifest[`${prefix}src/js/${name}.js`].file}`,import.meta.url);
    const core=await import(resolve('gsap-vendor'));
    const animation=await import(resolve('animation-vendor'));
    assert.equal(typeof core.gsap?.timeline,'function',owner);
    assert.equal(animation.gsap,core.gsap,owner);
    assert.equal(typeof animation.ScrollTrigger?.create,'function',owner);
    const entry=await readFile(resolve('gsap-vendor'),'utf8');
    assert.doesNotMatch(entry,/ScrollTrigger/,owner);
  }
});
