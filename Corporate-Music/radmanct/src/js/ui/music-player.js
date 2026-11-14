export function initializeMusicPlayer(root){
 const audio=root.querySelector('[data-rdc-music-player]');if(!audio)return()=>{};let active='';
 const sync=()=>{for(const button of root.querySelectorAll('[data-rdc-audio-key]'))button.setAttribute('aria-pressed',String(button.dataset.rdcAudioKey===active&&!audio.paused));};
 const click=event=>{const button=event.target.closest('[data-rdc-audio-src]');if(!button||!root.contains(button))return;const url=new URL(button.dataset.rdcAudioSrc,location.href);if(url.origin!==location.origin)return;const key=button.dataset.rdcAudioKey;
  if(active===key&&!audio.paused){audio.pause();return;}if(active!==key){active=key;audio.src=url.href;}audio.hidden=false;Promise.resolve(audio.play()).catch(()=>{sync();});
 };
 root.addEventListener('click',click);for(const event of ['play','pause','ended','error'])audio.addEventListener(event,sync);
 return()=>{audio.pause();audio.removeAttribute('src');audio.load();root.removeEventListener('click',click);for(const event of ['play','pause','ended','error'])audio.removeEventListener(event,sync);};
}
