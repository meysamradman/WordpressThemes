import {initializeScroll} from '../../shared/radmanct/src/js/runtime/scroll.js';
export const scroll=initializeScroll();
window.addEventListener('pagehide',event=>{if(!event.persisted)scroll.destroy();});
