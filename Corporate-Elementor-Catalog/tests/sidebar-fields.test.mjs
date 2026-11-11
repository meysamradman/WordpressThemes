import test from 'node:test';
import assert from 'node:assert/strict';

globalThis.window = { wp: { element: { createElement: (type, props, ...children) => ({ type, props: { ...props, children } }) } } };
const { SidebarFields } = await import('../radmanct/src/js/admin/sidebar-fields.js');
const { ContentViewFields } = await import('../radmanct/src/js/admin/content-view-fields.js');
const nodes = node => Array.isArray(node) ? node.flatMap(nodes) : node?.props ? [node, ...nodes(node.props.children)] : [];
const config = { labels: {}, widgetAreas: [['rdct-blog-archive', 'Blog']], widgetsUrl: '/wp-admin/widgets.php', contentModules: {}, contentLinks: { items: {}, archives: {} } };

test('sidebar controls use shared fields and preserve an unavailable area without changing settings', () => {
  const values = { blog_archive_sidebar_enabled: false, blog_archive_sidebar_area: 'retired-area' };
  const changes = [];
  let tree = SidebarFields({ scope: 'blog_archive', config, values, change: (...args) => changes.push(args) });
  const toggle = nodes(tree).find(node => node.props.name === 'blog_archive_sidebar_enabled');
  assert.equal(toggle.props.value, false);
  assert.equal(nodes(tree).some(node => node.props.name === 'blog_archive_sidebar_width'), false);
  toggle.props.onChange(toggle.props.name, true); assert.deepEqual(changes, [['blog_archive_sidebar_enabled', true]]);
  tree = SidebarFields({ scope: 'blog_archive', config, values: { ...values, blog_archive_sidebar_enabled: true }, change() {} });
  const area = nodes(tree).find(node => node.props.name === 'blog_archive_sidebar_area');
  assert.equal(area.props.value, 'retired-area'); assert.ok(area.props.options.some(([id]) => id === 'retired-area'));
  assert.equal(config.widgetAreas.length, 1);
  assert.ok(nodes(tree).some(node => node.type === 'a' && node.props.href === config.widgetsUrl));
});

test('each selected content view exposes only its independent sidebar settings', () => {
  for (const content of ['blog', 'catalog', 'portfolio']) {
    for (const view of ['archive', 'single']) {
      const scope = `${content}_${view}`;
      const selected = ContentViewFields({ scope, config, values: {}, change() {} });
      assert.deepEqual(nodes(selected).filter(node => node.type === SidebarFields).map(node => node.props.scope), [scope]);
    }
  }
});
