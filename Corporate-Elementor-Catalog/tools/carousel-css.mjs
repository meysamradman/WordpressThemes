export function scopeCarouselCss() {
  return {
    postcssPlugin: 'rdct-carousel-css',
    Rule(rule) {
      const file = rule.source?.input?.file?.replaceAll('\\', '/') ?? '';
      if (!file.includes('/node_modules/swiper/')) return;
      if (rule.parent?.type === 'rule' || /keyframes$/.test(rule.parent?.name ?? '')) return;
      rule.selectors = rule.selectors.map((selector) => selector === ':root' ? '.rdct-crs' : selector === ':host' ? '.rdct-crs__viewport' : `.rdct-crs ${selector}`);
    },
  };
}
