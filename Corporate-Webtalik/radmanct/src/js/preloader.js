(() => {
  const root = document.getElementById('rdc-pre');
  if (!root) return;
  const preference = matchMedia('(prefers-reduced-motion: reduce)');
  const isHistory = performance.getEntriesByType('navigation')[0]?.type === 'back_forward';
  const shouldEnter = document.readyState !== 'complete' && !preference.matches && !isHistory;
  // Runs inline before the hero is parsed, so delayed modules cannot flash its text.
  if(shouldEnter && root.dataset.entrance==='1'){
    const html=document.documentElement;
    html.dataset.rdcEnt='pending';
    const release=()=>{
      clearTimeout(timeout);
      const failed=html.dataset.rdcEnt==='pending';
      if(failed)html.dataset.rdcEnt='fallback';
      document.removeEventListener('rdc:ent-rdy',release);
      window.removeEventListener('pagehide',release);
      window.removeEventListener('pageshow',restore);
      preference.removeEventListener('change',changed);
      if(failed)document.dispatchEvent(new Event('rdc:ent-fb'));
    };
    const restore=event=>{if(event.persisted)release();};
    const changed=()=>{if(preference.matches)release();};
    const timeout=setTimeout(release,4000);
    document.addEventListener('rdc:ent-rdy',release);
    window.addEventListener('pagehide',release);
    window.addEventListener('pageshow',restore);
    preference.addEventListener('change',changed);
  }

  let timeout;
  let entryInput;
  let destination;
  let navigating = false;

  function reset() {
    const entered = root.classList.contains('is-in');
    clearTimeout(timeout);
    entryInput?.abort();
    root.hidden = true;
    root.classList.remove('is-in', 'is-out');
    destination = undefined;
    navigating = false;
    if (entered) document.dispatchEvent(new Event('rdc:pre-done'));
  }

  function navigate() {
    if (!destination || navigating) return;
    navigating = true;
    clearTimeout(timeout);
    // Downloads, canceled navigations and very slow responses must leave this page usable.
    timeout = setTimeout(reset, 1400);
    try { window.location.assign(destination); } catch { reset(); }
  }

  root.addEventListener('animationend', event => {
    if (event.target !== root) return;
    if (event.animationName === 'rdc-pre-in') reset();
    if (event.animationName === 'rdc-pre-out') navigate();
  });
  window.addEventListener('pagehide', reset);
  window.addEventListener('pageshow', event => { if (event.persisted) reset(); });
  preference.addEventListener('change', () => {
    if (!preference.matches) return;
    navigate();
    reset();
  });

  // Window bubbling lets archive and dialog handlers claim their own links first.
  window.addEventListener('click', event => {
    if (preference.matches || event.defaultPrevented || event.button !== 0 ||
        event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    const link = event.target.closest?.('a[href]');
    if (!link || link.matches('[download], [ping], [attributionsrc], [referrerpolicy], [rel~="external"], [rel~="noreferrer"], [data-rdc-gal], #wpadminbar a')) return;
    const target = link.getAttribute('target') ?? document.querySelector('base[target]')?.target;
    if (target && target.toLowerCase() !== '_self') return;
    if (!link.getAttribute('href').trim()) return;

    const url = new URL(link.href);
    const current = window.location;
    if (url.origin !== current.origin || !/^https?:$/.test(url.protocol) ||
        url.hash || link.getAttribute('href').includes('#') ||
        (url.pathname === current.pathname && url.search === current.search) ||
        /(?:^|\/)(?:wp-admin|wp-json)(?:\/|$)|\/[^/]+\.(?!html?$)[a-z\d]+$/i.test(url.pathname) ||
        ['rest_route', 'preview', 'customize_changeset_uuid'].some(key => url.searchParams.has(key))) return;

    event.preventDefault();
    if (destination) return;
    reset();
    destination = url.href;
    root.classList.add('is-out');
    root.hidden = false;
    timeout = setTimeout(navigate, 450);
  });

  if (shouldEnter) {
    entryInput = new AbortController();
    const options = { capture: true, signal: entryInput.signal };
    document.addEventListener('pointerdown', reset, options);
    document.addEventListener('keydown', reset, options);
    root.classList.add('is-in');
    root.hidden = false;
    timeout = setTimeout(reset, 650);
  }
})();
