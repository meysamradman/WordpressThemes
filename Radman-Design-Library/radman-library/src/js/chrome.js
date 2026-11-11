import {scroll} from './scroll.js';
import {initializeBrandMotion} from '../../shared/radmanct/src/js/ui/brand.js';
import {initializeHeader} from '../../shared/radmanct/src/js/ui/header.js';
import {initializeFooter} from '../../shared/radmanct/src/js/ui/footer.js';
const releases=[initializeBrandMotion(),initializeHeader(),initializeFooter(document.querySelector('.rdc-ftr'),scroll)];
window.addEventListener('pagehide',event=>{if(!event.persisted)releases.forEach(release=>release());});
