import {containers} from './builder-tree.js';

export function previewContentHeight(content) {
  return Math.max(48, Math.ceil(content ? content.getBoundingClientRect().height + content.offsetTop : 0));
}

export function resolveSelection(layout, selection) {
  const rows = containers(layout);
  if (!selection?.itemId && selection?.rowId && !layout.rows.some((row)=>row.id === selection.rowId)) selection = {...selection,itemId:selection.rowId};
  const row = rows.find((entry) => entry.items.some((item) => item.id === selection?.itemId)) || rows.find((entry) => entry.id === selection?.rowId) || layout.rows[0] || null;
  const item = row?.items.find((entry) => entry.id === selection?.itemId) || null;
  return { row, item };
}

export function pickPreviewTarget(document, x, y) {
  for (const attribute of ['data-builder-preview-item', 'data-builder-preview-row']) {
    for (const node of [...document.querySelectorAll(`[${attribute}]`)].reverse()) {
      const rect = node.getBoundingClientRect();
      if (rect.width && rect.height && x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom) {
        return { id: node.getAttribute(attribute), item: attribute === 'data-builder-preview-item' };
      }
    }
  }
  return null;
}
export function layoutAssignment(stored, language = 'all') {
  const direct = stored.defaults?.[language];
  const selected = direct ?? stored.defaults?.all ?? 'default';
  return { direct, selected, status: !stored.data.layout.enabled ? 'layoutDisabled' : selected === stored.id ? 'layoutAssigned' : 'layoutNotAssigned' };
}

