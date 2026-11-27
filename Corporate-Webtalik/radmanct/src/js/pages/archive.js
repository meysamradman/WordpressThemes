import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeSticky} from '../runtime/sticky.js';
const root=document.querySelector('[data-rdc-arc]');
const view=root?.querySelector('[data-rdc-res]');
if(root&&view){
 let pending,serial=0,timer;
 const status=root.querySelector('.rdc-arc-sts');
 const moreSelector='[data-rdc-load-more]';
 const loadedPages=new Set([location.href]);
 const observer=view.querySelector('[data-rdc-arc-items]')&&typeof IntersectionObserver==='function'
  ?new IntersectionObserver(entries=>{
    const link=view.querySelector(moreSelector);
    if(!pending&&link&&entries.some(entry=>entry.target===link&&entry.isIntersecting))navigate(link.href,true);
   })
  :null;
 function observeNext(){
  if(!observer)return;
  observer.disconnect();
  const link=view.querySelector(moreSelector);
  if(link&&link.dataset.fallback!=='true')observer.observe(link);
 }
 function appendResults(incoming){
  const list=view.querySelector('[data-rdc-arc-items]');
  const items=incoming.querySelector('[data-rdc-arc-items]');
  const link=view.querySelector(moreSelector);
  if(!list||!items?.children.length||!link)throw new Error('Archive results cannot be appended');
  const next=incoming.querySelector(moreSelector);
  const pager=link.closest('[data-rdc-arc-pg]');
  const focusNext=link===document.activeElement;
  const batch=[...items.children];
  for(const image of items.querySelectorAll('img'))image.loading='lazy';
  list.append(...batch);
  if(next)link.replaceWith(next);
  else pager?.remove();
  if(focusNext)batch[0].querySelector('a[href]:not([tabindex="-1"])')?.focus({preventScroll:true});
 }
 async function navigate(url,append=false,push=true){
  const target=new URL(url,location.href);
  if(target.origin!==location.origin||(append&&pending))return;
  observer?.disconnect();
  pending?.abort();
  const controller=new AbortController();
  pending=controller;
  const current=++serial;
  const loadingLink=append?view.querySelector(moreSelector):null;
  loadingLink?.setAttribute('aria-busy','true');
  if(!append)root.classList.add('is-load');
  root.setAttribute('aria-busy','true');
  if(status)status.textContent='در حال دریافت نتایج…';
  try{
   const response=await fetch(target.href,{signal:controller.signal,credentials:'same-origin'});
   const final=new URL(response.url||target.href);
   if(!response.ok||final.origin!==location.origin)throw new Error('Archive request failed');
   const html=await response.text();
   if(current!==serial)return;
   if(append){
    if(loadedPages.has(final.href))throw new Error('Archive page already loaded');
    const template=document.createElement('template');
    template.innerHTML=html;
    const incoming=template.content.querySelector('[data-rdc-arc] [data-rdc-res]');
    if(!incoming)throw new Error('Archive response is incomplete');
    appendResults(incoming);
    loadedPages.add(final.href);
   }else{
    const doc=new DOMParser().parseFromString(html,'text/html');
    const incoming=doc.querySelector('[data-rdc-arc] [data-rdc-res]');
    if(!incoming)throw new Error('Archive response is incomplete');
    view.replaceChildren(...incoming.childNodes);
    const heading=doc.querySelector('.rdc-pg-ttl');
    const oldHeading=document.querySelector('.rdc-pg-ttl');
    if(heading&&oldHeading)oldHeading.replaceWith(heading);
    for(const link of root.querySelectorAll('.rdc-cat-tree a')){
     const active=[...doc.querySelectorAll('.rdc-cat-tree a[aria-current]')].some(item=>item.href===link.href);
     active?link.setAttribute('aria-current','page'):link.removeAttribute('aria-current');
    }
    document.title=doc.title;
    if(push)history.pushState({},'',final.href);
    loadedPages.clear();
    loadedPages.add(final.href);
   }
   if(status)status.textContent=append?'موارد بیشتر نمایش داده شد.':'نتایج به‌روز شد.';
   document.dispatchEvent(new CustomEvent('rdc:arc-upd'));
   root.dispatchEvent(new CustomEvent('rdc:arc-upd'));
  }catch(error){
   if(current===serial&&error.name!=='AbortError'){
    if(status)status.textContent='دریافت نتایج انجام نشد؛ دوباره تلاش کنید.';
    if(loadingLink)loadingLink.dataset.fallback='true';
   }
  }finally{
   loadingLink?.removeAttribute('aria-busy');
   if(current===serial){
    pending=undefined;
    root.classList.remove('is-load');
    root.removeAttribute('aria-busy');
    observeNext();
   }
  }
 }
 root.addEventListener('click',event=>{
  const link=event.target.closest('a');
  if(!link||event.defaultPrevented||event.button!==0||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey||link.dataset.fallback==='true'||link.origin!==location.origin)return;
  if(link.matches('[data-rdc-load-more],.pagination a,.rdc-cat-tree a,.rdc-tags a')){
   event.preventDefault();
   navigate(link.href,link.matches(moreSelector));
  }
 });
 root.addEventListener('submit',event=>{if(!event.target.matches('[data-rdc-srch]'))return;event.preventDefault();clearTimeout(timer);const values=new FormData(event.target);const empty=!String(values.get('s')||'').trim();const url=new URL(empty?root.dataset.rdcBlogUrl:event.target.action,location.href);if(!empty)for(const [key,value]of values)url.searchParams.set(key,value);navigate(url);});
 root.addEventListener('input',event=>{if(!event.target.matches('[data-rdc-srch] input[type="search"]'))return;clearTimeout(timer);timer=setTimeout(()=>event.target.form.requestSubmit(),450);});
 window.addEventListener('popstate',()=>navigate(location.href,false,false));
 window.addEventListener('pagehide',()=>{
  observer?.disconnect();
  clearTimeout(timer);
  pending?.abort();
  pending=undefined;
  serial++;
  root.classList.remove('is-load');
  root.removeAttribute('aria-busy');
 });
 window.addEventListener('pageshow',event=>{if(event.persisted)observeNext();});
 observeNext();
}

const sidebar=document.querySelector('.rdc-blog-sb__cnt');
if(sidebar){const lifecycle=createPageLifecycle(window);lifecycle.mountRoot(sidebar,'natural-sidebar',()=>({}),()=>initializeSticky(sidebar,{target:sidebar,boundary:sidebar.closest('.rdc-cnt-ly'),gap:16}));}
