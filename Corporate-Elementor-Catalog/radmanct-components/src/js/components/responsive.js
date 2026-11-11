export function responsiveBreakpoints(options) {
  const tablet = options.breakpoints?.tablet;
  const desktop = options.breakpoints?.desktop;
  return Number.isInteger(tablet) && Number.isInteger(desktop) && tablet > 1 && desktop > tablet && desktop <= 10001
    ? { tablet, desktop } : { tablet: 768, desktop: 1024 };
}
