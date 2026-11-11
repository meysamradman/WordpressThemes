import {SelectControl,InputControl,SwitchControl,RangeControl,ButtonControl} from '../../../../shared/admin/controls.js';
import {pickImage} from '../../../../shared/admin/image-picker.js';
import {ColorField} from '../../../../shared/admin/color-field.js';
import {BuilderIconPicker} from './builder-icon-picker.js';
import {TabList} from '../../../../shared/admin/panel-tabs.js';
const {createElement:h,useState}=window.wp.element;
export function buttonControlState(key,field,responsive,current,legacy,fields,device){
  const base=current[key] ?? legacy[key] ?? field.effect_defaults?.[current.text_effect] ?? field.default;
  let value=responsive?current[`${key}_responsive`]?.[device] ?? (device==='desktop'?base:''):base;
  if(responsive && field.default===-1000 && Number(value)===-1000)value='';
  const unitKey=responsive?.unit_field;
  const unit=unitKey?current[`${unitKey}_responsive`]?.[device] ?? current[unitKey] ?? fields[unitKey]?.default ?? responsive.default_unit:'duration'===key || 'scroll_speed'===key?'s':key.includes('angle') || key.includes('rotation')?'deg':/gradient_(start|end)$/.test(key)?'%':'px';
  const range=responsive?.range?.[unit] || responsive || field;
  return {value,unit,min:range.min ?? field.min,max:range.max ?? field.max,step:responsive?.step ?? (['duration','scroll_speed'].includes(key)?.1:1)};
}
export function ButtonFields({item,update,config,labels,device='desktop',section='style'}){
  const [group,setGroup]=useState('appearance');
  const fields=config.buttonFields || {};const current=item.buttonOptions || {};
  const legacy={style:item.buttonStyle || 'solid',background:item.buttonBackground || '',padding_x:item.buttonPaddingX ?? 19,padding_y:item.buttonPaddingY ?? 10,radius:item.buttonRadius ?? fields.radius?.default};
  const change=(key,value)=>update({buttonOptions:{...current,[key]:value}});
  const typography=([key])=>['font_size','font_size_unit','font_weight','font_style','text_transform','text_decoration','line_height','line_height_unit','letter_spacing','letter_spacing_unit','word_spacing','word_spacing_unit'].includes(key);
  const groups=Object.entries(config.buttonGroups || {}).filter(([key])=>key!=='content');
  groups.splice(1,0,['typography',labels.typography]);
  const visible=Object.entries(fields).filter(([key,field])=>{
    const fieldGroup=field.group==='appearance' && typography([key])?'typography':field.group;
    if(fieldGroup!==(section==='content'?'content':group))return false;
    return Object.entries(field.condition || {}).every(([key,values])=>values.includes(current[key] ?? fields[key]?.default));
  });
  const shadow=([key])=>/^(hover_)?shadow_/.test(key);
  const iconColors=([key])=>/^(hover_)?(icon_|separator_color|ripple_color)/.test(key);
  const secondary=section==='style' && ['normal','hover'].includes(group) ? [[labels.iconColors,iconColors],[labels.shadow,shadow]] : [];
  const renderField=([key,field])=>{
    const responsive=config.buttonResponsive?.[key];
    const state=buttonControlState(key,field,responsive,current,legacy,fields,device);
    const {value}=state;
    const onChange=next=>responsive?change(`${key}_responsive`,{...(current[`${key}_responsive`] || {}),[device]:next}):change(key,next);
    let control;
    if(field.media)control=h(ButtonControl,{onClick:()=>pickImage({title:field.label,id:Number(value?.id ?? value) || 0,onSelect:image=>change(key,image.id)})},field.label);
    else if(key==='icon')control=h(BuilderIconPicker,{value,onChange,config,labels});
    else if(field.color)return h(ColorField,{key,id:`rdct-button-${item.id}-${key}`,label:field.label,value,onChange,resetLabel:labels.inheritAppearance});
    else if(field.options)control=h(SelectControl,{value,onChange,options:[...(responsive && device!=='desktop' && !Object.hasOwn(field.options,'')?[['',labels.inheritAppearance]]:[]),...Object.entries(field.options)]});
    else if(typeof field.default==='boolean')control=h(SwitchControl,{checked:value,onChange});
    else if(field.min!=null)control=h(RangeControl,{...state,onChange,label:field.label,numeric:false,placeholder:labels.inheritAppearance,fallback:state.min<0?0:state.min});
    else control=h(InputControl,{value,onChange,maxLength:500});
    return h('label',{key},field.label,control);
  };
  return h('div',null,section==='style' && h(TabList,{id:`rdct-button-style-${item.id}`,className:'rdct-bld-style-tabs',value:group,onChange:setGroup,tabs:groups.map(([id,label])=>({id,label:({normal:labels.normalState,hover:labels.hoverState,sticky:labels.stickyState})[id] || label}))}),
    h('div',section==='style'?{role:'tabpanel',id:`rdct-button-style-${item.id}-${group}-panel`,'aria-labelledby':`rdct-button-style-${item.id}-${group}-tab`}:{},
      ...visible.filter(entry=>!secondary.some(([,matches])=>matches(entry))).map(renderField),
      ...secondary.map(([title,matches])=>{const entries=visible.filter(matches);return entries.length>0 && h('section',{key:title,className:'rdct-bld-control-section'},h('h4',null,title),...entries.map(renderField));})));
}
