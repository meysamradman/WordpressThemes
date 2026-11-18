export const mediaWaitEvents=['play','waiting','playing','pause','ended','error','emptied'];

export function updateMediaWait(media,control,event){
 if(!control)return false;
 const waiting=!media.paused&&!media.ended&&
  (event==='waiting'||(event==='play'&&media.readyState<3));
 control.toggleAttribute('data-rdc-media-wait',waiting);
 return waiting;
}
