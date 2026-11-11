import { newId, cloneItem } from './builder-elements.js';
import {containers, findContainer, updateContainer, withinLimits} from './builder-tree.js';

export function moveRow(layout, from, to) {
  if (from < 0 || to < 0 || from >= layout.rows.length || to >= layout.rows.length || from === to) return layout;
  const rows = [...layout.rows];
  rows.splice(to, 0, rows.splice(from, 1)[0]);
  return { ...layout, rows };
}

export function duplicateRow(layout, rowId) {
  const index = layout.rows.findIndex((row) => row.id === rowId);
  if (index < 0 || layout.rows.length >= 8) return layout;
  const row = layout.rows[index];
  const copy = { ...row, id: newId(), items: row.items.map(cloneItem) };
  const rows = [...layout.rows];
  rows.splice(index + 1, 0, copy);
  const next = { ...layout, rows };
  return withinLimits(next) ? next : layout;
}

export function moveItem(layout, sourceId, itemId, targetId, targetIndex) {
  const source = findContainer(layout, sourceId);
  const target = findContainer(layout, targetId);
  const item = source?.items.find((entry) => entry.id === itemId);
  if (!item || !target || (sourceId !== targetId && target.items.length >= 6)) return layout;
  if (item.type === 'container' && containers({rows:[item]}).some((row) => row.id === targetId)) return layout;
  let next = updateContainer(layout, sourceId, (row) => ({...row, items:row.items.filter((entry) => entry.id !== itemId)}));
  next = updateContainer(next, targetId, (row) => {
    const items = [...row.items]; items.splice(Math.min(Math.max(0, targetIndex), items.length), 0, item);
    return {...row, items};
  });
  return withinLimits(next) ? next : layout;
}
