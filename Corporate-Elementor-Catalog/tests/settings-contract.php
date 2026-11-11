<?php

define( 'ABSPATH', '/' );
$saved = array( 'persian_font' => 'peyda', 'latin_font' => 'gt-walsheim', 'brand_color' => '#123456', 'container_width' => 1280, 'scroll_reveal' => false, 'smooth_scroll' => false );
$errors = array();

function get_option( $key, $default = false ) { return $GLOBALS['saved']; }
function add_settings_error( $setting, $code, $message ) { $GLOBALS['errors'][] = $code; }
function __( $message, $domain ) { return $message; }
function sanitize_text_field( $value ) { return strip_tags( $value ); }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function apply_filters( $hook, $value ) { return 'wpml_current_language' === $hook ? ( $GLOBALS['font_language_context'] ?? $value ) : $value; }
function current_user_can( $capability ) { return $GLOBALS['settings_caps'][ $capability ] ?? false; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

require dirname( __DIR__ ) . '/radmanct/inc/settings.php';
require dirname( __DIR__ ) . '/radmanct/inc/admin/settings-page.php';
$saved = array_replace( \RadmanCT\Settings\defaults(), $saved );

$scrollbar = \RadmanCT\Settings\sanitize( array( 'scrollbar_width' => '12', 'scrollbar_thumb' => '#AB1234', 'scrollbar_track' => '#F1F2F3' ) );
expect( 12 === $scrollbar['scrollbar_width'] && '#ab1234' === $scrollbar['scrollbar_thumb'], 'Scrollbar settings did not normalize.' );
$scrollbar_css = \RadmanCT\Settings\scrollbar_css( $scrollbar );
expect( str_contains( $scrollbar_css, 'width:12px' ) && str_contains( $scrollbar_css, 'background:#ab1234' ) && str_contains( $scrollbar_css, 'html:has(>body.rdct-site)' ), 'Scrollbar CSS lost its saved values or public-page scope.' );
foreach ( array( 0, 21, array(), '8px', '12;display:none' ) as $bad_width ) {
	expect( 8 === \RadmanCT\Settings\sanitize( array( 'scrollbar_width' => $bad_width ) )['scrollbar_width'], 'Invalid scrollbar width replaced its fallback.' );
}
expect( '#888888' === \RadmanCT\Settings\sanitize( array( 'scrollbar_thumb' => '#fff;background:red' ) )['scrollbar_thumb'], 'Unsafe scrollbar color reached CSS.' );
expect( '' === \RadmanCT\Settings\scrollbar_css( array_replace( $scrollbar, array( 'custom_scrollbar' => false ) ) ), 'Native scrollbar mode emitted custom styles.' );
$errors = array();

$invalid = \RadmanCT\Settings\sanitize( array( 'persian_font' => array(), 'latin_font' => 'https://example.invalid/font', 'brand_color' => '#fff;display:none', 'container_width' => '999999' ) );
expect( $invalid === $saved && count( $errors ) === 1, 'Invalid values did not preserve saved settings.' );
$valid = \RadmanCT\Settings\sanitize( array( 'brand_color' => '#AB12EF', 'container_width' => '1400', 'unknown' => '<script>bad</script>' ) );
expect( $valid['brand_color'] === '#ab12ef' && $valid['container_width'] === 1400 && ! isset( $valid['unknown'] ), 'Settings normalization failed.' );
expect( $valid['persian_font'] === 'peyda', 'Omitted fields lost their saved values.' );

$saved['catalog_single_color_background'] = '#123456';
$colors = \RadmanCT\Settings\sanitize( array( 'catalog_single_color_background' => 'red;background:url(//remote)', 'blog_archive_color_background' => '#ABCDEF' ) );
expect( '#123456' === $colors['catalog_single_color_background'] && '#abcdef' === $colors['blog_archive_color_background'], 'Content color validation lost previous values or independent scopes.' );
$colors = \RadmanCT\Settings\sanitize( array( 'catalog_single_color_background' => '' ) );
expect( '' === $colors['catalog_single_color_background'], 'Empty content color did not restore theme inheritance.' );
$smoothing = \RadmanCT\Settings\sanitize( array( 'smooth_duration' => '1.4' ) );
expect( 1.4 === $smoothing['smooth_duration'], 'Smoothing duration did not normalize.' );
foreach ( array( 0, 4, 'NaN', array() ) as $invalid_duration ) {
	expect( 0.8 === \RadmanCT\Settings\sanitize( array( 'smooth_duration' => $invalid_duration ) )['smooth_duration'], 'Invalid smoothing duration replaced its fallback.' );
}
$secondary = \RadmanCT\Settings\sanitize( array( 'preloader_background_secondary' => '#AABBCC' ) );
expect( '#aabbcc' === $secondary['preloader_background_secondary'], 'Curtain back color did not normalize.' );
$secondary = \RadmanCT\Settings\sanitize( array( 'preloader_background_secondary' => '' ) );
expect( '' === $secondary['preloader_background_secondary'], 'Empty curtain back color did not restore inheritance.' );
foreach ( \RadmanCT\Fonts\choices( 'persian' ) as $font ) {
	$fonts = \RadmanCT\Settings\sanitize( array( 'persian_font' => $font[0], 'latin_font' => 'gt-walsheim' ) );
	expect( $fonts['persian_font'] === $font[0] && $fonts['latin_font'] === 'gt-walsheim', 'Registered font selection rejected.' );
}
$wrong_group = \RadmanCT\Settings\sanitize( array( 'persian_font' => 'gt-walsheim', 'latin_font' => 'pinar' ) );
expect( $wrong_group['persian_font'] === $saved['persian_font'] && $wrong_group['latin_font'] === $saved['latin_font'], 'Font groups were not isolated.' );
$toggles = \RadmanCT\Settings\sanitize( array( 'scroll_reveal' => '1', 'smooth_scroll' => '0' ) );
expect( $toggles['scroll_reveal'] === true && $toggles['smooth_scroll'] === false, 'Motion switch values were not normalized.' );
$extras = \RadmanCT\Settings\sanitize( array( 'custom_cursor' => '1', 'cursor_inner_size' => '12', 'cursor_outer_size' => '60', 'scroll_progress_height' => '5', 'preloader_style' => 'curtain', 'cursor_image_label' => '<b>View</b>' ) );
expect( $extras['custom_cursor'] && $extras['cursor_inner_size'] === 12 && $extras['cursor_outer_size'] === 60 && $extras['scroll_progress_height'] === 5 && $extras['preloader_style'] === 'curtain' && $extras['cursor_image_label'] === 'View', 'Visual effect settings did not normalize.' );
$unsafe = \RadmanCT\Settings\sanitize( array( 'cursor_color' => 'url(https://example.invalid)', 'cursor_outer_size' => '10000', 'scroll_progress_height' => '999', 'preloader_style' => '<script>', 'preloader_background' => 'transparent;display:none' ) );
expect( $unsafe === $saved, 'Unsafe effect settings replaced saved values.' );
expect( \RadmanCT\Settings\sanitize( 'invalid' ) === $saved, 'Malformed submission overwrote settings.' );
$saved = array( 'brand_color' => 'url(https://example.invalid/)', 'container_width' => array(), 'persian_font' => array() );
expect( \RadmanCT\Settings\values() === \RadmanCT\Settings\defaults(), 'Invalid stored data reached the frontend.' );
$tokens = \RadmanCT\Settings\tokens();
expect( ! str_contains( $tokens, 'url(' ) && str_contains( $tokens, '1200px' ), 'Unsafe CSS tokens.' );
ob_start();
\RadmanCT\Admin\SettingsPage\render();
expect( ob_get_clean() === '', 'Unauthorized settings page rendered.' );
\RadmanCT\Admin\SettingsPage\enqueue( 'toplevel_page_rdct-settings' );
\RadmanCT\Admin\SettingsPage\enqueue( 'dashboard' );
$attributes = \RadmanCT\Admin\SettingsPage\script_attributes( array( 'id' => 'rdct-admin-settings-js', 'nonce' => 'test-nonce' ) );
expect( $attributes['type'] === 'module' && $attributes['nonce'] === 'test-nonce', 'Script attributes were not preserved.' );
expect( \RadmanCT\Admin\SettingsPage\script_attributes( array( 'id' => 'other-js' ) ) === array( 'id' => 'other-js' ), 'Unrelated script was modified.' );

$sidebar_settings = \RadmanCT\Settings\sanitize( array( 'catalog_single_sidebar_enabled' => '1', 'catalog_single_sidebar_area' => 'rdct-blog-single', 'catalog_single_sidebar_position' => 'start', 'catalog_single_sidebar_width' => '360' ) );
expect( true === $sidebar_settings['catalog_single_sidebar_enabled'] && 'rdct-blog-single' === $sidebar_settings['catalog_single_sidebar_area'] && 360 === $sidebar_settings['catalog_single_sidebar_width'], 'Sidebar fields were not accepted by the shared settings registry.' );
$sidebar_bad = \RadmanCT\Settings\sanitize( array( 'blog_archive_sidebar_enabled' => array(), 'blog_archive_sidebar_area' => '../custom.php', 'blog_archive_sidebar_position' => 'fixed', 'blog_archive_sidebar_width' => '320px' ) );
expect( false === $sidebar_bad['blog_archive_sidebar_enabled'] && 'rdct-blog-archive' === $sidebar_bad['blog_archive_sidebar_area'] && 'end' === $sidebar_bad['blog_archive_sidebar_position'] && 320 === $sidebar_bad['blog_archive_sidebar_width'], 'Invalid sidebar settings replaced safe previous values.' );

echo "Passed: settings validation, preserved values, safe tokens and unauthorized page guard.\n";
