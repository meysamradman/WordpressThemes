import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeSticky} from '../runtime/sticky.js';
const root=document.querySelector('[data-rdc-arc]');
if(root){let pending,serial=0,timer;const status=root.querySelector('.rdc-arc-sts');
 async function navigate(url,append=false,push=true){
  const target=new URL(url,location.href);if(target.origin!==location.origin)return;pending?.abort();const controller=new AbortController();pending=controller;const current=++serial;root.classList.add('is-load');root.setAttribute('aria-busy','true');
  try{const response=await fetch(target.href,{signal:controller.signal,credentials:'same-origin'});if(!response.ok)throw new Error('Archive request failed');const doc=new DOMParser().parseFromString(await response.text(),'text/html');const incoming=doc.querySelector('[data-rdc-arc] [data-rdc-res]');const view=root.querySelector('[data-rdc-res]');if(!incoming||!view)throw new Error('Archive response is incomplete');if(current!==serial)return;
   if(append){for(const card of incoming.querySelectorAll('.rdc-prjs>article'))view.querySelector('.rdc-prjs').append(card);view.querySelector('.rdc-prjs-pg')?.remove();const next=incoming.querySelector('.rdc-prjs-pg');if(next)view.append(next);}else{view.replaceChildren(...incoming.childNodes);const heading=doc.querySelector('.rdc-pg-ttl');const oldHeading=document.querySelector('.rdc-pg-ttl');if(heading&&oldHeading)oldHeading.replaceWith(heading);}
   for(const link of root.querySelectorAll('[data-rdc-categories] a,.rdc-cat-tree a')){const active=[...doc.querySelectorAll('[data-rdc-categories] a[aria-current],.rdc-cat-tree a[aria-current]')].some(item=>item.href===link.href);active?link.setAttribute('aria-current','page'):link.removeAttribute('aria-current');}document.title=doc.title;
   for(const selector of ['link[rel="canonical"]','meta[name="description"]','meta[property="og:title"]','meta[property="og:url"]','meta[name="twitter:title"]','script.yoast-schema-graph']){
    const fresh=doc.querySelector(selector),old=document.head.querySelector(selector);
    if(fresh){if(old)old.replaceWith(fresh);else document.head.append(fresh);}else old?.remove();
   }
   const newTitle=doc.querySelector('[data-rdc-arc] h1'),oldTitle=root.querySelector('h1');if(newTitle&&oldTitle)oldTitle.replaceWith(newTitle);
   if(push&&!append&&target.href!==location.href)history.pushState({...history.state,rdcArchive:true},'',target.href);
   if(push&&!append){view.setAttribute('tabindex','-1');view.focus({preventScroll:true});if(view.getBoundingClientRect().top<0)view.scrollIntoView({block:'start',behavior:'instant'});}
   if(status)status.textContent=append?'موارد بیشتر نمایش داده شد.':'نتایج به‌روز شد.';document.dispatchEvent(new CustomEvent('rdc:arc-upd'));root.dispatchEvent(new CustomEvent('rdc:arc-upd'));
  }catch(error){if(error.name!=='AbortError'&&current===serial){for(const link of root.querySelectorAll('.pagination a,[data-rdc-categories] a,.rdc-cat-tree a,.rdc-tags a'))if(link.href===target.href)link.dataset.fallback='true';if(status)status.textContent='دریافت نتایج انجام نشد؛ دوباره تلاش کنید.';if(append){const link=root.querySelector('[data-rdc-load-more]');if(link)link.dataset.fallback='true';}}}finally{if(current===serial){root.classList.remove('is-load');root.removeAttribute('aria-busy');}}}
 root.addEventListener('click',event=>{const link=event.target.closest('a');if(!link||event.defaultPrevented||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey||event.button!==0||link.target==='_blank'||link.hasAttribute('download'))return;if(link.dataset.fallback==='true'||new URL(link.href,location.href).origin!==location.origin)return;if(link.matches('[data-rdc-load-more],.pagination a,[data-rdc-categories] a,.rdc-cat-tree a,.rdc-tags a')){event.preventDefault();navigate(link.href,link.hasAttribute('data-rdc-load-more'));}});
 root.addEventListener('submit',event=>{if(!event.target.matches('[data-rdc-srch]'))return;event.preventDefault();clearTimeout(timer);const values=new FormData(event.target);const empty=!String(values.get('s')||'').trim();const url=new URL(empty?root.dataset.rdcBlogUrl:event.target.action,location.href);if(!empty)for(const [key,value]of values)url.searchParams.set(key,value);navigate(url);});
 root.addEventListener('input',event=>{if(!event.target.matches('[data-rdc-srch] input[type="search"]'))return;clearTimeout(timer);timer=setTimeout(()=>event.target.form.requestSubmit(),450);});
 window.addEventListener('popstate',()=>navigate(location.href,false,false));window.addEventListener('pagehide',()=>{clearTimeout(timer);pending?.abort();},{once:true});
}

const sidebar=document.querySelector('.rdc-blog-sb__cnt');
if(sidebar){const lifecycle=createPageLifecycle(window);lifecycle.mountRoot(sidebar,'natural-sidebar',()=>({}),()=>initializeSticky(sidebar,{target:sidebar,boundary:sidebar.closest('.rdc-cnt-ly'),gap:16}));}
