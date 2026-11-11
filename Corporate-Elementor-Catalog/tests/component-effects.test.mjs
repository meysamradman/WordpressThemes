import {test} from 'node:test';
import assert from 'node:assert/strict';
import {normalizeBorder,observeBorders} from '../radmanct-components/src/js/components/hover-border.js';
import {marqueeDirection,marqueeSpeed} from '../radmanct-components/src/js/components/marquee.js';

test('marquee changes speed at the same custom device edges used by carousel and PHP CSS', () => {
  const options = {marquee_speed:80,marquee_speed_tablet:40,marquee_speed_mobile:20,breakpoints:{tablet:901,desktop:1201}};
  assert.equal(marqueeSpeed(options,1201),80);
  assert.equal(marqueeSpeed(options,1200),40);
  assert.equal(marqueeSpeed(options,901),40);
  assert.equal(marqueeSpeed(options,900),20);
});

test('border validates CSS inputs and uses one isolated ring per element', () => {
  const value = normalizeBorder({enabled:'yes',width:900,duration:-2,color_start:'red;display:none',color_end:'#abc'});
  assert.equal(value.width,12); assert.equal(value.duration,1); assert.equal(value.color_start,'#64e8c6'); assert.equal(value.color_end,'#abc');
  const classes = new Set(); const rings = []; const properties = new Map(); let disconnected = false;
  const root = {tagName:'DIV',isConnected:true,getAttribute:() => JSON.stringify({enabled:true}),classList:{add:key => classes.add(key),remove:key => classes.delete(key)},append:ring => rings.push(ring)};
  const doc = {documentElement:{},querySelectorAll:() => [root],createElement:() => ({classList:{add(){}},setAttribute(){},style:{setProperty:(key,value) => properties.set(key,value)},remove:() => rings.pop()}),defaultView:{CSS:{registerProperty(){}},getComputedStyle:() => ({position:'static',direction:'rtl'}),MutationObserver:class {observe(){} disconnect(){disconnected = true;}}}};
  const cleanup = observeBorders(doc);
  assert.equal(observeBorders(doc),cleanup); assert.equal(rings.length,1);
  assert.equal(properties.get('--rdct-border-direction'),'reverse'); assert.equal(classes.has('rdct-bdr-positioned'),true);
  cleanup(); cleanup(); assert.equal(rings.length,0); assert.equal(disconnected,true); assert.equal(classes.size,0);
});

test('marquee follows logical direction without reversing text and uses bounded pixels per second', () => {
  assert.equal(marqueeDirection('start',false),-1); assert.equal(marqueeDirection('start',true),1);
  assert.equal(marqueeDirection('end',true),-1); assert.equal(marqueeDirection('left',true),-1); assert.equal(marqueeDirection('right',false),1);
  const options = {marquee_speed:80,marquee_speed_tablet:40,marquee_speed_mobile:20};
  assert.equal(marqueeSpeed(options,1440),80); assert.equal(marqueeSpeed(options,900),40); assert.equal(marqueeSpeed(options,390),20);
  assert.equal(marqueeSpeed({marquee_speed:800,marquee_speed_mobile:-1},390),500);
});
