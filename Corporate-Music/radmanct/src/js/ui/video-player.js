export function initializeExclusiveVideos(root){
 const buttons=[...root.querySelectorAll('[data-rdc-video-toggle]')];
 const closeEmbed=()=>{
  const player=root.querySelector('[data-rdc-aparat-player]');
  if(!player)return;
  const cover=player.parentElement.querySelector('[data-rdc-aparat-open]');
  if(cover)cover.hidden=false;
  player.remove();
 };
 const sync=video=>{const button=video.closest('.rdc-video-card__media')?.querySelector('[data-rdc-video-toggle]');if(button)button.hidden=!video.paused&&!video.ended;};
 const play=event=>{
  if(event.target.tagName!=='VIDEO')return;
  closeEmbed();
  for(const video of root.querySelectorAll('video'))if(video!==event.target)video.pause();
  sync(event.target);
 };
 const pause=event=>{if(event.target.tagName==='VIDEO')sync(event.target);};
 const click=event=>{
  const cover=event.target.closest('[data-rdc-aparat-open]');
  if(cover&&root.contains(cover)&&!event.defaultPrevented&&event.button===0&&!event.metaKey&&!event.ctrlKey&&!event.shiftKey&&!event.altKey){
   const player=cover.parentElement.querySelector('[data-rdc-aparat-tpl]')?.content.firstElementChild?.cloneNode(true);
   const frame=player?.querySelector('iframe');
   if(!frame)return;
   event.preventDefault();
   closeEmbed();
   for(const video of root.querySelectorAll('video'))video.pause();
   player.setAttribute('data-rdc-aparat-player','');
   const source=new URL(frame.src);
   source.searchParams.set('autoplay','true');
   frame.src=source.href;
   frame.allow+='; autoplay';
   frame.loading='eager';
   cover.hidden=true;
   cover.after(player);
   frame.focus({preventScroll:true});
   return;
  }
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
  closeEmbed();
  for(const video of root.querySelectorAll('video'))video.pause();
  for(const button of buttons)button.hidden=true;
 };
}
