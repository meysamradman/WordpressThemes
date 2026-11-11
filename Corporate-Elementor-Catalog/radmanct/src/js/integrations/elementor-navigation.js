import {observeMenus} from '../navigation/manager.js';
import {reportRuntimeError} from '../../../../shared/frontend/lifecycle.js';

const stopMenus = observeMenus(); let alive = true; let previewCleanup;
if (window.rdctNavigationPreview) import('./elementor-navigation-preview.js').then(({initializeNavigationPreview}) => { if (alive) previewCleanup = initializeNavigationPreview(window.rdctNavigationPreview); }).catch((error) => reportRuntimeError('navigation-preview',error));
window.addEventListener('pagehide',(event) => { if (!event.persisted) { alive = false; previewCleanup?.(); stopMenus(); } });
