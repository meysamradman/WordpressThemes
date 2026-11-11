export const newId = () => `b${Array.from(crypto.getRandomValues(new Uint32Array(4)), (value) => value.toString(16).padStart(8, '0')).join('')}`;

export function cloneItem(item) {
  return { ...item, id: newId(), ...(item.items ? {items: item.items.map(cloneItem)} : {}), ...(item.links ? { links: item.links.map((link) => ({ ...link, id: newId() })) } : {}) };
}

export function createItem(type, config) {
  const common = { id: newId(), type };
  if (type === 'container') return {...common, appearance:{width:'equal'}, layout:{rowLayout:'flex',direction:'row',wrap:'nowrap',gap:12,padding:0,mobileLayout:'inline'},items:[]};
  if (['logo','image','panel','theme-toggle','button','icon','contact','social'].includes(type)) common.appearance = {width:'auto'};
  if (['icon', 'contact'].includes(type)) return { ...common, icon: type === 'icon' ? 'phone' : '', text: '', url: '', size: 20, direction: 'auto' };
  if (type === 'panel') return {...common,panelContentType:'empty',panelTemplateId:0,panelMode:'sidebar',panelMotion:'standard'};
  if (type === 'social') return { ...common, links: [], size: 20 };
  if (type === 'button') return {...common,text:config.labels?.button || 'Button',url:''};
  if (type === 'menu' && config.kind === 'footer') return { ...common, menuId: 0, menuLayout: 'vertical', mobileDisplay: 'expanded' };
  return { ...common, ...(['text', 'button'].includes(type) ? { text: '' } : {}), ...(type === 'button' ? { url: '' } : {}), ...(type === 'menu' ? { menuId: 0 } : {}) };
}
