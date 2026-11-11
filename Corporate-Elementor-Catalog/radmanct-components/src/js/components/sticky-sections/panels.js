import {createStickyScene, pinWithinGroup} from './scene.js';

export function mount(root, {gsap, ScrollTrigger}) {
  return createStickyScene(root, gsap, ScrollTrigger, ({items, inners, top}) => {
    items.slice(0, -1).forEach((item, index) => {
      if (item.classList.contains('is-tall')) return;
      pinWithinGroup(ScrollTrigger, root, item, inners[index], top);
    });
  });
}
