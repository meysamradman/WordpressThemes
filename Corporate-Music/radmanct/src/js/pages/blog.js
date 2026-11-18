import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeSticky} from '../runtime/sticky.js';

export function initializeBlogArchive(root,navigate){
 const form=root.querySelector('[data-rdc-srch]');
 if(form){
  const input=form.querySelector('input[type="search"]');
  let timer;
  form.addEventListener('submit',event=>{
   event.preventDefault();
   clearTimeout(timer);
   const values=new FormData(form);
   const hasSearch=input.value.trim()!=='';
   const url=new URL(hasSearch?form.action:root.dataset.rdcBlogUrl,location.href);
   if(hasSearch)url.search=new URLSearchParams(values).toString();
   navigate(url);
  });
  input.addEventListener('input',()=>{
   clearTimeout(timer);
   timer=setTimeout(()=>form.requestSubmit(),450);
  });
  window.addEventListener('pagehide',()=>clearTimeout(timer),{once:true});
 }

 const sidebar=root.querySelector('.rdc-blog-sb__cnt');
 if(sidebar){
  const lifecycle=createPageLifecycle(window);
  lifecycle.mountRoot(sidebar,'natural-sidebar',()=>({}),()=>initializeSticky(sidebar,{
   target:sidebar,boundary:sidebar.closest('.rdc-cnt-ly'),gap:16
  }));
 }
}
