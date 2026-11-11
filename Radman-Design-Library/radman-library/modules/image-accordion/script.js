import '../../src/js/scroll.js';
import {mountImageAccordion} from '../../src/js/ui/image-accordion.js';
for(const root of document.querySelectorAll('[data-rdl-iac]'))mountImageAccordion(root);
