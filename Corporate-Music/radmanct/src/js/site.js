import {initializeBrandMotion} from './ui/brand.js';
import {initializeContact} from './ui/contact.js';
import {initializeCursor} from './ui/cursor.js';
import {initializeFooter} from './ui/footer.js';
import {initializeHeader} from './ui/header.js';
import {initializePageLoading} from './ui/page-loading.js';
import {initializeScroll} from './runtime/scroll.js';
import {reportRuntimeError} from './runtime/lifecycle.js';

const inputEvents = new AbortController();
const pageRoot = document.documentElement;
document.addEventListener('pointerdown', () => {
 pageRoot.toggleAttribute('data-rdc-pointer', true);
}, {capture:true, passive:true, signal:inputEvents.signal});
document.addEventListener('keydown', event => {
 if (!event.altKey && !event.ctrlKey && !event.metaKey && event.key !== 'Shift') {
  pageRoot.removeAttribute('data-rdc-pointer');
 }
}, {capture:true, signal:inputEvents.signal});

const releaseBrand=initializeBrandMotion();
const releasePageLoading=initializePageLoading();
const releaseContact=initializeContact(document.querySelector('[data-rdc-ctc]'));
const releaseCursor=initializeCursor(document.querySelector('.rdc-cur'));
const scroll=initializeScroll();const releaseFooter=initializeFooter(document.querySelector('.rdc-ftr'),scroll);const releaseHeader=initializeHeader();let alive=true,previews,pending;
const syncPreviews=()=>{if(alive&&!previews&&!pending&&document.querySelector('[data-rdc-card-pre]'))pending=import('./ui/card-preview.js').then(module=>{if(alive)previews=module.observeCardPreviews(document);}).catch(error=>reportRuntimeError('card-preview',error)).finally(()=>pending=undefined);};
const updated=()=>{syncPreviews();scroll.refresh();};syncPreviews();document.addEventListener('rdc:arc-upd',updated);
const dispose=event=>{if(event.persisted)return;alive=false;inputEvents.abort();releasePageLoading();releaseContact();releaseBrand();releaseFooter();releaseHeader();releaseCursor();scroll.destroy();previews?.();document.removeEventListener('rdc:arc-upd',updated);window.removeEventListener('pagehide',dispose);};
window.addEventListener('pagehide',dispose);
