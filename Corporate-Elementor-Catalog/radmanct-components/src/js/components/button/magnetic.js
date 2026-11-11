export function initializeMagnetic(root,{gsap}){
  let rect;let movers=[];
  const context=gsap.context(()=>{const targets=[root.dataset.magnetic==='true'?root:null,root.dataset.iconMagnetic==='true'?root.querySelector('.rdct-button__icon'):null].filter(Boolean);movers=targets.map(target=>[gsap.quickTo(target,'x',{duration:.35,ease:'power3.out'}),gsap.quickTo(target,'y',{duration:.35,ease:'power3.out'})]);},root);
  const enter=()=>{rect=root.getBoundingClientRect();};
  const move=event=>{if(!rect || event.pointerType==='touch')return;for(const [x,y] of movers){x(Math.max(-16,Math.min(16,(event.clientX-rect.left-rect.width/2)*.2)));y(Math.max(-12,Math.min(12,(event.clientY-rect.top-rect.height/2)*.2)));}};
  const reset=()=>{rect=null;for(const [x,y] of movers){x(0);y(0);}};
  const view=root.ownerDocument.defaultView;
  root.addEventListener('pointerenter',enter);root.addEventListener('pointermove',move,{passive:true});root.addEventListener('pointerleave',reset);view.addEventListener('scroll',reset,{passive:true});view.addEventListener('resize',reset);
  return()=>{root.removeEventListener('pointerenter',enter);root.removeEventListener('pointermove',move);root.removeEventListener('pointerleave',reset);view.removeEventListener('scroll',reset);view.removeEventListener('resize',reset);context.revert();};
}
