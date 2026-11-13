// Check the design lifecycle, not simulated FPS or vendor rendering quality.
import {readFileSync} from 'node:fs';
import {runInNewContext} from 'node:vm';
import assert from 'node:assert/strict';
const source=readFileSync(new URL('../radman-library/modules/particles/script.js',import.meta.url),'utf8').replace('export function','function').split('for(const root of document.querySelectorAll')[0];
const initialize=runInNewContext(`${source}\ninitializeParticles`,{AbortController});
class Target extends EventTarget{constructor(){super();this.dataset={};this.hidden=false;}setAttribute(k,v){this[k]=v;}removeAttribute(k){delete this.dataset[k.replace(/^data-/,'').replace(/-([a-z])/g,(_,c)=>c.toUpperCase())];}}
function fixture({reduced=false,loader}={}){
 const root=new Target(),doc=new Target(),view=new Target(),layer=new Target(),button=new Target(),media=new Target(),fine=new Target();
 root.ownerDocument=doc;doc.defaultView=view;root.isConnected=true;root.dataset.rdlPtcOptions='{"number":64,"size":2,"speed":0.65,"distance":150}';
 let bounds={width:1200,height:600},frames=new Map(),io,ro,loads=0,created=0,destroyed=0,lastOptions;
 root.querySelector=selector=>selector.includes('canvas')?layer:button;layer.getBoundingClientRect=()=>bounds;
 media.matches=reduced;fine.matches=true;view.matchMedia=query=>query.includes('reduced')?media:fine;
 view.getComputedStyle=()=>({getPropertyValue:()=> '#C59032'});
 view.IntersectionObserver=class{constructor(cb){this.cb=cb;io=this;}observe(){}disconnect(){this.dead=true;}};
 view.ResizeObserver=class{constructor(cb){this.cb=cb;ro=this;}observe(){}disconnect(){this.dead=true;}};
 view.requestAnimationFrame=cb=>{const id=frames.size+1;frames.set(id,cb);return id;};view.cancelAnimationFrame=id=>frames.delete(id);
 const load=()=>{loads++;return loader?loader():Promise.resolve({createParticleEngine:(host,options)=>{created++;lastOptions=options;return{count:options.particles.number.value,destroy(){destroyed++;}};}});};
 const release=initialize(root,load);
 const tick=async()=>{for(const [id,cb]of frames){frames.delete(id);cb();}await Promise.resolve();await Promise.resolve();};
 const visible=async value=>{io.cb([{isIntersecting:value}]);await tick();};
 return{root,doc,view,button,media,release,tick,visible,io:()=>io,ro:()=>ro,stats:()=>({loads,created,destroyed,lastOptions}),resize:async(width,height)=>{bounds={width,height};ro.cb();await tick();}};
}
let checks=0;
async function check(name,run){await run();checks++;console.log('PASS',name);}
await check('engine is lazy, bounded and owns one resize scheduler',async()=>{const f=fixture();assert.equal(f.stats().loads,0);await f.visible(true);assert.equal(f.stats().created,1);assert.ok(f.stats().lastOptions.particles.number.value<=72);assert.equal(f.stats().lastOptions.interactivity.events.resize,false);await f.resize(1200,600);assert.equal(f.stats().created,1);await f.resize(390,576);assert.ok(f.stats().lastOptions.particles.number.value<32);f.release();});
await check('manual pause stops and resumes the cached engine factory',async()=>{const f=fixture();await f.visible(true);f.button.dispatchEvent(new Event('click'));assert.equal(f.root.dataset.rdlPtcState,'paused');assert.equal(f.stats().destroyed,1);f.button.dispatchEvent(new Event('click'));assert.equal(f.stats().created,2);assert.equal(f.stats().loads,1);f.release();});
await check('offscreen, hidden tab and reduced motion release the canvas',async()=>{const f=fixture();await f.visible(true);await f.visible(false);assert.equal(f.stats().destroyed,1);await f.visible(true);f.doc.hidden=true;f.doc.dispatchEvent(new Event('visibilitychange'));assert.equal(f.stats().destroyed,2);f.doc.hidden=false;f.doc.dispatchEvent(new Event('visibilitychange'));f.media.matches=true;f.media.dispatchEvent(new Event('change'));assert.equal(f.root.dataset.rdlPtcState,'static');assert.equal(f.button.hidden,true);f.release();});
await check('reduced motion never requests the engine',async()=>{const f=fixture({reduced:true});await f.visible(true);assert.equal(f.stats().loads,0);assert.equal(f.button.hidden,true);f.release();});
await check('late import cannot start after teardown',async()=>{let resolve,created=0;const pending=new Promise(r=>{resolve=r;});const f=fixture({loader:()=>pending});await f.visible(true);f.release();resolve({createParticleEngine:()=>{created++;}});await f.tick();assert.equal(created,0);assert.equal(f.io().dead,true);assert.equal(f.ro().dead,true);});
console.log(`${checks} particle lifecycle checks passed (mock observers/engine).`);
