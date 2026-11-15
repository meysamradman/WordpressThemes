import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeAudioWave} from '../radmanct/src/js/ui/audio-wave.js';

test('visual playback stops for buffering, visibility, reduced motion and disposal without touching audio',()=>{
 const audio=new EventTarget();Object.assign(audio,{paused:true,ended:false,currentSrc:'/track.wav'});
 const motion=new EventTarget();motion.matches=false;
 let intersection,disconnected=false;
 const wave={dataset:{},clientWidth:0,querySelectorAll:()=>[]};
 const doc=new EventTarget();doc.hidden=false;
 doc.defaultView={matchMedia:()=>motion,IntersectionObserver:class {
  constructor(callback){intersection=callback;} observe(){} disconnect(){disconnected=true;}
 }};
 const dispose=initializeAudioWave({ownerDocument:doc,querySelector:()=>wave},audio);
 const emit=type=>audio.dispatchEvent(new Event(type));
 intersection([{isIntersecting:true}]);assert.equal(wave.dataset.rdcWaveRun,'false');
 audio.paused=false;emit('playing');assert.equal(wave.dataset.rdcWaveRun,'true');
 emit('waiting');assert.equal(wave.dataset.rdcWaveRun,'false');
 emit('playing');doc.hidden=true;doc.dispatchEvent(new Event('visibilitychange'));assert.equal(wave.dataset.rdcWaveRun,'false');
 doc.hidden=false;doc.dispatchEvent(new Event('visibilitychange'));assert.equal(wave.dataset.rdcWaveRun,'true');
 motion.matches=true;motion.dispatchEvent(new Event('change'));assert.equal(wave.dataset.rdcWaveRun,'false');
 motion.matches=false;motion.dispatchEvent(new Event('change'));intersection([{isIntersecting:false}]);assert.equal(wave.dataset.rdcWaveRun,'false');
 intersection([{isIntersecting:true}]);assert.equal(wave.dataset.rdcWaveRun,'true');
 audio.paused=true;emit('pause');assert.equal(wave.dataset.rdcWaveRun,'false');
 dispose();assert.equal(disconnected,true);emit('playing');assert.equal(wave.dataset.rdcWaveRun,undefined);
 assert.equal(audio.paused,true);
});
