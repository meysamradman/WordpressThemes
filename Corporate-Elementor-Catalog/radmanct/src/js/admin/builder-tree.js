export function containers(layout) {
  const result = [];
  const visit = (node, depth, parentId = null) => {
    result.push(node.type === 'container' ? {...node, appearance: node.layout || {}, depth, parentId} : node);
    for (const item of node.items || []) if (item.type === 'container') visit(item, depth + 1, node.id);
  };
  layout.rows.forEach((row) => visit(row, 0));
  return result;
}

export const findContainer = (layout, id) => containers(layout).find((row) => row.id === id);

export function updateContainer(layout, id, update) {
  const visit = (node) => {
    if (node.id === id) {
      if (node.type !== 'container') return update(node);
      const next = update({...node, appearance: node.layout || {}});
      return {...node, items: next.items, layout: next.appearance};
    }
    const items = node.items.map((item) => item.type === 'container' ? visit(item) : item);
    return {...node, items};
  };
  return {...layout, rows: layout.rows.map(visit)};
}

export function withinLimits(layout) {
  const rows = containers(layout);
  return rows.every((row) => (row.depth ?? 0) <= 2 && row.items.length <= 6) && rows.reduce((count, row) => count + row.items.length, layout.rows.length) <= 96;
}
