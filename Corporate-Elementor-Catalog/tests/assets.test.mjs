import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFile, stat, readdir } from 'node:fs/promises';
import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { createHash } from 'node:crypto';

const workspace = fileURLToPath(new URL('../', import.meta.url));
const read = async (path) => JSON.parse(await readFile(path, 'utf8'));

test('builder inspector loads selected field families without pulling menu and icon controls eagerly', async () => {
  const manifest=await read(resolve(workspace,'radmanct/dist/manifest.json'));
  const graph=(key,seen=new Set())=>{assert.ok(manifest[key],key);if(seen.has(key))return seen;seen.add(key);for(const next of manifest[key].imports ?? [])graph(next,seen);return seen;};
  const entry='src/js/admin/builder-inspector.js';
  const initial=[...graph(entry)].join('\n');
  assert.doesNotMatch(initial,/builder-(?:button-fields|menu-fields|menu-panel-fields|linked-fields|icon-picker)|(?:^|\/)icon-picker-/);
  for(const family of ['button','menu','menu-panel','linked'])assert.ok(manifest[`src/js/admin/builder-${family}-fields.js`]);
  for(const key of ['src/js/admin/builder.js',entry])assert.doesNotMatch([...graph(key)].join('\n'),/gsap|swiper|smooth-scroll|navigation\/motion/);
});

test('all nineteen reference-led review presets have independent CSS outside the base', async () => {
  const manifest = await read(resolve(workspace, 'radmanct-components/dist/manifest.json'));
  const entry = manifest['src/css/components/testimonial/base.css'];
  const base = await readFile(resolve(workspace, 'radmanct-components/dist', entry.file), 'utf8');
  assert.doesNotMatch(base, /rdct-tst--s\d/);
  for (let index=1; index<=19; ++index) {
    const style=`s${String(index).padStart(2,'0')}`;
    const item=manifest[`src/css/components/testimonial/${style}.css`]; assert.ok(item,style);
    const css=await readFile(resolve(workspace,'radmanct-components/dist',item.file),'utf8');
    assert.match(css,new RegExp(`rdct-tst--${style}`));
    assert.doesNotMatch(css,new RegExp(`rdct-tst--(?!${style})s\\d`));
  }
});

test('carousel arrow surfaces and shadows have separate built stylesheets', async () => {
  const manifest = await read(resolve(workspace, 'radmanct-components/dist/manifest.json'));
  for (const style of ['navigation-surface','navigation-shadow']) assert.ok(manifest[`src/css/components/carousel/${style}.css`]);
  const entry = manifest['src/css/components/carousel/base.css'];
  const css = await readFile(resolve(workspace, 'radmanct-components/dist', entry.file), 'utf8');
  assert.doesNotMatch(css, /data-fill=solid|data-shadow=hover/);
});

test('header text state remains an optional chunk without animation or carousel engines', async () => {
  const manifest = await read(resolve(workspace, 'radmanct-components/dist/manifest.json'));
  const collect = (key, seen = new Set()) => { assert.ok(manifest[key], key); if (seen.has(key)) return seen; seen.add(key); for (const next of manifest[key].imports ?? []) collect(next, seen); return seen; };
  assert.doesNotMatch([...collect('src/js/frontend.js')].join(' '), /header-state|animation-vendor|swiper/);
  assert.doesNotMatch([...collect('src/js/components/text/header-state.js')].join(' '), /animation-vendor|swiper/);
  assert.ok(manifest['src/css/components/text/header-state.css']);
});

test('image accordion keeps its motion chunk and stylesheet separate from legacy panels', async () => {
  const manifest = await read(resolve(workspace, 'radmanct-components/dist/manifest.json'));
  const collect = (key, seen = new Set()) => { assert.ok(manifest[key], key); if (seen.has(key)) return seen; seen.add(key); for (const next of manifest[key].imports ?? []) collect(next, seen); return seen; };
  assert.doesNotMatch([...collect('src/js/components/curtain.js')].join(' '), /curtain\/images|animation-vendor|swiper/);
  assert.doesNotMatch([...collect('src/js/components/curtain/images.js')].join(' '), /animation-vendor|swiper|sticky-sections/);
  assert.ok(manifest['src/css/components/curtain/images.css']);
});

test('header navigation keeps drawers and individual menu effects outside its initial static graph', async () => {
  const root=resolve(workspace,'radmanct/dist'); const manifest=await read(resolve(root,'manifest.json'));
  const graph=(key,seen=new Set())=>{assert.ok(manifest[key],key);if(seen.has(key))return seen;seen.add(key);for(const next of manifest[key].imports ?? [])graph(next,seen);return seen;};
  const manager=Object.keys(manifest).find(key=>key.startsWith('_manager-')); assert.ok(manager);
  for(const key of ['src/js/frontend.js',manager]) assert.doesNotMatch([...graph(key)].join(' '),/navigation\/(?:drawer|hover\/roll|motion\/curtain|motion\/wave)\.js|CustomEase|gsap|animation-vendor|swiper/);
  assert.ok(manifest['src/js/navigation/hover/roll.js']);
  for (const style of ['menu-hover/roll', 'menu-indicators/dot']) assert.ok(manifest[`src/css/components/${style}.css`]);
  assert.doesNotMatch([...graph('src/js/navigation/hover/roll.js')].join(' '), /swiper|motion\/(?:curtain|wave)/);
  assert.doesNotMatch([...graph('src/js/navigation/drawer.js')].join(' '),/motion\/(?:curtain|wave)\.js|animation-vendor|swiper/);
  for(const effect of ['curtain','wave']) {
    const selected=[...graph(`src/js/navigation/motion/${effect}.js`)].join(' ');
    assert.doesNotMatch(selected,/swiper/);
    assert.ok([...graph(`src/js/navigation/motion/${effect}.js`)].some(key => /gsap|timeline/.test(key)), 'Selected effect did not share the GSAP timeline');
    assert.ok(!selected.includes(`motion/${effect==='wave'?'curtain':'wave'}.js`));
  }
  for(const part of ['sidebar','fullscreen','split','motion','wave']) assert.ok(manifest[`src/css/components/menu-panel/${part}.css`]);
  for(const part of ['timeline','curtain','wave']) {
    const source=await readFile(resolve(workspace,`radmanct/src/js/navigation/motion/${part}.js`),'utf8');
    assert.doesNotMatch(source,/\b(?:fetch|XMLHttpRequest|WebSocket)\s*\(/);
    assert.doesNotMatch(source,/\.animate\s*\(|requestAnimationFrame/);
  }
  const svgSource=await readFile(resolve(workspace,'radmanct/src/js/navigation/motion/timeline.js'),'utf8');
  assert.match(svgSource,/createElementNS\(ns/); assert.match(svgSource,/http:\/\/www\.w3\.org\/2000\/svg/);

});

test('component bootstrap and marquee keep unrelated engines out of their static import graphs', async () => {
  const root=resolve(workspace,'radmanct-components/dist'); const manifest=await read(resolve(root,'manifest.json'));
  const collect=(key,seen=new Set()) => { if(seen.has(key))return seen; seen.add(key); for(const dependency of manifest[key]?.imports ?? [])collect(dependency,seen); return seen; };
  for(const key of ['src/js/frontend.js','src/js/components/marquee.js']) {
    assert.ok(manifest[key]);
    const graph=[...collect(key)].join('\n');
    assert.doesNotMatch(graph,/animation-vendor|swiper|components\/(?:carousel|particles|flipbox|image)\.js/);
  }
  for(const style of ['card','bubble','centered','split','outline','minimal']) assert.ok(manifest[`src/css/components/testimonial/${style}.css`]);
});

test('stack effect and scoped stylesheet stay outside the normal carousel static graph', async () => {
  const root = resolve(workspace,'radmanct-components/dist');
  const manifest = await read(resolve(root,'manifest.json'));
  const graph = async (key,seen = new Set()) => {
    if (seen.has(key)) return ''; seen.add(key);
    const entry = manifest[key]; assert.ok(entry,key);
    return await readFile(resolve(root,entry.file),'utf8') + (await Promise.all((entry.imports || []).map((next) => graph(next,seen)))).join('');
  };
  assert.doesNotMatch(await graph('src/js/components/carousel/engine.js'),/effect-creative|shadowPerProgress/);
  assert.doesNotMatch(await graph('src/js/components/carousel/stack.js'),/shadowPerProgress/);
  assert.match(await graph('src/js/components/carousel/stack-effect.js'),/shadowPerProgress/);
  const entry = manifest['src/css/components/carousel/stack.css']; assert.ok(entry);
  const css = await readFile(resolve(root,entry.file),'utf8');
  assert.match(css,/\.rdct-crs.*\.swiper-creative/);
  const php = await readFile(resolve(workspace,'radmanct-components/inc/components/carousel.php'),'utf8');
  assert.match(php,/'stack' === \$s\['mode'\].*carousel-stack/);
});

test('SplitText stays in the optional line-reveal chunk and outside normal text and GSAP startup graphs', async () => {
  const root = resolve(workspace, 'radmanct-components/dist');
  const manifest = await read(resolve(root, 'manifest.json'));
  const graph = async (key, seen = new Set()) => {
    if (seen.has(key)) return '';
    seen.add(key);
    const entry = manifest[key]; assert.ok(entry, `Missing manifest dependency: ${key}`);
    return await readFile(resolve(root, entry.file), 'utf8') + (await Promise.all((entry.imports ?? []).map((dependency) => graph(dependency, seen)))).join('\n');
  };
  for (const key of ['src/js/frontend.js', 'src/js/animation-vendor.js', 'src/js/components/text-reveal-entry.js']) {
    assert.doesNotMatch(await graph(key), /autoSplit|deepSlice/);
  }
  assert.match(await graph('src/js/components/text/lines.js'), /autoSplit/);
  assert.match(await graph('src/js/components/text/lines.js'), /deepSlice/);
});

test('rich text rules build separately from the shared text effects stylesheet', async () => {
  const root = resolve(workspace, 'radmanct-components/dist');
  const manifest = await read(resolve(root, 'manifest.json'));
  const file = (name) => Object.values(manifest).find((entry) => entry.name === name && entry.file.endsWith('.css'))?.file;
  assert.ok(file('text-segments')); assert.ok(file('text-effects'));
  const rich = await readFile(resolve(root, file('text-segments')), 'utf8');
  const common = await readFile(resolve(root, file('text-effects')), 'utf8');
  assert.match(rich, /rdct-text__part/); assert.match(rich, /forced-colors/); assert.match(rich, /@media print/);
  assert.doesNotMatch(common, /rdct-text__(?:part|figure|segment)/);
  assert.doesNotMatch(common, /rdct-text__(?:rotator|fill-word)|@keyframes rdct-text-highlight|data-highlight=(?:wave|circle|brush)/);
});

test('fade code and scoped CSS stay outside the normal carousel static dependency graph', async () => {
  const root = resolve(workspace, 'radmanct-components/dist');
  const manifest = await read(resolve(root, 'manifest.json'));
  const graph = async (key, seen = new Set()) => {
    if (seen.has(key)) return '';
    seen.add(key);
    const entry = manifest[key];
    assert.ok(entry, `Missing manifest dependency: ${key}`);
    return await readFile(resolve(root, entry.file), 'utf8') + (await Promise.all((entry.imports ?? []).map((dependency) => graph(dependency, seen)))).join('\n');
  };
  assert.doesNotMatch(await graph('src/js/components/carousel/engine.js'), /cross-fade/);
  assert.match(await graph('src/js/components/carousel/fade.js'), /cross-fade/);
  const style = Object.values(manifest).find((entry) => entry.name === 'carousel-fade' && entry.file.endsWith('.css'));
  assert.ok(style);
  assert.match(await readFile(resolve(root, style.file), 'utf8'), /\.rdct-crs\s+\.swiper-fade/);
});

test('Vite and Swiper versions match the pinned manifest, lockfile and installed packages', async () => {
  const manifest = await read(resolve(workspace, 'package.json'));
  const lock = await read(resolve(workspace, 'package-lock.json'));
  for (const [name,version] of [['vite','8.3.4'],['swiper','14.3.0']]) {
    const installed = await read(resolve(workspace,`node_modules/${name}/package.json`));
    assert.equal((manifest.dependencies ?? {})[name] ?? manifest.devDependencies[name],version);
    assert.equal(lock.packages[`node_modules/${name}`].version,version);
    assert.equal(installed.version,version);
  }
});

test('GSAP stays exactly pinned across manifest, lockfile and installed build dependency', async () => {
  const manifest = await read(resolve(workspace, 'package.json'));
  const lock = await read(resolve(workspace, 'package-lock.json'));
  const installed = await read(resolve(workspace, 'node_modules/gsap/package.json'));
  assert.equal(manifest.dependencies.gsap, '3.15.0');
  assert.equal(lock.packages[''].dependencies.gsap, '3.15.0');
  assert.equal(lock.packages['node_modules/gsap'].version, '3.15.0');
  assert.equal(installed.version, '3.15.0');
});

test('all packages build for ES2020 and keep own code independent of jQuery', async () => {
  const build = await readFile(resolve(workspace,'tools/build.mjs'),'utf8');
  assert.match(build,/target:\s*\['es2020'\]/);
  for (const name of ['radmanct','radmanct-child','radmanct-content','radmanct-components','radmanct-mcp','shared']) {
    const files = await readdir(resolve(workspace,name),{recursive:true});
    for (const file of files.filter((path) => !path.split(/[\\/]/).includes('dist') && !(name === 'radmanct' && /^vendor[\\/]ace[\\/]/.test(path)) && /\.(?:php|jsx?)$/.test(path))) {
      const source = await readFile(resolve(workspace,name,file),'utf8');
      assert.doesNotMatch(source,/\bjQuery\b|(?:from\s*|import\s*\()\s*['"]jquery['"]/,`${name}/${file}`);
      assert.doesNotMatch(source,/['"]jquery(?:-core|-migrate)?['"]/,`${name}/${file}`);
    }
  }
});

test('local editor Ace files match the pinned vendor manifest', async () => {
  const root = resolve(workspace,'radmanct/vendor/ace');
  const manifest = await read(resolve(root,'manifest.json'));
  assert.equal(manifest.version,'1.43.2');
  for (const [name, hash] of Object.entries(manifest.files)) {
    assert.equal(createHash('sha256').update(await readFile(resolve(root,name))).digest('hex'),hash,name);
  }
});

for (const theme of ['radmanct', 'radmanct-child', 'radmanct-content', 'radmanct-components']) {
  test(`${theme}: runtime manifest only exposes minified assets with readable counterparts`, async () => {
    const root = resolve(workspace, theme, 'dist');
    const minified = await read(resolve(root, 'manifest.json'));
    const readable = await read(resolve(root, 'manifest-readable.json'));
    const entries = Object.entries(minified).filter(([, value]) => value.isEntry);
    assert.ok(entries.length > 0);
    for (const [key, asset] of Object.entries(minified)) {
      assert.match(asset.file, /\.min\.(css|js)$/);
      assert.ok((await stat(resolve(root, asset.file))).size > 0);
      for (const reference of [...(asset.imports ?? []), ...(asset.dynamicImports ?? [])]) {
        assert.ok(minified[reference], `Missing imported chunk: ${reference}`);
      }
      if (asset.isEntry) {
        assert.ok(readable[key], `Missing readable entry: ${key}`);
        assert.doesNotMatch(readable[key].file, /\.min\./);
        assert.ok((await stat(resolve(root, readable[key].file))).size > 0);
      }
    }
  });
}

test('frontend starts without React, GSAP or editor CSS in its initial dependency graph', async () => {
 for (const packageName of ['radmanct', 'radmanct-components']) {
  const root = resolve(workspace, packageName, 'dist');
  const manifest = await read(resolve(root, 'manifest.json'));
  const visited = new Set();
  let totalBytes = 0;
  async function visit(key) {
    if (visited.has(key)) return;
    visited.add(key);
    const asset = manifest[key];
    const source = await readFile(resolve(root, asset.file), 'utf8');
    totalBytes += Buffer.byteLength(source);
    assert.doesNotMatch(source, /ScrollTrigger|react-dom|wp\.element|gsap\.registerPlugin/);
    assert.equal((asset.css ?? []).length, 0);
    for (const imported of asset.imports ?? []) await visit(imported);
  }
  await visit('src/js/frontend.js');
  assert.ok(totalBytes < 8192, `Initial JavaScript is ${totalBytes} bytes`);
  assert.ok(Object.values(manifest).some((asset) => asset.isDynamicEntry));
 }
});

test('all built JavaScript chunks stay local and do not bundle a separate React runtime', async () => {
  for (const packageName of ['radmanct', 'radmanct-components', 'radmanct-content']) {
    const root = resolve(workspace, packageName, 'dist');
    const manifest = await read(resolve(root, 'manifest.json'));
    for (const asset of Object.values(manifest).filter((asset) => asset.file.endsWith('.js'))) {
      const source = await readFile(resolve(root, asset.file), 'utf8');
      assert.doesNotMatch(source, /react\.production|react-dom\.production|__SECRET_INTERNALS|__CLIENT_INTERNALS_DO_NOT_USE/, asset.file);
      assert.doesNotMatch(source, /(?:import\s*(?:\(|[^;]*?from\s*)|fetch\s*\()\s*['"](?:https?:)?\/\//, asset.file);
    }
  }
});

test('Core product fields and specifications CSS are isolated in an optional admin chunk', async () => {
  const root = resolve(workspace, 'radmanct-content/dist');
  const manifest = await read(resolve(root, 'manifest.json'));
  const entry = manifest['src/js/admin/content.js'];
  const specificationKey = 'src/js/admin/product-fields.js';
  assert.ok(entry.dynamicImports.includes(specificationKey));
  assert.ok(manifest[specificationKey].isDynamicEntry);
  assert.ok(manifest[specificationKey].css.length > 0);
  for (const key of ['src/js/admin/content.js', specificationKey]) {
    const source = await readFile(resolve(root, manifest[key].file), 'utf8');
    assert.match(source, /wp\.element/);
    assert.doesNotMatch(source, /react\.production|react-dom|__SECRET_INTERNALS/);
  }
});

test('admin entries use WordPress React without bundling a second runtime', async () => {
  const root = resolve(workspace, 'radmanct/dist');
  const manifest = await read(resolve(root, 'manifest.json'));
  for (const key of ['src/js/admin/settings.js', 'src/js/admin/builder.js']) {
    const asset = manifest[key];
    const source = await readFile(resolve(root, asset.file), 'utf8');
    assert.match(source, /wp\.element/);
    assert.doesNotMatch(source, /react\.production|react-dom|__SECRET_INTERNALS/);
    const entryBudget = 20480;
    assert.ok(Buffer.byteLength(source) < entryBudget, `${key} exceeds its bounded admin entry budget`);
    for (const dependency of asset.imports ?? []) {
      const chunk = manifest[dependency];
      assert.ok(['preload-helper', 'builder-elements', 'builder-devices', 'controls', 'color-field', 'settings-fonts'].includes(chunk.name));
      const helper = await readFile(resolve(root, chunk.file), 'utf8');
      const helperBudget = chunk.name === 'controls' ? 2304 : 2048;
      assert.ok(Buffer.byteLength(helper) < helperBudget, `${chunk.name} exceeds its shared control budget`);
      assert.doesNotMatch(helper, /react\.production|react-dom|__SECRET_INTERNALS/);
    }
  }
});

test('PHP interface messages have Persian translation entries and no hardcoded Persian', async () => {
  const catalog = await read(resolve(workspace, 'tools/translations/fa_IR.json'));
  for (const messages of Object.values(catalog)) {
    for (const value of Object.values(messages)) assert.doesNotMatch(value, /\uFFFD|\?{4,}/, 'Corrupted translation encoding');
  }
  for (const theme of ['radmanct', 'radmanct-child', 'radmanct-content', 'radmanct-mcp', 'radmanct-components']) {
    const files = await readdir(resolve(workspace, theme), { recursive: true });
    for (const file of files.filter((path) => path.endsWith('.php'))) {
      const source = await readFile(resolve(workspace, theme, file), 'utf8');
      assert.doesNotMatch(source, /[\u0600-\u06ff]/, file);
      for (const match of source.matchAll(/(?:__|_e|esc_html__|esc_attr__|esc_html_e|esc_attr_e)\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'(radmanct(?:-(?:child|content|mcp|components))?)'\s*\)/g)) {
        const message = match[1].replace(/\\(['\\])/g, '$1');
        assert.ok(catalog[match[2]][message], `Missing translation: ${file}: ${message}`);
      }
    }
  }
});

test('independent image, text and carousel features stay outside their dispatchers static graphs', async () => {
  const root=resolve(workspace,'radmanct-components/dist'); const manifest=await read(resolve(root,'manifest.json'));
  const graph=(key,seen=new Set()) => { assert.ok(manifest[key],key); if(seen.has(key)) return seen; seen.add(key); for(const next of manifest[key].imports ?? []) graph(next,seen); return seen; };
  assert.doesNotMatch([...graph('src/js/components/image.js')].join(' '),/image\/(?:parallax|visibility|reveal|tilt|lightbox)\.js/);
  assert.doesNotMatch([...graph('src/js/components/button.js')].join(' '),/button\/(?:actions|liquid|magnetic)\.js|animation-vendor|ScrollTrigger/);
  assert.doesNotMatch([...graph('src/js/components/button/actions.js')].join(' '),/content-dialog|image-dialog|scroll-lock|button\/scroll\.js/);
  assert.doesNotMatch([...graph('src/js/components/particles.js')].join(' '),/vendor\/particles\.js|swiper|animation-vendor/);
  assert.doesNotMatch([...graph('src/js/components/curtain.js')].join(' '),/curtain\/images\.js|animation-vendor/);
  assert.doesNotMatch([...graph('src/js/components/image/parallax.js')].join(' '),/animation-vendor|swiper|sticky-sections|carousel/);
  assert.doesNotMatch([...graph('src/js/components/text.js')].join(' '),/text\/(?:rotate|reveal|lines)\.js|animation-vendor|marquee\.js/);
  assert.ok(!graph('src/js/components/text/lines.js').has('src/js/components/text/reveal.js'));
  assert.ok(!graph('src/js/components/text/reveal.js').has('src/js/components/text/lines.js'));
  assert.doesNotMatch([...graph('src/js/components/text-reveal-entry.js')].join(' '),/text\/(?:reveal|lines)\.js/);
  assert.doesNotMatch([...graph('src/js/components/carousel/engine.js')].join(' '),/carousel\/(?:sync|external|stack-effect|fade)\.js/);
  for (const feature of ['autoplay', 'navigation', 'pagination']) {
    const key = `src/js/components/carousel/modules/${feature}.js`;
    assert.ok(manifest[key]);
    assert.ok(!graph('src/js/components/carousel/engine.js').has(key));
    assert.ok(!graph('src/js/components/carousel-entry.js').has(key));
    for (const other of ['autoplay', 'navigation', 'pagination'].filter((name) => name !== feature)) assert.ok(!graph(key).has(`src/js/components/carousel/modules/${other}.js`));
  }
  for(const feature of ['parallax','float','shadow','tilt','reveal']) assert.ok(manifest[`src/css/components/image/${feature}.css`]);
  for(const feature of ['inline-image','segment-gradient','segment-mask']) assert.ok(manifest[`src/css/components/text/${feature}.css`]);
});

test('sticky service, stack and split effects keep independent graphs and selected styles', async () => {
  const manifest = await read(resolve(workspace,'radmanct-components/dist/manifest.json'));
  const graph = (key, seen = new Set()) => {assert.ok(manifest[key],key); if(seen.has(key))return seen;seen.add(key);for(const next of manifest[key].imports ?? [])graph(next,seen);return seen;};
  assert.doesNotMatch([...graph('src/js/components/sticky-sections.js')].join(' '), /animation-vendor|sticky-sections\/(?:stack|split|panels)\.js|swiper/);
  for (const mode of ['stack','split','panels']) {
    const key = `src/js/components/sticky-sections/${mode}.js`;
    for (const other of ['stack','split','panels'].filter((name)=>name!==mode)) assert.ok(!graph(key).has(`src/js/components/sticky-sections/${other}.js`));
    assert.ok(manifest[`src/css/components/sticky-sections/${mode}.css`]);
  }
});


test('portfolio shell excludes legacy card layouts while default archives retain them', async () => {
  const root=resolve(workspace,'radmanct/dist');
  const manifest=await read(resolve(root,'manifest.json'));
  const css=async key=>readFile(resolve(root,manifest[`src/css/${key}.css`].file),'utf8');
  const shell=await css('portfolio-shell');
  assert.match(shell,/\.rdct-cnt-listing\s*\{/);
  assert.match(shell,/\.rdct-archive-status:empty/);
  assert.doesNotMatch(shell,/\.rdct-cnt-(?:grid|card|meta)\s*\{/);
  assert.doesNotMatch(shell,/@import/);
  for(const key of ['portfolio','catalog']) {
    const legacy=await css(key);
    assert.match(legacy,/\.rdct-cnt-grid\s*\{/);
    assert.match(legacy,/\.rdct-cnt-card\s*\{/);
    assert.match(legacy,/\.rdct-cnt-meta\s*\{/);
  }
  const components=resolve(workspace,'radmanct-components/dist');
  const componentManifest=await read(resolve(components,'manifest.json'));
  const button=await readFile(resolve(components,componentManifest['src/css/components/button/base.css'].file),'utf8');
  assert.match(button,/\.rdct-button__text\s*\{/);
  assert.doesNotMatch(button,/@import|data-text-effect=["']?(?:liquid|marquee)/);
});
