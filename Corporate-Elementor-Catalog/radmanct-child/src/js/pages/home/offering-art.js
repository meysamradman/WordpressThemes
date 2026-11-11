import { createPageLifecycle, reportRuntimeError } from '../../../../../shared/frontend/lifecycle.js';

export function orbitPoint(turn, track) {
  if (track.points) {
    const t = ((turn % 1) + 1) % 1;
    const position = t * (track.points.length - 1), index = Math.floor(position);
    const a = track.points[index], b = track.points[Math.min(index + 1, track.points.length - 1)];
    return { x: a.x + (b.x - a.x) * (position - index), y: a.y + (b.y - a.y) * (position - index),
      scale: 1, opacity: Math.min(1, t * 12, (1 - t) * 12), front: true };
  }
  if (track.route) {
    const t = ((turn % 1) + 1) % 1, u = 1 - t, p = track.route;
    return {
      x: u ** 3 * p[0] + 3 * u ** 2 * t * p[2] + 3 * u * t ** 2 * p[4] + t ** 3 * p[6],
      y: u ** 3 * p[1] + 3 * u ** 2 * t * p[3] + 3 * u * t ** 2 * p[5] + t ** 3 * p[7],
      scale: .8, opacity: Math.sin(Math.PI * t) ** .5, front: false,
    };
  }
  const phase = turn * Math.PI * 2;
  const tilt = track.tilt * Math.PI / 180;
  const x = track.rx * Math.cos(phase);
  const y = track.ry * Math.sin(phase);
  const depth = (Math.sin(phase) + 1) / 2;
  return {
    x: track.cx + x * Math.cos(tilt) - y * Math.sin(tilt),
    y: track.cy + x * Math.sin(tilt) + y * Math.cos(tilt),
    scale: .78 + depth * .28,
    opacity: .76 + depth * .24,
    front: depth > .5,
  };
}

export function initializeOfferingArt(root, load) {
  const doc = root.ownerDocument;
  const view = doc.defaultView;
  const preference = view.matchMedia('(prefers-reduced-motion: reduce)');
  const parent = root.closest('.rdct-ss__inner');
  let alive = true; let visible = !view.IntersectionObserver; let pending = false; let failed = false;
  let context; let vendor; let tweens = []; let paint = () => {};
  let width = root.clientWidth; let height = root.clientHeight;
  const loadVendor = load || (() => {
    const url = new URL(root.dataset.gsapSrc, view.location.href);
    if (url.origin !== view.location.origin) throw new Error('Animation source must be local');
    return import(/* @vite-ignore */ url.href);
  });
  const active = () => alive && root.isConnected && visible && !doc.hidden && !preference.matches
    && (!parent || Number.parseFloat(parent.style.opacity || '1') > .65);

  function mount(gsap) {
    const tracks = [...root.querySelectorAll('[data-orbit-tilt], [data-orbit-route], [data-orbit-pipe]')].map(node => ({
      cx: Number(node.dataset.orbitCx), cy: Number(node.dataset.orbitCy),
      rx: Number(node.dataset.orbitRx), ry: Number(node.dataset.orbitRy),
      tilt: Number(node.dataset.orbitTilt),
      route: node.dataset.orbitRoute ? node.dataset.orbitRoute.split(',').map(Number) : null,
      points: node.hasAttribute('data-orbit-pipe') ? (() => {
        const length = node.getTotalLength();
        const box = node.ownerSVGElement.viewBox.baseVal;
        return Array.from({ length: 121 }, (_, index) => {
          const point = node.getPointAtLength(length * index / 120);
          return { x: (point.x - box.x) / box.width * 1000, y: (point.y - box.y) / box.height * 625 };
        });
      })() : null,
      duration: Number(node.dataset.orbitDuration) || 0,
    }));
    const states = tracks.map(() => ({ turn: 0 }));
    const orbs = [...root.querySelectorAll('[data-orbit-track]')].map(node => ({
      node, track: Number(node.dataset.orbitTrack), phase: Number(node.dataset.orbitPhase), front: null,
      x: gsap.quickSetter(node, 'x', 'px'), y: gsap.quickSetter(node, 'y', 'px'),
      scale: gsap.quickSetter(node, 'scale'), opacity: gsap.quickSetter(node, 'opacity'),
    })).filter(orb => tracks[orb.track]);
    context = gsap.context(() => {
      gsap.set(orbs.map(orb => orb.node), { left: 0, top: 0, xPercent: -50, yPercent: -50, zIndex: (_, node) => node.style.zIndex });
      paint = (trackIndex = null) => {
        for (const orb of orbs) {
          if (trackIndex !== null && orb.track !== trackIndex) continue;
          const point = orbitPoint(states[orb.track].turn + orb.phase, tracks[orb.track]);
          orb.x(point.x / 1000 * width); orb.y(point.y / 625 * height);
          orb.scale(point.scale); orb.opacity(point.opacity);
          if (orb.front !== point.front) { orb.node.style.zIndex = point.front ? '4' : '1'; orb.front = point.front; }
        }
      };
      tweens = states.map((state, index) => gsap.to(state, {
        turn: tracks[index].route || tracks[index].points ? 1 : index ? -1 : 1, duration: tracks[index].duration || (index ? 42 : 34), repeat: -1, ease: 'none', paused: true, onUpdate: () => paint(index),
      }));
      const sheen = root.querySelector('[data-art-sheen]');
      if (sheen) tweens.push(gsap.fromTo(sheen, { x: 0 }, { x: 260, duration: 3.5, repeat: -1, repeatDelay: 5, ease: 'sine.inOut', paused: true }));
      for (const [index, label] of [...root.querySelectorAll('[data-art-label]')].entries()) {
        tweens.push(gsap.fromTo(label, { y: 3, opacity: .5 }, { y: 0, opacity: 1, duration: 1.6, delay: Number(label.dataset?.artLabelDelay ?? index * .22),
          repeat: -1, yoyo: true, repeatDelay: 4, ease: 'sine.inOut', paused: true }));
      }
      paint();
    }, root);
  }

  async function sync() {
    if (!alive) return;
    if (preference.matches && context) {
      context.revert(); context = null; tweens = []; paint = () => {};
    }
    const running = active();
    for (const tween of tweens) tween.paused(!running);
    if (!running || context || pending || failed) return;
    pending = true;
    try {
      vendor ||= await loadVendor();
      if (active()) { mount(vendor.gsap); for (const tween of tweens) tween.play(); }
    } catch (error) {
      failed = true;
      context?.revert(); context = null; tweens = []; paint = () => {};
      reportRuntimeError('client-offering-art', error, view);
    } finally { pending = false; }
  }

  const observer = view.IntersectionObserver ? new view.IntersectionObserver(entries => {
    visible = entries.some(entry => entry.isIntersecting); sync();
  }, { threshold: .05 }) : null;
  observer?.observe(root);
  const resize = view.ResizeObserver ? new view.ResizeObserver(entries => {
    const rect = entries[0]?.contentRect;
    if (rect) { width = rect.width; height = rect.height; paint(); }
  }) : null;
  resize?.observe(root);
  const measure = () => { width = root.clientWidth; height = root.clientHeight; paint(); sync(); };
  if (!resize) view.addEventListener('resize', measure, { passive: true });
  view.addEventListener('scroll', sync, { passive: true });
  doc.addEventListener('visibilitychange', sync);
  doc.addEventListener('rdct:smooth-update', sync);
  preference.addEventListener('change', sync);
  sync();
  return () => {
    if (!alive) return;
    alive = false; observer?.disconnect(); resize?.disconnect(); context?.revert();
    view.removeEventListener('resize', measure); view.removeEventListener('scroll', sync);
    doc.removeEventListener('visibilitychange', sync); doc.removeEventListener('rdct:smooth-update', sync);
    preference.removeEventListener('change', sync);
  };
}

if (typeof window !== 'undefined' && typeof document !== 'undefined') {
  const page = createPageLifecycle(window);
  for (const root of document.querySelectorAll('[data-rdct-offer-art]')) {
    page.mountRoot(root, 'client-offering-art', () => ({}), () => initializeOfferingArt(root));
  }
}
