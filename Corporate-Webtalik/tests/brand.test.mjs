import test from 'node:test';
import assert from 'node:assert/strict';
import {initializeBrandMotion} from '../radmanct/src/js/ui/brand.js';
function fixture(reduced=false){
 const doc=new EventTarget(),preference=new EventTarget();preference.matches=reduced;
 let callback,started=0,disconnected=0;const seen=new Set(),logos=[0,1].map(()=>({toggleAttribute:(name,on)=>on?seen.add(name):seen.delete(name),removeAttribute:name=>seen.delete(name)}));
 const observed=new Set();doc.defaultView=Object.assign(new EventTarget(),{matchMedia:()=>preference,IntersectionObserver:class{constructor(fn){callback=fn;started++;}observe(node){observed.add(node);}unobserve(node){observed.delete(node);}disconnect(){observed.clear();disconnected++;}}});
 let preloader=true;doc.getElementById=()=>preloader?{}:null;doc.querySelectorAll=()=>logos;
 return {doc,preference,logos,observed,seen,started:()=>started,disconnected:()=>disconnected,loaded(){preloader=false;doc.dispatchEvent(new Event('rdc:pre-done'));},show(node,visible=true){callback([{target:node,isIntersecting:visible,intersectionRatio:visible?1:0}]);}};
}
test('repeating logo motion waits for preloader and runs only while visible',()=>{
 const f=fixture(),release=initializeBrandMotion(f.doc);assert.equal(f.started(),0);f.loaded();assert.equal(f.started(),1);
 f.show(f.logos[0]);assert.ok(f.seen.has('data-rdc-logo-act'));assert.equal(f.observed.size,2);
 f.show(f.logos[0],false);assert.equal(f.seen.has('data-rdc-logo-act'),false);
 f.show(f.logos[0]);assert.ok(f.seen.has('data-rdc-logo-act'));
 release();assert.equal(f.observed.size,0);assert.equal(f.seen.size,0);
});
test('reduced motion and disposal never start deferred logo animation',()=>{
 const f=fixture(true),release=initializeBrandMotion(f.doc);f.loaded();assert.equal(f.started(),0);release();
 const g=fixture(),stop=initializeBrandMotion(g.doc);stop();g.loaded();assert.equal(g.started(),0);
});

test('returning to a cached page restarts only visible logo animations and disposes the listener',()=>{
 const f=fixture(),release=initializeBrandMotion(f.doc);f.loaded();let plays=0;
 const animation={currentTime:3000,play(){plays++;}};
 f.logos[0].getAnimations=()=>[animation];f.logos[1].getAnimations=()=>assert.fail('Hidden footer animated');
 f.show(f.logos[0]);f.doc.defaultView.dispatchEvent(new Event('pageshow'));assert.equal(animation.currentTime,0);assert.equal(plays,1);
 release();f.doc.defaultView.dispatchEvent(new Event('pageshow'));assert.equal(plays,1);
});
