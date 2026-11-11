import test from 'node:test';
import assert from 'node:assert/strict';
import { resetSettings } from '../radmanct/src/js/admin/settings-reset.js';

test('primary font reset follows a Latin primary site and preserves complementary settings', () => {
  const defaults = { latin_font: 'default-latin', persian_font: 'default-arabic', h2_latin_size: '', h2_persian_size: '', mixed_language_fonts: false };
  const current = { latin_font: 'custom-latin', persian_font: 'custom-arabic', h2_latin_size: '48', h2_persian_size: '52', mixed_language_fonts: true };
  const reset = resetSettings(current, defaults, 'typography_primary', [], false, 'latin');
  assert.equal(reset.latin_font, 'default-latin');
  assert.equal(reset.h2_latin_size, '');
  assert.equal(reset.persian_font, 'custom-arabic');
  assert.equal(reset.h2_persian_size, '52');
  assert.equal(reset.mixed_language_fonts, true);
});

test('colors and content layout reset independently after panel reorganization', () => {
  const defaults = { brand_color: '#123456', blog_archive_color_background: '', portfolio_single_color_background: '', blog_archive_sidebar_enabled: false, blog_archive_renderer: 'custom' };
  const current = { brand_color: '#abcdef', blog_archive_color_background: '#ffffff', portfolio_single_color_background: '#000000', blog_archive_sidebar_enabled: true, blog_archive_renderer: 'elementor' };
  assert.deepEqual(resetSettings(current, defaults, 'design'), { ...current, brand_color: defaults.brand_color });
  assert.deepEqual(resetSettings(current, defaults, 'blog_archive'), { ...current, blog_archive_color_background: '', blog_archive_sidebar_enabled: false, blog_archive_renderer: 'custom' });
});

test('typography reset includes both languages and devices while preserving cursor dimensions', () => {
  const defaults = { persian_font: 'local', h1_persian_size: '', h1_persian_tablet_size: '', h1_latin_mobile_size: '', h2_latin_weight: '', body_latin_line_height: '', cursor_inner_size: 7, brand_color: '#123456' };
  const current = { ...defaults, h1_persian_size: '64', h1_persian_tablet_size: '48', h1_latin_mobile_size: '32', h2_latin_weight: '700', body_latin_line_height: '1.8', cursor_inner_size: 12, brand_color: '#abcdef' };
  const reset = resetSettings(current, defaults, 'typography');
  assert.deepEqual(reset, { ...defaults, cursor_inner_size: 12, brand_color: '#abcdef' });
  assert.equal(current.h1_persian_size, '64');
});

test('reset all respects custom-code permissions and preserves keys outside the defaults registry', () => {
  const defaults = { brand_color: '#123456', custom_css: '', custom_css_mobile: '', custom_js: '' };
  const current = { brand_color: '#abcdef', custom_css: 'body{}', custom_css_mobile: 'a{}', custom_js: 'init()', extensionValue: 'keep' };
  assert.deepEqual(resetSettings(current, defaults, null), { ...current, brand_color: '#123456' });
  assert.deepEqual(resetSettings(current, defaults, null, [], true), { ...defaults, extensionValue: 'keep' });
});

test('section reset stays within its settings owner', () => {
  const defaults = { content_layout: 'contained', content_mobile_top: null, container_width: 1200, reading_width: 760, blog_single_renderer: 'custom', blog_single_gutter: null, analytics_enabled: false };
  const current = { content_layout: 'full', content_mobile_top: 24, container_width: 1400, reading_width: 800, blog_single_renderer: 'elementor', blog_single_gutter: 20, analytics_enabled: true };
  const layout = resetSettings(current, defaults, 'layout');
  assert.equal(layout.content_mobile_top, null);
  assert.equal(layout.blog_single_gutter, 20);
  assert.equal(layout.blog_single_renderer, 'elementor');
  assert.equal(resetSettings(current, defaults, 'blog_single').blog_single_gutter, null);
  assert.equal(layout.analytics_enabled, true);
  const analytics = resetSettings(current, defaults, 'analytics', ['analytics_enabled']);
  assert.deepEqual(analytics, { ...current, analytics_enabled: false });
});
