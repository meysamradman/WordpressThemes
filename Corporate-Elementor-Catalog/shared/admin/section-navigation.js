import { ButtonControl } from './controls.js';
import { navigateTabs } from './tab-navigation.js';

const { createElement: h, useState, useEffect } = window.wp.element;

export function SectionNavigation({ groups, value, onChange, label, id = 'rdct' }) {
  const parent = groups.find((group) => group.items.some((item) => item.id === value))?.id;
  const [expanded, setExpanded] = useState(() => parent ?? null);
  useEffect(() => { setExpanded(parent ?? null); }, [parent]);
  return h('nav', { className: 'rdct-adm-menu rdct-adm-scroll', 'aria-label': label },
    ...groups.map((group) => {
      const open = expanded === group.id;
      return h('section', { key: group.id },
        h('h3', null, h(ButtonControl, { variant: 'ghost', icon: group.icon, className: 'rdct-adm-menu__toggle',
          'aria-expanded': open, 'aria-controls': `${id}-group-${group.id}`,
          onClick: () => setExpanded((current) => current === group.id ? null : group.id) },
          h('span', null, group.label), h('span', { className: 'dashicons dashicons-arrow-down-alt2 rdct-adm-menu__arrow', 'aria-hidden': true }))),
        h('div', { className: 'rdct-adm-menu__reveal', 'data-open': open, 'aria-hidden': !open },
        h('div', { id: `${id}-group-${group.id}`, inert: open ? undefined : '', className: 'rdct-adm-menu__children', role: 'tablist', 'aria-label': group.label, 'aria-orientation': 'vertical',
          onKeyDown: (event) => navigateTabs(event, onChange, 'vertical') }, ...group.items.map((item, index) => h(ButtonControl, { key: item.id, variant: 'ghost', className: 'rdct-adm-menu__item',
            id: `${id}-tab-${item.id}`, 'data-section': item.id, 'data-tab-id': item.id, role: 'tab', 'aria-selected': value === item.id, 'aria-controls': `${id}-panel-${item.id}`,
            tabIndex: value === item.id || (!group.items.some((entry) => entry.id === value) && index === 0) ? 0 : -1,
            onClick: () => onChange(item.id) }, item.label)))));
    }));
}
