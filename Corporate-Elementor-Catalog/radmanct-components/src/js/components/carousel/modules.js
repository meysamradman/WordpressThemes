export async function loadCarouselModules(options) {
  const loads = [];
  if (options.autoplay) loads.push(import('./modules/autoplay.js'));
  if (options.arrows) loads.push(import('./modules/navigation.js'));
  if (options.pagination && options.pagination !== 'none') loads.push(import('./modules/pagination.js'));
  return (await Promise.all(loads)).map((module) => module.default);
}
