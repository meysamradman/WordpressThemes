const root=document.querySelector('[data-rdc-arc]');
const view=root?.querySelector('[data-rdc-res]');
if(root&&view){
 const status=root.querySelector('.rdc-arc-sts');
 const metadata='link[rel="canonical"],link[rel="prev"],link[rel="next"],meta[name="description"],meta[name="robots"],meta[property^="og:"],meta[property^="article:"],meta[name^="twitter:"],script.yoast-schema-graph,script.rank-math-schema';
 const key=url=>url.origin+url.pathname+url.search;
 let rendered=key(new URL(location.href));
 let pending;
 const clearLoading=()=>{
  root.classList.remove('is-load');
  view.removeAttribute('aria-busy');
 };
 const cancel=()=>{
  if(pending){
   clearTimeout(pending.indicator);
   clearTimeout(pending.timeout);
   pending.controller.abort();
   pending=undefined;
   if(status)status.textContent='';
  }
  clearLoading();
 };
 async function navigate(url,push=true){
  const target=new URL(url,location.href);
  if(target.origin!==location.origin)return;
  cancel();
  const controller=new AbortController();
  const request={controller,timedOut:false};
  pending=request;
  view.setAttribute('aria-busy','true');
  if(status)status.textContent='';
  request.indicator=setTimeout(()=>{
   root.classList.add('is-load');
   if(status)status.textContent='در حال دریافت نتایج…';
  },160);
  request.timeout=setTimeout(()=>{request.timedOut=true;controller.abort();},12000);
  try{
   const response=await fetch(target.href,{signal:controller.signal,credentials:'same-origin'});
   if(!response.ok)throw new Error('Archive request failed');
   const final=new URL(response.url||target.href);
   if(final.origin!==location.origin)throw new Error('Archive response has a different origin');
   const doc=new DOMParser().parseFromString(await response.text(),'text/html');
   const incoming=doc.querySelector('[data-rdc-arc] [data-rdc-res]');
   if(!incoming)throw new Error('Archive response is incomplete');
   if(pending!==request)return;
   for(const media of view.querySelectorAll?.('audio,video')||[])media.pause();
   view.replaceChildren(...incoming.childNodes);const heading=doc.querySelector('.rdc-pg-ttl');const oldHeading=document.querySelector('.rdc-pg-ttl');if(heading&&oldHeading)oldHeading.replaceWith(heading);
   for(const link of root.querySelectorAll('[data-rdc-categories] a,.rdc-cat-tree a')){const active=[...doc.querySelectorAll('[data-rdc-categories] a[aria-current],.rdc-cat-tree a[aria-current]')].some(item=>item.href===link.href);active?link.setAttribute('aria-current','page'):link.removeAttribute('aria-current');}document.title=doc.title;
   for(const node of document.head.querySelectorAll(metadata))node.remove();
   document.head.append(...doc.querySelectorAll(metadata));
   const newTitle=doc.querySelector('[data-rdc-arc] h1'),oldTitle=root.querySelector('h1');if(newTitle&&oldTitle)oldTitle.replaceWith(newTitle);
   if(final.href!==location.href){
    if(push)history.pushState({...history.state,rdcArchive:true},'',final.href);
    else history.replaceState(history.state,'',final.href);
   }
   rendered=key(final);
   if(push){view.setAttribute('tabindex','-1');view.focus({preventScroll:true});if(view.getBoundingClientRect().top<0)view.scrollIntoView({block:'start',behavior:'instant'});}
   if(status)status.textContent='نتایج به‌روز شد.';document.dispatchEvent(new CustomEvent('rdc:arc-upd'));root.dispatchEvent(new CustomEvent('rdc:arc-upd'));
  }catch(error){
   if(pending===request&&(error.name!=='AbortError'||request.timedOut)){
    if(!push){location.reload();return;}
    for(const link of root.querySelectorAll('.pagination a,[data-rdc-categories] a,.rdc-cat-tree a,.rdc-tags a')){
     if(link.href===target.href)link.dataset.fallback='true';
    }
    if(status)status.textContent='دریافت نتایج انجام نشد؛ دوباره تلاش کنید.';
   }
  }finally{
   if(pending===request){
    clearTimeout(request.indicator);
    clearTimeout(request.timeout);
    pending=undefined;
    clearLoading();
   }
  }
 }
 root.addEventListener('click',event=>{const link=event.target.closest('a');if(!link||event.defaultPrevented||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey||event.button!==0||link.target==='_blank'||link.hasAttribute('download'))return;if(link.dataset.fallback==='true'||new URL(link.href,location.href).origin!==location.origin)return;if(link.matches('.pagination a')||(!root.hasAttribute?.('data-rdc-pagination-only')&&link.matches('[data-rdc-categories] a,.rdc-cat-tree a,.rdc-tags a'))){event.preventDefault();navigate(link.href);}});
 if(root.dataset.rdcArc==='blog'){
  import('./blog.js').then(({initializeBlogArchive})=>initializeBlogArchive(root,navigate)).catch(()=>{});
 }
 window.addEventListener('popstate',()=>{
  if(key(new URL(location.href))===rendered){cancel();return;}
  navigate(location.href,false);
 });window.addEventListener('pagehide',cancel);
}
