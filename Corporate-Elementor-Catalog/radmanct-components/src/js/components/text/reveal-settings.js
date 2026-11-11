const numeric = (value, fallback, min, max) => value !== undefined && value !== '' && Number.isFinite(Number(value)) ? Math.min(max, Math.max(min, Number(value))) : fallback;
const position = (value, fallback) => /^(?:top|center|bottom|(?:100|[0-9]{1,2}(?:\.[0-9]{1,2})?)%) (?:top|center|bottom|(?:100|[0-9]{1,2}(?:\.[0-9]{1,2})?)%)$/.test(value) ? value : fallback;

export function revealTween(root, mask = false) {
  const data = root.dataset;
  const fill = data.rdctTextReveal === 'fill';
  return {
    from: fill ? { backgroundSize: '0% 100%, 100% 100%' } : { opacity: numeric(data.initialOpacity, 0.25, 0.1, 1), ...(mask ? { yPercent: 100 } : {}) },
    to: {
      ...(fill ? { backgroundSize: '100% 100%, 100% 100%' } : { opacity: numeric(data.finalOpacity, 1, 0.1, 1), ...(mask ? { yPercent: 0 } : {}) }), ease: 'none',
      stagger: { each: numeric(data.revealStagger, 0.12, 0, 1), from: ['start', 'end', 'center', 'edges', 'random'].includes(data.revealFrom) ? data.revealFrom : 'start' },
      scrollTrigger: { trigger: root, start: position(data.revealStart, 'top 80%'), end: position(data.revealEnd, 'bottom 35%'), scrub: numeric(data.revealScrub, 0.5, 0, 5), invalidateOnRefresh: true },
    },
  };
}

