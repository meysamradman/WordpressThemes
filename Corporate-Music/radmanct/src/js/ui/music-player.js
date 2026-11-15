import {initializeAudioControls} from './audio-controls.js';
export function homeTracks(buttons,origin){
 const tracks=new Map();
 for(const button of buttons){const key=button.dataset.rdcAudioKey,source=button.dataset.rdcAudioSrc;if(!key||!source||tracks.has(key))continue;try{const url=new URL(source,origin);if(url.origin===origin)tracks.set(key,{key,url:url.href,button});}catch{}}
 return [...tracks.values()];
}
export function initializeMusicPlayer(root){
 const panel=root.querySelector('[data-rdc-home-player]'),audio=panel?.querySelector('[data-rdc-music-player]');if(!panel||!audio)return()=>{};
 const doc=root.ownerDocument,view=doc.defaultView,buttons=[...root.querySelectorAll('[data-rdc-audio-key]')];
 const tracks=homeTracks(buttons,view.location.origin),byKey=new Map(tracks.map(track=>[track.key,track])),owner=panel.parentNode,after=panel.nextSibling;
 const title=panel.querySelector('[data-rdc-home-track]'),artist=panel.querySelector('[data-rdc-home-artist]'),cover=panel.querySelector('[data-rdc-home-cover]');
 const close=panel.querySelector('[data-rdc-home-player-close]'),state=panel.querySelector('.rdc-home-player__lbl'),status=panel.querySelector('[data-rdc-player-status]');
 const duration=panel.querySelector('[data-rdc-player-duration]'),previous=panel.querySelector('[data-rdc-home-prev]'),next=panel.querySelector('[data-rdc-home-next]');
 const labels=new Map(buttons.map(button=>[button,{label:button.getAttribute('aria-label'),text:[...button.querySelectorAll('.rdc-btn__lbl span')].map(span=>span.textContent)}]));
 let active='',opener,request=0,alive=true;
 // Keep the dock outside transformed scroll wrappers; still only one native audio engine.
 doc.body.append(panel);
 const releaseControls=initializeAudioControls(panel.querySelector('[data-rdc-player]'));
 previous.disabled=next.disabled=tracks.length<2;
 const sync=()=>{
  const playing=!audio.paused&&!audio.ended;state.textContent=playing?'در حال پخش':audio.ended?'پخش به پایان رسید':'پخش متوقف است';
  for(const [button,original] of labels){const selected=button.dataset.rdcAudioKey===active&&playing;button.setAttribute('aria-pressed',String(selected));if(selected)button.setAttribute('aria-label','توقف '+button.dataset.rdcAudioTitle);else if(original.label!==null)button.setAttribute('aria-label',original.label);else button.removeAttribute('aria-label');button.querySelectorAll('.rdc-btn__lbl span').forEach((span,index)=>{span.textContent=selected?'توقف پخش':original.text[index];});}
 };
 const select=(track,toggle=true)=>{
  if(!track)return;const data=track.button.dataset;panel.hidden=false;doc.body.classList.add('rdc-has-player');status.textContent='';title.textContent=data.rdcAudioTitle||'آهنگ انتخاب‌شده';artist.textContent=data.rdcAudioArtist||'';audio.setAttribute('aria-label','پخش '+title.textContent);
  if(active===track.key&&!audio.paused&&toggle){request++;audio.pause();return;}
  if(active!==track.key){active=track.key;duration.textContent=data.rdcAudioDuration||'0:00';cover.hidden=true;cover.removeAttribute('src');if(data.rdcAudioCover){try{const url=new URL(data.rdcAudioCover,view.location.origin);if(url.origin===view.location.origin){cover.src=url.href;cover.hidden=false;}}catch{}}audio.src=track.url;}
  const ticket=++request;Promise.resolve(audio.play()).catch(error=>{if(!alive||ticket!==request||error?.name==='AbortError')return;status.textContent='پخش انجام نشد؛ دوباره تلاش کنید.';sync();});
 };
 const click=event=>{const button=event.target.closest('[data-rdc-audio-src]');if(!button||!root.contains(button))return;const track=byKey.get(button.dataset.rdcAudioKey);if(!track)return;opener=button;select(track);};
 const move=direction=>{const index=tracks.findIndex(track=>track.key===active);select(tracks[(index+direction+tracks.length)%tracks.length],false);};
 const prev=()=>move(-1),following=()=>move(1);
 const dismiss=()=>{request++;audio.pause();audio.removeAttribute('src');audio.load();active='';panel.hidden=true;doc.body.classList.remove('rdc-has-player');sync();if(opener?.isConnected)opener.focus({preventScroll:true});};
 const keyboard=event=>{if(event.key==='Escape'){event.preventDefault();dismiss();}};
 const media=event=>{if(event.target.tagName==='VIDEO')audio.pause();};
 const started=()=>{for(const video of root.querySelectorAll('video'))video.pause();sync();};
 root.addEventListener('click',click);root.addEventListener('play',media,true);close.addEventListener('click',dismiss);previous.addEventListener('click',prev);next.addEventListener('click',following);panel.addEventListener('keydown',keyboard);audio.addEventListener('play',started);for(const event of ['pause','ended','error'])audio.addEventListener(event,sync);
 return()=>{alive=false;request++;root.removeEventListener('click',click);root.removeEventListener('play',media,true);close.removeEventListener('click',dismiss);previous.removeEventListener('click',prev);next.removeEventListener('click',following);panel.removeEventListener('keydown',keyboard);audio.removeEventListener('play',started);for(const event of ['pause','ended','error'])audio.removeEventListener(event,sync);releaseControls();audio.removeAttribute('src');audio.load();panel.hidden=true;active='';sync();doc.body.classList.remove('rdc-has-player');owner.insertBefore(panel,after);};
}
