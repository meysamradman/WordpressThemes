import test from 'node:test';
import assert from 'node:assert/strict';
import {initializePageLoading} from '../radmanct/src/js/ui/page-loading.js';

function setup(useNavigation=false,reducedMotion=false){
 const doc=new EventTarget(),view=new EventTarget(),timers=new Map(),attrs=new Set(),storage=new Map();
 let next=0;
 doc.documentElement={setAttribute:key=>attrs.add(key),removeAttribute:key=>attrs.delete(key)};
 view.document=doc;view.location=new URL('https://site.test/music/');
 view.matchMedia=()=>({matches:reducedMotion});
 view.sessionStorage={getItem:key=>storage.get(key),setItem:(key,value)=>storage.set(key,value),removeItem:key=>storage.delete(key)};
 if(useNavigation)view.navigation=new EventTarget();
 view.setTimeout=callback=>{const id=++next;timers.set(id,callback);return id;};
 view.clearTimeout=id=>timers.delete(id);
 const release=initializePageLoading(view);
 const tick=()=>{for(const [id,callback] of [...timers]){timers.delete(id);callback();}};
 const click=(url,options={})=>{
  const event=new Event('click',{cancelable:true});
  const link={href:url,target:options.target??'',hasAttribute:()=>!!options.download};
  Object.defineProperty(event,'target',{value:{closest:()=>link}});
  Object.assign(event,{button:0,...options});
  if(options.handled)event.preventDefault();
  doc.dispatchEvent(event);
 };
 const navigate=(options={})=>{
  const event=new Event('navigate',{cancelable:true});
  Object.assign(event,{destination:{url:'https://site.test/videos/',sameDocument:false},...options});
  view.navigation.dispatchEvent(event);
 };
 return {click,navigate,view,tick,release,storage,visible:()=>attrs.has('data-rdc-page-load')};
}
test('native link fallback shows feedback as soon as an internal navigation starts',()=>{
 const fixture=setup();
 for(const [href,options] of [
  ['https://site.test/videos/',{handled:true}],
  ['https://site.test/videos/',{ctrlKey:true}],
  ['https://site.test/video.mp4',{download:true}],
  ['https://other.test/videos/',{}],
  ['https://site.test/music/#items',{}],
 ]){
  fixture.click(href,options);fixture.tick();assert.equal(fixture.visible(),false);
 }
 fixture.click('https://site.test/videos/');
 assert.equal(fixture.visible(),true);
 fixture.view.dispatchEvent(new Event('pagehide'));
 assert.equal(fixture.visible(),false);
 assert.equal(fixture.storage.has('rdc:page-curtain'),true);
 fixture.view.dispatchEvent(new Event('pageshow'));
 fixture.click('https://site.test/gallery/');
 assert.equal(fixture.visible(),true);
 fixture.tick();assert.equal(fixture.visible(),false);
 fixture.release();
});

test('browser navigation ignores same-document updates and clears cancelled document loads',()=>{
 const fixture=setup(true),controller=new AbortController();
 fixture.navigate({destination:{url:'https://site.test/videos/page/2/',sameDocument:true}});
 assert.equal(fixture.visible(),false);
 fixture.navigate({downloadRequest:'file.mp3'});
 assert.equal(fixture.visible(),false);
 fixture.navigate({signal:controller.signal});
 assert.equal(fixture.visible(),true);
 controller.abort();
 assert.equal(fixture.visible(),false);
 assert.equal(fixture.storage.has('rdc:page-curtain'),false);
 fixture.release();
});

test('reduced motion skips the curtain and the arrival marker',()=>{
 const fixture=setup(true,true);
 fixture.navigate();
 assert.equal(fixture.visible(),false);
 assert.equal(fixture.storage.size,0);
 fixture.release();
});
