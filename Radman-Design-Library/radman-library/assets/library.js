const root=document.querySelector('[data-rdl-lib]');
if(root){
 let pending,serial=0,timer;
 const status=root.querySelector('[data-rdl-sts]');
 const navigate=async(url,{push=true,paging=false}={})=>{
  const target=new URL(url,location.href);if(target.origin!==location.origin)return;
  pending?.abort();const controller=new AbortController();pending=controller;const current=++serial;
  root.classList.add('is-load');root.querySelector('[data-rdl-res]')?.setAttribute('aria-busy','true');status.textContent='در حال دریافت طراحی‌ها…';
  try{
   const response=await fetch(target,{signal:controller.signal,credentials:'same-origin'});
   if(!response.ok)throw new Error('Library request failed');
   const doc=new DOMParser().parseFromString(await response.text(),'text/html');
   const incoming=doc.querySelector('[data-rdl-res]');if(!incoming)throw new Error('Library response missing');if(current!==serial||controller.signal.aborted)return;
   const active=document.activeElement,id=active?.id,activeUrl=active?.tagName==='A'?active.href:null,selection=typeof active?.selectionStart==='number'?[active.selectionStart,active.selectionEnd]:null;
   root.querySelector('[data-rdl-res]').replaceWith(incoming);
   for(const selector of ['.rdl-sb']){const next=doc.querySelector(selector);if(next)root.querySelector(selector)?.replaceWith(next);}
   document.title=doc.title;if(push)history.pushState({},'',target.href);
   status.textContent='طراحی‌ها به‌روز شدند.';
   if(id){const next=document.getElementById(id);next?.focus({preventScroll:true});if(selection&&next?.setSelectionRange)next.setSelectionRange(...selection);}
   else if(activeUrl)[...root.querySelectorAll('a')].find(link=>link.href===activeUrl)?.focus({preventScroll:true});
   if(paging)root.querySelector('[data-rdl-res]').scrollIntoView({block:'start',behavior:matchMedia('(prefers-reduced-motion:reduce)').matches?'instant':'smooth'});
  }catch(error){if(error.name!=='AbortError'&&current===serial){status.textContent='دریافت انجام نشد؛ در حال بازکردن صفحه…';location.assign(target.href);}}
  finally{if(current===serial){root.classList.remove('is-load');root.querySelector('[data-rdl-res]')?.removeAttribute('aria-busy');}}
 };
 const formUrl=form=>{const url=new URL(form.action,location.href);url.search='';for(const [key,value] of new FormData(form))if(String(value).trim())url.searchParams.set(key,value);return url.href;};
 root.addEventListener('click',event=>{
  const link=event.target.closest('a');if(!link||event.defaultPrevented||event.button!==0||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey)return;
  if(link.matches('.rdc-cat-tree a,.rdl-all,.pagination a')){event.preventDefault();clearTimeout(timer);navigate(link.href,{paging:link.matches('.pagination a')});}
 });
 root.addEventListener('submit',event=>{if(!event.target.matches('[data-rdl-srch]'))return;event.preventDefault();clearTimeout(timer);navigate(formUrl(event.target));});
 root.addEventListener('input',event=>{
  if(event.target.matches('[data-rdl-srch] input[type=search]')){pending?.abort();++serial;clearTimeout(timer);timer=setTimeout(()=>navigate(formUrl(event.target.form)),350);}
 });
 window.addEventListener('popstate',()=>{clearTimeout(timer);navigate(location.href,{push:false});});
 window.addEventListener('pagehide',()=>{clearTimeout(timer);pending?.abort();},{once:true});
}
