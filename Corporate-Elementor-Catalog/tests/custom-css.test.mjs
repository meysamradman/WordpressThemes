import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import {spawnSync} from 'node:child_process';
import {fileURLToPath} from 'node:url';
import {compileCustomCss} from '../radmanct/src/js/integrations/custom-css-compiler.js';

const fixtures = JSON.parse(await readFile(new URL('./fixtures/custom-css.json',import.meta.url),'utf8'));
test('element CSS preserves content strings, scopes selectors and rejects unsafe/malformed resources', () => {
  for (const entry of fixtures) {
    const result = compileCustomCss(entry.input,entry.id ?? 'sample');
    if (!entry.body) { assert.equal(result,null,JSON.stringify(entry)); continue; }
    assert.ok(result,JSON.stringify(entry));
    assert.equal(result.css,`.${result.class}{${entry.body}\n}`);
  }
  assert.equal(compileCustomCss('x'.repeat(16385),'sample'),null);
  assert.equal(compileCustomCss('ف'.repeat(9000),'sample'),null);
});
test('PHP output and live-preview output are identical for all shared fixtures', {skip:!process.env.RDCT_PHP}, () => {
  const response = spawnSync(process.env.RDCT_PHP,['-n',fileURLToPath(new URL('./custom-css-contract.php',import.meta.url)),'--json'],{encoding:'utf8'});
  assert.equal(response.status,0,response.stdout + response.stderr);
  assert.deepEqual(JSON.parse(response.stdout),fixtures.map((entry) => compileCustomCss(entry.input,entry.id ?? 'sample')));
});
test('style identity changes with content, survives typed storage and isolates template instances', () => {
  const css='selector{color:red}';
  assert.deepEqual(compileCustomCss(css,'first'),compileCustomCss({$$type:'string',value:css},'first'));
  assert.notEqual(compileCustomCss(css,'first').class,compileCustomCss(css,'second').class);
  assert.notEqual(compileCustomCss(css,'first').class,compileCustomCss('selector{color:blue}','first').class);
});

test('live CSS preview isolates nodes, removes cleared styles and detaches replaced settings', async () => {
  const frames = new Map(); let observer; let frameId = 0;
  globalThis.requestAnimationFrame = (callback) => { frames.set(++frameId,callback); return frameId; };
  globalThis.cancelAnimationFrame = (id) => frames.delete(id);
  globalThis.MutationObserver = class { constructor(callback) {observer=callback;} observe() {} disconnect() {} };
  globalThis.window = {addEventListener() {},removeEventListener() {}}; window.parent=window;
  const {connectCustomCssPreview} = await import('../radmanct/src/js/integrations/elementor-custom-css-preview.js');
  const styles = new Set();
  const makeRoot = (id) => {
    const classes = new Set(['existing']);
    return {id,isConnected:true,getAttribute:() => id,classList:{add:(name) => classes.add(name),remove:(name) => classes.delete(name),[Symbol.iterator]:() => classes.values()}};
  };
  const first=makeRoot('first'); const second=makeRoot('second'); const callbacks=new Set();
  let value={$$type:'string',value:'selector{color:red}'};
  const settings={get:() => value,on:(event,callback) => {assert.equal(event,'change:rdct_custom_css');callbacks.add(callback);},off:(_,callback) => callbacks.delete(callback)};
  const document={body:{},head:{append:(style) => styles.add(style)},createElement:() => ({textContent:'',remove() {styles.delete(this);}}),querySelectorAll:() => [first,second].filter((root) => root.isConnected)};
  const containers={first:{settings},second:{settings:{get:() => '',on() {},off() {}}}};
  const cleanup=connectCustomCssPreview(document,{getContainer:(id) => containers[id]});
  const red=compileCustomCss(value,'first');
  assert.ok([...first.classList].includes(red.class)); assert.equal(styles.size,1);
  assert.deepEqual([...second.classList],['existing']);
  value='selector{color:blue}'; callbacks.forEach((callback) => callback());
  assert.ok(![...first.classList].includes(red.class)); assert.equal(styles.size,1);
  assert.ok([...styles][0].textContent.includes('blue'));
  value=''; callbacks.forEach((callback) => callback()); assert.equal(styles.size,0);
  const oldCallback=[...callbacks][0];
  containers.first.settings={get:() => 'selector{opacity:.5}',on() {},off() {}};
  observer(); for (const callback of frames.values()) callback(); frames.clear();
  assert.equal(callbacks.size,0); assert.equal(styles.size,1);
  first.isConnected=false; observer(); for (const callback of frames.values()) callback(); frames.clear();
  assert.equal(styles.size,0);
  cleanup(); oldCallback(); assert.equal(styles.size,0);
});
