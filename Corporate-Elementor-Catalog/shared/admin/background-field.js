import {SelectControl,RangeControl,ButtonControl,FieldRow} from './controls.js';
import {ColorField} from './color-field.js';
import {pickMedia} from './image-picker.js';
const {createElement:h,useEffect,useRef} = window.wp.element;

export function BackgroundField({value = {}, onChange, labels, media = false}) {
  const s={type:'color',color:'',second:'#233640',gradient:'linear',angle:135,start:0,end:100,image:0,video:0,...value};
  const picker=useRef();
  const latest=useRef(); latest.current={s,onChange};
  useEffect(()=>()=>picker.current?.(),[]);
  const set=(key,next)=>{const draft={...latest.current.s,[key]:next};if(key==='start')draft.end=Math.max(draft.end,next);if(key==='end')draft.start=Math.min(draft.start,next);latest.current.onChange(draft);};
  const number=(key,label,max)=>h(FieldRow,{key,label},h(RangeControl,{label,numeric:true,min:0,max,value:s[key],unit:key==='angle'?'deg':'%',onChange:n=>set(key,n===''?0:n)}));
  const selectMedia=(key,type)=>{picker.current?.();picker.current=pickMedia({type,id:s[key],title:labels[key],onSelect:item=>set(key,item.id)});};
  return h('div',{className:'rdct-background-fields'},
    h(FieldRow,{label:labels.type},h(SelectControl,{'aria-label':labels.type,value:s.type,onChange:n=>set('type',n),options:[['color',labels.color],['gradient',labels.gradient],...(media?[['image',labels.image],['video',labels.video]]:[])]})),
    h(FieldRow,{label:labels.color},h(ColorField,{label:labels.color,showLabel:false,value:s.color,onChange:n=>set('color',n),resetLabel:labels.inherit})),
    s.type==='gradient' && h('div',null,
      h(FieldRow,{label:labels.second},h(ColorField,{label:labels.second,showLabel:false,value:s.second,onChange:n=>set('second',n),resetLabel:labels.inherit})),
      h(FieldRow,{label:labels.gradient},h(SelectControl,{'aria-label':labels.gradient,value:s.gradient,onChange:n=>set('gradient',n),options:[['linear',labels.linear],['radial',labels.radial]]})),
      s.gradient==='linear' && number('angle',labels.angle,360),number('start',labels.start,100),number('end',labels.end,100)),
    media && ['image','video'].includes(s.type) && h('div',null,
      ...['image',...(s.type==='video'?['video']:[])].map(key=>h(FieldRow,{key,label:s.type==='video' && key==='image'?labels.poster:labels[key]},
        h('div',{className:'rdct-background-fields__actions'},h(ButtonControl,{onClick:()=>selectMedia(key,key)},labels.choose),
        !!s[key] && h('span',null,` #${s[key]} `),!!s[key] && h(ButtonControl,{onClick:()=>set(key,0)},labels.remove))))));
}
