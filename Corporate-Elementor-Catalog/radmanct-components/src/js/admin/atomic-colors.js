import { registerResponsiveControls } from './atomic-responsive-control.js';
import { registerColorControl } from './atomic-color-control.js';
const controls = window.elementorV2?.editorControls;
const registry = window.elementorV2?.editorEditingPanel?.controlsRegistry;
const h = window.wp?.element?.createElement;
if (h && controls?.useBoundProp && registry) {
  if (window.rdctResponsiveControls) registerResponsiveControls(registry,controls,window.rdctResponsiveControls);
  registerColorControl(registry, controls, h);
  registerColorControl(registry, controls, h, 'rdct-color-alpha');
}
