import {PageTitleFields} from './page-title-fields.js';
import {ContentTypographyFields} from './content-typography-fields.js';
const {createElement:h} = window.wp.element;

export function PageTitleSettings({config,values,change}) {
  return h('div',null,h(PageTitleFields,{value:values.page_title,change:next=>change('page_title',next),labels:config.labels,global:true}),h('details',null,h('summary',null,config.labels.page_title_typography),h(ContentTypographyFields,{scope:'page_title',config:{...config,typographyRoles:['body','h1','link']},values,change})));
}
