const SHOW_DELAY=160;
const CLEAR_DELAY=8000;

export function initializePageLoading(view=window){
 const doc=view.document,html=doc.documentElement;
 let showTimer,clearTimer;
 const reset=()=>{
  view.clearTimeout(showTimer);
  view.clearTimeout(clearTimer);
  html.removeAttribute('data-rdc-page-load');
 };
 const click=event=>{
  if(event.defaultPrevented||event.button!==0||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey)return;
  const link=event.target.closest?.('a[href]');
  if(!link||link.hasAttribute('download')||(link.target&&link.target.toLowerCase()!=='_self'))return;
  const url=new URL(link.href,view.location.href);
  if(url.origin!==view.location.origin||!['http:','https:'].includes(url.protocol))return;
  if(url.pathname===view.location.pathname&&url.search===view.location.search)return;
  reset();
  showTimer=view.setTimeout(()=>{
   if(event.defaultPrevented||doc.hidden)return;
   html.setAttribute('data-rdc-page-load','');
   clearTimer=view.setTimeout(reset,CLEAR_DELAY);
  },SHOW_DELAY);
 };
 doc.addEventListener('click',click);
 view.addEventListener('pagehide',reset);
 view.addEventListener('pageshow',reset);
 return()=>{
  reset();
  doc.removeEventListener('click',click);
  view.removeEventListener('pagehide',reset);
  view.removeEventListener('pageshow',reset);
 };
}
