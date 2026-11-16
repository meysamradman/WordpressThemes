import {createPageLifecycle} from '../../shared/radmanct/src/js/runtime/lifecycle.js';
import {observeSizes} from '../../shared/radmanct/src/js/runtime/observers.js';
export function initializeDepth(root){
 const doc=root.ownerDocument,view=doc.defaultView,stage=root.querySelector('[data-rdl-depth-stage]');
 const cards=[...root.querySelectorAll('[data-rdl-depth-card]')],dots=[...root.querySelectorAll('[data-rdl-depth-dot]')],controls=root.querySelector('.rdl-depth__ctrl'),status=root.querySelector('[data-rdl-depth-status]');
 if(cards.length<2)return()=>{};
 const sign=getComputedStyle(root).direction==='rtl'?-1:1,listeners=[];
 let index=Math.min(3,cards.length-1),step=180,pointer=null,frame=0,drag=0,moved=false;
 const wrap=n=>((n%cards.length)+cards.length)%cards.length;
 const listen=(node,type,handler,options)=>{node.addEventListener(type,handler,options);listeners.push(()=>node.removeEventListener(type,handler,options));};
 const paint=(position=index)=>{
  cards.forEach((card,i)=>{
   const delta=wrap(i-position+cards.length/2)-cards.length/2,abs=Math.abs(delta);
   const props={'x':`${delta*step*sign}px`,'y':`${Math.min(abs,3)*11}px`,'scale':String(1-Math.min(abs,3)*.09),'order':String(Math.round(100-abs*10)),'opacity':String(Math.max(0,Math.min(1,4-abs)))};
   for(const [key,value] of Object.entries(props))card.style.setProperty(`--rdl-depth-${key}`,value);
   card.dataset.rdlDepthActive=String(i===index);
   card.style.pointerEvents=abs>=4?'none':'';
   card.setAttribute('aria-hidden',String(abs>=4));
  });
 };
 const select=next=>{index=wrap(next);paint();dots.forEach((dot,i)=>{if(i===index)dot.setAttribute('aria-current','true');else dot.removeAttribute('aria-current');});status.textContent=cards[index].getAttribute('aria-label');};
 const resize=()=>{step=Math.min(stage.clientWidth*.16,195);if(stage.clientWidth<600)step=stage.clientWidth*.57;paint();};
 listen(root.querySelector('[data-rdl-depth-prev]'),'click',()=>select(index-1));
 listen(root.querySelector('[data-rdl-depth-next]'),'click',()=>select(index+1));
 dots.forEach((dot,i)=>listen(dot,'click',()=>select(i)));
 listen(stage,'keydown',event=>{
  if(!['ArrowLeft','ArrowRight','Home','End'].includes(event.key))return;
  event.preventDefault();select(event.key==='Home'?0:event.key==='End'?cards.length-1:index+(event.key==='ArrowRight'?sign:-sign));
 });
 listen(stage,'pointerdown',event=>{if(event.button!==0||pointer)return;pointer={id:event.pointerId,x:event.clientX,y:event.clientY};drag=0;moved=false;});
 listen(stage,'pointermove',event=>{
  if(!pointer||event.pointerId!==pointer.id)return;
  const x=event.clientX-pointer.x,y=event.clientY-pointer.y;
  if(!moved&&Math.abs(y)>Math.abs(x)&&Math.abs(y)>8){pointer=null;return;}
  if(Math.abs(x)>6){moved=true;stage.setPointerCapture(event.pointerId);root.dataset.rdlDepthDrag='true';}
  if(!moved)return;drag=Math.max(-1.2,Math.min(1.2,x/(step*sign)));
  if(!frame)frame=view.requestAnimationFrame(()=>{frame=0;paint(index-drag);});
 });
 const finish=event=>{if(!pointer||event.pointerId!==pointer.id)return;if(frame)view.cancelAnimationFrame(frame);frame=0;pointer=null;delete root.dataset.rdlDepthDrag;if(stage.hasPointerCapture(event.pointerId))stage.releasePointerCapture(event.pointerId);select(event.type==='pointercancel'?index:index-(Math.abs(drag)>.18?Math.sign(drag):0));};
 listen(stage,'pointerup',finish);listen(stage,'pointercancel',finish);listen(stage,'lostpointercapture',event=>{if(pointer)finish(event);});
 listen(stage,'click',event=>{if(moved){moved=false;return;}const card=event.target.closest('[data-rdl-depth-card]');if(card)select(cards.indexOf(card));});
 listen(stage,'dragstart',event=>event.preventDefault());
 root.dataset.rdlDepthReady='true';controls.hidden=false;resize();select(index);
 const releaseSize=observeSizes(view,[stage],resize);
 return()=>{if(frame)view.cancelAnimationFrame(frame);if(pointer&&stage.hasPointerCapture(pointer.id))stage.releasePointerCapture(pointer.id);for(const release of listeners)release();releaseSize();controls.hidden=true;delete root.dataset.rdlDepthReady;delete root.dataset.rdlDepthDrag;for(const card of cards){card.removeAttribute('style');card.removeAttribute('aria-hidden');delete card.dataset.rdlDepthActive;}};
}
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdl-depth]'))page.mountRoot(root,'depth-carousel',()=>({}),()=>initializeDepth(root));
