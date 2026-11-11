export function pinWithinGroup(ScrollTrigger, root, trigger, target, top) {
  return ScrollTrigger.create({trigger, start:()=>`top ${top}`, endTrigger:root, end:()=>`bottom ${top + target.offsetHeight}`, pin:target, pinSpacing:false, invalidateOnRefresh:true});
}

export function createStickyScene(root, gsap, ScrollTrigger, setup) {
  const view = root.ownerDocument.defaultView;
  const list = root.querySelector(':scope > .rdct-ss__list') || root;
  const items = [...list.children].filter((node) => node.classList.contains('rdct-ss__item'));
  const inners = items.map((item) => item.querySelector(':scope > .rdct-ss__inner'));
  if (items.length < (root.dataset.mode === 'split' ? 1 : 2) || inners.some((node) => !node)) return () => {};
  const width = Number.isFinite(Number(root.dataset.minWidth)) ? Math.max(0, Math.min(1920, Number(root.dataset.minWidth))) : 992;
  const media = view.matchMedia(`(min-width: ${width}px) and (prefers-reduced-motion: no-preference)`);
  const headers = [...root.ownerDocument.querySelectorAll('.rdct-bld--sticky-all, .rdct-bld--sticky-desktop, #wpadminbar')];
  let alive = true; let focused = false; let frame = 0; let context; let signature = '';
  const reset = () => {
    context?.revert(); context = null;
    root.classList.remove('is-enhanced');
    root.style.removeProperty('--rdct-ss-top'); root.style.removeProperty('--rdct-ss-step');
    items.forEach((item) => item.classList.remove('is-tall'));
  };
  const rebuild = () => {
    frame = 0;
    if (!alive) return;
    const headerHeight = Math.max(0, ...headers.filter((node) => node.id !== 'wpadminbar' && ['fixed','sticky'].includes(view.getComputedStyle(node).position)).map((node) => node.getBoundingClientRect().height));
    const toolbar = headers.find((node) => node.id === 'wpadminbar');
    const toolbarHeight = toolbar && view.getComputedStyle(toolbar).position === 'fixed' ? toolbar.getBoundingClientRect().height : 0;
    const top = Math.max(0, Math.min(300, Number(root.dataset.offset) || 0)) + headerHeight + toolbarHeight;
    const geometry = () => [media.matches, focused, view.innerWidth, view.innerHeight, top, ...inners.map((node) => node.offsetHeight), root.querySelector('.rdct-ss__fixed')?.offsetHeight].join(':');
    if (geometry() === signature) return;
    reset();
    if (!media.matches || focused || root.parentElement?.closest('[data-rdct-sticky-sections].is-enhanced')) {signature = geometry(); return;}
    gsap.registerPlugin(ScrollTrigger);
    context = gsap.context(() => {
      root.classList.add('is-enhanced');
      const available = view.innerHeight - top - 24;
      inners.forEach((inner, index) => items[index].classList.toggle('is-tall', inner.offsetHeight > available));
      root.style.setProperty('--rdct-ss-top', `${top}px`);
      if (setup({root, items, inners, top, available, gsap, ScrollTrigger}) === false) root.classList.remove('is-enhanced');
    }, root);
    signature = geometry();
  };
  const schedule = () => {if (alive && !frame) frame = view.requestAnimationFrame(rebuild);};
  const focus = () => {if (!focused) {focused = true; schedule();}};
  media.addEventListener('change', schedule); view.addEventListener('resize', schedule);
  root.addEventListener('load', schedule, true); root.addEventListener('focusin', focus);
  const observer = view.ResizeObserver ? new view.ResizeObserver(schedule) : null;
  [...inners, ...headers, root.querySelector('.rdct-ss__fixed')].filter(Boolean).forEach((node) => observer?.observe(node));
  root.ownerDocument.fonts?.ready.then(schedule).catch(() => {});
  rebuild();
  return () => {
    if (!alive) return;
    alive = false; if (frame) view.cancelAnimationFrame(frame);
    observer?.disconnect(); media.removeEventListener('change', schedule); view.removeEventListener('resize', schedule);
    root.removeEventListener('load', schedule, true); root.removeEventListener('focusin', focus); reset();
  };
}
