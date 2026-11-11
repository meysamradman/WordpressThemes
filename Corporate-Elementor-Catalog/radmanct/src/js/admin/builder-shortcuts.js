export function historyShortcut(event) {
  if (!(event.ctrlKey || event.metaKey) || event.altKey || event.isComposing || event.defaultPrevented) return null;
  if (event.target?.closest?.('input, textarea, select, [contenteditable]:not([contenteditable="false"]), [role="textbox"]')) return null;
  const key = event.key?.toLowerCase();
  if (key === 'z') return event.shiftKey ? 'redo' : 'undo';
  return key === 'y' && !event.shiftKey ? 'redo' : null;
}
