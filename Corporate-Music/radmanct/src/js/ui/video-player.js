export function initializeExclusiveVideos(root){
 const doc=root.ownerDocument,frames=new Map();
 const resetFrames=()=>{for(const [box,frame] of frames){frame.remove();box.querySelector('[data-rdc-aparat-play]').hidden=false;}frames.clear();};
 const nativePlay=event=>{if(['AUDIO','VIDEO'].includes(event.target.tagName))resetFrames();};
 const buttons=[...root.querySelectorAll('[data-rdc-video-toggle]')];
 const sync=video=>{const button=video.closest('.rdc-video-card__media')?.querySelector('[data-rdc-video-toggle]');if(button)button.hidden=!video.paused&&!video.ended;};
 const play=event=>{if(event.target.tagName!=='VIDEO')return;for(const video of root.querySelectorAll('video'))if(video!==event.target)video.pause();sync(event.target);};
 const pause=event=>{if(event.target.tagName==='VIDEO')sync(event.target);};
 const click=event=>{
  const link=event.target.closest('[data-rdc-aparat-play]');
  if(link&&root.contains(link)){
   if(event.button!==0||event.ctrlKey||event.metaKey||event.shiftKey||event.altKey)return;
   const box=link.closest('[data-rdc-aparat]'),hash=box?.dataset.rdcAparat;
   if(!/^[a-zA-Z0-9]{5,32}$/.test(hash??''))return;
   event.preventDefault();resetFrames();for(const media of doc.querySelectorAll('audio,video'))media.pause();
   const frame=doc.createElement('iframe');
   frame.title=link.getAttribute('aria-label');frame.allowFullscreen=true;frame.allow='autoplay; fullscreen; encrypted-media; picture-in-picture';frame.referrerPolicy='strict-origin-when-cross-origin';
   // Only created by the user's play activation; delegate playback to Aparat.
   frame.src=`https://www.aparat.com/video/video/embed/videohash/${hash}/vt/frame?autoplay=true`;
   link.hidden=true;box.append(frame);frames.set(box,frame);frame.focus();return;
  }
  const button=event.target.closest('[data-rdc-video-toggle]');if(!button||!root.contains(button))return;const video=button.parentElement.querySelector('video');if(video)Promise.resolve(video.play()).catch(()=>sync(video));
 };
 for(const button of buttons){const video=button.parentElement.querySelector('video');if(video)sync(video);}
 doc.addEventListener('play',nativePlay,true);
 root.addEventListener('play',play,true);root.addEventListener('pause',pause,true);root.addEventListener('ended',pause,true);root.addEventListener('click',click);
 return()=>{doc.removeEventListener('play',nativePlay,true);resetFrames();root.removeEventListener('play',play,true);root.removeEventListener('pause',pause,true);root.removeEventListener('ended',pause,true);root.removeEventListener('click',click);for(const video of root.querySelectorAll('video'))video.pause();for(const button of buttons)button.hidden=true;};
}
