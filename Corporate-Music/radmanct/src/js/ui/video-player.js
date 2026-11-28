export function initializeExclusiveVideos(root){
 const buttons=()=>root.querySelectorAll('[data-rdc-video-toggle]');
 const visiblePlayers=new Map();
 let observer;
 const createPlayer=cover=>{
  const player=cover.parentElement.querySelector('[data-rdc-aparat-tpl]')?.content.firstElementChild?.cloneNode(true);
  const frame=player?.querySelector('iframe');
  if(!frame)return null;
  frame.loading='eager';
  return {player,frame};
 };
 const observeCovers=()=>{
  if(!observer)return;
  for(const cover of root.querySelectorAll('[data-rdc-aparat-visible]'))if(!visiblePlayers.has(cover))observer.observe(cover);
 };
 if(window.IntersectionObserver){
  observer=new IntersectionObserver(entries=>{
   for(const {target:cover,isIntersecting,intersectionRatio} of entries){
    if(!isIntersecting||intersectionRatio<0.25||visiblePlayers.has(cover))continue;
    observer.unobserve(cover);
    const embed=createPlayer(cover);
    if(!embed)continue;
    const {player,frame}=embed;
    visiblePlayers.set(cover,player);
    player.setAttribute('data-rdc-aparat-pending','');
    frame.addEventListener('load',()=>{
     if(!player.isConnected)return;
     player.removeAttribute('data-rdc-aparat-pending');
     cover.hidden=true;
    },{once:true});
    cover.after(player);
   }
  },{threshold:0.25});
   observeCovers();
 }
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
 const syncButtons=()=>{
  observeCovers();
  for(const button of buttons()){const video=button.parentElement.querySelector('video');if(video)sync(video);}
 };
 syncButtons();
 root.addEventListener('rdc:arc-upd',syncButtons);
 root.addEventListener('play',play,true);
 root.addEventListener('pause',pause,true);
 root.addEventListener('ended',pause,true);
 root.addEventListener('click',click);
 return()=>{
  root.removeEventListener('play',play,true);
  root.removeEventListener('pause',pause,true);
  root.removeEventListener('ended',pause,true);
  root.removeEventListener('click',click);
  root.removeEventListener('rdc:arc-upd',syncButtons);
  observer?.disconnect();
  for(const [cover,player] of visiblePlayers){player.remove();cover.hidden=false;}
  visiblePlayers.clear();
  for(const video of root.querySelectorAll('video'))video.pause();
  for(const button of buttons())button.hidden=true;
 };
}
