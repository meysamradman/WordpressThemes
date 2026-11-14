(() => {
  const root = document.getElementById('rdc-pre');
  if (!root) return;
  const preference=matchMedia('(prefers-reduced-motion: reduce)');
  if (document.readyState === 'complete' || preference.matches) { root.remove(); return; }
  // Runs inline before the hero is parsed, so delayed modules cannot flash its text.
  if(root.dataset.entrance==='1'){
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
  // Keep the hero gate on internal navigation, even when the curtain is skipped.
  // Storage may be denied; in that case retain the bounded original loader.
  try {
    const key='rdct:preloader-seen';
    if(window.sessionStorage.getItem(key)==='1'){
      root.remove();
      document.dispatchEvent(new Event('rdc:pre-done'));
      return;
    }
    window.sessionStorage.setItem(key,'1');
  } catch {}
  const events = new AbortController();
  const options = { once: true, signal: events.signal };
  let finished = false; let ready = false; let painted = false; let leaving = false;
  const duration = Number(root.dataset.exitDuration);
  const exitDuration = (Number.isFinite(duration) && duration >= 200 && duration <= 1200 ? duration : 350) + 60;
  let safety; let exit; let wait; let frame;
  const finish = () => {
    if (finished) return;
    finished = true;
    clearTimeout(safety); clearTimeout(exit); clearTimeout(wait); cancelAnimationFrame(frame);
    events.abort();
    root.remove();
    document.dispatchEvent(new Event('rdc:pre-done'));
  };
  const leave = () => {
    if (finished || leaving || !ready || !painted) return;
    leaving = true;
    clearTimeout(wait);
    root.classList.add('is-out');
    exit = setTimeout(finish, exitDuration);
  };
  window.addEventListener('load', () => { ready = true; leave(); }, options);
  window.addEventListener('pageshow', (event) => { if (event.persisted) finish(); }, options);
  window.addEventListener('pagehide', finish, options);
  document.addEventListener('pointerdown', finish, options);
  document.addEventListener('keydown', finish, options);
  root.classList.add('is-vis');
  root.hidden = false;
  safety = setTimeout(finish, 1800);
  wait = setTimeout(() => { ready = true; leave(); }, 1800 - exitDuration);
  frame = requestAnimationFrame(() => {
    if (finished) return;
    frame = requestAnimationFrame(() => { if (!finished) { painted = true; leave(); } });
  });
})();
