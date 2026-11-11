import test from 'node:test';
import assert from 'node:assert/strict';
import { editorReady } from '../radmanct-components/src/js/admin/component-editor.js';
import { prioritizeRadmanPanel } from '../radmanct-components/src/js/admin/elementor-panel.js';
import { createLibraryDialog } from '../radmanct-components/src/js/admin/component-dialog.js';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

test('library stays unavailable until Elementor has its document, preview container and commands', () => {
  const editor = { $previewContents: [{ readyState: 'complete' }], getPreviewContainer: () => ({}), documents: { getCurrentId: () => 13 } };
  assert.equal(editorReady(undefined, undefined), false);
  assert.equal(editorReady(editor, {}), false);
  assert.equal(editorReady({ ...editor, $previewContents: [{ readyState: 'loading' }] }, { run() {} }), false);
  assert.equal(editorReady({ ...editor, getPreviewContainer: () => null }, { run() {} }), false);
  assert.equal(editorReady({ ...editor, documents: { getCurrentId: () => null } }, { run() {} }), false);
  assert.equal(editorReady(editor, { run() {} }), true);
  assert.equal(editorReady({ getPreviewContainer() { throw Error('Not loaded'); } }, { run() {} }), false);
});

test('library appears on document readiness, fetches only on click and permits retry after failure', async () => {
  const source = (await readFile(new URL('../radmanct-components/src/js/admin/component-editor.js', import.meta.url), 'utf8')).replace(/^import .*\r?\n/gm, '').replace('export function', 'function');
  const events = new Map(); let documentId = null; let requests = 0;
  const launcher = { textContent: 'Library', hidden: true, dataset: { libraryUrl: '/wp-admin/admin-ajax.php', libraryNonce: 'nonce', libraryError: 'Retry' }, addEventListener(name, callback) { events.set(`button:${name}`, callback); }, setAttribute() {}, removeAttribute() {} };
  const preview = { readyState: 'complete', querySelectorAll: () => [], addEventListener(name,callback,capture) { events.set(`preview:${name}`,{callback,capture}); } };
  const editor = { $previewContents: [preview], documents: { getCurrentId: () => documentId }, getPreviewContainer: () => ({}), on(name, callback) { events.set(name, callback); } };
  const target = {setAttribute(){},addEventListener(){},querySelector:()=>null};
  const document = { querySelector: (selector) => selector === '[data-library-open]' ? launcher : target, getElementById: () => null, createElement: () => ({ dataset: {}, style:{}, setAttribute() {} }) };
  const dialogsManager = {createWidget:()=>({getElements:()=>[target],show(){},hide(){}})};
  const window = { elementor: editor, elementorCommon:{dialogsManager}, $e: { run() {} }, location: { href: 'https://radman.test/wp-admin/post.php', origin: 'https://radman.test' }, addEventListener(name, callback) { events.set(name, callback); } };
  vm.runInNewContext(source, { window, document, URL, URLSearchParams, AbortController, prioritizeRadmanPanel, createLibraryDialog, fetch: async () => { requests++; throw Error('Offline'); } });
  assert.equal(requests, 0); assert.equal(launcher.hidden, true);
  documentId = 13; events.get('document:loaded')();
  assert.equal(launcher.hidden, true); assert.equal(requests, 0);
  launcher.hidden = true; events.get('elementor/init')();
  assert.equal(launcher.hidden, true); assert.equal(requests, 0);
  assert.equal(events.get('preview:click').capture,true);
  let opened = 0; let stopped = false;
  launcher.click = () => {opened++;};
  const opener = {};
  events.get('preview:click').callback({target:{closest:() => opener},preventDefault(){},stopPropagation(){stopped=true;}});
  let nativeContainerEdit = 0;
  if (!stopped) nativeContainerEdit++;
  assert.equal(opened,1); assert.equal(nativeContainerEdit,0); assert.equal(launcher.rdctLibraryOpener,opener);
  await events.get('button:click')({ stopImmediatePropagation() {} });
  assert.equal(requests, 1); assert.equal(launcher.disabled, false); assert.equal(launcher.textContent, 'Retry');
  await events.get('button:click')({ stopImmediatePropagation() {} });
  assert.equal(requests, 2);
});

test('library modal delegates dismissal and scroll locking to Elementor', () => {
  let options; let hidden = 0; let shown = 0;
  const attributes = new Map();
  let keydown;
  const doc = {activeElement:null};
  const controls = [0,1].map(() => ({getClientRects:()=>[{}],focus(){doc.activeElement=this;}}));
  const node = {setAttribute:(name,value)=>attributes.set(name,value),addEventListener:(_,listener)=>{keydown=listener;},querySelectorAll:()=>controls,ownerDocument:doc};
  const manager = {createWidget(type,settings) {
    assert.equal(type,'lightbox'); options = settings;
    return {getElements:()=>[node],show(){shown++;},hide(){options.onHide();}};
  }};
  assert.equal(createLibraryDialog(null,{}),null);
  const dialog = createLibraryDialog(manager,{label:'Library',closeLabel:'Close',onHide:()=>hidden++});
  assert.equal(options.preventScroll,true);
  assert.equal(options.closeButton,true);
  assert.deepEqual(options.hide,{onBackgroundClick:true,onOutsideClick:true,onEscKeyPress:true});
  assert.equal(attributes.get('aria-modal'),'true');
  doc.activeElement=controls[1]; let prevented=false;
  keydown({key:'Tab',preventDefault(){prevented=true;}});
  assert.equal(prevented,true); assert.equal(doc.activeElement,controls[0]);
  keydown({key:'Tab',shiftKey:true,preventDefault(){}});
  assert.equal(doc.activeElement,controls[1]);
  dialog.show(); assert.equal(dialog.open,true); assert.equal(shown,1);
  dialog.hide(); assert.equal(dialog.open,false); assert.equal(hidden,1);
  dialog.hide(); assert.equal(hidden,1);
});

test('Radman category leads the native collection without duplicating widgets or changing category settings', () => {
  const names = ['favorites', 'basic', 'radmanct-components', 'general'];
  const categories = Object.fromEntries(names.map((name) => [name, { title:name, active:name !== 'general' }]));
  let resets = 0;
  const collection = { models:names.map((name) => ({ get:() => name })), reset(models) { this.models = models; ++resets; } };
  const editor = { config:{document:{panel:{elements_categories:categories}}}, getPanelView:() => ({getCurrentPageView:() => ({categoriesCollection:collection})}) };
  prioritizeRadmanPanel(editor);
  assert.deepEqual(Object.keys(editor.config.document.panel.elements_categories), ['radmanct-components', 'favorites', 'basic', 'general']);
  assert.deepEqual(collection.models.map((model) => model.get('name')), ['radmanct-components', 'favorites', 'basic', 'general']);
  assert.equal(editor.config.document.panel.elements_categories.general, categories.general);
  prioritizeRadmanPanel(editor); assert.equal(resets, 1);
  assert.doesNotThrow(() => prioritizeRadmanPanel({getPanelView() {throw Error('Not ready');}}));
  assert.doesNotThrow(() => prioritizeRadmanPanel());
});
