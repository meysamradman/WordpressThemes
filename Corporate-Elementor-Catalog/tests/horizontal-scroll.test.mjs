import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import {runInNewContext} from 'node:vm';
import {motionOptions} from '../radmanct/src/js/components/container-motion-options.js';
const source = (await readFile(new URL('../radmanct/src/js/components/horizontal-scroll.js',import.meta.url),'utf8')).replace(/^import .*;\r?\n/gm,'').replaceAll('export function','function');
const context = {}; runInNewContext(source+'\nthis.geometry=horizontalGeometry;',context);

test('horizontal travel ends at the last panel and mirrors direction without changing content', () => {
  assert.equal(context.geometry(1000,2840,700,900,false).x,-1840);
  assert.equal(context.geometry(1000,2840,700,900,true).x,1840);
  assert.equal(context.geometry(1000,2840,700,900,true).usable,true);
  assert.equal(context.geometry(1000,900,700,900,false).usable,false);
  assert.equal(context.geometry(1000,2840,950,900,false).usable,false);
  assert.equal(context.geometry(0,2840,700,900,false).usable,false);
});

test('pinned controls reject unsupported CSS, unsafe offsets and invalid timing', () => {
  const options = motionOptions({horizontal:'yes',horizontal_gap:-50,horizontal_width:999,horizontal_direction:'url(bad)',horizontal_scrub:999,horizontal_min_width:0});
  assert.equal(options.horizontal,true); assert.equal(options.horizontal_gap,0);
  assert.equal(options.horizontal_width,100); assert.equal(options.horizontal_direction,'auto');
  assert.equal(options.horizontal_scrub,5); assert.equal(options.horizontal_min_width,320);
});
