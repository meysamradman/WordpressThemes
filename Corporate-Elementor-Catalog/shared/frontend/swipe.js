export function bindImageSwipe(image, advance, { enabled = () => true, rtl = () => false } = {}) {
  const events = new AbortController();
  const options = { passive: true, signal: events.signal };
  let start;
  const reset = () => { start = null; };
  image.addEventListener('pointerdown', (event) => {
    if (event.isPrimary === false) { reset(); return; }
    if (!enabled() || !['touch', 'pen'].includes(event.pointerType) || event.button !== 0) return;
    const viewport = image.ownerDocument?.defaultView?.visualViewport;
    if (viewport && viewport.scale > 1) return;
    start = { id: event.pointerId, x: event.clientX, y: event.clientY, time: event.timeStamp };
    image.setPointerCapture?.(event.pointerId);
  }, options);
  image.addEventListener('pointerup', (event) => {
    const initial = start;
    reset();
    if (!initial || initial.id !== event.pointerId || !enabled()) return;
    const viewport = image.ownerDocument?.defaultView?.visualViewport;
    if (viewport && viewport.scale > 1) return;
    const x = event.clientX - initial.x;
    const y = event.clientY - initial.y;
    if (event.timeStamp - initial.time > 700 || Math.abs(x) < 48 || Math.abs(x) < Math.abs(y) * 1.5) return;
    advance((x < 0) !== rtl() ? 1 : -1);
  }, options);
  image.addEventListener('pointercancel', reset, options);
  image.addEventListener('lostpointercapture', reset, options);
  return () => { reset(); events.abort(); };
}
