export function formatAudioTime(value) {
 const seconds=Number.isFinite(value)&&value>0?Math.floor(value):0;
 const minutes=Math.floor(seconds/60);
 return `${minutes}:${String(seconds%60).padStart(2,'0')}`;
}

export function initializeAudioControls(root) {
 const audio=root.querySelector('audio'),controls=root.querySelector('.rdc-player__controls');
 if(!audio||!controls||root.dataset.rdcPlayerReady)return()=>{};
 const play=root.querySelector('[data-rdc-player-play]'),seek=root.querySelector('[data-rdc-player-seek]');
 const time=root.querySelector('[data-rdc-player-time]'),duration=root.querySelector('[data-rdc-player-duration]');
 const mute=root.querySelector('[data-rdc-player-mute]'),volume=root.querySelector('[data-rdc-player-volume]');
 const start=root.querySelector('[data-rdc-player-start]'),pause=root.querySelector('[data-rdc-player-pause]'),status=root.querySelector('[data-rdc-player-status]');
 const releases=[];
 const listen=(node,type,handler)=>{node.addEventListener(type,handler);releases.push(()=>node.removeEventListener(type,handler));};
 const update=()=>{
  const length=Number.isFinite(audio.duration)&&audio.duration>0?audio.duration:0;
  time.textContent=formatAudioTime(audio.currentTime);
  if(length)duration.textContent=formatAudioTime(length);
  seek.disabled=!length;seek.value=String(length?Math.round(audio.currentTime/length*1000):0);
  seek.setAttribute('aria-valuetext',`${formatAudioTime(audio.currentTime)} از ${formatAudioTime(length)}`);
  const playing=!audio.paused&&!audio.ended;
  play.setAttribute('aria-pressed',String(playing));play.setAttribute('aria-label',playing?'توقف پخش':'پخش آهنگ');
  start.hidden=playing;pause.hidden=!playing;
  mute.setAttribute('aria-pressed',String(audio.muted));mute.setAttribute('aria-label',audio.muted?'وصل صدا':'قطع صدا');
  volume.value=String(audio.muted?0:audio.volume);
 };
 listen(play,'click',()=>{
  status.textContent='';
  if(!audio.paused){audio.pause();return;}
  Promise.resolve(audio.play()).catch(()=>{status.textContent='پخش انجام نشد؛ دوباره تلاش کنید.';update();});
 });
 listen(seek,'input',()=>{if(Number.isFinite(audio.duration)&&audio.duration>0)audio.currentTime=Number(seek.value)/1000*audio.duration;update();});
 listen(mute,'click',()=>{audio.muted=!audio.muted;update();});
 listen(volume,'input',()=>{audio.volume=Number(volume.value);audio.muted=audio.volume===0;update();});
 for(const event of ['loadedmetadata','durationchange','timeupdate','play','pause','ended','volumechange','emptied'])listen(audio,event,update);
 listen(audio,'error',()=>{status.textContent='فایل صوتی قابل پخش نیست. می‌توانید از لینک دانلود استفاده کنید.';update();});
 controls.hidden=false;audio.controls=false;audio.hidden=true;root.dataset.rdcPlayerReady='true';update();
 return()=>{audio.pause();for(const release of releases)release();controls.hidden=true;audio.controls=true;audio.hidden=false;delete root.dataset.rdcPlayerReady;};
}
