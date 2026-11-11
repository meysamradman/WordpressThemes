import {createPanelTimeline, svgNode, easeOut, easeIn} from './timeline.js';

let sequence = 0;
export function createPanelMotion(dialog, duration) {
  const timeline = createPanelTimeline(dialog);
  const doc = dialog.ownerDocument;
  const svg = svgNode(doc, 'svg', {'aria-hidden':'true', width:0, height:0, style:'position:absolute;pointer-events:none'});
  const defs = svgNode(doc, 'defs'); const id = `rdct-menu-curtain-${++sequence}`;
  const clip = svgNode(doc, 'clipPath', {id, clipPathUnits:'objectBoundingBox'}); const path = svgNode(doc, 'path');
  clip.append(path); defs.append(clip); svg.append(defs); dialog.append(svg);
  const originalClip = dialog.style.clipPath; let generation = 0;
  return {
    play(open) {
      const current = ++generation;
      const stagger = Math.min(50,duration / 40); const delay = open ? duration * .2 : 0;
      const total = Math.max(duration * 1.5, delay + duration * .9 + timeline.itemSpan * stagger);
      const result = timeline.run(total, (elapsed) => {
        const progress = Math.min(1,elapsed * total / (duration * 1.5));
        const travel = open ? easeOut(progress) : 1 - easeIn(progress);
        const edge = 1 - travel; const bend = Math.sin(Math.PI * progress) * .28;
        path.setAttribute('d', `M0 1 V${edge} C.159 ${edge-bend*2/3} .326 ${edge-bend} .5 ${edge-bend} C.674 ${edge-bend} .841 ${edge-bend*2/3} 1 ${edge} V1 Z`);
        dialog.style.clipPath = `url(#${id})`;
      });
      timeline.items(open, {delay, duration:duration * .9, stagger, distance:open ? -55 : -35});
      return result.then((completed) => { if (current === generation) dialog.style.clipPath = originalClip; return completed; });
    },
    cancel() { ++generation; timeline.cancel(); dialog.style.clipPath = originalClip; },
    destroy() { ++generation; timeline.destroy(); dialog.style.clipPath = originalClip; svg.remove(); },
  };
}
