export function loadPreview({ request, id, layout, direction, errorMessage, onStart, onSuccess, onError, delay = 400 }) {
  const controller = new AbortController();
  let current = true;
  onStart();
  const timer = setTimeout(async () => {
    try {
      const result = await request('preview', id, { layout: { ...layout, enabled: false }, direction }, controller.signal);
      if (!current) return;
      if (typeof result?.html !== 'string' || !result.html.trim()) throw new Error(errorMessage);
      onSuccess(result.html);
    } catch (error) {
      if (current && error?.name !== 'AbortError') onError(error?.message || errorMessage);
    }
  }, delay);
  return () => { current = false; clearTimeout(timer); controller.abort(); };
}
