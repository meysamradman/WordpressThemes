import {findContainer} from './builder-tree.js';

export function findPreviewDrop(targets, x, y, layout, mobile = false, rtl = false, device = mobile ? 'mobile' : 'desktop') {
  const contains = ({ rect }) => rect.width > 0 && rect.height > 0 && x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom;
  const region = targets.filter((entry) => !entry.item && contains(entry)).sort((a,b) => (findContainer(layout,b.rowId)?.depth ?? 0)-(findContainer(layout,a.rowId)?.depth ?? 0) || a.rect.width*a.rect.height-b.rect.width*b.rect.height)[0];
  const item = targets.find((entry) => entry.item && (!region || entry.rowId === region.rowId) && contains(entry));
  const target = item || region;
  if (!target) return null;
  const row = findContainer(layout, target.rowId);
  if (!row) return null;
  if (!item) return { rowId: row.id, index: row.items.length, token: row.id };
  const index = row.items.findIndex((entry) => entry.id === item.id);
  if (index < 0) return null;
  const grid = row.appearance?.rowLayout === 'grid';
  const columns = device === 'mobile' ? (row.appearance?.mobileColumns ?? 1) : (device === 'desktop' ? null : row.appearance?.[`${device}Columns`]) ?? row.appearance?.columns ?? 3;
  const override = device === 'desktop' ? 'inherit' : row.items[index].appearance?.[`${device}Width`];
  const width = !override || override === 'inherit' ? row.items[index].appearance?.width : override;
  const flow = row.appearance?.[`${device}Layout`] ?? (mobile ? 'stack' : 'inline');
  const direction = device === 'desktop' ? row.appearance?.direction : (!row.appearance?.[`${device}Direction`] || row.appearance[`${device}Direction`] === 'inherit' ? row.appearance?.direction : row.appearance[`${device}Direction`]);
  const vertical = (direction === 'column' && !grid) || (grid ? columns === 1 : flow === 'stack') || width === 'full';
  const after = vertical ? y > item.rect.top + item.rect.height / 2 : rtl ? x < item.rect.left + item.rect.width / 2 : x > item.rect.left + item.rect.width / 2;
  return { rowId: row.id, index: index + Number(after), token: item.id };
}

export function dropIndex(layout, source, rowId, index) {
  const row = findContainer(layout, rowId);
  if (!row) return 0;
  const before = row.items.findIndex((item) => item.id === source.itemId);
  return source.rowId === rowId && before >= 0 && before < index ? index - 1 : index;
}

export function previewDropLine(target, targets, layout, mobile = false, rtl = false, device = mobile ? 'mobile' : 'desktop') {
  if (!target) return null;
  const row = findContainer(layout,target.rowId);
  const region = targets.find((entry)=>!entry.item && entry.id === target.rowId)?.rect;
  if (!row || !region) return null;
  const last = target.index >= row.items.length;
  const itemId = row.items[last ? row.items.length-1 : target.index]?.id;
  const rect = targets.find((entry)=>entry.item && entry.id === itemId)?.rect;
  if (!rect) return {...region,inside:true};
  const direction = row.appearance?.[`${device}Direction`];
  const flow = row.appearance?.[`${device}Layout`] ?? (mobile ? 'stack' : 'inline');
  const vertical = (direction && direction !== 'inherit' ? direction : row.appearance?.direction) === 'column' || flow === 'stack' || (row.appearance?.rowLayout === 'grid' && (row.appearance?.[`${device}Columns`] ?? row.appearance.columns) === 1);
  if (vertical) return {left:region.left,top:(last ? rect.bottom : rect.top)-1.5,width:region.width,height:3};
  return {left:(rtl ? (last ? rect.left : rect.right) : (last ? rect.right : rect.left))-1.5,top:region.top,width:3,height:region.height};
}
