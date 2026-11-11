export const limits = { columns: 12, rows: 100 };
export function normalizeTable(value) {
  const columns = (Array.isArray(value?.columns) ? value.columns : ['','']).slice(0,limits.columns).map((cell) => typeof cell === 'string' ? cell : '');
  if (!columns.length) columns.push('');
  const rows = (Array.isArray(value?.rows) ? value.rows : [{cells:[]}]).slice(0,limits.rows).map((row) => ({cells:columns.map((_,i) => typeof row?.cells?.[i] === 'string' ? row.cells[i] : '')}));
  return {columns,rows};
}
export function changeColumn(input, index, action) {
  const data = normalizeTable(input);
  if (action === 'add' && data.columns.length < limits.columns) { data.columns.push(''); data.rows.forEach((row) => row.cells.push('')); }
  if (action === 'remove' && data.columns.length > 1 && index >= 0 && index < data.columns.length) { data.columns.splice(index,1); data.rows.forEach((row) => row.cells.splice(index,1)); }
  const next = index + (action === 'earlier' ? -1 : 1);
  if (['earlier','later'].includes(action) && index >= 0 && index < data.columns.length && next >= 0 && next < data.columns.length) {
    for (const cells of [data.columns,...data.rows.map((row) => row.cells)]) [cells[index],cells[next]] = [cells[next],cells[index]];
  }
  return data;
}
const wrap = ($$type,value) => ({$$type,value});
export function encodeTable(value) {
  const data = normalizeTable(value);
  const strings = (values) => wrap('rdct-table-strings',values.map((cell) => wrap('string',cell)));
  return { columns:strings(data.columns), rows:wrap('rdct-table-rows',data.rows.map((row) => wrap('rdct-table-row',{cells:strings(row.cells)}))) };
}
export function decodeTable(value) {
  const strings = (prop) => (Array.isArray(prop?.value) ? prop.value : []).map((cell) => cell?.value);
  return normalizeTable({columns:strings(value?.columns), rows:(Array.isArray(value?.rows?.value) ? value.rows.value : []).map((row) => ({cells:strings(row?.value?.cells)}))});
}
