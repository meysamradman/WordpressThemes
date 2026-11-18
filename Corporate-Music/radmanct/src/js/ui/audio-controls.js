import {initializeAudioWave} from './audio-wave.js';
const waitLimit=12000;

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
 let alive=true,request=0,waitTimer,scrub=null,pendingSeek=null,playedSource='';
 const listen=(node,type,handler)=>{node.addEventListener(type,handler);releases.push(()=>node.removeEventListener(type,handler));};
 const source=()=>audio.src||'';
 const mediaDuration=()=>Number.isFinite(audio.duration)&&audio.duration>0?audio.duration:0;
 const updatePosition=(length,fraction)=>{
  const displayLength=length||(duration.textContent||'').split(':').reduce((seconds,part)=>seconds*60+Number(part),0);
  const progress=fraction??(length?Math.min(1,Math.max(0,audio.currentTime/length)):0);
  const position=fraction===undefined?audio.currentTime:progress*displayLength;
  time.textContent=formatAudioTime(position);
  if(length)duration.textContent=formatAudioTime(length);
  seek.disabled=!length&&!source();seek.value=String(Math.round(progress*1000));
  root.style.setProperty('--rdc-audio-progress',`${progress*100}%`);
  seek.setAttribute('aria-valuetext',`${formatAudioTime(position)} از ${formatAudioTime(displayLength)}`);
 };
 const update=()=>{
  updatePosition(mediaDuration(),(scrub||pendingSeek)?.fraction);
  const playing=!audio.paused&&!audio.ended;
  play.setAttribute('aria-pressed',String(playing));play.setAttribute('aria-label',playing?(waitTimer?'در انتظار پخش؛ برای توقف کلیک کنید':'توقف پخش'):'پخش آهنگ');
  start.hidden=playing;pause.hidden=!playing;
  mute.setAttribute('aria-pressed',String(audio.muted));mute.setAttribute('aria-label',audio.muted?'وصل صدا':'قطع صدا');
  volume.value=String(audio.muted?0:audio.volume);
 };
 const stopWaiting=()=>{
  clearTimeout(waitTimer);
  waitTimer=undefined;
  play.toggleAttribute('data-rdc-media-wait',false);
 };
 const startWaiting=()=>{
  if(waitTimer)return;
  play.toggleAttribute('data-rdc-media-wait',true);
  waitTimer=setTimeout(()=>{
   request++;
   pendingSeek=null;
   stopWaiting();
   audio.pause();
   status.textContent='بارگذاری آهنگ طول کشید. دوباره پخش کنید یا فایل را دانلود کنید.';
   update();
  },waitLimit);
 };
 const startPlayback=()=>{
  status.textContent='';
  const ticket=++request;
  Promise.resolve(audio.play()).catch(error=>{
   if(!alive||ticket!==request)return;
   pendingSeek=null;
   stopWaiting();
   if(error?.name!=='AbortError')status.textContent='پخش انجام نشد؛ دوباره تلاش کنید.';
   update();
  });
 };
 listen(play,'click',()=>{
  if(!audio.paused){audio.pause();return;}
  startPlayback();
 });
 const applySeek=()=>{
  if(pendingSeek?.source!==source())pendingSeek=null;
  const length=mediaDuration();
  if(!pendingSeek||!length||(audio.currentSrc&&audio.currentSrc!==source()))return;
  const {fraction}=pendingSeek;
  pendingSeek=null;
  audio.currentTime=fraction*length;
 };
 const seekTo=fraction=>{
  pendingSeek={source:source(),fraction};
  applySeek();
  if(source()&&audio.paused&&playedSource!==source())startPlayback();
  update();
 };
 const preview=event=>{
  scrub.fraction=Math.min(1,Math.max(0,(event.clientX-scrub.left)/scrub.width));
  updatePosition(mediaDuration(),scrub.fraction);
 };
 const clearScrub=()=>{
  if(!scrub)return;
  const {id}=scrub;
  scrub=null;
  delete root.dataset.rdcSeeking;
  if(seek.hasPointerCapture(id))seek.releasePointerCapture(id);
 };
 listen(seek,'pointerdown',event=>{
  if(scrub||!event.isPrimary||event.button!==0||seek.disabled)return;
  const {left,width}=seek.getBoundingClientRect();
  if(!width)return;
  event.preventDefault();
  scrub={id:event.pointerId,left,width,fraction:0};
  root.dataset.rdcSeeking='true';
  seek.setPointerCapture(event.pointerId);
  preview(event);
 });
 listen(seek,'pointermove',event=>{if(scrub?.id===event.pointerId)preview(event);});
 listen(seek,'pointerup',event=>{
  if(scrub?.id!==event.pointerId)return;
  preview(event);
  const {fraction}=scrub;
  clearScrub();
  seekTo(fraction);
 });
 for(const event of ['pointercancel','lostpointercapture'])listen(seek,event,event=>{
  if(scrub?.id!==event.pointerId)return;
  clearScrub();update();
 });
 listen(seek,'input',()=>{if(!scrub)seekTo(Number(seek.value)/1000);});
 listen(mute,'click',()=>{audio.muted=!audio.muted;update();});
 listen(volume,'input',()=>{audio.volume=Number(volume.value);audio.muted=audio.volume===0;update();});
 for(const event of ['timeupdate','volumechange'])listen(audio,event,update);
 for(const event of ['loadedmetadata','durationchange'])listen(audio,event,()=>{applySeek();update();});
 for(const event of ['play','waiting','stalled'])listen(audio,event,()=>{
  if(!audio.paused&&!audio.ended&&audio.readyState<3)startWaiting();
  update();
 });
 listen(audio,'playing',()=>{playedSource=source();stopWaiting();update();});
 listen(audio,'canplay',()=>{if(!audio.seeking)stopWaiting();update();});
 listen(audio,'emptied',()=>{
  clearScrub();
  if(playedSource!==source())playedSource='';
  if(pendingSeek?.source!==source())pendingSeek=null;
  if(audio.paused||!source())stopWaiting();
  update();
 });
 for(const event of ['pause','ended','error'])listen(audio,event,()=>{
  request++;
  pendingSeek=null;
  stopWaiting();
  if(event==='error'){
   clearScrub();
   status.textContent='فایل صوتی قابل پخش نیست. می‌توانید از لینک دانلود استفاده کنید.';
  }
  update();
 });
 controls.hidden=false;audio.controls=false;audio.hidden=true;root.dataset.rdcPlayerReady='true';update();
 releases.push(initializeAudioWave(root,audio));
 return()=>{alive=false;request++;clearScrub();stopWaiting();audio.pause();for(const release of releases)release();controls.hidden=true;audio.controls=true;audio.hidden=false;delete root.dataset.rdcPlayerReady;};
}
