import '../../css/components/horizontal-scroll.css';

export function horizontalGeometry(viewport, content, height, available, rtl) {
  const distance = Math.max(0, content - viewport);
  return { distance, x: (rtl ? 1 : -1) * distance, usable: viewport > 0 && distance > 1 && height > 0 && height <= available };
}

export function initializeHorizontalScroll(root, options, gsap, ScrollTrigger) {
  if (root.parentElement?.closest('[data-rdct-horizontal-owner]')) return () => {};
  const doc = root.ownerDocument; const view = doc.defaultView;
  const host = [...root.children].find((node) => node.matches('.e-con-inner, .elementor-container, .elementor-widget-wrap')) || root;
  const query = view.matchMedia(`(min-width:${options.horizontal_min_width}px) and (prefers-reduced-motion:no-preference)`);
  let alive = true; let context; let restore; let frame = 0; let force = false; let signature = ''; let focused = false;
  const savedOwner = root.getAttribute('data-rdct-horizontal-owner');
  root.setAttribute('data-rdct-horizontal-owner','true');
  const resizeObserver = view.ResizeObserver ? new view.ResizeObserver(() => queue(false)) : null;
  const mutationObserver = new view.MutationObserver(() => queue(true));
  const offset = () => {
    let top = options.horizontal_offset;
    const toolbar = doc.getElementById('wpadminbar');
    if (toolbar && view.getComputedStyle(toolbar).position === 'fixed') top += toolbar.getBoundingClientRect().height;
    let header = 0;
    for (const node of doc.querySelectorAll('.rdct-bld--sticky-all, .rdct-bld--sticky-desktop')) {
      const style = view.getComputedStyle(node);
      if (style.position === 'sticky' || style.position === 'fixed') header = Math.max(header,node.getBoundingClientRect().height);
    }
    return top + header;
  };
  const reset = () => {
    mutationObserver.disconnect(); resizeObserver?.disconnect(); context?.revert(); context = null; restore?.(); restore = null;
    root.classList.remove('rdct-hs');
  };
  const observe = () => {
    const track = root.querySelector('.rdct-hs__track');
    signature = `${root.clientWidth}:${view.innerHeight}:${track?.scrollWidth || 0}:${root.getBoundingClientRect().height}:${track?.scrollHeight || 0}:${offset()}`;
    mutationObserver.observe(host,{childList:true,subtree:true});
    resizeObserver?.observe(root);
    for (const node of root.querySelectorAll('.rdct-hs__panel')) resizeObserver?.observe(node);
  };
  const rebuild = () => {
    reset();
    if (!alive || !root.isConnected || !query.matches || focused || root.matches(':focus-within')) { observe(); return; }
    const items = [...host.children].filter((node) => !node.matches('script, style, template, [hidden]'));
    if (items.length < 2) { observe(); return; }
    const viewport = doc.createElement('div'); viewport.className = 'rdct-hs__viewport';
    const track = doc.createElement('div'); track.className = 'rdct-hs__track';
    const direction = view.getComputedStyle(root).direction;
    const rtl = options.horizontal_direction === 'right' || (options.horizontal_direction === 'auto' && direction === 'rtl');
    track.style.direction = rtl ? 'rtl' : 'ltr';
    track.style.setProperty('--rdct-hs-width', `${options.horizontal_width}%`);
    track.style.setProperty('--rdct-hs-gap',`${options.horizontal_gap}px`);
    track.style.setProperty('--rdct-hs-align',({start:'flex-start',center:'center',end:'flex-end',stretch:'stretch'})[options.horizontal_align]);
    const entries = items.map((item) => {
      const placeholder = doc.createComment('rdct-horizontal'); item.before(placeholder);
      const panel = doc.createElement('div'); panel.className = 'rdct-hs__panel'; panel.style.direction = direction; panel.append(item); track.append(panel);
      return {item,placeholder,panel};
    });
    viewport.append(track); host.append(viewport); root.classList.add('rdct-hs');
    restore = () => { for (const {item,placeholder,panel} of entries) { if (placeholder.isConnected) { if (item.parentNode === panel) placeholder.replaceWith(item); else placeholder.remove(); } } viewport.remove(); };
    const distance = () => Math.max(0,track.scrollWidth-viewport.clientWidth);
    const geometry = horizontalGeometry(viewport.clientWidth,track.scrollWidth,root.getBoundingClientRect().height,view.innerHeight-offset(),rtl);
    if (!geometry.usable) { reset(); observe(); return; }
    context = gsap.context(() => {
      gsap.to(track,{x:() => (rtl ? 1 : -1)*distance(),ease:'none',scrollTrigger:{
        trigger:root, pin:root, pinSpacing:true, start:() => `top ${offset()}px`, end:() => `+=${distance()}`,
        scrub:options.horizontal_scrub, anticipatePin:1, invalidateOnRefresh:true,
      }});
    });
    signature = `${root.clientWidth}:${view.innerHeight}:${track.scrollWidth}:${root.getBoundingClientRect().height}`;
    observe(); ScrollTrigger.refresh(true);
  };
  function queue(required = false) {
    force ||= required;
    if (frame || !alive) return;
    frame = view.requestAnimationFrame(() => {
      frame = 0;
      const track = root.querySelector('.rdct-hs__track');
      const next = `${root.clientWidth}:${view.innerHeight}:${track?.scrollWidth || 0}:${root.getBoundingClientRect().height}:${track?.scrollHeight || 0}:${offset()}`;
      if (force || next !== signature) { force = false; rebuild(); }
    });
  }
  const resize = () => queue(false);
  const change = () => queue(true);
  const focus = (event) => { if (focused) return; focused = true; reset(); observe(); event.target.focus({preventScroll:true}); event.target.scrollIntoView({block:'nearest',inline:'nearest'}); };
  root.addEventListener('focusin',focus); root.addEventListener('load',change,true);
  view.addEventListener('resize',resize); query.addEventListener('change',change);
  rebuild();
  return () => {
    if (!alive) return; alive = false;
    if (frame) view.cancelAnimationFrame(frame);
    root.removeEventListener('focusin',focus); root.removeEventListener('load',change,true);
    view.removeEventListener('resize',resize); query.removeEventListener('change',change);
    reset();
    if (savedOwner === null) root.removeAttribute('data-rdct-horizontal-owner'); else root.setAttribute('data-rdct-horizontal-owner',savedOwner);
  };
}
