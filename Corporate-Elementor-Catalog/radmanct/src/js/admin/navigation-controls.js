import {createNativeColorControl} from '../../../../radmanct-components/src/js/admin/atomic-color-control.js';

const v2 = window.elementorV2;
const controls = v2?.editorControls;
const registry = v2?.editorEditingPanel?.controlsRegistry;
if (controls && registry) {
  const {createElement:h} = window.wp.element;
  const {Select,MenuItem} = v2.ui;
  if (!registry.get('rdct-navigation-choice')) registry.register('rdct-navigation-choice',function Choice({options}) {
    const bound = controls.useBoundProp(v2.editorProps.numberPropTypeUtil);
    const value = bound.value ?? 0;
    const choices = options.some((item) => Number(item.value) === value) ? options : [{value,label:`#${value}`},...options];
    return h(Select,{size:'tiny',fullWidth:true,value,disabled:bound.disabled,onChange:(event) => bound.setValue(Number(event.target.value))},choices.map(({value,label}) => h(MenuItem,{key:value,value},label)));
  },'full',v2.editorProps.numberPropTypeUtil);
  if (!registry.get('rdct-navigation-color')) registry.register('rdct-navigation-color',createNativeColorControl(controls,h,v2.editorProps.stringPropTypeUtil),'full',v2.editorProps.stringPropTypeUtil);
}
