const motion = matchMedia('(prefers-reduced-motion: no-preference)');

if (motion.matches && window.IntersectionObserver) {
  // Elements already on screen use the first-paint animation in CSS.
  const waiting = new Set([...document.querySelectorAll('main [data-rdc-en]')].filter(node => {
    const target = node.dataset.rdcEn === 'intro' ? node.querySelector(':scope > p') : node;
    return target && target.getBoundingClientRect().top >= window.innerHeight;
  }));

  if (waiting.size) {
    const observer = new IntersectionObserver(entries => {
      for (const { target, isIntersecting } of entries) {
        if (!isIntersecting || !waiting.delete(target)) continue;
        target.classList.replace('is-wait', 'is-vis');
        observer.unobserve(target);
      }
      if (!waiting.size) stop();
    }, { rootMargin: '0px 0px -5% 0px' });

    function stop() {
      observer.disconnect();
      for (const node of waiting) node.classList.remove('is-wait');
      waiting.clear();
      motion.removeEventListener('change', stop);
      window.removeEventListener('pagehide', stop);
    }

    for (const node of waiting) {
      node.classList.add('is-wait');
      observer.observe(node);
    }
    motion.addEventListener('change', stop);
    window.addEventListener('pagehide', stop, { once: true });
  }
}
