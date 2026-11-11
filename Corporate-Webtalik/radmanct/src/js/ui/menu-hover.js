import {CustomEase} from 'gsap/CustomEase';

let ease;

export function mount(root,{gsap}) {
  gsap.registerPlugin(CustomEase);
  ease ??= CustomEase.create('rdc-menu-roll', '.5,1.5,.35,1');
  const reduced = matchMedia('(prefers-reduced-motion: reduce)');
  const fine = matchMedia('(hover: hover) and (pointer: fine)');
  const listeners = [];
  const entries = [];
  const listen = (target, type, handler) => {
    target.addEventListener(type, handler);
    listeners.push(() => target.removeEventListener(type, handler));
  };
  const context = gsap.context(() => {
    for (const label of root.querySelectorAll('.rdc-menu-roll')) {
      if (label.closest('[data-rdc-menu]') !== root) continue;
      const link = label.closest('a');
      const text = label.querySelector('.rdc-menu-roll__tx');
      const copy = label.querySelector('.rdc-menu-roll__copy');
      if (!link || !text || !copy) continue;
      const hitArea = link.closest?.('li') || link;
      const animation = gsap.timeline({paused: true})
        .to(text, {y: -4, yPercent: -100, duration: .45, ease}, 0)
        .fromTo(copy, {y: 4, yPercent: 100}, {y: 0, yPercent: 0, duration: .45, ease, immediateRender: false}, 0);
      const entry = {animation, pointer: false, focused: link.contains(document.activeElement)};
      entries.push(entry);
      const update = () => {
        if (reduced.matches) animation.pause(0);
        else if (entry.focused || entry.pointer) animation.play();
        else animation.reverse();
      };
      listen(hitArea, 'pointerenter', (event) => { entry.pointer = fine.matches && event.pointerType !== 'touch'; update(); });
      listen(hitArea, 'pointerleave', () => { entry.pointer = false; update(); });
      listen(link, 'focusin', () => { entry.focused = true; update(); });
      listen(link, 'focusout', (event) => { entry.focused = link.contains(event.relatedTarget); update(); });
      update();
    }
  }, root);
  listen(reduced, 'change', () => entries.forEach(({animation}) => animation.pause(0)));
  listen(fine, 'change', () => entries.forEach((entry) => { entry.pointer = false; entry.animation.pause(0); }));
  return () => { listeners.forEach((remove) => remove()); context.revert(); };
}
