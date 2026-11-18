export function initializeTextEntrance(view = window) {
  const doc = view.document;
  const preference = view.matchMedia('(prefers-reduced-motion: reduce)');
  if (preference.matches || !view.IntersectionObserver || !view.Element.prototype.animate) return;

  const targets = [...doc.querySelectorAll('main [data-rdc-en]')].map(node =>
    node.dataset.rdcEn === 'intro' ? node.querySelector(':scope > p') : node
  ).filter(node => node?.textContent.trim());
  if (!targets.length) return;

  const active = new Set();
  let remaining = targets.length;
  const observer = new view.IntersectionObserver(entries => {
    for (const { target, isIntersecting } of entries) {
      if (!isIntersecting || preference.matches) continue;
      observer.unobserve(target);
      if (!--remaining) observer.disconnect();
      const animation = target.animate(
        { translate: ['0 10px', '0 0'], opacity: [.75, 1] },
        { duration: 460, easing: 'cubic-bezier(.22,.65,.36,1)' }
      );
      active.add(animation);
      animation.onfinish = () => active.delete(animation);
    }
  });

  const start = () => { for (const target of targets) observer.observe(target); };
  const stop = () => {
    observer.disconnect();
    for (const animation of active) animation.cancel();
    active.clear();
    doc.removeEventListener('rdc:curtain-end', start);
    preference.removeEventListener('change', stop);
    view.removeEventListener('pagehide', stop);
  };

  preference.addEventListener('change', stop, { once: true });
  view.addEventListener('pagehide', stop, { once: true });
  if (doc.documentElement.getAttribute('data-rdc-page-load') === 'enter') {
    doc.addEventListener('rdc:curtain-end', start, { once: true });
  } else start();
}

initializeTextEntrance();
