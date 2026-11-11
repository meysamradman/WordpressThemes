import {reportRuntimeError} from './lifecycle.js';

export function initializeMediaParallax(root, {frame, target, amount = 12, minWidth = 768, direction = 'up'}, load) {
  const view = root.ownerDocument.defaultView;
  if (!frame || !target || view.elementorFrontend?.isEditMode?.()) return () => {};
  const bounded = (value, fallback, min, max) => Number.isFinite(Number(value)) ? Math.min(max, Math.max(min, Number(value))) : fallback;
  amount = bounded(amount, 12, 1, 30);
  const width = bounded(minWidth, 768, 0, 1920);
  const media = view.matchMedia(`(min-width: ${width}px) and (prefers-reduced-motion: no-preference)`);
  let active = true; let pending = false; let context; let trigger; let refreshFrame = 0;
  const reset = () => {context?.revert(); context = null; trigger = null;};
  const update = async () => {
    if (!active) return;
    if (!media.matches) {reset(); return;}
    if (context || pending) return;
    pending = true;
    try {
      const {gsap, ScrollTrigger} = await load();
      if (!active || !root.isConnected || !media.matches) return;
      gsap.registerPlugin(ScrollTrigger);
      const from = direction === 'down' ? -amount : amount;
      const scale = 1 + amount * .02 + .02;
      context = gsap.context(() => {}, root);
      context.add(() => {
        trigger = gsap.fromTo(target, {yPercent:from, scale}, {yPercent:-from, scale, ease:'none', scrollTrigger:{trigger:frame, start:'top bottom', end:'bottom top', scrub:.5, invalidateOnRefresh:true}}).scrollTrigger;
      });
    } catch (error) {reset(); reportRuntimeError('image-parallax',error,view);}
    finally {pending = false;}
  };
  const refresh = () => {
    if (!active || !context || refreshFrame) return;
    refreshFrame = view.requestAnimationFrame(() => {refreshFrame = 0; if (active && media.matches) trigger?.refresh();});
  };
  const observer = view.ResizeObserver ? new view.ResizeObserver(refresh) : null;
  observer?.observe(frame);
  media.addEventListener('change', update); view.addEventListener('resize', refresh); root.addEventListener('load', refresh, true);
  update();
  return () => {
    if (!active) return;
    active = false; if (refreshFrame) view.cancelAnimationFrame(refreshFrame);
    observer?.disconnect(); media.removeEventListener('change', update); view.removeEventListener('resize', refresh); root.removeEventListener('load', refresh, true); reset();
  };
}
