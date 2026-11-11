import {IconPicker} from '../../../../shared/admin/icon-picker.js';

const {createElement:h} = window.wp.element;

export function BuilderIconPicker({value,onChange,config,labels}) {
  return h(IconPicker,{value,onChange,icons:config.iconChoices,labels:{
    title:labels.chooseIcon,choose:labels.chooseIcon,search:labels.searchIcons,none:labels.noIcon,
    close:labels.closeIconPicker,empty:labels.noMatchingIcons,unavailable:labels.unavailableIcon,more:labels.moreIcons,
  }});
}
