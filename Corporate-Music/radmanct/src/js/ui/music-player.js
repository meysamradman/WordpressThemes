import {initializeAudioControls} from './audio-controls.js';
export function initializeMusicPlayer(root){
 const audio=root.querySelector('[data-rdc-music-player]'),panel=root.querySelector('[data-rdc-home-player]');if(!audio||!panel)return()=>{};
 const releaseControls=initializeAudioControls(panel.querySelector('[data-rdc-player]')),title=panel.querySelector('[data-rdc-home-track]'),close=panel.querySelector('[data-rdc-home-player-close]'),state=panel.querySelector('.rdc-home-player__lbl');
 const labels=new Map();let active='',opener;
 for(const button of root.querySelectorAll('[data-rdc-audio-key]'))labels.set(button,{label:button.getAttribute('aria-label'),text:[...button.querySelectorAll('.rdc-btn__lbl span')].map(span=>span.textContent)});
 const sync=()=>{state.textContent=!audio.paused&&!audio.ended?'در حال پخش':audio.ended?'پخش به پایان رسید':'آمادهٔ پخش';for(const [button,original] of labels){const playing=button.dataset.rdcAudioKey===active&&!audio.paused&&!audio.ended;button.setAttribute('aria-pressed',String(playing));button.setAttribute('aria-label',playing?'توقف '+button.dataset.rdcAudioTitle:original.label);button.querySelectorAll('.rdc-btn__lbl span').forEach((span,index)=>{span.textContent=playing?'توقف پخش':original.text[index];});}};
 const click=event=>{const button=event.target.closest('[data-rdc-audio-src]');if(!button||!root.contains(button))return;const url=new URL(button.dataset.rdcAudioSrc,location.href);if(url.origin!==location.origin)return;const key=button.dataset.rdcAudioKey;opener=button;
  const feature=root.querySelector('[data-rdc-feature-track]'),index=feature?Math.round(Math.abs(feature.scrollLeft)/feature.clientWidth):0;
  const host=button.closest('.rdc-feature__copy')||feature?.children[index]?.querySelector('.rdc-feature__copy');if(host&&panel.parentElement!==host)host.append(panel);
  panel.hidden=false;title.textContent=button.dataset.rdcAudioTitle||'آهنگ انتخاب‌شده';
  if(active===key&&!audio.paused){audio.pause();return;}if(active!==key){active=key;audio.src=url.href;}
  Promise.resolve(audio.play()).catch(()=>{panel.querySelector('[data-rdc-player-status]').textContent='پخش انجام نشد؛ دوباره تلاش کنید.';sync();});
 };
 const dismiss=()=>{audio.pause();panel.hidden=true;opener?.focus({preventScroll:true});};
 root.addEventListener('click',click);close.addEventListener('click',dismiss);for(const event of ['play','pause','ended','error'])audio.addEventListener(event,sync);
 return()=>{audio.pause();root.removeEventListener('click',click);close.removeEventListener('click',dismiss);for(const event of ['play','pause','ended','error'])audio.removeEventListener(event,sync);releaseControls();audio.removeAttribute('src');audio.load();panel.hidden=true;};
}
