import { test } from 'node:test';
import assert from 'node:assert/strict';
import { loadPreview } from '../radmanct/src/js/admin/builder-preview-loader.js';
import { historyShortcut } from '../radmanct/src/js/admin/builder-shortcuts.js';
import { prepareMenuPreview } from '../radmanct/src/js/admin/builder-preview-menu.js';

test('inert builder preview exposes menu state to the same responsive CSS without executing scripts', () => {
  const button = {hidden: true}; const body = {};
  const menu = {dataset: {}, querySelector: (selector) => selector.includes('toggle') ? button : body};
  const incomplete = {dataset: {}, querySelector: () => null};
  prepareMenuPreview({querySelectorAll: () => [menu, incomplete]});
  assert.equal(menu.dataset.enhanced, 'preview'); assert.equal(button.hidden, false);
  assert.equal(incomplete.dataset.enhanced, undefined);
});

const tick = () => new Promise((resolve) => setTimeout(resolve, 5));
const deferred = () => { let resolve; const promise = new Promise((done) => { resolve = done; }); return { promise, resolve }; };

test('preview cancellation drops late responses and preserves the editable layout', async () => {
  const original = { enabled: true, rows: [] };
  const first = deferred();
  const calls = []; const updates = []; const errors = [];
  const options = { id: 'header', layout: original, direction: 'rtl', delay: 0, errorMessage: 'Request failed',
    onStart() {}, onSuccess: (html) => updates.push(html), onError: (error) => errors.push(error),
    request: (...args) => { calls.push(args); return first.promise; } };
  const cancel = loadPreview(options);
  await tick();
  assert.equal(calls[0][0], 'preview');
  assert.equal(calls[0][2].layout.enabled, false);
  assert.equal(original.enabled, true);
  cancel();
  assert.equal(calls[0][3].aborted, true);
  const stop = loadPreview({ ...options, request: async () => ({ html: '<html>Current</html>' }) });
  await tick();
  first.resolve({ html: '<html>Obsolete</html>' });
  await tick();
  assert.deepEqual(updates, ['<html>Current</html>']);
  assert.deepEqual(errors, []);
  stop();
});

test('preview debounce cancels before sending and failure can be retried without saving', async () => {
  let requests = 0; const updates = []; const errors = [];
  const options = { id: 'footer', layout: { rows: [] }, direction: 'ltr', delay: 0, errorMessage: 'Request failed',
    onStart() {}, onSuccess: (html) => updates.push(html), onError: (error) => errors.push(error),
    request: async () => { ++requests; return { html: '' }; } };
  loadPreview(options)();
  await tick();
  assert.equal(requests, 0);
  const cancel = loadPreview(options);
  await tick();
  assert.deepEqual(errors, ['Request failed']);
  cancel();
  const retry = loadPreview({ ...options, request: async () => ({ html: '<html>Recovered</html>' }) });
  await tick();
  assert.deepEqual(updates, ['<html>Recovered</html>']);
  retry();
});

test('history shortcuts keep native text editing, composition and unrelated keys intact', () => {
  assert.equal(historyShortcut({ key: 'z', ctrlKey: true }), 'undo');
  assert.equal(historyShortcut({ key: 'Z', metaKey: true, shiftKey: true }), 'redo');
  assert.equal(historyShortcut({ key: 'y', ctrlKey: true }), 'redo');
  for (const event of [{ key: 'z' }, { key: 'z', ctrlKey: true, altKey: true }, { key: 'z', ctrlKey: true, isComposing: true }, { key: 'z', ctrlKey: true, defaultPrevented: true }, { key: 's', ctrlKey: true }]) {
    assert.equal(historyShortcut(event), null);
  }
  for (const tag of ['input', 'textarea', 'select', 'contenteditable', 'textbox']) {
    assert.equal(historyShortcut({ key: 'z', ctrlKey: true, target: { closest: (selector) => { assert.ok(selector.includes(tag)); return {}; } } }), null);
  }
});

let state; let cursor; let effects = [];
globalThis.window = { rdctBuilder: { labels: { retryPreview: 'Retry preview' }, direction: 'ltr' }, wp: { element: {
  createElement: (type, props, ...children) => ({ type, props: { ...props, children } }),
  useState: () => [state[cursor++], () => {}], useEffect(callback) { effects.push(callback); }, useRef: () => ({ current: null }),
} } };
const { Preview } = await import('../radmanct/src/js/admin/builder-preview.js');
const nodes = (node) => Array.isArray(node) ? node.flatMap(nodes) : node?.props ? [node, ...nodes(node.props.children)] : [];

test('preview tools stay locked for obsolete layouts, loading, errors and save operations', () => {
  const layout = { rows: [{ id: 'row', items: [] }] };
  const key = JSON.stringify(['header', 'ltr', JSON.stringify(layout)]);
  const target = { id: 'row', rowId: 'row', rect: { left: 0, top: 0, width: 100, height: 50 } };
  const render = ({ renderedKey = key, status = '', failed = false, disabled = false, viewportWidth, onWidthChange, ready = true } = {}) => {
    state = ['<html>Preview</html>', status, failed, 0, renderedKey, 0, 240, ready, [target]]; cursor = 0; effects = [];
    return Preview({ layout, id: 'header', selected: { rowId: 'row' }, disabled, dragProps: () => ({}), dropResolver: {}, actions: [], viewportWidth, onWidthChange, onLoadingChange:value=>notifications.push(value) });
  };
  const notifications = [];
  const boxes = (tree) => nodes(tree).filter((node) => node.props.className?.includes('rdct-bld-preview__box'));
  assert.equal(boxes(render()).length, 1);
  assert.equal(nodes(render()).some(node => node.type?.name === 'LoadingStatus'),false);
  const pendingFrame=render({ready:false});
  assert.equal(pendingFrame.props['aria-busy'],true);
  assert.equal(nodes(pendingFrame).some(node => node.type?.name === 'LoadingStatus'),false);
  effects[0]();assert.equal(notifications.at(-1),true);
  const controlled = render({ viewportWidth: 900 });
  const frame = nodes(controlled).find((node) => node.props.className === 'rdct-bld-preview__frame');
  assert.equal(frame.props.style.width, '900px');
  const stale = render({ renderedKey: 'older-layout' });
  assert.equal(boxes(stale).length, 0);
  assert.equal(stale.props['aria-busy'], true);
  assert.equal(boxes(render({ status: 'Loading' })).length, 0);
  assert.equal(boxes(render({ disabled: true })).length, 0);
  const failed = render({ status: 'Request failed', failed: true });
  assert.equal(boxes(failed).length, 0);
  assert.equal(failed.props['aria-busy'], false);
  effects[0]();assert.equal(notifications.at(-1),false);
  assert.equal(nodes(failed).filter((node) => node.props.role === 'alert').length, 1);
  assert.equal(nodes(failed).some(node => node.type?.name === 'LoadingStatus'),false);
  assert.equal(nodes(failed).filter((node) => (node.type === 'button' || node.type?.name === 'ButtonControl') && node.props.children.includes('Retry preview')).length, 1);
});

test('preview batches resize and font events and cancels measurements on teardown', async (t) => {
  const pending=new Map();const listeners=new Map();let next=0;let reads=0;let resized;let disconnected=false;
  const names=['requestAnimationFrame','cancelAnimationFrame','ResizeObserver'];
  const original=names.map(name=>Object.getOwnPropertyDescriptor(globalThis,name));
  t.after(()=>names.forEach((name,index)=>{if(original[index])Object.defineProperty(globalThis,name,original[index]);else delete globalThis[name];}));
  globalThis.requestAnimationFrame=fn=>{const id=++next;pending.set(id,fn);return id;};
  globalThis.cancelAnimationFrame=id=>pending.delete(id);
  globalThis.ResizeObserver=class {constructor(fn){resized=fn;}observe(){}disconnect(){disconnected=true;}};
  const layout={rows:[]};state=['<html>Preview</html>','',false,0,'',0,48,true,[]];cursor=0;effects=[];
  const tree=Preview({layout,id:'header',dragProps:()=>({}),dropResolver:{}});
  const iframe=nodes(tree).find(node=>node.type==='iframe');
  const content={getBoundingClientRect:()=>({height:48}),scrollHeight:48,querySelectorAll:()=>{++reads;return [];}};
  iframe.props.ref.current={contentDocument:{querySelector:()=>content,fonts:{ready:Promise.resolve()}},contentWindow:{addEventListener:(name,fn)=>listeners.set(name,fn),removeEventListener:name=>listeners.delete(name)}};
  const cleanup=effects[3]();resized();listeners.get('resize')();await Promise.resolve();
  assert.equal(pending.size,1);
  for(const [id,fn] of pending){pending.delete(id);fn();}
  assert.equal(reads,1);
  resized();cleanup();resized();await Promise.resolve();
  assert.equal(pending.size,0);assert.equal(listeners.size,0);assert.equal(disconnected,true);
});
