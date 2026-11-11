import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

export function initializeMotion() {
  gsap.registerPlugin(ScrollTrigger);
  const media = gsap.matchMedia();
  media.add('(prefers-reduced-motion: no-preference)', () => {
    gsap.utils.toArray('[data-rdct-reveal]').forEach((element) => {
      gsap.from(element, {
        y: 20,
        opacity: 0,
        duration: 0.55,
        ease: 'power2.out',
        scrollTrigger: { trigger: element, start: 'top 92%', once: true },
      });
    });
  });
  return () => media.revert();
}
