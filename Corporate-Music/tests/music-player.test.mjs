import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeAudioControls,formatAudioTime} from '../radmanct/src/js/ui/audio-controls.js';
import {homeTracks} from '../radmanct/src/js/ui/music-player.js';
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
 const progress=new Map(),root={dataset:{},style:{setProperty:(key,value)=>progress.set(key,value)},querySelector:key=>nodes[key]};
 return {root,audio,nodes,progress};
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
 const {root,audio,nodes,progress}=fixture();const release=initializeAudioControls(root);
 const seek=nodes['[data-rdc-player-seek]'];assert.equal(seek.disabled,true);
 audio.duration=120;audio.dispatchEvent(new Event('loadedmetadata'));assert.equal(seek.disabled,false);
 seek.value='500';seek.dispatchEvent(new Event('input'));assert.equal(audio.currentTime,60);
 assert.equal(nodes['[data-rdc-player-time]'].textContent,'1:00');
 assert.equal(progress.get('--rdc-audio-progress'),'50%');
 audio.currentTime=120;audio.dispatchEvent(new Event('timeupdate'));assert.equal(progress.get('--rdc-audio-progress'),'100%');
 const volume=nodes['[data-rdc-player-volume]'];volume.value='0';volume.dispatchEvent(new Event('input'));assert.equal(audio.muted,true);
 volume.value='.5';volume.dispatchEvent(new Event('input'));assert.equal(audio.volume,.5);assert.equal(audio.muted,false);
 release();
});
test('time formatting rejects invalid durations',()=>{assert.equal(formatAudioTime(NaN),'0:00');assert.equal(formatAudioTime(Infinity),'0:00');assert.equal(formatAudioTime(125.8),'2:05');});

test('cancelled playback is silent while real playback failure remains actionable',async()=>{
 for(const name of ['AbortError','NotSupportedError']){
  const {root,audio,nodes}=fixture();audio.play=()=>Promise.reject(Object.assign(new Error(name),{name}));
  const release=initializeAudioControls(root);nodes['[data-rdc-player-play]'].dispatchEvent(new Event('click'));await Promise.resolve();
  const message=nodes['[data-rdc-player-status]'].textContent;
  if(name==='AbortError')assert.equal(message,'');else assert.ok(message.length>0);
  release();
 }
});

test('home queue deduplicates hero/card buttons and rejects missing or foreign media',()=>{
 const button=(key,url)=>({dataset:{rdcAudioKey:key,rdcAudioSrc:url}});
 const tracks=homeTracks([button('1','/uploads/one.mp3'),button('1','/uploads/one.mp3'),button('2','https://foreign.example/two.mp3'),button('3',''),button('4','http://127.0.0.1:8098/uploads/four.mp3')],'http://127.0.0.1:8098');
 assert.deepEqual(tracks.map(track=>track.key),['1','4']);
 assert.equal(tracks[0].url,'http://127.0.0.1:8098/uploads/one.mp3');
});
