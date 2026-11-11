const stateKey=Symbol.for('radmanct.document-scroll-lock');

export function lockPanelScroll(doc, owner) {
  let state = doc[stateKey];
  if (!state) {
    const root=doc.documentElement; const view=doc.defaultView;
    const width=root?.getBoundingClientRect?.().width ?? root?.clientWidth;
    const gap=Number.isFinite(view?.innerWidth) && Number.isFinite(width) ? Math.max(0,view.innerWidth-width) : 0;
    state = {overflow:doc.body.style.overflow, rootOverflow:root?.style.overflow, gutter:root?.style.scrollbarGutter, padding:root?.style.paddingRight ?? '', gap, owners:new Set()};
    doc[stateKey]=state;
    if(view?.CustomEvent) doc.dispatchEvent(new view.CustomEvent('rdct:panel-scroll-lock',{detail:{locked:true,gap}}));
    if(root){
      const padding=view?.getComputedStyle?.(root).paddingRight || '0px';
      const stable=gap>0 && view?.CSS?.supports?.('scrollbar-gutter','stable');
      if(stable)root.style.scrollbarGutter='stable';
      else root.style.scrollbarGutter='auto';
      root.style.overflow='hidden';
      if(gap>0 && !stable)root.style.paddingRight=`calc(${padding} + ${gap}px)`;
    }
  }
  state.owners.add(owner); doc.body.style.overflow = 'hidden'; let released = false;
  return () => {
    if (released) return; released = true; state.owners.delete(owner);
    if (!state.owners.size) {
      doc.body.style.overflow = state.overflow;
      if(doc.documentElement){doc.documentElement.style.overflow=state.rootOverflow;doc.documentElement.style.scrollbarGutter=state.gutter;doc.documentElement.style.paddingRight=state.padding;}
      delete doc[stateKey];
      const view=doc.defaultView;
      if(view?.CustomEvent) doc.dispatchEvent(new view.CustomEvent('rdct:panel-scroll-lock',{detail:{locked:false}}));
    }
  };
}
