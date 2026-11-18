import {initializeCarousel} from '../ui/carousel.js';
import {initializeExclusiveVideos} from '../ui/video-player.js';
import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeMusicPlayer} from '../ui/music-player.js';

const FEATURE_INTERVAL = 8000;

function initializeFeatured(feature) {
 if (!feature) return () => {};
 const track = feature.querySelector('[data-rdc-feature-track]');
 const rotation = feature.querySelector('[data-rdc-feature-rotation]');
 if (!rotation) return () => {};

 const nav = feature.querySelector('[data-rdc-feature-nav]');
 const toggle = rotation.querySelector('[data-rdc-feature-toggle]');
 const playIcon = toggle.querySelector('[data-rdc-feature-play]');
 const pauseIcon = toggle.querySelector('[data-rdc-feature-pause]');
 const position = rotation.querySelector('[data-rdc-feature-position]');
 const slides = [...track.children];
 const doc = feature.ownerDocument;
 const view = doc.defaultView;
 const reduced = view.matchMedia('(prefers-reduced-motion: reduce)');
 const format = new Intl.NumberFormat(doc.documentElement.lang || 'fa-IR');
 let current = -1;
 let timer;
 let rotating = !reduced.matches && !feature.contains(doc.activeElement);
 let hovering = view.matchMedia('(hover: hover)').matches && feature.matches(':hover');
 let touching = false;
 let inView = !view.IntersectionObserver;

 const schedule = () => {
  view.clearTimeout(timer);
  if (rotating && inView && !doc.hidden && !hovering && !touching) {
   timer = view.setTimeout(() => move(current + 1), FEATURE_INTERVAL);
  }
 };
 const syncSlides = (focused = doc.activeElement) => {
  slides.forEach((slide, index) => {
   slide.inert = index !== current && !slide.contains(focused);
  });
 };
 const syncPosition = () => {
  if (!track.clientWidth) return;
  const index = Math.min(slides.length - 1, Math.round(Math.abs(track.scrollLeft) / track.clientWidth));
  if (current === index) return;
  current = index;
  position.textContent = `${format.format(index + 1)} از ${format.format(slides.length)}`;
  syncSlides();
  schedule();
 };
 const direction = () => view.getComputedStyle(track).direction === 'rtl' ? -1 : 1;
 const move = index => {
  const next = (index + slides.length) % slides.length;
  track.scrollTo({
   left: direction() * next * track.clientWidth,
   behavior: reduced.matches ? 'instant' : 'smooth',
  });
  schedule();
 };
 const syncRotation = () => {
  const label = rotating ? 'توقف حرکت خودکار اسلایدها' : 'شروع حرکت خودکار اسلایدها';
  toggle.setAttribute('aria-label', label);
  toggle.title = label;
  toggle.disabled = reduced.matches;
  playIcon.hidden = rotating;
  pauseIcon.hidden = !rotating;
  position.setAttribute('aria-live', rotating ? 'off' : 'polite');
  schedule();
 };
 const onClick = event => {
  if (toggle.contains(event.target)) {
   rotating = !rotating;
   syncRotation();
  } else if (event.target.closest('[data-rdc-feature-prev]')) {
   move(current - 1);
  } else if (event.target.closest('[data-rdc-feature-next]')) {
   move(current + 1);
  }
 };
 const onKey = event => {
  if (event.target !== track || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) return;
  if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
   event.preventDefault();
   move(current + (event.key === 'ArrowRight' ? 1 : -1) * direction());
  }
 };
 const stopOnFocus = event => {
  if (!rotating || (event.target === toggle && !toggle.matches(':focus-visible'))) return;
  rotating = false;
  syncRotation();
 };
 const onFocusOut = event => syncSlides(event.relatedTarget);
 const onEnter = event => { if (event.pointerType === 'mouse') { hovering = true; schedule(); } };
 const onLeave = event => { if (event.pointerType === 'mouse') { hovering = false; schedule(); } };
 const onTouch = () => { touching = true; schedule(); };
 const onTouchEnd = event => { touching = event.touches.length > 0; schedule(); };
 const onPreference = () => { if (reduced.matches) rotating = false; syncRotation(); };
 const observer = view.IntersectionObserver && new view.IntersectionObserver(([entry]) => {
  inView = entry.intersectionRatio >= 0.25;
  schedule();
 }, {threshold: 0.25});
 const listeners = {
  click: onClick, keydown: onKey, focusin: stopOnFocus, focusout: onFocusOut,
  pointerenter: onEnter, pointerleave: onLeave,
  touchstart: onTouch, touchend: onTouchEnd, touchcancel: onTouchEnd,
 };

 rotation.hidden = nav.hidden = false;
 syncRotation();
 syncPosition();
 observer?.observe(feature);
 track.addEventListener('scroll', syncPosition, {passive: true});
 doc.addEventListener('visibilitychange', schedule);
 reduced.addEventListener('change', onPreference);
 for (const [event, listener] of Object.entries(listeners)) {
  feature.addEventListener(event, listener, {passive: event.startsWith('touch')});
 }
 return () => {
  view.clearTimeout(timer);
  observer?.disconnect();
  track.removeEventListener('scroll', syncPosition);
  doc.removeEventListener('visibilitychange', schedule);
  reduced.removeEventListener('change', onPreference);
  for (const [event, listener] of Object.entries(listeners)) feature.removeEventListener(event, listener);
  for (const slide of slides) slide.inert = false;
  rotation.hidden = nav.hidden = true;
 };
}

const page=createPageLifecycle(window),root=document.querySelector('[data-rdc-music-home]');
if(root)page.mountRoot(root,'music-home',()=>({}),()=>{
 const releaseRails=[...root.querySelectorAll('[data-rdc-rail]')].map(initializeCarousel);
 const releasePlayer=initializeMusicPlayer(root);
 const releaseFeatured=initializeFeatured(root.querySelector('[data-rdc-feature]'));
 const releaseVideos=initializeExclusiveVideos(root);
 return()=>{for(const release of releaseRails)release();releasePlayer();releaseFeatured();releaseVideos();};
});
