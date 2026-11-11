export function initializePanelContent(dialog,trigger,beforeChange=()=>{}){
 const mobile=dialog.querySelector?.('[data-rdc-pnl-mob]'),host=dialog.querySelector?.('[data-rdc-pnl-desk-host]'),template=dialog.querySelector?.('[data-rdc-pnl-desk]');
 if(!mobile||!host||!template)return {prepare(){},destroy(){}};
 const view=dialog.ownerDocument.defaultView,desktop=view.matchMedia('(min-width:1024px)');
 const mobileLabel=dialog.getAttribute('aria-label'),triggerLabel=trigger?.getAttribute('aria-label');let mounted=false;
 const sync=()=>{
  beforeChange();
  mobile.hidden=desktop.matches;mobile.inert=desktop.matches;host.hidden=!desktop.matches;host.inert=!desktop.matches;
  if(desktop.matches&&dialog.open&&!mounted){host.append(template.content.cloneNode(true));mounted=true;}
  dialog.setAttribute('aria-label',desktop.matches?template.dataset.label:mobileLabel);
  trigger?.setAttribute('aria-label',desktop.matches?template.dataset.label:triggerLabel);
  if(dialog.open){dialog.scrollTop=0;dialog.querySelector('[data-rdc-menu-cls]')?.focus({preventScroll:true});}
 };
 desktop.addEventListener('change',sync);sync();
 return {prepare:sync,destroy(){desktop.removeEventListener('change',sync);host.replaceChildren();mounted=false;mobile.hidden=false;mobile.inert=false;host.hidden=true;host.inert=true;dialog.setAttribute('aria-label',mobileLabel);trigger?.setAttribute('aria-label',triggerLabel);}};
}
