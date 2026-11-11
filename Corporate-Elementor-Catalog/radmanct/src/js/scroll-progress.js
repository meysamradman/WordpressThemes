export function initializeScrollProgress() {
  const root = document.getElementById('rdct-scroll-progress');
  if (!root || root.dataset.enhanced) return;
  const bar = root.querySelector('span');
  if (!bar) return;
  root.dataset.enhanced = 'true';
  const events = new AbortController();
  const options = { signal: events.signal, passive: true };
  let frame = 0;
  let length = 0;
  let measure = true;
  let smoothedTop = null;
  const paint = () => {
    frame = 0;
    if (measure) {
      length = Math.max(0, document.documentElement.scrollHeight - document.documentElement.clientHeight);
      measure = false;
    }
    const position = smoothedTop ?? window.scrollY;
    const ratio = length > 0 ? Math.min(1, Math.max(0, position / length)) : 0;
    root.hidden = length === 0 || position <= 1;
    bar.style.transform = `scaleX(${ratio})`;
  };
  const schedule = () => { if (!frame) frame = requestAnimationFrame(paint); };
  const resize = () => { measure = true; schedule(); };
  document.addEventListener('scroll', schedule, options);
  document.addEventListener('rdct:smooth-update', (event) => {
    smoothedTop = Number.isFinite(event.detail?.scrollTop) ? event.detail.scrollTop : null;
    schedule();
  }, options);
  document.addEventListener('load', resize, { ...options, capture: true });
  window.addEventListener('resize', resize, options);
  window.addEventListener('pageshow', resize, options);
  const observer = typeof ResizeObserver === 'undefined' ? null : new ResizeObserver(resize);
  observer?.observe(document.body);
  observer?.observe(document.documentElement);
  window.addEventListener('pagehide', (event) => {
    cancelAnimationFrame(frame); frame = 0;
    if (!event.persisted) { observer?.disconnect(); events.abort(); delete root.dataset.enhanced; }
  }, options);
  resize();
}
