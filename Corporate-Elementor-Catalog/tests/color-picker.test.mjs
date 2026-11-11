import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';

test('external reset cancels an older pending color and unmount cancels delayed callbacks', async () => {
  const handlers = new Map(); const changes = [];
  let color = '#123456'; let removed = 0; let destroyed = 0;
  const app = { remove: () => removed++ };
  const value = next => ({ toHEXA: () => ({ toString: () => next }) });
  const picker = {
    getRoot: () => ({ app }), on(name, callback) { handlers.set(name, callback); return this; },
    getColor: () => value(color), setColor(next) { color = next; }, show() {}, destroy() { destroyed++; },
  };
  globalThis.rdctTestPickr = { create: () => picker };
  try {
    const source = (await readFile(new URL('../shared/admin/color-picker.js', import.meta.url), 'utf8'))
      .replace("import Pickr from '@simonwep/pickr';", 'const Pickr = globalThis.rdctTestPickr;')
      .replace("import '@simonwep/pickr/dist/themes/monolith.min.css';", '');
    const { createColorPicker } = await import(`data:text/javascript;base64,${Buffer.from(source).toString('base64')}`);
    const controller = createColorPicker({ ownerDocument: { body: {} } }, { value: color, colors: [], label: 'Color', resetLabel: 'Reset', onChange: next => changes.push(next), onOpen() {}, onClose() {} });
    handlers.get('init')();
    handlers.get('change')(value('#abcdef'));
    controller.setColor('#ffffff');
    await new Promise(resolve => setTimeout(resolve, 150));
    assert.deepEqual(changes, []);
    assert.equal(color, '#ffffff');
    handlers.get('change')(value('#AABBCC'));
    handlers.get('changestop')();
    assert.deepEqual(changes, ['#aabbcc']);
    handlers.get('change')(value('#654321'));
    controller.destroy(); controller.destroy();
    await new Promise(resolve => setTimeout(resolve, 150));
    assert.deepEqual(changes, ['#aabbcc']);
    assert.equal(destroyed, 1); assert.equal(removed, 1);
  } finally { delete globalThis.rdctTestPickr; }
});
