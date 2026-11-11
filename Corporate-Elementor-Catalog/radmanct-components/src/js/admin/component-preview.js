if (document.querySelector('[data-rdct-particles]')) import('../components/particles.js').then(({initializeParticleGroups}) => { const cleanup = initializeParticleGroups(document); window.addEventListener('pagehide', cleanup, {once:true}); });

if (document.querySelector('[data-rdct-text-reveal=scroll], [data-rdct-text-reveal=fill], [data-rdct-text-reveal=rotate], [data-rdct-text-reveal=marquee]')) {
  import('../components/text.js').then(({ initializeTexts }) => {
    const cleanup = initializeTexts(); window.addEventListener('pagehide', cleanup, { once: true });
  });
}
if (document.querySelector('[data-rdct-curtain]')) {
  import('../components/curtain.js').then(({ initializeCurtains }) => {
    const cleanup = initializeCurtains(); window.addEventListener('pagehide', cleanup, { once: true });
  });
}

if (document.querySelector('[data-rdct-flipbox]')) import('../components/flipbox.js').then(({initializeFlipBox}) => { const cleanup = initializeFlipBox(document.querySelector('[data-rdct-flipbox]')); window.addEventListener('pagehide', cleanup, {once:true}); });

if (document.querySelector('[data-rdct-image]')) import('../components/image.js').then(({initializeImages}) => { const cleanup = initializeImages(); window.addEventListener('pagehide',cleanup,{once:true}); });

if (document.querySelector('[data-rdct-carousel]')) import('../components/carousel-entry.js').then(({initializeCarousels}) => { const cleanup = initializeCarousels(); window.addEventListener('pagehide',cleanup,{once:true}); });

if (document.querySelector('[data-rdct-sticky-sections]')) import('../components/sticky-sections.js').then(({initializeStickySectionGroups}) => {const cleanup = initializeStickySectionGroups(); window.addEventListener('pagehide',cleanup,{once:true});});

if(document.querySelector('[data-rdct-button]'))import('../components/button.js').then(({initializeButtons})=>{const cleanup=initializeButtons(document);window.addEventListener('pagehide',cleanup,{once:true});});
