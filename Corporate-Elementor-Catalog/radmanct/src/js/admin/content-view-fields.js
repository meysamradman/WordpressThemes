import { SelectField } from './fields.js';
import { ContentLayoutFields } from './layout-fields.js';
import { ContentColorFields } from './content-color-fields.js';
import { SidebarFields } from './sidebar-fields.js';
import { FieldGroup } from '../../../../shared/admin/controls.js';
import { PageTitleFields } from './page-title-fields.js';
const { createElement: h } = window.wp.element;

export function ContentViewFields({ scope, config, values, change }) {
  const { labels } = config;
  const [content, view] = scope.split('_');
  const renderer = `${scope}_renderer`;
  return h('div', null,
    !config.contentModules[content] && h('p', { className: 'description' }, labels.moduleDisabled),
    config.contentModules[content] && h('div', { className: 'rdct-stg__actions' },
      config.contentLinks?.items?.[content] && h('a', { className: 'rdct-btn', href: config.contentLinks.items[content] }, labels.manageContentItems),
      config.contentLinks?.archives?.[content] && h('a', { className: 'rdct-btn', href: config.contentLinks.archives[content], target: '_blank', rel: 'noopener noreferrer' }, labels.viewContentArchive)),
    h(SelectField, { name: renderer, label: view === 'single' ? labels.singleDesign : labels.archiveDesign, value: values[renderer], onChange: change,
      options: [['custom', labels.customDesign], ['elementor', labels.elementorDesign]] }),
    values[renderer] === 'elementor' && !config.elementorPro && h('p', { className: 'description' }, labels.proUnavailable),
    h(FieldGroup, { title: labels.layout, hint: labels.contentLayoutsHint }, h(ContentLayoutFields, { scope, config, values, change })),
    h(ContentColorFields, { scope, config, values, change }),
    h(PageTitleFields,{idPrefix:`rdct-${scope}-title`,value:values[`${scope}_page_title`],change:next=>change(`${scope}_page_title`,next),labels}),
    h(SidebarFields, { scope, config, values, change }));
}
