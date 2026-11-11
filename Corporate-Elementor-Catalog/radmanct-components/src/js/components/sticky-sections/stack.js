import {createStickyScene, pinWithinGroup} from './scene.js';

export function mount(root, {gsap, ScrollTrigger}) {
  return createStickyScene(root, gsap, ScrollTrigger, ({items, inners, top, available}) => {
    const zoom = root.dataset.mode === 'zoom';
    const step = zoom ? 0 : Math.max(0, Math.min(120, Number(root.dataset.spacer) || 0));
    const minimum = Math.max(.5, Math.min(1, Number(root.dataset.minScale) || .8));
    root.style.setProperty('--rdct-ss-step', `${step}px`);
    items.forEach((item, index) => {
      if (inners[index].offsetHeight + index * step > available) item.classList.add('is-tall');
      if (index === items.length - 1 || item.classList.contains('is-tall')) return;
      pinWithinGroup(ScrollTrigger, root, item, item, top + index * step);
      const scale = zoom ? 1 - (1 - minimum) * .35 : minimum + index / Math.max(1, items.length - 1) * (1 - minimum);
      gsap.to(inners[index], {scale, ease:'none', scrollTrigger:{trigger:items[index + 1], start:()=>`top ${top + (index + 1) * step}`, end:()=>`top ${top - inners[index].offsetHeight}`, scrub:.25, invalidateOnRefresh:true}});
    });
  });
}
