import {observeSizes} from '../runtime/observers.js';
import {createRefreshRequest} from '../runtime/refresh.js';
import {getSmoothScroller} from '../runtime/scroll-state.js';
import {loadAnimation} from '../motion.js';
import {reportRuntimeError} from '../runtime/lifecycle.js';

const query = '(min-width:992px) and (min-height:680px) and (hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference)';

export function mountServiceRows(root) {
  const view = root.ownerDocument.defaultView;
  let active = true;
  let release;
  loadAnimation().then(vendor => {
    if (active && root.isConnected) release = initializeServiceRows(root, vendor);
  }).catch(error => {
    if (active) reportRuntimeError('service-sticky-rows', error, view);
  });
  return () => { active = false; release?.(); };
}

function initializeServiceRows(root, {gsap, ScrollTrigger}) {
  const doc = root.ownerDocument;
  const view = doc.defaultView;
  const list = root.querySelector('.rdc-stk-rows__list');
  const rows = [...list.children].slice(0, -1);
  if (!rows.length) return;

  gsap.registerPlugin(ScrollTrigger);
  const media = gsap.matchMedia();
  media.add(query, () => {
    const refresh = createRefreshRequest(view, ScrollTrigger);
    const toolbar = doc.getElementById('wpadminbar');
    const offset = index => {
      const adminOffset = toolbar && view.getComputedStyle(toolbar).position === 'fixed' ? toolbar.offsetHeight : 0;
      const step = Number.parseFloat(view.getComputedStyle(root).getPropertyValue('--rdc-stk-step'));
      return adminOffset + 28 + index * step;
    };
    let pins;
    const mountPins = () => {
      pins?.revert();
      pins = gsap.context(() => {
        const pinType = getSmoothScroller(doc) ? 'transform' : undefined;
        rows.forEach((row, index) => ScrollTrigger.create({
          trigger: row,
          start: () => `top ${offset(index)}`,
          endTrigger: list,
          end: () => `bottom ${row.offsetHeight + offset(index)}`,
          pin: true,
          pinType,
          pinSpacing: false,
          invalidateOnRefresh: true,
        }));
      }, root);
      refresh.request();
    };
    mountPins();
    const unobserve = observeSizes(view, [list, ...rows], () => refresh.request());
    doc.addEventListener('rdc:scr-mode', mountPins);
    return () => {
      doc.removeEventListener('rdc:scr-mode', mountPins);
      unobserve();
      pins.revert();
      // Other owners need the updated geometry after pins have been removed.
      if (root.isConnected) refresh.flush();
      refresh.dispose();
    };
  });
  return () => media.revert();
}
