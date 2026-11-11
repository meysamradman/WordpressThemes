import { ButtonControl, SelectControl, RangeControl, SwitchControl } from '../../../../shared/admin/controls.js';
import { ColorField } from '../../../../shared/admin/color-field.js';
import { pickImage } from '../../../../shared/admin/image-picker.js';
import { devices, deviceKey } from './builder-devices.js';

const { createElement: h } = window.wp.element;

export function VisualFields({ item, update, labels, device, section = 'style' }) {
  const image = item.type === 'image';
  const imageKey = (id) => deviceKey(id, image ? 'imageId' : 'logoImageId');
  const imageLabel = image ? labels.image : labels.logoImage;
  const number = (key, max, fallback = 0) => h('fieldset', { key }, h('legend', null, labels[key]), h(RangeControl, { label: labels[key],
    type: 'number', numeric: true, min: 0, max, value: item[key] ?? fallback, onChange: (value) => update({ [key]: value }),
  }));
  if (item.type === 'logo' || image) return h('div', null,
    section === 'content' && image && h('p', { className: 'description' }, labels.builderImageHint),
    ...devices.filter(({ id }) => !device || device === id).map(({ id }) => h('fieldset', { key: id }, !device && h('legend', null, labels[id]),
      section === 'content' && h('fieldset', null, h('legend', null, imageLabel),
        h(ButtonControl, { type: 'button', onClick: () => pickImage({ title: imageLabel, id: item[imageKey(id)] || 0, onSelect: (imageId) => update({ [imageKey(id)]: imageId }) }) }, item[imageKey(id)] ? labels.changeImage : labels.chooseImage),
        !!item[imageKey(id)] && h(ButtonControl, { type: 'button', onClick: () => update({ [imageKey(id)]: 0 }) }, labels.remove),
        !image && h('p', { className: 'description' }, id === 'desktop' ? labels.logoSiteFallback : labels.logoDeviceFallback)),
      ...(section === 'style' ? [[image ? 'imageWidth' : 'logoWidth', 512, image ? labels.imageResponsiveWidth : labels.logoResponsiveWidth], [image ? 'imageHeight' : 'logoHeight', 256, image ? labels.imageResponsiveHeight : labels.logoResponsiveHeight]] : []).map(([base, max, label]) => {
        const key = deviceKey(id, base);
        return h('fieldset', { key }, h('legend', null, label), h(RangeControl, { label, type: 'number', numeric: true, min: 0, max, value: item[key] ?? 0, onChange: (value) => update({ [key]: value }) }));
      }))),
    section === 'content' && !image && window.rdctBuilder.kind === 'header' && ['lightSectionLogoId','darkSectionLogoId','stickyLogoId'].map(key=>h('fieldset',{key},h('legend',null,labels[key]),
      h(ButtonControl,{type:'button',onClick:()=>pickImage({title:labels[key],id:item[key] || 0,onSelect:id=>update({[key]:id})})},item[key]?labels.changeImage:labels.chooseImage),
      !!item[key] && h(ButtonControl,{type:'button',onClick:()=>update({[key]:0})},labels.remove))),
    section === 'style' && h('p', { className: 'description' }, labels.logoSizeHint));
  return h('div', null,
    h('label', null, labels.buttonStyle, h(SelectControl, { value: item.buttonStyle || 'solid', onChange: (value) => update({ buttonStyle: value }), options: ['solid', 'outline', 'plain'].map((value) => [value, labels[`${value}Button`]]) })),
    h(ColorField, { label: labels.buttonBackground, value: item.buttonBackground || '', fallback: '#21685a', resetLabel: labels.inheritAppearance, onChange: (value) => update({ buttonBackground: value }) }),
    h('label', null, h(SwitchControl, { checked: item.buttonRadius == null, onChange: (checked) => update({ buttonRadius: checked ? null : 6 }) }), labels.inheritButtonRadius),
    item.buttonRadius != null && number('buttonRadius', 64),
    number('buttonPaddingX', 64, 19), number('buttonPaddingY', 48, 10));
}
