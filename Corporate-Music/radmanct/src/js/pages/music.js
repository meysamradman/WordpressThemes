import {createPageLifecycle} from '../runtime/lifecycle.js';
import {initializeAudioControls} from '../ui/audio-controls.js';
const page=createPageLifecycle(window);
for(const root of document.querySelectorAll('[data-rdc-player]'))page.mountRoot(root,'audio-controls',()=>({}),()=>initializeAudioControls(root));
