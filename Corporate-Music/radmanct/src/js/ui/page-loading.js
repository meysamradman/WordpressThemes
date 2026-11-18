const CLEAR_DELAY=8000;
const CURTAIN_KEY='rdc:page-curtain';

export function initializePageLoading(view=window){
 const doc=view.document,html=doc.documentElement;
 const preference=view.matchMedia('(prefers-reduced-motion:reduce)');
 let clearTimer,signal,ticket;
 const reset=(keepTicket=false)=>{
  signal?.removeEventListener('abort',cancel);
  signal=undefined;
  view.clearTimeout(clearTimer);
  if(ticket&&!keepTicket){
   try{
    if(view.sessionStorage.getItem(CURTAIN_KEY)===ticket)view.sessionStorage.removeItem(CURTAIN_KEY);
   }catch{}
  }
  ticket=undefined;
  if(html.hasAttribute('data-rdc-page-load')){
   html.removeAttribute('data-rdc-page-load');
   if(!keepTicket)doc.dispatchEvent(new Event('rdc:curtain-end'));
  }
 };
 const cancel=()=>reset();
 const leave=()=>reset(true);
 const restore=event=>{
  if(!event.persisted)return;
  reset();
  try{view.sessionStorage.removeItem(CURTAIN_KEY);}catch{}
 };
 const sameWindow=target=>!target||target.toLowerCase()==='_self';
 const start=(event,address)=>{
  const url=new URL(address,view.location.href);
  if(event.defaultPrevented||doc.hidden||preference.matches||url.origin!==view.location.origin||!['http:','https:'].includes(url.protocol))return;
  reset();
  signal=event.signal;
  signal?.addEventListener('abort',cancel,{once:true});
  ticket=JSON.stringify({expires:Date.now()+CLEAR_DELAY,destination:url.pathname+url.search});
  try{view.sessionStorage.setItem(CURTAIN_KEY,ticket);}catch{}
  html.setAttribute('data-rdc-page-load','leave');
  clearTimer=view.setTimeout(cancel,CLEAR_DELAY);
 };
 const navigate=event=>{
  if(event.destination.sameDocument||event.hashChange||event.downloadRequest!=null)return;
  start(event,event.destination.url);
 };
 const click=event=>{
  if(event.defaultPrevented||event.button!==0||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey)return;
  const link=event.target.closest?.('a[href]');
  if(!link||link.hasAttribute('download')||!sameWindow(link.target))return;
  const url=new URL(link.href,view.location.href);
  if(url.pathname===view.location.pathname&&url.search===view.location.search)return;
  start(event,url);
 };
 const submit=event=>{
  const form=event.target,button=event.submitter;
  if(event.defaultPrevented||form.method!=='get'||!sameWindow(button?.getAttribute('formtarget')??form.target))return;
  start(event,button?.getAttribute('formaction')??form.action);
 };
 const navigation=view.navigation;
 if(navigation)navigation.addEventListener('navigate',navigate);
 else{
  doc.addEventListener('click',click);
  doc.addEventListener('submit',submit);
 }
 view.addEventListener('pagehide',leave);
 view.addEventListener('pageshow',restore);
 return()=>{
  reset();
  if(navigation)navigation.removeEventListener('navigate',navigate);
  else{
   doc.removeEventListener('click',click);
   doc.removeEventListener('submit',submit);
  }
  view.removeEventListener('pagehide',leave);
  view.removeEventListener('pageshow',restore);
 };
}
