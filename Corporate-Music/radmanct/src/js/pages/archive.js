const root=document.querySelector('[data-rdc-arc]');
if(root){let pending,serial=0;const status=root.querySelector('.rdc-arc-sts');
 async function navigate(url,push=true){
  const target=new URL(url,location.href);if(target.origin!==location.origin)return;pending?.abort();const controller=new AbortController();pending=controller;const current=++serial;root.classList.add('is-load');root.setAttribute('aria-busy','true');
  try{const response=await fetch(target.href,{signal:controller.signal,credentials:'same-origin'});if(!response.ok)throw new Error('Archive request failed');const doc=new DOMParser().parseFromString(await response.text(),'text/html');const incoming=doc.querySelector('[data-rdc-arc] [data-rdc-res]');const view=root.querySelector('[data-rdc-res]');if(!incoming||!view)throw new Error('Archive response is incomplete');if(current!==serial)return;
   for(const media of view.querySelectorAll?.('audio,video')||[])media.pause();
   view.replaceChildren(...incoming.childNodes);const heading=doc.querySelector('.rdc-pg-ttl');const oldHeading=document.querySelector('.rdc-pg-ttl');if(heading&&oldHeading)oldHeading.replaceWith(heading);
   for(const link of root.querySelectorAll('[data-rdc-categories] a,.rdc-cat-tree a')){const active=[...doc.querySelectorAll('[data-rdc-categories] a[aria-current],.rdc-cat-tree a[aria-current]')].some(item=>item.href===link.href);active?link.setAttribute('aria-current','page'):link.removeAttribute('aria-current');}document.title=doc.title;
   for(const selector of ['link[rel="canonical"]','meta[name="description"]','meta[property="og:title"]','meta[property="og:url"]','meta[name="twitter:title"]','script.yoast-schema-graph']){
    const fresh=doc.querySelector(selector),old=document.head.querySelector(selector);
    if(fresh){if(old)old.replaceWith(fresh);else document.head.append(fresh);}else old?.remove();
   }
   const newTitle=doc.querySelector('[data-rdc-arc] h1'),oldTitle=root.querySelector('h1');if(newTitle&&oldTitle)oldTitle.replaceWith(newTitle);
   if(push&&target.href!==location.href)history.pushState({...history.state,rdcArchive:true},'',target.href);
   if(push){view.setAttribute('tabindex','-1');view.focus({preventScroll:true});if(view.getBoundingClientRect().top<0)view.scrollIntoView({block:'start',behavior:'instant'});}
   if(status)status.textContent='نتایج به‌روز شد.';document.dispatchEvent(new CustomEvent('rdc:arc-upd'));root.dispatchEvent(new CustomEvent('rdc:arc-upd'));
  }catch(error){if(error.name!=='AbortError'&&current===serial){for(const link of root.querySelectorAll('.pagination a,[data-rdc-categories] a,.rdc-cat-tree a,.rdc-tags a'))if(link.href===target.href)link.dataset.fallback='true';if(status)status.textContent='دریافت نتایج انجام نشد؛ دوباره تلاش کنید.';}}finally{if(current===serial){root.classList.remove('is-load');root.removeAttribute('aria-busy');}}}
 root.addEventListener('click',event=>{const link=event.target.closest('a');if(!link||event.defaultPrevented||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey||event.button!==0||link.target==='_blank'||link.hasAttribute('download'))return;if(link.dataset.fallback==='true'||new URL(link.href,location.href).origin!==location.origin)return;if(link.matches('.pagination a')||(!root.hasAttribute?.('data-rdc-pagination-only')&&link.matches('[data-rdc-categories] a,.rdc-cat-tree a,.rdc-tags a'))){event.preventDefault();navigate(link.href);}});
 if(root.dataset.rdcArc==='blog'){
  import('./blog.js').then(({initializeBlogArchive})=>initializeBlogArchive(root,navigate)).catch(()=>{});
 }
 window.addEventListener('popstate',()=>navigate(location.href,false));window.addEventListener('pagehide',()=>pending?.abort(),{once:true});
}
