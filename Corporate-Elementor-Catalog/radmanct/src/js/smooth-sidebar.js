export function pinSmoothSidebars(content, gsap, ScrollTrigger) {
  const doc=content.ownerDocument, view=doc.defaultView;
  const media=gsap.matchMedia();
  media.add('(min-width:64.01rem)',()=>{
    const roots=[...content.querySelectorAll('[data-rdct-sticky-sidebar]')];
    const parents=roots.map(root=>root.parentElement);
    for(const root of roots){root.setAttribute('data-rdct-smooth-sidebar','');}
    const triggers=roots.map((root,index)=>{
      const offset=()=>{
        const style=view.getComputedStyle(root);
        const natural=parseFloat(style.getPropertyValue('--rdct-natural-sticky-top'));
        if(root.hasAttribute?.('data-rdct-natural-sidebar') && Number.isFinite(natural)) return natural;
        return (parseFloat(style.getPropertyValue('--rdct-blog-sticky-offset')) || parseFloat(style.getPropertyValue('--rdct-blog-admin-offset')) || 0)+(parseFloat(view.getComputedStyle(doc.documentElement).fontSize) || 16);
      };
      const anchor=value=>`top${value < 0 ? '-=' : '+='}${Math.abs(value)}`;
      return ScrollTrigger.create({trigger:root,pin:root,pinType:'transform',pinSpacing:false,endTrigger:parents[index],start:()=>`top ${anchor(offset())}`,end:()=>`bottom ${anchor(offset()+root.offsetHeight)}`,invalidateOnRefresh:true});
    });
    let frame=0;
    const refresh=()=>{if(!frame)frame=view.requestAnimationFrame(()=>{frame=0;ScrollTrigger.refresh();});};
    const observer=typeof view.ResizeObserver==='function' ? new view.ResizeObserver(refresh) : null;
    for(const parent of parents)observer?.observe(parent);
    doc.addEventListener('rdct:archive-updated',refresh);doc.addEventListener('rdct:sidebar-offset',refresh);
    ScrollTrigger.refresh();
    return()=>{if(frame)view.cancelAnimationFrame(frame);observer?.disconnect();doc.removeEventListener('rdct:archive-updated',refresh);doc.removeEventListener('rdct:sidebar-offset',refresh);triggers.forEach(trigger=>trigger.kill());for(const root of roots){root.removeAttribute('data-rdct-smooth-sidebar');}};
  });
  return()=>media.revert();
}
