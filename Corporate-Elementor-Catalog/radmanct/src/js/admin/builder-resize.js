import {deviceKey} from './builder-devices.js';

const {createElement:h,useRef,useState} = window.wp.element;
export const resizePercent = (width, parentWidth, delta = 0) => Math.max(5,Math.min(100,Math.round((width + delta) / Math.max(1,parentWidth) * 100)));

export function ResizeHandle({target,parent,item,device,rtl,label,scale,onCommit}) {
  const drag = useRef(null);
  const [draft,setDraft] = useState(null);
  if (!parent?.rect.width) return null;
  const initial = resizePercent(target.rect.width,parent.rect.width);
  const commit = (value) => onCommit({...item.appearance,[deviceKey(device,'width')]:'custom',[deviceKey(device,'customWidth')]:value});
  return h('div',{className:'rdct-bld-resize'},
    draft !== null && h('div',{className:'rdct-bld-resize__guide',style:{inlineSize:parent.rect.width*draft/100}}),
    draft !== null && h('output',{className:'rdct-bld-resize__value'},`${draft}%`),
    h('button',{type:'button',className:'rdct-bld-resize__handle',role:'slider','aria-label':label,'aria-valuemin':5,'aria-valuemax':100,'aria-valuenow':draft ?? initial,'aria-orientation':'horizontal',
      onClick:(event)=>event.stopPropagation(),
      onPointerDown:(event)=>{event.preventDefault();event.stopPropagation();event.currentTarget.setPointerCapture(event.pointerId);drag.current={x:event.clientX,value:initial};setDraft(initial);},
      onPointerMove:(event)=>{if (!drag.current) return; const value=resizePercent(target.rect.width,parent.rect.width,(event.clientX-drag.current.x)*(rtl?-1:1)/scale);drag.current.value=value;setDraft(value);},
      onPointerUp:()=>{if (drag.current) commit(drag.current.value);drag.current=null;setDraft(null);},
      onPointerCancel:()=>{drag.current=null;setDraft(null);},
      onKeyDown:(event)=>{if (event.key === 'Escape') {drag.current=null;setDraft(null);return;} if (!['ArrowLeft','ArrowRight','Home','End'].includes(event.key)) return;event.preventDefault();event.stopPropagation();const step=(event.shiftKey?10:1)*(event.key==='ArrowRight'?1:-1)*(rtl?-1:1);commit(event.key==='Home'?5:event.key==='End'?100:Math.max(5,Math.min(100,initial+step)));},
    }));
}
