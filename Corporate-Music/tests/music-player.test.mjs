import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeAudioControls,formatAudioTime} from '../radmanct/src/js/ui/audio-controls.js';
class Control extends EventTarget {
 constructor(){super();this.hidden=false;this.value='0';this.attributes={};}
 setAttribute(key,value){this.attributes[key]=value;}
}
function fixture(){
 const selectors=['audio','.rdc-player__controls','[data-rdc-player-play]','[data-rdc-player-seek]','[data-rdc-player-time]','[data-rdc-player-duration]','[data-rdc-player-mute]','[data-rdc-player-volume]','[data-rdc-player-start]','[data-rdc-player-pause]','[data-rdc-player-status]'];
 const nodes=Object.fromEntries(selectors.map(key=>[key,new Control()]));
 const audio=nodes.audio;Object.assign(audio,{paused:true,ended:false,currentTime:0,duration:NaN,volume:1,muted:false,controls:true});
 audio.play=()=>{audio.paused=false;audio.dispatchEvent(new Event('play'));return Promise.resolve();};
 audio.pause=()=>{audio.paused=true;audio.dispatchEvent(new Event('pause'));};
 const root={dataset:{},querySelector:key=>nodes[key]};
 return {root,audio,nodes};
}
test('native audio gains accessible controls without autoplay and restores fallback on cleanup',()=>{
 const {root,audio,nodes}=fixture();const release=initializeAudioControls(root);
 assert.equal(audio.paused,true);assert.equal(audio.controls,false);
 const play=nodes['[data-rdc-player-play]'];play.dispatchEvent(new Event('click'));
 assert.equal(audio.paused,false);assert.equal(play.attributes['aria-pressed'],'true');
 play.dispatchEvent(new Event('click'));assert.equal(audio.paused,true);
 release();assert.equal(audio.controls,true);assert.equal(audio.hidden,false);assert.equal(root.dataset.rdcPlayerReady,undefined);
});
test('seek waits for valid metadata; position, duration and volume stay synchronized',()=>{
 const {root,audio,nodes}=fixture();const release=initializeAudioControls(root);
 const seek=nodes['[data-rdc-player-seek]'];assert.equal(seek.disabled,true);
 audio.duration=120;audio.dispatchEvent(new Event('loadedmetadata'));assert.equal(seek.disabled,false);
 seek.value='500';seek.dispatchEvent(new Event('input'));assert.equal(audio.currentTime,60);
 assert.equal(nodes['[data-rdc-player-time]'].textContent,'1:00');
 const volume=nodes['[data-rdc-player-volume]'];volume.value='0';volume.dispatchEvent(new Event('input'));assert.equal(audio.muted,true);
 volume.value='.5';volume.dispatchEvent(new Event('input'));assert.equal(audio.volume,.5);assert.equal(audio.muted,false);
 release();
});
test('time formatting rejects invalid durations',()=>{assert.equal(formatAudioTime(NaN),'0:00');assert.equal(formatAudioTime(Infinity),'0:00');assert.equal(formatAudioTime(125.8),'2:05');});
