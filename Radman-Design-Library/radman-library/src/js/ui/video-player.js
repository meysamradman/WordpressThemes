export function initializeExclusiveVideos(root){
 const play=event=>{if(event.target.tagName!=='VIDEO')return;for(const video of root.querySelectorAll('video'))if(video!==event.target)video.pause();};
 root.addEventListener('play',play,true);
 return()=>{root.removeEventListener('play',play,true);for(const video of root.querySelectorAll('video'))video.pause();};
}
