const box=document.getElementById('rdc-home-featured');
const nonce=box?.querySelector('[name="rdc_home_featured_nonce"]')?.value;

if(nonce){
 for(const picker of box.querySelectorAll('[data-rdc-track-picker]')){
  const search=picker.querySelector('input[type="search"]');
  const select=picker.querySelector('select');
  const nav=picker.querySelector('[data-rdc-track-nav]');
  const prev=nav.querySelector('[data-rdc-track-prev]');
  const next=nav.querySelector('[data-rdc-track-next]');
  const status=nav.querySelector('[role="status"]');
  const step=Number(picker.dataset.step);
  let offset=0,more=picker.dataset.more==='1',query='',request,debounce;

  const sync=()=>{
   const busy=Boolean(request||debounce);
   prev.disabled=busy||offset===0;
   next.disabled=busy||!more;
  };

  async function load(term,start){
   request?.abort();
   const controller=new AbortController();
   request=controller;
   const timeout=setTimeout(()=>controller.abort(),10000);
   sync();
   select.setAttribute('aria-busy','true');
   status.textContent='';
   const indicator=setTimeout(()=>{
    if(request===controller)status.textContent='در حال بارگذاری آهنگ‌ها…';
   },160);
   try{
    const body=new URLSearchParams({action:'rdc_home_tracks',nonce,page:picker.dataset.page,search:term,offset:String(start)});
    const response=await fetch(window.ajaxurl,{method:'POST',body,signal:controller.signal});
    if(!response.ok)throw new Error('Track lookup failed');
    const result=await response.json();
    if(!result.success||!Array.isArray(result.data?.items))throw new Error('Invalid track results');
    if(request!==controller)return;

    const selected=select.value;
    const chosen=select.selectedOptions[0];
    const options=result.data.items.map(({id,title})=>new Option(title,String(id)));
    if(selected&&!options.some(option=>option.value===selected))options.unshift(chosen);
    select.replaceChildren(select.options[0],...options);
    select.value=selected;
    query=term;
    offset=start;
    more=result.data.more;
    status.textContent=!result.data.items.length?'آهنگی پیدا نشد.':'نتایج آماده‌اند. آهنگ را از فهرست انتخاب کنید.';
   }catch{
    if(request===controller)status.textContent='بارگذاری انجام نشد. برای تلاش دوباره Enter را بزنید.';
   }finally{
    clearTimeout(timeout);
    clearTimeout(indicator);
    if(request===controller){
     request=undefined;
     select.removeAttribute('aria-busy');
     sync();
    }
   }
  }

  const searchNow=()=>{
   clearTimeout(debounce);
   debounce=undefined;
   load(search.value.trim(),0);
  };
  search.addEventListener('input',()=>{
   request?.abort();
   request=undefined;
   clearTimeout(debounce);
   status.textContent='';
   debounce=setTimeout(searchNow,250);
   sync();
  });
  search.addEventListener('keydown',event=>{
   if(event.key==='Enter'&&!event.isComposing){
    event.preventDefault();
    searchNow();
   }
  });
  prev.addEventListener('click',()=>load(query,offset-step));
  next.addEventListener('click',()=>load(query,offset+step));
  picker.querySelector('[data-rdc-track-search]').hidden=false;
  nav.hidden=false;
  sync();
 }
}

