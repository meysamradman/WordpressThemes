import { test } from 'node:test';
import assert from 'node:assert/strict';

globalThis.window = { wp: { element: { createElement: (type, props, ...children) => ({ type, props: { ...props, children } }) } } };
const { ContentViewFields } = await import('../radmanct/src/js/admin/content-view-fields.js');
const { ContentLayoutFields } = await import('../radmanct/src/js/admin/layout-fields.js');
const nodes = (node) => Array.isArray(node) ? node.flatMap(nodes) : node?.props ? [node, ...nodes(node.props.children)] : [];

test('design preferences stay editable without Pro or Core and do not activate modules', () => {
  const changes = [];
  const config = { labels: {}, elementor: false, elementorPro: false, contentModules: { blog: true, catalog: false, portfolio: false }, contentLinks: { items: {}, archives: {} }, pagesUrl: '/wp-admin/edit.php?post_type=page' };
  const fields = ['blog', 'catalog', 'portfolio'].flatMap(scope => ['single', 'archive'].flatMap(kind =>
    nodes(ContentViewFields({ scope: `${scope}_${kind}`, config, values: {}, change: (...args) => changes.push(args) })).filter(node => node.props.name?.endsWith('_renderer'))));
  assert.equal(fields.length, 6);
  for (const field of fields) {
    assert.notEqual(field.props.disabled, true);
    assert.deepEqual(field.props.options.map(([value]) => value), ['custom', 'elementor']);
    assert.ok(field.props.options.every((entry) => !entry[2]));
  }
  fields[2].props.onChange(fields[2].props.name, 'elementor');
  assert.deepEqual(changes, [['catalog_single_renderer', 'elementor']]);
  assert.deepEqual(config.contentModules, { blog: true, catalog: false, portfolio: false });
});

test('content layouts reuse the shared fields for six independent views and retain zero spacing', () => {
  const changes = [];
  const config = { labels: {}, contentModules: {}, layoutFields: { width: [320, 2560], gutter: [0, 160], mobile_gutter: [0, 160] } };
  const values = { blog_archive_layout: 'custom', blog_archive_width: 1100, blog_archive_gutter: 0 };
  for (const content of ['blog', 'catalog', 'portfolio']) for (const view of ['archive', 'single']) {
    const scope = `${content}_${view}`;
    const field = nodes(ContentViewFields({ scope, config, values, change: (...args) => changes.push(args) })).find(node => node.type === ContentLayoutFields);
    assert.equal(field.type, ContentLayoutFields);
    assert.equal(field.props.scope, scope);
    const controls = nodes(ContentLayoutFields(field.props));
    const mode = controls.find(node => node.props.name === `${scope}_layout`);
    assert.deepEqual(mode.props.options.map(([key]) => key), ['inherit', 'contained', 'full', 'custom']);
    if (scope === 'blog_archive') {
      const gutter = controls.find(node => node.props.name === `${scope}_gutter`);
      assert.equal(gutter.props.value, 0);
      gutter.props.onChange(gutter.props.name, 24);
    }
  }
  assert.deepEqual(changes, [['blog_archive_gutter', 24]]);
});
