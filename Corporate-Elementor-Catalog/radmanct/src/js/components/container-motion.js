import { reportRuntimeError } from '../../../../shared/frontend/lifecycle.js';

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { motionOptions, motionTargets, revealClip } from './container-motion-options.js';

const instances = new WeakMap();
export function initializeContainerMotion(root, raw, preview = false, play = false) {
  instances.get(root)?.();
  if (!root.isConnected || root.hasAttribute('data-rdct-native-motion')) return () => {};
  const options = motionOptions(raw);
  gsap.registerPlugin(ScrollTrigger);
  const media = gsap.matchMedia();
  media.add('(prefers-reduced-motion: no-preference)', () => {
    let focus; let horizontalCleanup; let alive = true;
    if (options.horizontal && !preview) import('./horizontal-scroll.js').then(({initializeHorizontalScroll}) => {
      if (alive && root.isConnected) horizontalCleanup = initializeHorizontalScroll(root, options, gsap, ScrollTrigger);
    }).catch((error) => reportRuntimeError('horizontal-scroll', error));
    if (options.reveal && !options.horizontal && (!preview || play) && !root.matches(':focus-within')) {
      const style = root.ownerDocument.defaultView.getComputedStyle(root);
      const original = style.clipPath;
      const reveal = gsap.fromTo(root, { clipPath: revealClip(options.reveal_direction, style.direction === 'rtl') }, {
        clipPath: original === 'none' ? 'inset(0 0% 0 0%)' : original,
        ease: 'none', duration: preview ? 1.2 : 1,
        ...(preview ? {} : { scrollTrigger: { trigger: root, start: `top ${options.reveal_start}%`, end: `top ${options.reveal_end}%`, scrub: options.reveal_scrub, invalidateOnRefresh: true } }),
      });
      focus = () => {
        reveal.scrollTrigger?.kill(); reveal.kill();
        gsap.set(root, { clipPath: original });
      };
      root.addEventListener('focusin', focus);
    }
    if (options.animate && !options.horizontal && !(options.parallax && options.targets === 'self')) {
      const targets = motionTargets(root, options);
      if (targets.length) gsap.fromTo(targets, options.from, {
        ...options.to, duration: options.duration, delay: options.delay, ease: options.ease,
        stagger: { each: options.stagger, from: options.order },
        ...(preview ? {} : { scrollTrigger: { trigger: root, start: 'top 85%', once: true } }),
      });
    }
    if (options.parallax && !options.horizontal) gsap.fromTo(root, options.parallax_from, {
      ...options.parallax_to, ease: options.parallax_ease,
      ...(preview && play ? {duration:options.duration} : {scrollTrigger:{trigger:root,start:options.parallax_start,end:options.parallax_end,scrub:options.scrub,invalidateOnRefresh:true}}),
    });
    return () => { alive = false; horizontalCleanup?.(); if (focus) root.removeEventListener('focusin', focus); };
  });
  let active = true;
  const cleanup = () => { if (!active) return; active = false; media.revert(); if (instances.get(root) === cleanup) instances.delete(root); };
  instances.set(root, cleanup);
  return cleanup;
}
