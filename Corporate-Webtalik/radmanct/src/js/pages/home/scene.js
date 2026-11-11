import {observeSizes} from '../../runtime/observers.js';
import {createRefreshRequest} from '../../runtime/refresh.js';

export function getStickyItems(root) {
  const list = root.querySelector(':scope > .rdc-ss__list') || root;
  return [...list.children].filter((node) => node.classList.contains('rdc-ss__itm'));
}

export function createStickyScene(root, gsap, ScrollTrigger, setup, onDisable=()=>{}) {
  const view = root.ownerDocument.defaultView;
  const items = getStickyItems(root);
  const inners = items.map((item) => item.querySelector(':scope > .rdc-ss__inr'));
  if (items.length < (root.dataset.mode === 'split' ? 1 : 2) || inners.some((node) => !node)) return () => {};
  const width = Number.isFinite(Number(root.dataset.minWidth)) ? Math.max(0, Math.min(1920, Number(root.dataset.minWidth))) : 992;
  const media = view.matchMedia(`(min-width: ${width}px) and (prefers-reduced-motion: no-preference)`);
  const headers = [...root.ownerDocument.querySelectorAll('.rdc-bld--sticky-all, .rdc-bld--sticky-desktop, #wpadminbar')];
  let alive = true; let focused = false; let frame = 0; let context; let releaseSetup; let signature = '';
  const refresh=createRefreshRequest(view,ScrollTrigger);
  const reset = () => {
    // Reverting a pin reparents DOM nodes and can blur a keyboard user.
    const active = root.ownerDocument.activeElement;
    const restoreFocus = active && root.contains(active);
    context?.revert(); context = null; releaseSetup?.(); releaseSetup=undefined;
    root.classList.remove('is-enh');
    root.style.removeProperty('--rdc-ss-top'); root.style.removeProperty('--rdc-ss-step');
    items.forEach((item) => item.classList.remove('is-tall'));
    if (restoreFocus && root.ownerDocument.activeElement !== active) active.focus({preventScroll:true});
  };
  const rebuild = () => {
    frame = 0;
    if (!alive) return;
    // Mobile toolbar height changes cannot affect an already disabled desktop pin.
    if(!media.matches&&!context&&signature)return;
    const headerHeight = Math.max(0, ...headers.filter((node) => node.id !== 'wpadminbar' && ['fixed','sticky'].includes(view.getComputedStyle(node).position)).map((node) => node.getBoundingClientRect().height));
    const toolbar = headers.find((node) => node.id === 'wpadminbar');
    const toolbarHeight = toolbar && view.getComputedStyle(toolbar).position === 'fixed' ? toolbar.getBoundingClientRect().height : 0;
    const top = Math.max(0, Math.min(300, Number(root.dataset.offset) || 0)) + headerHeight + toolbarHeight;
    const geometry = () => [media.matches, focused, view.innerWidth, view.innerHeight, top, ...inners.map((node) => node.offsetHeight), root.querySelector('.rdc-ss__fix')?.offsetHeight].join(':');
    if (geometry() === signature) return;
    const hadScene=!!context;
    const active = focused ? root.ownerDocument.activeElement : null;
    const focusTop = active && root.contains(active) ? active.getBoundingClientRect().top : null;
    reset();
    if (!media.matches || focused || root.parentElement?.closest('[data-rdc-stk-secs].is-enh')) {
      onDisable();signature = geometry(); if(hadScene)refresh.flush();
      if(focusTop!==null)root.ownerDocument.dispatchEvent(new view.CustomEvent('rdc:fcs-aln',{detail:{target:active,top:focusTop}}));
      return;
    }
    gsap.registerPlugin(ScrollTrigger);
    context = gsap.context(() => {
      root.classList.add('is-enh');
      const available = view.innerHeight - top - 24;
      const heights=inners.map(inner=>inner.offsetHeight);
      items.forEach((item,index)=>item.classList.toggle('is-tall',heights[index]>available));
      root.style.setProperty('--rdc-ss-top', `${top}px`);
      const result=setup({root, items, inners, top, available, gsap, ScrollTrigger});
      if(typeof result==='function')releaseSetup=result;
      else if(result===false){root.classList.remove('is-enh');onDisable();}
    }, root);
    signature = geometry();
    // A rebuilt pin changes the flow and the start positions of sections below it.
    refresh.flush();
  };
  const schedule = () => {if (alive && !frame) frame = view.requestAnimationFrame(rebuild);};
  const focus = () => {
    const active = root.ownerDocument.activeElement;
    const next = !!active && root.contains(active) && active.matches(':focus-visible');
    if (next !== focused) {focused = next; schedule();}
  };
  // Read the settled active element, including transfers within the section.
  const blur = () => {view.queueMicrotask(() => {if (alive) focus();});};
  media.addEventListener('change', schedule); view.addEventListener('resize', schedule);
  root.addEventListener('load', schedule, true); root.addEventListener('focusin', focus); root.addEventListener('focusout', blur);
  const releaseSize=observeSizes(view,[...inners,...headers,root.querySelector('.rdc-ss__fix')],schedule);
  root.ownerDocument.fonts?.ready.then(schedule).catch(() => {});
  rebuild();
  return () => {
    if (!alive) return;
    alive = false; if (frame) view.cancelAnimationFrame(frame);
    releaseSize();refresh.dispose(); media.removeEventListener('change', schedule); view.removeEventListener('resize', schedule);
    root.removeEventListener('load', schedule, true); root.removeEventListener('focusin', focus); root.removeEventListener('focusout', blur); reset();
  };
}
