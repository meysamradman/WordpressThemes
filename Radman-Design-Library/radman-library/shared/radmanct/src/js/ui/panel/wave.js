import {createPanelTimeline, svgNode, mix, easeIn, easeOut, curveIn, curveOut} from './timeline.js';

export function waveFrame(progress, open, duration) {
  const bend = duration * .375;
  const total = (duration + bend) * 2; const time = progress * total;
  const half = duration + bend; const cover = time <= half;
  const local = cover ? time : time - half;
  let edge; let control;
  if (local <= duration && cover) {
    const position = easeIn(local / duration);
    edge = mix(open ? 100 : 0, 50, position); control = mix(open ? 100 : 0, open ? 0 : 100, position);
  } else if (cover) {
    const position = curveOut(Math.min(1, (local - duration) / bend));
    edge = mix(50, open ? 0 : 100, position); control = open ? 0 : 100;
  } else if (local <= bend) {
    const position = curveIn(local / bend);
    edge = mix(open ? 100 : 0, 50, position); control = mix(open ? 100 : 0, open ? 0 : 100, position);
  } else {
    const position = easeOut(Math.min(1, (local - bend) / duration));
    edge = mix(50, open ? 0 : 100, position); control = mix(open ? 0 : 100, open ? 0 : 100, position);
  }
  const bottom = cover === open;
  return {covered:open ? cover : !cover, path:bottom ? `M0 100 V${edge} Q50 ${control} 100 ${edge} V100 Z` : `M0 0 V${edge} Q50 ${control} 100 ${edge} V0 Z`};
}

export function createPanelMotion(dialog, duration) {
  const timeline = createPanelTimeline(dialog);
  const svg = svgNode(dialog.ownerDocument, 'svg', {'aria-hidden':'true', viewBox:'0 0 100 100', preserveAspectRatio:'none', class:'rdc-pnl-wave'});
  const path = svgNode(dialog.ownerDocument, 'path'); svg.append(path); dialog.append(svg);
  return {
    play(open) {
      const base=duration,phase=base/2.75;
      const stagger=open?70:35,itemDuration=open?650:300;
      const delay=open?base/2:0;
      const total=Math.max(base,delay+itemDuration+timeline.itemSpan*stagger);
      const result = timeline.run(total, (progress) => {
        const frame = waveFrame(Math.min(1,progress * total / base), open, phase); path.setAttribute('d', frame.path);
        dialog.toggleAttribute('data-motion-covered', !timeline.reduced && frame.covered);
      });
      timeline.items(open,{delay,duration:itemDuration,stagger,distance:open?38:24});
      return result;
    },
    cancel() { timeline.cancel(); dialog.removeAttribute('data-motion-covered'); path.setAttribute('d','M0 0 Z'); },
    destroy() { timeline.destroy(); dialog.removeAttribute('data-motion-covered'); svg.remove(); },
  };
}
