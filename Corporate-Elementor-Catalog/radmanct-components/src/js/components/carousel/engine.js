import { reportRuntimeError } from '../../../../../shared/frontend/lifecycle.js';
import Swiper from 'swiper';
import { A11y } from 'swiper/modules';
import { responsiveBreakpoints } from '../responsive.js';

const instances = new WeakMap();

export function initializeCarousel(root, effectModules = [], state = null, featureModules = []) {
  if (instances.has(root)) return instances.get(root);
  const viewport = root.querySelector(':scope > .rdct-crs__frame > .rdct-crs__viewport');
  if (!viewport) return () => {};
  let options;
  try { options = JSON.parse(root.dataset.options); } catch { return () => {}; }
  if (!options || typeof options !== 'object') return () => {};
  const view = root.ownerDocument.defaultView;
  const breakpoints = responsiveBreakpoints(options);
  const reduced = view.matchMedia('(prefers-reduced-motion: reduce)');
  const controls = [...root.querySelectorAll(':scope > [data-rdct-carousel-controls], :scope > .rdct-crs__frame > [data-rdct-carousel-controls]')];
  const control = (selector) => controls.map((slot) => slot.querySelector(selector)).find(Boolean);
  const play = control('[data-play]');
  const fade = options.mode === 'slider' && options.transition === 'fade' && effectModules.length > 0;
  const stack = options.mode === 'stack' && effectModules.length > 0;
  const single = options.mode === 'slider' || options.mode === 'stack';
  let wanted = typeof state?.wanted === 'boolean' ? state.wanted : Boolean(options.autoplay); let visible = !view.IntersectionObserver; let hovering = false; let alive = true; let automaticStop = false;
  const originalTabindex = viewport.getAttribute('tabindex');
  const originalDirection = viewport.getAttribute('dir');
  const slideAttributes = new Map();
  const autoplay = { delay: options.delay, disableOnInteraction: !options.resume_interaction, pauseOnMouseEnter: options.pause_hover !== false, stopOnLastSlide: Boolean(options.stop_last), reverseDirection: options.autoplay_direction === 'reverse', waitForTransition: true };
  const variableWidth = options.mode === 'carousel' && options.variable_width === true;
  const perView = (value) => single ? 1 : variableWidth ? 'auto' : value;
  const group = (value) => options.group_slides && !variableWidth ? Math.max(1, Math.floor(perView(value))) : 1;
  const fixedHeight = () => { const value = (view.getComputedStyle(root).getPropertyValue?.('--rdct-carousel-height') ?? '').trim(); return Boolean(value && value !== 'auto'); };
  viewport.dir = view.getComputedStyle(root).direction;
  viewport.classList.add('swiper');
  const pagination = options.pagination === 'none' ? false : {
    el: control('.rdct-crs__pagination'),
    type: options.pagination === 'fraction' ? 'fraction' : 'bullets',
    clickable: true,
    renderBullet: (index, className) => `<button type="button" class="${className}">${options.pagination === 'numbers' ? index + 1 : ''}</button>`,
    bulletElement: 'button',
  };
  const widthStyles = variableWidth ? new Map([...viewport.querySelectorAll(':scope > .swiper-wrapper > .swiper-slide')].map((slide) => [slide, slide.getAttribute('style')])) : new Map();
  const slider = new Swiper(viewport, {
    modules: [A11y, ...effectModules, ...featureModules],
    effect: stack ? 'creative' : fade ? 'fade' : 'slide',
    ...(fade ? { fadeEffect: { crossFade: true } } : {}),
    ...(stack ? { creativeEffect: {limitProgress: 2, perspective: true, prev: {translate: [viewport.dir === 'rtl' ? '-120%' : '120%', 0, 0], opacity: 0, scale: 1}, next: {translate: [0, 0, -80], opacity: 1, scale: 1}} } : {}),
    initialSlide: Number.isInteger(state?.index) ? Math.max(0, Math.min((options.slide_count ?? 1) - 1, state.index)) : options.autoplay && options.autoplay_direction === 'reverse' ? Math.max(0, (options.slide_count ?? 1) - 1) : 0,
    slidesPerView: perView(options.slides_mobile),
    slidesPerGroup: group(options.slides_mobile),
    slidesPerGroupAuto: variableWidth && Boolean(options.group_slides),
    centeredSlides: options.alignment === 'center',
    centerInsufficientSlides: true,
    allowTouchMove: options.draggable !== false,
    autoHeight: options.height_mode === 'adaptive' && !fixedHeight(),
    spaceBetween: options.gap,
    speed: reduced.matches ? 0 : options.duration,
    rewind: options.rewind !== false,
    loop: false,
    watchOverflow: true,
    breakpoints: { [breakpoints.tablet]: { slidesPerView: perView(options.slides_tablet), slidesPerGroup: group(options.slides_tablet) }, [breakpoints.desktop]: { slidesPerView: perView(options.slides), slidesPerGroup: group(options.slides) } },
    navigation: options.arrows ? { prevEl: control('[data-previous]'), nextEl: control('[data-next]') } : false,
    pagination,
    autoplay: options.autoplay ? autoplay : false,
    a11y: { prevSlideMessage: control('[data-previous]')?.getAttribute('aria-label'), nextSlideMessage: control('[data-next]')?.getAttribute('aria-label'), slideLabelMessage: options.messages?.slide, paginationBulletMessage: options.messages?.bullet },
  });
  root.classList.add('is-enhanced');
  if (stack) root.classList.add('is-stack');
  controls.forEach((slot) => { slot.hidden = false; });
  const sync = () => { if (play) { play.setAttribute('aria-pressed', String(Boolean(slider.autoplay?.running))); play.disabled = reduced.matches; } };
  const stop = () => { automaticStop = true; slider.autoplay?.stop(); automaticStop = false; sync(); };
  const stopped = () => { if (!automaticStop) wanted = false; sync(); };
  const playback = () => {
    if (!alive) return;
    if (!wanted || !visible || hovering || root.ownerDocument.hidden || reduced.matches) { stop(); return; }
    if (!slider.autoplay?.running) { slider.params.autoplay = { ...autoplay, enabled: true }; slider.autoplay?.start(); }
    sync();
  };
  const focus = (event) => { if (event.target !== play) { wanted = false; stop(); } };
  const toggle = () => {
    if (wanted) { wanted = false; stop(); }
    else if (!reduced.matches) {
      if (options.stop_last && (options.autoplay_direction === 'reverse' ? slider.isBeginning : slider.isEnd)) slider.slideTo(options.autoplay_direction === 'reverse' ? slider.slides.length - 1 : 0);
      wanted = true; playback();
    }
  };
  const motion = () => { slider.params.speed = reduced.matches ? 0 : options.duration; if (reduced.matches) { wanted = false; stop(); } sync(); };
  const enter = (event) => { if (event.pointerType === 'mouse' && options.pause_hover !== false) hovering = true; };
  const leave = (event) => { if (event.pointerType === 'mouse' && hovering) { hovering = false; playback(); } };
  const interact = () => { if (!options.resume_interaction) { wanted = false; stop(); } };
  const accessibleSlides = () => {
    if (!fade && !stack) return;
    for (const [index, slide] of [...(slider.slides ?? [])].entries()) {
      if (!slideAttributes.has(slide)) slideAttributes.set(slide, { inert: slide.getAttribute('inert'), hidden: slide.getAttribute('aria-hidden') });
      const original = slideAttributes.get(slide);
      if (index === slider.activeIndex) {
        if (original.inert === null) slide.removeAttribute('inert'); else slide.setAttribute('inert', original.inert);
        if (original.hidden === null) slide.removeAttribute('aria-hidden'); else slide.setAttribute('aria-hidden', original.hidden);
      } else {
        if (slide.contains(root.ownerDocument.activeElement)) viewport.focus({ preventScroll: true });
        slide.setAttribute('inert', ''); slide.setAttribute('aria-hidden', 'true');
      }
    }
  };
  if (fade || stack) viewport.setAttribute('tabindex', '-1');
  root.addEventListener('focusin', focus);
  play?.addEventListener('click', toggle);
  reduced.addEventListener('change', motion);
  root.addEventListener('pointerenter', enter); root.addEventListener('pointerleave', leave);
  root.ownerDocument.addEventListener?.('visibilitychange', playback);
  const observer = options.autoplay && view.IntersectionObserver ? new view.IntersectionObserver(([entry]) => { visible = entry.isIntersecting; playback(); }) : null;
  observer?.observe(root);
  const end = () => { accessibleSlides(); if (options.stop_last && wanted && (options.autoplay_direction === 'reverse' ? slider.isBeginning : slider.isEnd)) { wanted = false; stop(); } };
  const height = () => { const adaptive = options.height_mode === 'adaptive' && !fixedHeight(); if (slider.params.autoHeight === adaptive) return; slider.params.autoHeight = adaptive; viewport.classList.toggle('swiper-autoheight', adaptive); if (!adaptive) slider.wrapperEl?.style.removeProperty('height'); slider.update(); };
  slider.on('autoplayStop', stopped); slider.on('autoplayStart', sync); slider.on('slideChange', end); slider.on('resize', height); slider.on('sliderFirstMove', interact);
  let widthFrame = 0;
  const refreshWidth = () => {
    if (!alive || widthFrame) return;
    widthFrame = view.requestAnimationFrame(() => { widthFrame = 0; if (alive) slider.update(); });
  };
  const widthObserver = variableWidth && view.ResizeObserver ? new view.ResizeObserver(refreshWidth) : null;
  if (variableWidth) {
    for (const slide of slider.slides) widthObserver?.observe(slide);
    root.ownerDocument.fonts?.ready.then(refreshWidth).catch(() => {});
  }
  if (reduced.matches) { wanted = false; stop(); }
  end(); playback();
  let releaseSync = () => {}; let releaseExternal = () => {};
  if (options.controller_id || options.controlled_ids) import('./sync.js').then(({registerCarouselSync}) => { if (alive) releaseSync = registerCarouselSync(root, slider, options); }).catch((error) => reportRuntimeError('carousel-sync', error));
  if (options.external_previous_class || options.external_next_class) import('./external.js').then(({registerCarouselExternal}) => { if (alive) releaseExternal = registerCarouselExternal(root, slider, options, interact); }).catch((error) => reportRuntimeError('carousel-external', error));
  const cleanup = () => {
    if (!instances.has(root)) return;
    alive = false;
    releaseSync();
    releaseExternal();
    widthObserver?.disconnect(); if (widthFrame) view.cancelAnimationFrame(widthFrame);
    root.removeEventListener('focusin', focus); play?.removeEventListener('click', toggle); reduced.removeEventListener('change', motion);
    observer?.disconnect(); root.ownerDocument.removeEventListener?.('visibilitychange', playback);
    root.removeEventListener('pointerenter', enter); root.removeEventListener('pointerleave', leave);
    slider.destroy(true, true); viewport.classList.remove('swiper'); viewport.removeAttribute('dir'); root.classList.remove('is-enhanced'); controls.forEach((slot) => { slot.hidden = true; });
    for (const [slide, style] of widthStyles) { if (style === null) slide.removeAttribute('style'); else slide.setAttribute('style', style); }
    if (originalDirection !== null) viewport.setAttribute('dir', originalDirection);
    for (const [slide, original] of slideAttributes) {
      if (original.inert === null) slide.removeAttribute('inert'); else slide.setAttribute('inert', original.inert);
      if (original.hidden === null) slide.removeAttribute('aria-hidden'); else slide.setAttribute('aria-hidden', original.hidden);
    }
    if (stack) root.classList.remove('is-stack');
    if (fade || stack) { if (originalTabindex === null) viewport.removeAttribute('tabindex'); else viewport.setAttribute('tabindex', originalTabindex); }
    instances.delete(root);
  };
  cleanup.snapshot = () => ({index: slider.activeIndex, wanted});
  instances.set(root, cleanup);
  return cleanup;
}

export function initializeCarousels(scope = document) {
  const roots = scope.matches?.('[data-rdct-carousel]') ? [scope] : [...scope.querySelectorAll('[data-rdct-carousel]')];
  const cleanups = roots.map(initializeCarousel);
  return () => cleanups.forEach((cleanup) => cleanup());
}
