import '../../src/js/scroll.js';
import {mountTextMarquee} from '../../src/js/ui/text-marquee.js';
for(const root of document.querySelectorAll('[data-rdl-mtx]'))mountTextMarquee(root);
