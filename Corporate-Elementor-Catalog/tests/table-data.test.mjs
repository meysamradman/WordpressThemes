import test from 'node:test';
import assert from 'node:assert/strict';
import {normalizeTable,changeColumn,encodeTable,decodeTable} from '../radmanct-components/src/js/admin/table-data.js';

test('moving or deleting a column moves every corresponding cell without changing the original',() => {
  const value = {columns:['A','B','C'],rows:[{cells:['1','2','3']},{cells:['4','5','6']}]};
  const moved = changeColumn(value,1,'earlier');
  assert.deepEqual(moved,{columns:['B','A','C'],rows:[{cells:['2','1','3']},{cells:['5','4','6']}]});
  assert.deepEqual(changeColumn(moved,1,'remove'),{columns:['B','C'],rows:[{cells:['2','3']},{cells:['5','6']}]});
  assert.deepEqual(value.rows[0].cells,['1','2','3']);
  assert.equal(changeColumn({columns:['A'],rows:[]},0,'remove').columns.length,1);
});
test('matrix limits and malformed values preserve rectangular empty cells',() => {
  const value = normalizeTable({columns:Array(20).fill('Title'),rows:Array(120).fill({cells:['one',null,{bad:true}]})});
  assert.equal(value.columns.length,12); assert.equal(value.rows.length,100);
  assert.deepEqual(value.rows[0].cells.slice(0,4),['one','','','']);
  assert.equal(changeColumn(value,0,'add').columns.length,12);
  assert.deepEqual(decodeTable({rows:{value:{bad:true}}}),{columns:[''],rows:[]});
});
test('native Atomic table data round trips Persian, markup, blank cells and row order',() => {
  const value = {columns:['مشخصه','Value'],rows:[{cells:['جنس','<strong>Steel</strong>\n304']},{cells:['','']}]};
  assert.deepEqual(decodeTable(encodeTable(value)),value);
});
