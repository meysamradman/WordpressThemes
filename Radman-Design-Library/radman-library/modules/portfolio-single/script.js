import '../../src/js/scroll.js';
import '../../shared/radmanct/src/js/ui/gallery.js';
import {observeCardPreviews} from '../../shared/radmanct/src/js/ui/card-preview.js';
const release=observeCardPreviews(document);window.addEventListener('pagehide',event=>{if(!event.persisted)release();});
