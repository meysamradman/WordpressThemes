import test from 'node:test';
import assert from 'node:assert/strict';
import {registerCarouselSync} from '../radmanct-components/src/js/components/carousel/sync.js';

function progressCarousel(doc, id, targets = '', speed = 500) {
  const handlers = new Map(); const calls = [];
  const emit = (name, ...args) => { for (const fn of handlers.get(name) ?? []) fn(slider, ...args); };
  const slider = {activeIndex: 0, progress: 0, slides: Array(4), params: {speed},
    on(name, fn) { if (!handlers.has(name)) handlers.set(name, new Set()); handlers.get(name).add(fn); },
    off(name, fn) { handlers.get(name)?.delete(fn); },
    setProgress(value, duration) { this.progress = value; calls.push({value, duration}); emit('setTransition', duration); emit('setTranslate'); },
  };
  const release = registerCarouselSync({ownerDocument: doc}, slider, {controller_id: id, controlled_ids: targets, sync_mode: 'progress'});
  return {slider, calls, emit, release, handlers};
}

test('continuous progress follows intermediate drag values and transition duration across a cycle', () => {
  const doc = {}; const a = progressCarousel(doc, 'a', 'b'); const b = progressCarousel(doc, 'b', 'a');
  a.emit('setTransition', 360); a.slider.progress = .375; a.emit('setTranslate');
  assert.deepEqual(b.calls.at(-1), {value: .375, duration: 360});
  assert.equal(a.calls.length, 0);
  a.emit('setTransition', 0); a.slider.progress = .42; a.emit('setTranslate');
  assert.deepEqual(b.calls.at(-1), {value: .42, duration: 0});
  a.release(); b.release();
  assert.equal([...a.handlers.values()].reduce((sum, set) => sum + set.size, 0), 0);
});

test('progress clamps overscroll, skips invalid progress and respects target reduced motion', () => {
  const doc = {}; const a = progressCarousel(doc, 'a', 'b'); const b = progressCarousel(doc, 'b', '', 0);
  a.emit('setTransition', 600); a.slider.progress = 1.3; a.emit('setTranslate');
  assert.deepEqual(b.calls.at(-1), {value: 1, duration: 0});
  const count = b.calls.length; a.slider.progress = NaN; a.emit('setTranslate'); assert.equal(b.calls.length, count);
  a.release(); b.release();
});

test('a late progress target adopts the continuous position without starting a transition', () => {
  const doc = {}; const a = progressCarousel(doc, 'a', 'b'); a.slider.progress = .625;
  const b = progressCarousel(doc, 'b');
  assert.deepEqual(b.calls, [{value: .625, duration: 0}]);
  a.release(); b.release();
});

function carousel(doc, id, targets = '', count = 5, index = 0, speed = 600) {
  const handlers = new Set(); const calls = [];
  const slider = {activeIndex:index, slides:Array(count), params:{speed}, on:(_,fn) => handlers.add(fn), off:(_,fn) => handlers.delete(fn), slideTo(next,duration,callbacks) {
    this.activeIndex = next; calls.push({next,duration,callbacks});
    for (const handler of handlers) handler();
  }};
  const release = registerCarouselSync({ownerDocument:doc},slider,{controller_id:id,controlled_ids:targets});
  return {slider,calls,release,handlers,move:(index) => slider.slideTo(index,speed,true)};
}

test('late targets adopt the controller index and follow later changes',() => {
  const doc = {}; const source = carousel(doc,'images','details',5,2);
  const target = carousel(doc,'details');
  assert.equal(target.slider.activeIndex,2);
  source.move(4); assert.equal(target.slider.activeIndex,4);
  target.move(1); assert.equal(source.slider.activeIndex,4);
  source.release(); target.release();
});

test('cycles and chains propagate once without recursive slider events',() => {
  const doc = {}; const a = carousel(doc,'a','b,c'); const b = carousel(doc,'b','c'); const c = carousel(doc,'c','a');
  a.move(3);
  assert.equal(b.slider.activeIndex,3); assert.equal(c.slider.activeIndex,3);
  assert.equal(a.calls.length,1); assert.equal(b.calls.length,1); assert.equal(c.calls.length,1);
  a.release(); b.release(); c.release();
});

test('short targets clamp the index and preserve target reduced-motion speed',() => {
  const doc = {}; const a = carousel(doc,'a','b,c'); const b = carousel(doc,'b','',2,0,0); const c = carousel(doc,'c');
  a.move(4);
  assert.deepEqual(b.calls,[{next:1,duration:0,callbacks:true}]);
  assert.equal(c.slider.activeIndex,4);
  a.release(); b.release(); c.release();
});

test('duplicate IDs are ignored and iframe documents remain independent',() => {
  const doc = {}; const a = carousel(doc,'a','b'); const b = carousel(doc,'b'); const duplicate = carousel(doc,'b'); const other = carousel({},'b');
  a.move(2); assert.equal(b.slider.activeIndex,0); assert.equal(duplicate.slider.activeIndex,0); assert.equal(other.slider.activeIndex,0);
  duplicate.release(); a.move(3); assert.equal(b.slider.activeIndex,3);
  a.release(); b.release(); other.release();
});

test('cleanup is idempotent and rebuilt targets reconnect without stale listeners',() => {
  const doc = {}; const a = carousel(doc,'a','b'); const old = carousel(doc,'b');
  old.release(); old.release(); assert.equal(old.handlers.size,0);
  a.move(2); assert.equal(old.slider.activeIndex,0);
  const rebuilt = carousel(doc,'b'); assert.equal(rebuilt.slider.activeIndex,2);
  a.release(); assert.equal(a.handlers.size,0); a.move(4); assert.equal(rebuilt.slider.activeIndex,2);
  rebuilt.release();
});

test('invalid selectors cannot target arbitrary elements and empty sliders are skipped',() => {
  const doc = {}; const a = carousel(doc,'a','.b, #b, b, body div'); const b = carousel(doc,'b','empty'); const empty = carousel(doc,'empty','',0);
  a.move(2); assert.equal(b.calls.length,1); assert.equal(empty.calls.length,0);
  a.release(); b.release(); empty.release();
  const slider = {on:assert.fail};
  registerCarouselSync({ownerDocument:doc},slider,{controller_id:'bad id',controlled_ids:'body > div'})();
  registerCarouselSync({ownerDocument:doc},slider,{controller_id:'valid',mode:'marquee'})();
});
