import {createStickyScene, pinWithinGroup} from './scene.js';

export function mount(root, {gsap, ScrollTrigger}) {
  return createStickyScene(root, gsap, ScrollTrigger, ({top, available}) => {
    const fixed = root.querySelector(':scope > .rdct-ss__aside > .rdct-ss__fixed');
    const list = root.querySelector(':scope > .rdct-ss__list');
    if (!fixed || fixed.offsetHeight > available) return false;
    if (list.offsetHeight <= fixed.offsetHeight) return false;
    pinWithinGroup(ScrollTrigger, root, root, fixed, top);
  });
}
