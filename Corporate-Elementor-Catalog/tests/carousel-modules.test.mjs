import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';

test('carousel downloads only selected control modules', async () => {
  const source = await readFile(new URL('../radmanct-components/src/js/components/carousel/modules.js', import.meta.url), 'utf8');
  for (const [options, expected] of [[{}, []], [{autoplay: true}, ['autoplay']], [{arrows: true, pagination: 'none'}, ['navigation']], [{pagination: 'fraction'}, ['pagination']], [{autoplay: true, arrows: true, pagination: 'numbers'}, ['autoplay', 'navigation', 'pagination']]]) {
    const requested = [];
    const context = vm.createContext({loadOptional: async (path) => {const name = path.split('/').at(-1).replace('.js', ''); requested.push(name); return {default: name};}});
    vm.runInContext(source.replace('export async function', 'async function').replaceAll('import(', 'loadOptional('), context);
    const modules = await context.loadCarouselModules(options);
    assert.deepEqual(requested, expected); assert.deepEqual([...modules], expected);
  }
});

test('carousel entry preserves Marquee independence and guards pending control imports', async () => {
  const source = await readFile(new URL('../radmanct-components/src/js/components/carousel-entry.js', import.meta.url), 'utf8');
  for (const mode of ['marquee', 'carousel', 'fade', 'stack', 'released']) {
    let resolve; let initialized = 0; let destroyed = 0; let controlLoads = 0; let args;
    const controls = new Promise((done) => {resolve = done;});
    const initialize = (...values) => {initialized++; args = values; return () => destroyed++;};
    const context = vm.createContext({loadCarouselModules: () => {controlLoads++; return controls;}, loadOptional: async () => ({initializeCarousel: initialize, initializeMarquee: initialize}), reportRuntimeError: (_feature, error) => {throw error;}});
    vm.runInContext(source.replace(/^import .*;\r?\n/gm, '').replaceAll('export function', 'function').replaceAll('import(', 'loadOptional('), context);
    const root = {isConnected: true, dataset: mode === 'marquee' ? {mode} : {mode: mode === 'fade' ? 'slider' : mode, transition: 'fade', options: JSON.stringify({arrows: true})}};
    const cleanup = context.initializeCarousel(root);
    if (mode === 'released') cleanup();
    resolve(['navigation']); await controls; await new Promise((done) => setImmediate(done));
    assert.equal(controlLoads, mode === 'marquee' ? 0 : 1);
    assert.equal(initialized, mode === 'released' ? 0 : 1);
    if (mode !== 'marquee' && mode !== 'released') assert.deepEqual([...args.at(-1)], ['navigation']);
    cleanup(); cleanup(); assert.equal(destroyed, mode === 'released' ? 0 : 1);
  }
});
