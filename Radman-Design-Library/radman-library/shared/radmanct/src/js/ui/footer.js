export function initializeFooter(root,scroll){
 const link=root?.querySelector('[data-rdc-scr-top]');
 if(!link)return()=>{};
 const top=event=>{event.preventDefault();scroll.toTop();};
 link.addEventListener('click',top);
 return()=>link.removeEventListener('click',top);
}
