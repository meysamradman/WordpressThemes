const root=document.querySelector('[data-rdc-arc]');
const view=root?.querySelector('[data-rdc-res]');
if(root&&view){
 const status=root.querySelector('.rdc-arc-sts');
 const metadata='link[rel="canonical"],link[rel="prev"],link[rel="next"],meta[name="description"],meta[name="robots"],meta[property^="og:"],meta[property^="article:"],meta[name^="twitter:"],script.yoast-schema-graph,script.rank-math-schema';
 const key=url=>url.origin+url.pathname+url.search;
 let rendered=key(new URL(location.href));
 let pending,nextLink,loadedPage=rendered;
 const observer=typeof window.IntersectionObserver==='function'&&view.querySelector('[data-rdc-arc-items]')
  ?new window.IntersectionObserver(entries=>{
   if(!pending&&nextLink&&entries.some(entry=>entry.target===nextLink&&entry.isIntersecting))navigate(nextLink.href,true,true);
  },{rootMargin:'0px 0px 220px'})
  :null;
 const observeNext=()=>{
  if(!observer)return;
  observer.disconnect();
  nextLink=view.querySelector('.pagination a.next');
  if(!nextLink||nextLink.dataset.fallback==='true')return;
  nextLink.closest('.pagination').setAttribute('data-rdc-arc-auto','');
  observer.observe(nextLink);
 };
 const announceUpdate=message=>{
  if(status)status.textContent=message;
  document.dispatchEvent(new CustomEvent('rdc:arc-upd'));
  root.dispatchEvent(new CustomEvent('rdc:arc-upd'));
 };
 function appendResults(html,final){
  const template=document.createElement('template');
  template.innerHTML=html;
  const incoming=template.content.querySelector('[data-rdc-arc] [data-rdc-res]');
  const items=incoming?.querySelector('[data-rdc-arc-items]');
  if(!items?.children.length||key(final)===loadedPage)throw new Error('Archive page cannot be appended');
  for(const image of items.querySelectorAll('img'))image.loading='lazy';
  const batch=[...items.children];
  const pager=nextLink.closest('.pagination');
  const focused=pager.contains(document.activeElement);
  view.querySelector('[data-rdc-arc-items]').append(...batch);
  const nextPager=incoming.querySelector('.pagination');
  if(nextPager?.querySelector('a.next'))pager.replaceWith(nextPager);
  else pager.remove();
  loadedPage=key(final);
  observeNext();
  if(focused)(batch[0].matches('a')?batch[0]:batch[0].querySelector('a'))?.focus({preventScroll:true});
  announceUpdate('موارد بیشتری اضافه شد.');
 }
 const clearLoading=()=>{
  root.classList.remove('is-load');
  view.removeAttribute('aria-busy');
 };
 const cancel=()=>{
  if(pending){
   pending.link?.removeAttribute('aria-busy');
   clearTimeout(pending.indicator);
   clearTimeout(pending.timeout);
   pending.controller.abort();
   pending=undefined;
   if(status)status.textContent='';
  }
  clearLoading();
 };
 async function navigate(url,push=true,append=false){
  const target=new URL(url,location.href);
  if(target.origin!==location.origin||(append&&pending))return;
  observer?.disconnect();
  cancel();
  const controller=new AbortController();
  const request={controller,timedOut:false,link:append?nextLink:null};
  pending=request;
  view.setAttribute('aria-busy','true');
  if(status)status.textContent='';
  request.indicator=setTimeout(()=>{
   if(request.link)request.link.setAttribute('aria-busy','true');
   else root.classList.add('is-load');
   if(status)status.textContent='در حال دریافت نتایج…';
  },160);
  request.timeout=setTimeout(()=>{request.timedOut=true;controller.abort();},12000);
  try{
   const response=await fetch(target.href,{signal:controller.signal,credentials:'same-origin'});
   if(!response.ok)throw new Error('Archive request failed');
   const final=new URL(response.url||target.href);
   if(final.origin!==location.origin)throw new Error('Archive response has a different origin');
   const html=await response.text();
   if(pending!==request)return;
   if(append){appendResults(html,final);return;}
   const doc=new DOMParser().parseFromString(html,'text/html');
   const incoming=doc.querySelector('[data-rdc-arc] [data-rdc-res]');
   if(!incoming)throw new Error('Archive response is incomplete');
   for(const media of view.querySelectorAll?.('audio,video')||[])media.pause();
   view.replaceChildren(...incoming.childNodes);const heading=doc.querySelector('.rdc-pg-ttl');const oldHeading=document.querySelector('.rdc-pg-ttl');if(heading&&oldHeading)oldHeading.replaceWith(heading);
   for(const link of root.querySelectorAll('[data-rdc-categories] a,.rdc-cat-tree a')){const active=[...doc.querySelectorAll('[data-rdc-categories] a[aria-current],.rdc-cat-tree a[aria-current]')].some(item=>item.href===link.href);active?link.setAttribute('aria-current','page'):link.removeAttribute('aria-current');}document.title=doc.title;
   for(const node of document.head.querySelectorAll(metadata))node.remove();
   document.head.append(...doc.head.querySelectorAll(metadata));
   const newTitle=doc.querySelector('[data-rdc-arc] h1'),oldTitle=root.querySelector('h1');if(newTitle&&oldTitle)oldTitle.replaceWith(newTitle);
   if(final.href!==location.href){
    if(push)history.pushState({...history.state,rdcArchive:true},'',final.href);
    else history.replaceState(history.state,'',final.href);
   }
   rendered=key(final);
   loadedPage=rendered;
   observeNext();
   if(push){view.setAttribute('tabindex','-1');view.focus({preventScroll:true});if(view.getBoundingClientRect().top<0)view.scrollIntoView({block:'start',behavior:'instant'});}
   announceUpdate('نتایج به‌روز شد.');
  }catch(error){
   if(pending===request&&(error.name!=='AbortError'||request.timedOut)){
    if(!push){location.reload();return;}
    for(const link of root.querySelectorAll('.pagination a,[data-rdc-categories] a,.rdc-cat-tree a,.rdc-tags a')){
     if(link.href===target.href)link.dataset.fallback='true';
    }
    if(request.link)request.link.closest('.pagination').removeAttribute('data-rdc-arc-auto');
    if(status)status.textContent='دریافت نتایج انجام نشد؛ دوباره تلاش کنید.';
   }
  }finally{
   if(pending===request){
    clearTimeout(request.indicator);
    clearTimeout(request.timeout);
    request.link?.removeAttribute('aria-busy');
    pending=undefined;
    clearLoading();
   }
  }
 }
 root.addEventListener('click',event=>{const link=event.target.closest('a');if(!link||event.defaultPrevented||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey||event.button!==0||link.target==='_blank'||link.hasAttribute('download'))return;if(link.dataset.fallback==='true'||new URL(link.href,location.href).origin!==location.origin)return;if(link.matches('.pagination a')||(!root.hasAttribute?.('data-rdc-pagination-only')&&link.matches('[data-rdc-categories] a,.rdc-cat-tree a,.rdc-tags a'))){event.preventDefault();navigate(link.href,true,link===nextLink);}});
 if(root.dataset.rdcArc==='blog'){
  import('./blog.js').then(({initializeBlogArchive})=>initializeBlogArchive(root,navigate)).catch(()=>{});
 }
 window.addEventListener('popstate',()=>{
  if(key(new URL(location.href))===rendered){cancel();observeNext();return;}
  navigate(location.href,false);
 });
 window.addEventListener('pagehide',()=>{observer?.disconnect();cancel();});
 if(observer)window.addEventListener('pageshow',event=>{if(event.persisted)observeNext();});
 observeNext();
}
