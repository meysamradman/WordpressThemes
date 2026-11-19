import {loadGsap} from '../motion.js';
import {reportRuntimeError} from '../runtime/lifecycle.js';

export function initializeTextMarquee(root) {
  const doc = root.ownerDocument;
  const view = doc.defaultView;
  const preference = view.matchMedia('(prefers-reduced-motion:reduce)');
  const win = root.querySelector('.rdc-mtx__win');
  const track = root.querySelector('.rdc-mtx__trk');
  const group = track.firstElementChild;
  const button = root.querySelector('[data-rdc-mtx-pause]');
  let alive = true, visible = !view.IntersectionObserver, pending = false, failed = false;
  let paused = false, focused = false, context, loop, clone;

  const teardown = () => {
    context?.revert();
    context = loop = undefined;
    clone?.remove();
    root.classList.remove('is-enh');
    button.hidden = true;
  };

  async function start() {
    pending = true;
    try {
      const {gsap} = await loadGsap();
      if (!alive || preference.matches || doc.hidden || !visible) return;
      clone = group.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      clone.inert = true;
      track.append(clone);
      root.classList.add('is-enh');
      context = gsap.context(() => {
        loop = gsap.fromTo(track, {xPercent:0}, {xPercent:-50,duration:30,ease:'none',repeat:-1,paused:true});
      }, root);
      button.hidden = false;
      if (win.contains(doc.activeElement)) focus({target:doc.activeElement});
      sync();
    } catch (error) {
      failed = true;
      teardown();
      if (alive) reportRuntimeError('text-marquee', error, view);
    } finally { pending = false; }
  }

  function sync() {
    if (!alive) return;
    if (preference.matches) { teardown(); return; }
    if (loop) { loop.paused(paused || focused || !visible || doc.hidden); return; }
    if (visible && !doc.hidden && !pending && !failed) void start();
  }

  function toggle() {
    paused = !paused;
    button.setAttribute('aria-pressed', String(paused));
    sync();
  }

  function focus(event) {
    if (!loop) return;
    focused = true;
    loop.pause(0);
    if (event.target !== win) {
      const bounds = event.target.getBoundingClientRect();
      const viewport = win.getBoundingClientRect();
      if (bounds.left < viewport.left) win.scrollLeft += bounds.left - viewport.left;
      if (bounds.right > viewport.right) win.scrollLeft += bounds.right - viewport.right;
    }
  }

  function blur(event) {
    if (win.contains(event.relatedTarget)) return;
    focused = false;
    win.scrollLeft = 0;
    sync();
  }

  const observer = view.IntersectionObserver ? new view.IntersectionObserver(([entry]) => {
    visible = entry.isIntersecting;
    sync();
  }) : null;
  observer?.observe(root);
  button.addEventListener('click', toggle);
  win.addEventListener('focusin', focus);
  win.addEventListener('focusout', blur);
  doc.addEventListener('visibilitychange', sync);
  preference.addEventListener('change', sync);
  sync();
  return () => {
    alive = false;
    observer?.disconnect();
    button.removeEventListener('click', toggle);
    win.removeEventListener('focusin', focus);
    win.removeEventListener('focusout', blur);
    doc.removeEventListener('visibilitychange', sync);
    preference.removeEventListener('change', sync);
    teardown();
  };
}
