import { SplitText } from 'gsap/SplitText';
import { revealTween } from './reveal-settings.js';
import { reportRuntimeError } from '../../../../../shared/frontend/lifecycle.js';

const instances = new WeakMap();

export function initializeTextLines(root, { gsap, ScrollTrigger }) {
  if (!root || root.dataset.rdctTextReveal !== 'scroll' || root.dataset.revealSplit !== 'lines') return () => {};
  if (instances.has(root)) return instances.get(root);
  const target = root.querySelector?.(':scope > a') || root;
  const text = target.textContent;
  if (!text.trim() || text.split(/\s+/u).filter(Boolean).length > 400) return () => {};
  const doc = root.ownerDocument || document;
  gsap.registerPlugin(ScrollTrigger, SplitText);
  const media = gsap.matchMedia();
  media.add('(prefers-reduced-motion: no-preference)', (context) => {
    let active = true; let split; let changed = false;
    context.add('setup', () => {
      if (!active || !root.isConnected || changed) return;
      const accessible = doc.createElement('span');
      accessible.className = 'rdct-tr__accessible'; accessible.textContent = text;
      const visual = doc.createElement('span');
      visual.className = 'rdct-tr__lines'; visual.setAttribute('aria-hidden', 'true'); visual.textContent = text;
      changed = true; target.replaceChildren(accessible, visual);
      split = SplitText.create(visual, {
        type: 'lines', tag: 'span', linesClass: 'rdct-tr__line', aria: 'none', autoSplit: true, reduceWhiteSpace: false,
        ...(root.dataset.revealMask === 'true' ? { mask: 'lines' } : {}),
        onSplit(self) {
          if (!active) return;
          const { from, to } = revealTween(root, root.dataset.revealMask === 'true');
          return gsap.fromTo(self.lines, from, to);
        },
      });
    });
    Promise.resolve(doc.fonts?.ready).then(() => context.setup(), () => context.setup()).catch((error) => {
      split?.revert(); if (changed) { target.textContent = text; changed = false; }
      reportRuntimeError('text-lines', error);
    });
    return () => { active = false; split?.revert(); if (changed) target.textContent = text; };
  }, root);
  let destroyed = false;
  const destroy = () => { if (destroyed) return; destroyed = true; media.revert(); instances.delete(root); };
  instances.set(root, destroy);
  return destroy;
}
