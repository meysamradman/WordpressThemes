export function commit(state, value) {
  const next = typeof value === 'function' ? value(state.present) : value;
  if (JSON.stringify(next) === JSON.stringify(state.present)) return state;
  return { past: [...state.past.slice(-19), state.present], present: next, future: [] };
}

export function undo(state) {
  if (!state.past.length) return state;
  return { past: state.past.slice(0, -1), present: state.past.at(-1), future: [state.present, ...state.future] };
}

export function redo(state) {
  if (!state.future.length) return state;
  return { past: [...state.past, state.present], present: state.future[0], future: state.future.slice(1) };
}
