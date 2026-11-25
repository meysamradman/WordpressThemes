import {initializeCarousel} from '../ui/carousel.js';
import {initializeExclusiveVideos} from '../ui/video-player.js';
import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeMusicPlayer} from '../ui/music-player.js';

const FEATURE_INTERVAL = 8000;

function initializeFeatured(feature) {
 if (!feature) return () => {};
 const nav = feature.querySelector('[data-rdc-feature-nav]');
 if (!nav) return () => {};

 const track = feature.querySelector('[data-rdc-feature-track]');
 const slides = [...track.children];
 const dots = [...nav.querySelectorAll('[data-rdc-feature-dot]')];
 const doc = feature.ownerDocument;
 const view = doc.defaultView;
 const reduced = view.matchMedia('(prefers-reduced-motion: reduce)');
 const keyboardFocus = element => !doc.documentElement.hasAttribute('data-rdc-pointer') &&
  element?.matches(':focus-visible');
 let current = -1;
 let timer;
 let rotating = !reduced.matches && !(feature.contains(doc.activeElement) && keyboardFocus(doc.activeElement));
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
  dots.forEach((dot, position) => {
   if (position === current) dot.setAttribute('aria-current', 'true');
   else dot.removeAttribute('aria-current');
  });
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
 const onClick = event => {
  const dot = event.target.closest('[data-rdc-feature-dot]');
  if (dot) move(Number(dot.dataset.rdcFeatureDot));
 };
 const onKey = event => {
  if (event.target !== track || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) return;
  if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
   event.preventDefault();
   move(current + (event.key === 'ArrowRight' ? 1 : -1) * direction());
  }
 };
 const stopRotation = () => {
  rotating = false;
  view.clearTimeout(timer);
 };
 const onFocus = event => { if (keyboardFocus(event.target)) stopRotation(); };
 const onFocusOut = event => syncSlides(event.relatedTarget);
 const onEnter = event => { if (event.pointerType === 'mouse') { hovering = true; schedule(); } };
 const onLeave = event => { if (event.pointerType === 'mouse') { hovering = false; schedule(); } };
 const onTouch = () => { touching = true; schedule(); };
 const onTouchEnd = event => {
  if (!touching || event.touches.length) return;
  touching = false;
  schedule();
 };
 const onPreference = () => { if (reduced.matches) stopRotation(); };
 const observer = view.IntersectionObserver && new view.IntersectionObserver(([entry]) => {
  inView = entry.intersectionRatio >= 0.25;
  schedule();
 }, {threshold: 0.25});
 const listeners = {
  click: onClick, keydown: onKey, focusin: onFocus, focusout: onFocusOut,
  pointerenter: onEnter, pointerleave: onLeave,
  touchstart: onTouch,
 };

 nav.hidden = false;
 syncPosition();
 observer?.observe(feature);
 track.addEventListener('scroll', syncPosition, {passive: true});
 doc.addEventListener('visibilitychange', schedule);
 doc.addEventListener('touchend', onTouchEnd, {capture:true,passive:true});
 doc.addEventListener('touchcancel', onTouchEnd, {capture:true,passive:true});
 reduced.addEventListener('change', onPreference);
 for (const [event, listener] of Object.entries(listeners)) {
  feature.addEventListener(event, listener, {passive: event.startsWith('touch')});
 }
 return () => {
  view.clearTimeout(timer);
  observer?.disconnect();
  track.removeEventListener('scroll', syncPosition);
  doc.removeEventListener('visibilitychange', schedule);
  doc.removeEventListener('touchend', onTouchEnd, true);
  doc.removeEventListener('touchcancel', onTouchEnd, true);
  reduced.removeEventListener('change', onPreference);
  for (const [event, listener] of Object.entries(listeners)) feature.removeEventListener(event, listener);
  for (const slide of slides) slide.inert = false;
  nav.hidden = true;
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
