export function normalizePickerColor(value, canvasContext) {
  if (value == null || value === '') return null;
  if (typeof value !== 'string') return undefined;
  if (/^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{8})$/i.test(value)) return value;
  if (/^#[a-f0-9]{4}$/i.test(value)) return `#${[...value.slice(1)].map((part) => part + part).join('')}`;
  if (value === 'transparent') return '#00000000';
  const rgb = value.match(/^rgba?\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)(?:\s*,\s*(0(?:\.\d+)?|1(?:\.0+)?|\.\d+))?\s*\)$/i);
  if (rgb && rgb.slice(1,4).every((part) => Number(part) <= 255)) {
    const bytes = rgb.slice(1,4).map((part) => Math.round(Number(part)));
    if (rgb[4] !== undefined) bytes.push(Math.round(Number(rgb[4]) * 255));
    return `#${bytes.map((part) => part.toString(16).padStart(2,'0')).join('')}`;
  }
  if (!canvasContext || !globalThis.CSS?.supports('color',value) || /var\(|currentcolor|inherit|initial|unset|revert/i.test(value)) return undefined;
  canvasContext.clearRect(0,0,1,1); canvasContext.fillStyle = value; canvasContext.fillRect(0,0,1,1);
  const bytes = [...canvasContext.getImageData(0,0,1,1).data];
  if (bytes[3] === 255) bytes.pop();
  return `#${bytes.map((part) => part.toString(16).padStart(2,'0')).join('')}`;
}

export function createNativeColorControl(controls,h,util) {
  let context;
  return function NativeColorControl() {
    const {setValue} = controls.useBoundProp(util);
    return h(controls.ColorControl,{propTypeUtil:util,onChange:(next) => {
      if (context === undefined && typeof document !== 'undefined') context = document.createElement('canvas').getContext('2d');
      const normalized = normalizePickerColor(next,context);
      if (normalized !== undefined) setValue(normalized);
    }});
  };
}

export function registerColorControl(registry,controls,h,type = 'rdct-color') {
  if (registry.get(type) || !controls.ColorControl) return;
  const util = window.elementorV2.editorProps.stringPropTypeUtil;
  registry.register(type,createNativeColorControl(controls,h,util),'full',util);
}
