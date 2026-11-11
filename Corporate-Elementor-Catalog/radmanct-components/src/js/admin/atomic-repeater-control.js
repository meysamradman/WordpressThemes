import {createNativeColorControl} from './atomic-color-control.js';
import {nativeEditorRow,createNativeTemplateSelect} from './atomic-native-fields.js';
import {pickImage} from '../../../../shared/admin/image-picker.js';

const wrap = (type,value) => ({$$type:type,value});
export function matchesFieldConditions(row, fields, conditions = {}) {
  return Object.entries(conditions).every(([name, expected]) => {
    const negative = name.endsWith('!');
    const key = negative ? name.slice(0, -1) : name;
    const current = row.value?.[key]?.value ?? fields[key]?.default;
    const matches = (Array.isArray(expected) ? expected : [expected]).includes(current);
    return negative ? !matches : matches;
  });
}
export function createRepeaterItem(fields,values = {}, itemType = 'rdct-curtain-item') {
  return nativeEditorRow(wrap(itemType,Object.fromEntries(Object.entries(fields).filter(([key,field]) => !(field.image || field.size || field.media) || values[key]).map(([key,field]) => [key,field.image || field.size ? values[key] : wrap(typeof field.default === 'number' ? 'number' : typeof field.default === 'boolean' ? 'boolean' : 'string',values[key] ?? field.default)]))),fields);
}

export function appendImageItems(rows, ids, fields, maximum = 50, itemType = 'rdct-carousel-item') {
  const next = rows.slice(0, maximum);
  for (const id of new Set(ids)) {
    if (next.length >= maximum) break;
    if (Number.isSafeInteger(id) && id > 0) next.push(createRepeaterItem(fields, {image_id:id,content_type:'content',title:''}, itemType));
  }
  return next;
}

export function registerItemRepeater(registry,controls,config,element,ui,props,media) {
  const controlKey = config?.controlKey ?? 'rdct-curtain-items';
  const maximum = config?.maximum ?? 12;
  if (!config || registry.get(controlKey) || !controls.Repeater || !controls.PropProvider || !controls.PropKeyProvider || !controls.TextControl || !controls.TextAreaControl || !controls.ColorControl || !ui?.Stack) return;
  const {createElement:h,createContext,useContext,useState} = element;
  const Context = createContext(null);
  const propTypeKey = config.propTypeKey ?? controlKey;
  const util = {key:propTypeKey,create:(value) => wrap(propTypeKey,value),extract:(prop) => prop?.value ?? []};
  const ColorControl = createNativeColorControl(controls,h,props.stringPropTypeUtil);
  const TemplateSelect = createNativeTemplateSelect(controls,h);

  function BatchImages({rows,commit,disabled}) {
    const latest = element.useRef({rows,commit,disabled}); latest.current = {rows,commit,disabled};
    const cancel = element.useRef(null);
    element.useEffect(() => () => cancel.current?.(), []);
    return h(ui.Button,{type:'button',variant:'outlined',size:'small',disabled:disabled || rows.length >= maximum,onClick:() => {
      cancel.current?.();
      cancel.current = pickImage({media,title:config.labels.addImages,multiple:true,maximum,onSelect:(ids) => {
        const current = latest.current;
        if (!current.disabled) current.commit(appendImageItems(current.rows,ids,config.fields,maximum,config.itemType));
      }});
    }},config.labels.addImages);
  }

  function Field({row,index,fieldKey,field}) {
    const context = useContext(Context);
    if (!matchesFieldConditions(row,config.fields,field.condition)) return null;
    if (field.image || field.media) return controls.ImageControl ? h(controls.PropKeyProvider,{bind:fieldKey},h(controls.ImageControl,{...config.imageProps,label:field.label})) : null;
    if (field.size) return controls.SizeControl ? h(ui.Stack,{spacing:1},h(controls.ControlFormLabel,null,field.label),h(controls.PropKeyProvider,{bind:fieldKey},h(controls.SizeControl,{min:field.min,enablePropTypeUnits:true,disableCustom:true,ariaLabel:field.label}))) : null;

    const choices = fieldKey === 'icon' ? config.icons : (field.template ? config.templates : field.options);
    let input;
    if (field.template) input = h(TemplateSelect,{options:Object.entries(choices).map(([value,label]) => ({value,label})),ariaLabel:field.label});
    else if (choices) input = h(controls.SelectControl,{options:Object.entries(choices).map(([value,label]) => ({value,label})),ariaLabel:field.label});
    else if (field.rich) input = h(controls.InlineEditingControl,{context:context.editorContext});
    else if (field.color) input = h(ColorControl);
    else if (typeof field.default === 'boolean') input = h(controls.SwitchControl);
    else if (typeof field.default === 'number') input = h(controls.NumberControl,{min:field.min,max:field.max,step:field.step,shouldForceInt:!field.step});
    else input = h(field.multiline ? controls.TextAreaControl : controls.TextControl,{ariaLabel:field.label});
    return h(ui.Stack,{spacing:1},h(controls.ControlFormLabel,null,field.label),h(controls.PropKeyProvider,{bind:fieldKey},input));
  }

  function Content({value:row,index}) {
    row = nativeEditorRow(row,config.fields);
    const context = useContext(Context);
    const [group,setGroup] = useState(config.initialGroup ?? 'normal');
    const fields = Object.entries(config.fields).filter(([,field]) => !field.internal && (!field.group || field.group === group));
    const body = h(ui.Stack,{spacing:2},
      config.groups !== false && h(ui.ToggleButtonGroup,{size:'tiny',exclusive:true,value:group,disabled:context.disabled,onChange:(_,next) => {if (next) setGroup(next);}},Object.entries(config.groupOptions ?? {normal:config.labels.normal,active:config.labels.active}).map(([key,label]) => h(ui.ToggleButton,{key,value:key},label))),
      fields.map(([fieldKey,field]) => h(Field,{key:fieldKey,row,index,fieldKey,field})));
    const item = h(controls.PropProvider,{propType:context.itemType,value:row.value,isDisabled:() => context.disabled,setValue:(next,options,meta) => context.replace(index,{...row,value:next},options,meta)},body);
    return h(controls.PropProvider,{propType:context.arrayType,value:context.rows,setValue:context.commit,isDisabled:() => context.disabled},h(controls.PropKeyProvider,{bind:String(index)},item));
  }

  function ItemsControl({context:editorContext} = {}) {
    const bound = controls.useBoundProp(util);
    const rows = Array.isArray(bound.value) ? bound.value : [];
    const commit = (next,options,meta) => { if (!bound.disabled && Array.isArray(next) && next.length <= maximum) bound.setValue(next,options,meta); };
    const replace = (index,row,options,meta) => commit(rows.map((existing,i) => i === index ? row : existing),options,meta);
    const itemSchema = bound.propType.item_prop_type;
    const itemType = itemSchema.kind === 'union' ? itemSchema.prop_types[config.itemType ?? 'rdct-curtain-item'] : itemSchema;
    const context = {disabled:bound.disabled,editorContext,arrayType:bound.propType,rows,commit,itemType,replace,update:(index,changes) => replace(index,{...rows[index],value:{...rows[index].value,...Object.fromEntries(Object.entries(changes).map(([key,value]) => [key,wrap(typeof value === 'number' ? 'number' : 'string',value)]))}})};
    const repeater = h(controls.Repeater,{
      label:config.labels.item,values:rows,setValues:commit,disabled:bound.disabled,showToggle:false,showDuplicate:rows.length < maximum,disableAddItemButton:rows.length >= maximum,openOnAdd:true,isSortable:true,
      itemSettings:{initialValues:createRepeaterItem(config.fields,{},config.itemType ?? 'rdct-curtain-item'),Icon:() => null,Label:({value}) => value.value?.title?.value || config.labels.item,Content},
    });
    return h(Context.Provider,{value:context},config.batchImages && ui.Button && media ? h(ui.Stack,{spacing:1},h(BatchImages,{rows,commit,disabled:bound.disabled}),repeater) : repeater);
  }
  registry.register(controlKey,ItemsControl,'full',util);
}

