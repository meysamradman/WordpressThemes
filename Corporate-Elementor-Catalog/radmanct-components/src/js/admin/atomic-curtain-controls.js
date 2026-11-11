import {registerItemRepeater} from './atomic-repeater-control.js';
import {registerNativeRichText,registerNativeTemplateSelect} from './atomic-native-fields.js';
export {createRepeaterItem as curtainItem, registerItemRepeater as registerCurtainControls} from './atomic-repeater-control.js';

if (typeof window !== 'undefined') {
  const controls = window.elementorV2?.editorControls;
  const registry = window.elementorV2?.editorEditingPanel?.controlsRegistry;
  if (controls && registry) registerNativeRichText(registry,controls,window.wp.element,window.elementorV2.editorProps);
  if (controls && registry) registerNativeTemplateSelect(registry,controls,window.wp.element,window.elementorV2.editorProps);
  const profiles = (window.rdctCarouselProfileControls ?? []).map((profile) => ({...window.rdctCarouselControls,...profile,fields:Object.fromEntries(profile.fields.map((key) => [key,window.rdctCarouselControls.fields[key]]))}));
  if (controls && registry) for (const config of [window.rdctAccordionControls, window.rdctTimelineControls, window.rdctStickySectionControls, window.rdctCurtainControls, window.rdctCarouselControls, ...profiles, window.rdctTestimonialControls, window.rdctTextControls, window.rdctTextSegmentsControls, window.rdctParticleColors]) if (config) registerItemRepeater(registry,controls,{...config,imageProps:window.rdctImageControlProps},window.wp.element,window.elementorV2.ui,window.elementorV2.editorProps,window.wp.media);
}
