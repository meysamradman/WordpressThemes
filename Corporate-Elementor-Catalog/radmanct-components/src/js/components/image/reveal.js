export function createImageReveal(root) {
  const view = root.ownerDocument.defaultView;
  const direction = root.dataset.reveal;
  let timer;
  if (direction === 'start' || direction === 'end') { const rtl = view.getComputedStyle(root).direction === 'rtl'; root.dataset.reveal = (direction === 'start') !== rtl ? 'lr' : 'rl'; }
  const reset = () => { root.classList.remove('is-revealing'); view.clearTimeout(timer); };
  const enter = () => {
    if (root.matches(':focus-within')) return;
    root.classList.add('is-revealing');
    const delay = parseFloat(view.getComputedStyle(root).getPropertyValue('--rdct-im-reveal-delay')) || 0;
    timer = view.setTimeout(reset, Math.min(10000, Math.max(0, delay)) + 1250);
  };
  root.addEventListener('focusin', reset);
  return { enter, reset, destroy() { reset(); root.removeEventListener('focusin', reset); root.dataset.reveal = direction; } };
}
