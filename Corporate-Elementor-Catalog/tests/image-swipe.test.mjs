import test from 'node:test';
import assert from 'node:assert/strict';
import { bindImageSwipe } from '../shared/frontend/swipe.js';

function fixture(options = {}) {
  const image = new EventTarget();
  image.ownerDocument = { defaultView: { visualViewport: { scale: 1 } } };
  const steps = [];
  const destroy = bindImageSwipe(image, (step) => steps.push(step), options);
  const fire = (type, values = {}) => {
    const event = new Event(type, { cancelable: true });
    for (const [key, value] of Object.entries({ pointerId: 1, pointerType: 'touch', isPrimary: true, button: 0, clientX: 150, clientY: 100, timeStamp: 100, ...values })) Object.defineProperty(event, key, { value });
    image.dispatchEvent(event);
    assert.equal(event.defaultPrevented, false);
  };
  return { image, steps, destroy, fire };
}

test('image swipe advances in LTR and reverses in RTL without blocking native events', () => {
  for (const rtl of [false, true]) {
    const { fire, steps, destroy } = fixture({ rtl: () => rtl });
    fire('pointerdown'); fire('pointerup', { clientX: 70, timeStamp: 300 });
    fire('pointerdown'); fire('pointerup', { clientX: 230, timeStamp: 300 });
    assert.deepEqual(steps, rtl ? [-1, 1] : [1, -1]);
    destroy();
  }
});

test('vertical movement, taps, mouse, long holds and cancelled gestures do not navigate', () => {
  const { fire, steps, destroy } = fixture();
  for (const values of [{ clientX: 130 }, { clientX: 70, clientY: 200 }, { clientX: 70, timeStamp: 1000 }, { clientX: 70, pointerId: 2 }]) {
    fire('pointerdown'); fire('pointerup', values);
  }
  fire('pointerdown', { pointerType: 'mouse' }); fire('pointerup', { clientX: 70 });
  for (const type of ['pointercancel', 'lostpointercapture']) {
    fire('pointerdown'); fire(type); fire('pointerup', { clientX: 70 });
  }
  assert.deepEqual(steps, []); destroy();
});

test('pinch zoom, multiple fingers, disabled state and disposed listeners preserve the image', () => {
  let enabled = true;
  const { image, fire, steps, destroy } = fixture({ enabled: () => enabled });
  fire('pointerdown'); fire('pointerdown', { isPrimary: false, pointerId: 2 }); fire('pointerup', { clientX: 70 });
  fire('pointerdown'); image.ownerDocument.defaultView.visualViewport.scale = 2; fire('pointerup', { clientX: 70 });
  fire('pointerdown'); image.ownerDocument.defaultView.visualViewport.scale = 1; fire('pointerup', { clientX: 70 });
  fire('pointerdown'); enabled = false; fire('pointerup', { clientX: 70 });
  enabled = true; destroy(); fire('pointerdown'); fire('pointerup', { clientX: 70 });
  assert.deepEqual(steps, []);
});
