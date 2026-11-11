let pending;
let gsapPending;

export function loadGsap() {
  if (!gsapPending) {
    const source = window.rdctComponents?.gsapUrl;
    gsapPending = (source ? import(/* @vite-ignore */ source) : import('./gsap-vendor.js')).catch((error) => { gsapPending = undefined; throw error; });
  }
  return gsapPending;
}

export function loadAnimation() {
  if (!pending) {
    const source = window.rdctComponents?.animationUrl;
    pending = (source ? import(/* @vite-ignore */ source) : import('./animation-vendor.js')).catch((error) => {
      pending = undefined;
      throw error;
    });
  }
  return pending;
}
