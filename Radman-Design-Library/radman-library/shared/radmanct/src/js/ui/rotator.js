const instances = new WeakMap();

export function initializeRotator(root, {gsap} = {}) {
  if (instances.has(root)) return instances.get(root);
  let active = true; let cleanup = () => {};
    const rotator = root.querySelector('.rdc-tx__rtr');
    const words = [...(rotator?.querySelectorAll('.rdc-tx__wd') ?? [])];
    if (!rotator || words.length < 2) return () => {};
    const view = root.ownerDocument.defaultView;
    if (!view.IntersectionObserver) return () => {};
    const reduced = view.matchMedia('(prefers-reduced-motion: reduce)');
    const delay = Math.min(30, Math.max(1, Number(root.dataset.wordDelay) || 3.5)) * 1000;
    const duration = Math.min(3, Math.max(.1, Number(root.dataset.wordDuration) || .8));
    let tween;
    const play = root.querySelector('[data-word-play]');
    let index = 0; let timer; let visible = false; let hovering = false; let focused = false; let paused = false;
    const original = words.map((word) => ({style: word.getAttribute('style'), hidden: word.getAttribute('aria-hidden')}));
    const show = (animate = false, previous = index) => { words.forEach((word, i) => {
      let offset = (i - index + words.length) % words.length;
      if (offset > words.length / 2) offset -= words.length;
      word.style.setProperty('--rdc-wd-off', String(offset));
      word.dataset.current = String(i === index);
      word.setAttribute('aria-hidden', 'true');
    });
      if (!gsap || reduced.matches) return;
      tween?.kill();
      root.classList.add('has-word-motion');
      const list = rotator.dataset.wordStyle === 'list';
      if (list) {
        const settings = {yPercent:(i) => Number(words[i].style.getPropertyValue('--rdc-wd-off')) * 100, opacity:(i) => i === index ? 1 : .3, visibility:'visible'};
        tween = animate ? gsap.to(words, {...settings,duration,ease:'power4.inOut'}) : gsap.set(words, settings);
        return;
      }
      gsap.set(words, {autoAlpha:0,yPercent:0,rotationX:0});
      if (!animate) { gsap.set(words[index], {autoAlpha:1}); return; }
      const slide = rotator.dataset.wordStyle !== 'basic';
      tween = gsap.timeline({defaults:{duration,ease:'power4.inOut'}})
        .fromTo(words[previous], {autoAlpha:1,yPercent:0,rotationX:0}, {autoAlpha:0,yPercent:slide ? -65 : 0,rotationX:slide ? 95 : 0}, 0)
        .fromTo(words[index], {autoAlpha:0,yPercent:slide ? 65 : 0,rotationX:slide ? -95 : 0}, {autoAlpha:1,yPercent:0,rotationX:0}, 0);
    };
    rotator.setAttribute('role', 'img'); rotator.setAttribute('aria-label', words.map((word) => word.textContent).join(', '));
    const schedule = () => {
      clearTimeout(timer);
      if (!active || paused || reduced.matches || !visible || hovering || focused || root.ownerDocument.hidden) return;
      timer = setTimeout(() => { const previous = index; index = (index + 1) % words.length; show(true, previous); schedule(); }, delay + duration * 1000);
    };
    const motion = () => { if (reduced.matches) { tween?.kill(); root.classList.remove('has-word-motion'); words.forEach((word, i) => { if (original[i].style === null) word.removeAttribute('style'); else word.setAttribute('style', original[i].style); }); } else show(); root.classList.toggle('is-tx-rot', !reduced.matches); if (play) { play.hidden = reduced.matches; play.setAttribute('aria-pressed', String(!paused)); } schedule(); };
    const enter = () => { hovering = true; schedule(); }; const leave = () => { hovering = false; schedule(); };
    const focus = () => { focused = true; schedule(); }; const blur = (event) => { if (!root.contains(event.relatedTarget)) { focused = false; schedule(); } };
    const toggle = () => { paused = !paused; play.setAttribute('aria-pressed', String(!paused)); schedule(); };
    const observer = new view.IntersectionObserver(([entry]) => { visible = entry.isIntersecting; schedule(); });
    observer.observe(root); show(); motion();
    reduced.addEventListener('change', motion); root.ownerDocument.addEventListener('visibilitychange', schedule);
    root.addEventListener('mouseenter', enter); root.addEventListener('mouseleave', leave); root.addEventListener('focusin', focus); root.addEventListener('focusout', blur); play?.addEventListener('click', toggle);
    cleanup = () => {
      clearTimeout(timer); observer.disconnect(); reduced.removeEventListener('change', motion); root.ownerDocument.removeEventListener('visibilitychange', schedule);
      root.removeEventListener('mouseenter', enter); root.removeEventListener('mouseleave', leave); root.removeEventListener('focusin', focus); root.removeEventListener('focusout', blur); play?.removeEventListener('click', toggle);
      tween?.kill(); root.classList.remove('is-tx-rot', 'has-word-motion'); rotator.removeAttribute('role'); rotator.removeAttribute('aria-label'); if (play) play.hidden = true;
      words.forEach((word, i) => { delete word.dataset.current; for (const [key, value] of Object.entries({'style':original[i].style, 'aria-hidden':original[i].hidden})) { if (value === null) word.removeAttribute(key); else word.setAttribute(key, value); } });
    };
  const destroy = () => { if (!active) return; active = false; cleanup(); instances.delete(root); };
  instances.set(root, destroy); return destroy;
}
