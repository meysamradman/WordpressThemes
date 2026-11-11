export function registerResponsiveControls(registry, controls, config) {
  const {createElement:h,useState} = window.wp.element;
  const ui = window.elementorV2.ui;
  if (!ui?.Stack || !ui.ToggleButtonGroup || !ui.ToggleButton || !ui.Button || !ui.Slider || !controls.PropProvider || !controls.PropKeyProvider || !controls.NumberControl || !controls.ToggleControl) return;
  const util = {key:'rdct-responsive',create:(value) => ({$$type:'rdct-responsive',value}),extract:(prop) => prop?.value ?? {}};
  for (const [type,spec] of Object.entries(config.controls)) {
    if (registry.get(type)) continue;
    function ResponsiveControl() {
      const bound = controls.useBoundProp(util);
      const {value,setValue,disabled} = bound;
      const [device,setDevice] = useState('desktop');
      const devices = spec.devices ?? ['desktop','tablet','mobile'];
      const raw = value?.[device]?.value ?? '';
      const inherited = devices.slice(0,devices.indexOf(device)).reverse().map((key) => value?.[key]?.value).find((entry) => entry !== undefined) ?? spec.default;
      const update = (next) => {
        const result = {...value};
        if (next === '') delete result[device];
        else result[device] = {$$type:spec.options ? 'string' : 'number',value:spec.options ? next : Math.min(spec.max,Math.max(spec.min,Number(next)))};
        setValue(result);
      };
      const sized = Boolean(spec.units && controls.SizeControl);
      const kind = sized ? 'size' : spec.options ? 'string' : 'number';
      const placeholder = sized && typeof inherited !== 'object' ? {size:{$$type:'number',value:Number(inherited ?? 0)},unit:{$$type:'string',value:spec.default_unit ?? spec.units[0]}} : inherited;
      const choice = spec.options && (spec.options_ui === 'select' && controls.SelectControl
        ? h(controls.SelectControl,{options:Object.entries(spec.options).map(([value,label]) => ({value,label})),ariaLabel:spec.label})
        : h(controls.ToggleControl,{options:Object.entries(spec.options).map(([value,label]) => ({value,label,renderContent:() => label})),fullWidth:true,allowEmpty:true}));
      const field = h(controls.PropProvider,{...bound,isDisabled:() => disabled,placeholder:{[device]:{$$type:kind,value:placeholder}}},
        h(controls.PropKeyProvider,{bind:device},choice || (sized ? h(controls.SizeControl,{min:spec.min,max:spec.max,enablePropTypeUnits:true,disableCustom:true,ariaLabel:spec.label}) : h(controls.NumberControl,{min:spec.min,max:spec.max,step:spec.step ?? 1,shouldForceInt:false,placeholder:String(inherited)}))));
      return h(ui.Stack,{spacing:1},
        h(ui.ToggleButtonGroup,{size:'tiny',exclusive:true,value:device,disabled,'aria-label':config.labels.device,onChange:(_,next) => {if (next) setDevice(next);}},devices.map((key) => h(ui.ToggleButton,{key,value:key,'aria-label':config.labels[key]},config.labels[key]))),
        spec.options || sized ? field : h(ui.Stack,{direction:'row',spacing:2,alignItems:'center'},h(ui.Slider,{size:'small',min:spec.min,max:spec.max,step:spec.step ?? 1,value:Math.min(spec.max,Math.max(spec.min,Number(raw === '' ? inherited : raw))),disabled,'aria-label':spec.label,onChange:(_,next) => {if (typeof next === 'number') update(next);}}),field),
        h(ui.Button,{size:'tiny',variant:'text',disabled:disabled || raw === '',onClick:() => update('')},config.labels.inherit));
    }
    registry.register(type,ResponsiveControl,'full',util);
  }
}
