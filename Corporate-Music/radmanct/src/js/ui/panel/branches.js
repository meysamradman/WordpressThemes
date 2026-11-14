export function initializePanelBranches(dialog){
 const stage=dialog.querySelector?.('[data-rdc-pnl-br]');
 if(!stage)return {back:()=>false,reset(){},destroy(){}};
 const doc=dialog.ownerDocument;
 const content=stage.querySelector('[data-rdc-pnl-br-cnt]');
 const title=stage.querySelector('[data-rdc-pnl-br-ttl]');
 const backButton=stage.querySelector('[data-rdc-pnl-back]');
 const nav=dialog.querySelector('.rdc-nav'),info=dialog.querySelector('.rdc-pnl__info');
 const entries=new Map();
 let active=null;
 for(const button of dialog.querySelectorAll('[data-rdc-pnl-tgl]')){
  const menu=doc.getElementById(button.getAttribute('aria-controls'));
  if(!menu||!dialog.contains(menu))continue;
  const marker=doc.createComment('submenu');menu.before(marker);
  entries.set(button,{button,menu,marker,label:button.parentElement.querySelector(':scope > a')?.textContent.trim()||'',parentMenu:button.closest('.rdc-pnl-sub')});
 }
 const restore=entry=>{entry.menu.hidden=true;entry.button.setAttribute('aria-expanded','false');entry.marker.after(entry.menu);};
 const sync=()=>{
  nav.inert=Boolean(active);
  info.hidden=Boolean(active);info.inert=info.hidden;
 };
 const select=(entry,focus=true)=>{
  if(active)restore(active);
  active=entry;
  stage.hidden=!entry;
  dialog.toggleAttribute('data-panel-branch-open',Boolean(entry));
  if(entry){
   title.textContent=entry.label;content.append(entry.menu);entry.menu.hidden=false;
   entry.menu.scrollTop=0;entry.button.setAttribute('aria-expanded','true');
   if(focus)backButton.focus({preventScroll:true});
  }
  sync();
 };
 const back=()=>{
  if(!active)return false;
  const previous=active,parent=[...entries.values()].find(entry=>entry.menu===previous.parentMenu);
  select(parent||null);
  if(!parent)previous.button.focus({preventScroll:true});
  return true;
 };
 const click=event=>{
  if(event.target.closest('[data-rdc-pnl-back]')){back();return;}
  const entry=entries.get(event.target.closest('[data-rdc-pnl-tgl]'));
  if(entry)select(entry===active?null:entry);
 };
 const reset=()=>select(null,false);
 dialog.addEventListener('click',click);
 return {back,reset,destroy(){
  reset();dialog.removeEventListener('click',click);
  for(const entry of entries.values()){restore(entry);entry.marker.remove();}
 }};
}
