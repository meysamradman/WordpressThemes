import {TypographyFields} from './typography-fields.js';
import {FontFaces} from './fields.js';
import {TabList} from '../../../../shared/admin/panel-tabs.js';
import {ButtonControl} from '../../../../shared/admin/controls.js';
import {resolveProfile} from './typography-profile.js';

const {createElement:h, useState} = window.wp.element;

export function ContentTypographyFields({scope, config, values, change}) {
  const [group, setGroup] = useState(config.primaryFontGroup);
  const key = `${scope}_typography`;
  const raw = values[key] || {};
  const resolve = draft => resolveProfile(values, draft, config.fontRoles);
  return h('div', null,
    h('p', {className:'description'}, config.labels.contentTypographyHint),
    h(ButtonControl, {onClick:()=>change(key,{})}, config.labels.contentTypographyReset),
    h(TabList, {id:`rdct-fonts-${scope}`, value:group, onChange:setGroup, tabs:[{id:'persian',label:config.labels.arabicScript},{id:'latin',label:config.labels.latinScript}]}),
    h('div', {id:`rdct-fonts-${scope}-${group}-panel`,role:'tabpanel','aria-labelledby':`rdct-fonts-${scope}-${group}-tab`},
      h(FontFaces, {config, values:resolve(raw), groups:[group]}),
      h(TypographyFields, {key:group,config,values:raw,group,resolveValues:resolve,inheritLabel:config.labels.inheritBodyFont,
        change:(name,value)=>change(key, current=>({...current,[name]:value}))})));
}
