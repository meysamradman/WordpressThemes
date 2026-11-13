// Workshop-only: resize one existing browsing context, never the sample itself.
(()=>{
 const frame=document.querySelector('[data-rdl-pv-frame]');if(!frame)return;
 const workspace=document.querySelector('[data-rdl-pv-work]'),port=document.querySelector('[data-rdl-pv-port]'),shell=document.querySelector('[data-rdl-pv-shell]');
 const links=[...document.querySelectorAll('[data-rdl-pv-device]')],fitButton=document.querySelector('[data-rdl-pv-fit]'),dim=document.querySelector('[data-rdl-pv-dim]'),zoom=document.querySelector('[data-rdl-pv-zoom]');
 const events=new AbortController(),choices=new Map(links.map(link=>[link.dataset.rdlPvDevice,link]));
 let width=Number(frame.width),height=Number(frame.height),fit=true,raf,alive=true;
 const layout=()=>{
  raf=undefined;if(!alive)return;
  const style=getComputedStyle(workspace),availableWidth=Math.max(1,workspace.clientWidth-parseFloat(style.paddingLeft)-parseFloat(style.paddingRight)-20),availableHeight=Math.max(1,workspace.clientHeight-parseFloat(style.paddingTop)-parseFloat(style.paddingBottom)-20);
  const scale=fit?Math.min(1,availableWidth/width,availableHeight/height):1;
  port.style.width=`${width*scale}px`;port.style.height=`${height*scale}px`;shell.style.transform=`scale(${scale})`;zoom.textContent=`${Math.round(scale*100)}%`;
 };
 const queue=()=>{if(alive&&!raf)raf=requestAnimationFrame(layout);};
 const apply=link=>{
  const size=link.dataset.rdlPvSize.split('x').map(Number);if(size.length!==2||!size.every(n=>Number.isFinite(n)&&n>=240&&n<=2048))return;
  [width,height]=size;
  port.style.setProperty('--rdl-pv-w',`${width}px`);port.style.setProperty('--rdl-pv-h',`${height}px`);
  frame.width=width;frame.height=height;shell.dataset.rdlPvShell=link.dataset.rdlPvDevice;
  for(const item of links){if(item===link)item.setAttribute('aria-current','page');else item.removeAttribute('aria-current');}
  dim.textContent=`${width} × ${height}`;frame.title=`پیش‌نمایش ${link.textContent.trim()}`;queue();
 };
 for(const link of links)link.addEventListener('click',event=>{
  if(link.dataset.rdlPvDevice==='desktop'||event.button!==0||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey)return;
  event.preventDefault();apply(link);history.pushState(null,'',link.href);
 },{signal:events.signal});
 fitButton.addEventListener('click',()=>{fit=!fit;fitButton.setAttribute('aria-pressed',String(fit));queue();},{signal:events.signal});
 window.addEventListener('popstate',()=>{const key=new URL(location.href).searchParams.get('rdl_view')||'desktop';const link=choices.get(key);if(link&&key!=='desktop')apply(link);else if(link)location.assign(link.href);},{signal:events.signal});
 const observer=window.ResizeObserver?new ResizeObserver(queue):null;observer?.observe(workspace);
 if(!observer)window.addEventListener('resize',queue,{signal:events.signal});
 window.addEventListener('pagehide',event=>{if(event.persisted)return;alive=false;events.abort();observer?.disconnect();if(raf)cancelAnimationFrame(raf);},{signal:events.signal});
 queue();
})();
