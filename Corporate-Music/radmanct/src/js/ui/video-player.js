export function initializeExclusiveVideos(root){
 const buttons=[...root.querySelectorAll('[data-rdc-video-toggle]')];
 const sync=video=>{const button=video.closest('.rdc-video-card__media')?.querySelector('[data-rdc-video-toggle]');if(button)button.hidden=!video.paused&&!video.ended;};
 const play=event=>{
  if(event.target.tagName!=='VIDEO')return;
  for(const video of root.querySelectorAll('video'))if(video!==event.target)video.pause();
  sync(event.target);
 };
 const pause=event=>{if(event.target.tagName==='VIDEO')sync(event.target);};
 const click=event=>{
  const button=event.target.closest('[data-rdc-video-toggle]');
  if(!button||!root.contains(button))return;
  const video=button.parentElement.querySelector('video');
  if(video)Promise.resolve(video.play()).catch(()=>sync(video));
 };
 for(const button of buttons){const video=button.parentElement.querySelector('video');if(video)sync(video);}
 root.addEventListener('play',play,true);
 root.addEventListener('pause',pause,true);
 root.addEventListener('ended',pause,true);
 root.addEventListener('click',click);
 return()=>{
  root.removeEventListener('play',play,true);
  root.removeEventListener('pause',pause,true);
  root.removeEventListener('ended',pause,true);
  root.removeEventListener('click',click);
  for(const video of root.querySelectorAll('video'))video.pause();
  for(const button of buttons)button.hidden=true;
 };
}
