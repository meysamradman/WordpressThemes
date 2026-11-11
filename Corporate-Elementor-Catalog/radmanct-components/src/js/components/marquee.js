import { responsiveBreakpoints } from './responsive.js';

const instances = new WeakMap();
let copyId = 0;

export function marqueeDirection(value, rtl) {
  if (!['start','end','left','right'].includes(value)) value = 'start';
  return value === 'right' || value === 'start' && rtl || value === 'end' && !rtl ? 1 : -1;
}

export function marqueeSpeed(options, width) {
  const number = (value, fallback) => value != null && value !== '' && Number.isFinite(Number(value)) ? Math.min(500,Math.max(5,Number(value))) : fallback;
  let speed = number(options.marquee_speed,60);
  const breakpoints = responsiveBreakpoints(options);
  if (width < breakpoints.desktop && Number(options.marquee_speed_tablet) >= 0) speed = number(options.marquee_speed_tablet,speed);
  if (width < breakpoints.tablet && Number(options.marquee_speed_mobile) >= 0) speed = number(options.marquee_speed_mobile,speed);
  return speed;
}

export function visualCopy(source) {
  const clone = source.cloneNode(true); const prefix = `rdct-marquee-copy-${++copyId}-`; const ids = new Map();
  const nodes = [clone,...clone.querySelectorAll('*')];
  for (const node of nodes) if (node.id) {ids.set(node.id,prefix+ids.size); node.id = ids.get(node.id);}
  for (const node of nodes) {
    for (const attr of [...node.attributes]) {
      if (/^on/i.test(attr.name) || /^data-rdct-/.test(attr.name) || ['data-id','data-element_type','autofocus','name'].includes(attr.name)) {node.removeAttribute(attr.name); continue;}
      let value = attr.value.replace(/url\(\s*['"]?#([^)'"\s]+)['"]?\s*\)/g,(match,id) => ids.has(id) ? `url(#${ids.get(id)})` : match);
      if (['href','xlink:href'].includes(attr.name) && value.startsWith('#') && ids.has(value.slice(1))) value = '#'+ids.get(value.slice(1));
      if (['for','aria-labelledby','aria-describedby','aria-controls','headers','list'].includes(attr.name)) value = value.split(/\s+/).map(id => ids.get(id) ?? id).join(' ');
      if (value !== attr.value) {if (attr.namespaceURI) node.setAttributeNS(attr.namespaceURI,attr.name,value); else node.setAttribute(attr.name,value);}
    }
    if (node.matches('a,button,input,select,textarea,[tabindex],[contenteditable]')) node.setAttribute('tabindex','-1');
  }
  clone.querySelectorAll('script,style,iframe,object,embed').forEach(node => node.remove());
  clone.setAttribute('aria-hidden','true'); clone.setAttribute('data-rdct-marquee-copy',''); clone.inert = true;
  return clone;
}

export function initializeMarquee(root) {
  if (instances.has(root)) return instances.get(root);
  const viewport = root.querySelector(':scope > .rdct-marquee__viewport');
  const track = viewport?.querySelector(':scope > .rdct-marquee__track');
  const original = track?.querySelector(':scope > .rdct-marquee__group');
  if (!original) return () => {};
  let options; try {options = JSON.parse(root.dataset.rdctMarquee);} catch {return () => {};}
  if (!options || typeof options !== 'object') return () => {};
  const doc = root.ownerDocument; const view = doc.defaultView;
  if (!track.animate || !view.ResizeObserver || !view.IntersectionObserver) return () => {};
  const unsafe = () => Boolean(original.querySelector('a[href],form,input,textarea,select,button,iframe,video,audio,[contenteditable],[data-rdct-carousel],[data-rdct-curtain],[data-rdct-flipbox]') || [...original.querySelectorAll('*')].some(node => node.localName.includes('-')));
  if (unsafe()) return () => {};
  const units = [...original.children]; const widths = units.map(node => [node.style.getPropertyValue('width'),node.style.getPropertyPriority('width')]);
  const reduced = view.matchMedia('(prefers-reduced-motion: reduce)'); const play = root.querySelector(':scope > [data-marquee-play]');
  let animation; let duration = 0; let phase = 0; let frame = 0; let signature = ''; let alive = true; let visible = false; let paused = false; let hovering = false; let focused = false; let focusContent = false;
  const copies = [];
  const clear = () => {
    if (animation) {phase = duration ? (Number(animation.currentTime) || 0) % duration / duration : 0; animation.cancel(); animation = null;}
    copies.splice(0).forEach(node => node.remove()); root.classList.remove('is-marquee-running');
    if (play) play.hidden = true;
  };
  const sync = () => {
    if (!animation) return;
    if (paused || hovering || focused || !visible || reduced.matches || doc.hidden) animation.pause(); else animation.play();
    if (play) play.setAttribute('aria-pressed',String(!paused));
  };
  const build = () => {
    frame = 0; if (!alive) return;
    if (reduced.matches || focusContent) {clear(); signature = ''; return;}
    const width = viewport.getBoundingClientRect().width;
    if (width < 1 || !units.length) {clear(); signature = ''; return;}
    const breakpoints = responsiveBreakpoints(options);
    const columns = options.columns?.[view.innerWidth < breakpoints.tablet ? 2 : view.innerWidth < breakpoints.desktop ? 1 : 0];
    const gap = Math.max(0,Math.min(300,Number(options.marquee_gap) || 0));
    if (Number.isFinite(columns) && columns > 0) units.forEach(node => {node.style.width = Math.max(32,(width-(columns-1)*gap)/columns)+'px';});
    const direction = view.getComputedStyle(root).direction; original.style.direction = direction;
    const unitWidths = units.map(node => node.getBoundingClientRect().width);
    const speed = marqueeSpeed(options,view.innerWidth);
    const next = JSON.stringify([width,unitWidths,speed,direction]);
    if (signature === next && animation) return;
    clear(); signature = next;
    if (unsafe()) return;
    const nodeCount = units.reduce((count,node) => count+node.querySelectorAll('*').length+1,0);
    const baseWidth = original.getBoundingClientRect().width;
    const repeat = Math.ceil((width+gap)/(baseWidth+gap));
    if (!(baseWidth > 0) || repeat > 24 || nodeCount*(repeat*2-1) > 4096) return;
    for (let i = 1; i < repeat; i++) for (const unit of units) {const copy = visualCopy(unit); original.append(copy); copies.push(copy);}
    const distance = original.getBoundingClientRect().width+gap;
    const duplicate = visualCopy(original); track.append(duplicate); copies.push(duplicate);
    const right = marqueeDirection(options.marquee_direction,direction === 'rtl') > 0;
    duration = distance/speed*1000;
    animation = track.animate([{transform:`translateX(${right ? -distance : 0}px)`},{transform:`translateX(${right ? 0 : -distance}px)`}],{duration,iterations:Infinity,easing:'linear'});
    animation.currentTime = phase*duration;
    root.classList.add('is-marquee-running'); root.dataset.marqueeFade = String(Boolean(options.marquee_fade)); if (play) play.hidden = false; sync();
  };
  const queue = () => {if (alive && !frame) frame = view.requestAnimationFrame(build);};
  const enter = () => {hovering = options.marquee_pause !== false; sync();}; const leave = () => {hovering = false; sync();};
  const focus = event => {focused = true; focusContent = event.target !== play; if (focusContent) {clear(); signature = ''; event.target.scrollIntoView?.({block:'nearest',inline:'nearest'});} sync();};
  const blur = event => {if (!root.contains(event.relatedTarget)) {focused = false; focusContent = false; queue(); sync();}};
  const toggle = () => {paused = !paused; sync();};
  const motion = () => {if (reduced.matches) {clear(); signature = '';} else queue();};
  const load = event => {if (original.contains(event.target) && !event.target.closest('[data-rdct-marquee-copy]')) queue();};
  const resize = new view.ResizeObserver(queue); resize.observe(viewport); units.forEach(node => resize.observe(node));
  const intersection = new view.IntersectionObserver(([entry]) => {visible = entry.isIntersecting; sync();}); intersection.observe(root);
  const content = new view.MutationObserver(queue); units.forEach(node => content.observe(node,{subtree:true,childList:true,characterData:true}));
  root.addEventListener('mouseenter',enter); root.addEventListener('mouseleave',leave); root.addEventListener('focusin',focus); root.addEventListener('focusout',blur); root.addEventListener('load',load,true);
  doc.addEventListener('visibilitychange',sync); reduced.addEventListener('change',motion); view.addEventListener('resize',queue,{passive:true}); play?.addEventListener('click',toggle);
  Promise.resolve(doc.fonts?.ready).then(queue,queue); queue();
  const cleanup = () => {
    if (!alive) return; alive = false; if (frame) view.cancelAnimationFrame(frame); clear(); resize.disconnect(); intersection.disconnect(); content.disconnect();
    root.removeEventListener('mouseenter',enter); root.removeEventListener('mouseleave',leave); root.removeEventListener('focusin',focus); root.removeEventListener('focusout',blur); root.removeEventListener('load',load,true);
    doc.removeEventListener('visibilitychange',sync); reduced.removeEventListener('change',motion); view.removeEventListener('resize',queue); play?.removeEventListener('click',toggle);
    original.style.removeProperty('direction'); units.forEach((node,i) => {if (widths[i][0]) node.style.setProperty('width',...widths[i]); else node.style.removeProperty('width');}); delete root.dataset.marqueeFade; instances.delete(root);
  };
  instances.set(root,cleanup); return cleanup;
}

export function initializeMarquees(scope = document) {
  const roots = scope.matches?.('[data-rdct-marquee]') ? [scope] : [...scope.querySelectorAll('[data-rdct-marquee]')].filter(root => !root.closest('[data-rdct-marquee-copy]'));
  const cleanups = roots.map(initializeMarquee); return () => cleanups.forEach(cleanup => cleanup());
}

