import {loadGsap} from '../motion.js';
import {initializeOfferingArt} from '../runtime/art-orbit.js';
import {createPageLifecycle} from '../runtime/lifecycle.js';

const page = createPageLifecycle(window);

function initialize(scene) {
  const header = scene.closest('.rdc-mini');
  const original = header.querySelector('.rdc-mini__art');
  const patch = scene.querySelector('[data-rdc-mini-paint]');
  const button = header.querySelector('[data-rdc-mini-toggle]');

  async function load() {
    const image = new Image();
    image.src = scene.dataset.rdcMiniPatches;
    const [vendor] = await Promise.all([loadGsap(), image.decode(), original.decode()]);
    return vendor;
  }

  function ready() {
    patch.setAttribute('href', scene.dataset.rdcMiniPatches);
    scene.setAttribute('data-rdc-mini-ready', '');
    button.hidden = false;

    function toggle() {
      const paused = scene.toggleAttribute('data-rdc-mini-off');
      button.setAttribute('aria-pressed', String(paused));
    }

    button.addEventListener('click', toggle);
    return () => {
      button.removeEventListener('click', toggle);
      button.setAttribute('aria-pressed', 'false');
      button.hidden = true;
      scene.removeAttribute('data-rdc-mini-ready');
      scene.removeAttribute('data-rdc-mini-off');
    };
  }

  return initializeOfferingArt(scene, load, ready);
}

for (const scene of document.querySelectorAll('[data-rdc-mini-art]')) {
  page.mountRoot(scene, 'compact-title-art', () => ({}), () => initialize(scene));
}
