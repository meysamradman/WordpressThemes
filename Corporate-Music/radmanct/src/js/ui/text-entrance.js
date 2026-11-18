const heading = document.querySelector('main [data-rdc-en="viewport"]');
const motion = matchMedia('(prefers-reduced-motion: no-preference)');

if (heading?.animate && motion.matches && window.IntersectionObserver) {
  let entrance;
  const observer = new IntersectionObserver(entries => {
    if (!motion.matches || !entries.some(entry => entry.isIntersecting)) return;
    observer.disconnect();
    entrance = heading.animate(
      { translate: ['0 .55em', '0 0'], opacity: [.65, 1] },
      { duration: 650, easing: 'cubic-bezier(.22,1,.36,1)' }
    );
    entrance.onfinish = stop;
  });
  function stop() {
    observer.disconnect();
    entrance?.cancel();
    motion.removeEventListener('change', stop);
    window.removeEventListener('pagehide', stop);
  }
  observer.observe(heading);
  motion.addEventListener('change', stop);
  window.addEventListener('pagehide', stop, { once: true });
}
