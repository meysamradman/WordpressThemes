<?php

namespace RadmanCT\Renderers {
	function context() { return $GLOBALS['context']; }
	function uses_elementor() { return $GLOBALS['pro']; }
}
namespace {
	define( 'ABSPATH', '/' );
	$saved = array(); $context = array(); $pro = false; $page = true; $template = ''; $plugin = false; $built = ''; $opt_in = false;
	$page_layout = array(); $layout_writes = 0; $revision = false; $autosave = false; $layout_caps = array( 'edit_theme_options' => true, 'edit_post' => true );
	function get_option( $key, $default = false ) { return $GLOBALS['saved']; }
	function apply_filters( $hook, $value ) {
		if ( 'radmanct/layout/custom_templates' === $hook ) { return \RadmanCTChild\custom_templates( $value ); }
		return 'radmanct/layout/manage_custom_template' === $hook ? $GLOBALS['opt_in'] : $value;
	}
	function add_action( ...$args ) {}
	function add_filter( ...$args ) {}
	function add_settings_error( ...$args ) {}
	function __( $text, $domain ) { return $text; }
	function sanitize_text_field( $text ) { return strip_tags( $text ); }
	function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
	function is_page() { return $GLOBALS['page']; }
	function get_page_template_slug() { return $GLOBALS['template']; }
	function did_action( $hook ) { return $GLOBALS['plugin']; }
	function get_post_meta( $id, $key, $single = true ) { return '_rdct_page_layout' === $key ? $GLOBALS['page_layout'] : $GLOBALS['built']; }
	function update_post_meta( $id, $key, $value ) { ++$GLOBALS['layout_writes']; $GLOBALS['page_layout'] = $value; }
	function delete_post_meta( $id, $key ) { ++$GLOBALS['layout_writes']; $GLOBALS['page_layout'] = array(); }
	function wp_is_post_revision( $id ) { return $GLOBALS['revision']; }
	function wp_is_post_autosave( $id ) { return $GLOBALS['autosave']; }
	function wp_verify_nonce( $nonce, $action ) { return 'nonce-' . $action === $nonce; }
	function current_user_can( $capability, ...$args ) { return $GLOBALS['layout_caps'][ $capability ] ?? false; }
	function get_post_type( $id ) { return 'page'; }
	function wp_unslash( $value ) { return $value; }
	function get_queried_object_id() { return 10; }
	function expect( $condition, $message ) { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	function is_admin() { return false; }
	require __DIR__ . '/../radmanct/inc/settings.php';
	require __DIR__ . '/../radmanct/inc/layout.php';
	require __DIR__ . '/../radmanct-child/functions.php';
	require __DIR__ . '/../radmanct/inc/builder/layout.php';
	$base = \RadmanCT\Settings\values();
	expect( 'contained' === $base['content_layout'] && null === $base['content_gutter'], 'Legacy layout defaults changed.' );
	$saved = \RadmanCT\Settings\sanitize( array( 'content_layout' => 'custom', 'content_width' => '1440', 'content_gutter' => '30', 'content_top' => '0', 'content_mobile_gutter' => '0', 'blog_single_layout' => 'custom', 'blog_single_width' => '1000', 'blog_single_bottom' => '12' ) );
	$blog = \RadmanCT\Layout\resolve( $saved, 'blog_single' );
	expect( 1000 === $blog['width'] && 30 === $blog['gutter'] && 0 === $blog['top'] && 12 === $blog['bottom'] && 0 === $blog['mobile_gutter'], 'Scope inheritance or explicit zero lost.' );
	$catalog = \RadmanCT\Layout\resolve( $saved, 'catalog_archive' );
	expect( 1440 === $catalog['width'] && 'custom' === $catalog['layout'], 'Inherited scope changed global settings.' );
	$tablet = \RadmanCT\Settings\sanitize( array( 'content_tablet_gutter' => '24', 'content_tablet_top' => '0', 'blog_single_tablet_bottom' => '16' ) );
	$tablet_blog = \RadmanCT\Layout\resolve( $tablet, 'blog_single' );
	expect( 24 === $tablet_blog['tablet_gutter'] && 0 === $tablet_blog['tablet_top'] && 16 === $tablet_blog['tablet_bottom'], 'Tablet scope inheritance or zero lost.' );
	$tablet_css = \RadmanCT\Layout\variables( $tablet_blog );
	expect( str_contains( $tablet_css, '--rdct-content-tablet-top:0px;' ) && str_contains( $tablet_css, '--rdct-content-mobile-top:var(--rdct-content-top);' ), 'Tablet values leaked into mobile inheritance.' );
	$tablet_invalid = \RadmanCT\Settings\sanitize( array( 'content_tablet_gutter' => 161, 'content_tablet_top' => '-1' ) );
	expect( $tablet_invalid === $saved, 'Invalid tablet values changed saved settings.' );
	$tablet_reset = \RadmanCT\Settings\sanitize( array( 'content_tablet_gutter' => '' ) );
	expect( null === $tablet_reset['content_tablet_gutter'], 'Tablet reset did not restore inheritance.' );
	$invalid = \RadmanCT\Settings\sanitize( array( 'content_layout' => 'url(bad)', 'content_width' => '999999', 'reading_width' => array(), 'content_top' => '-1' ) );
	expect( $invalid === $saved, 'Invalid layout input replaced saved data.' );
	$reset = \RadmanCT\Settings\sanitize( array( 'content_gutter' => '' ) );
	expect( null === $reset['content_gutter'], 'Empty spacing did not restore inheritance.' );
	$context = array( 'blog', 'single' );
	$attrs = \RadmanCT\Layout\content_attributes();
	expect( str_contains( $attrs, '--rdct-content-max:1000px' ) && str_contains( $attrs, '--rdct-content-top:0px' ), 'Context-specific main layout missing.' );
	$pro = true; expect( 'class="rdct-lyt-external"' === \RadmanCT\Layout\content_attributes(), 'Pro output constrained by PHP content layout.' );
	$pro = false; $template = 'templates/elementor.php';
	expect( 'class="rdct-lyt-external"' === \RadmanCT\Layout\content_attributes(), 'Elementor template constrained.' );
	$template = ''; $plugin = true; $built = 'builder';
	expect( \RadmanCT\Layout\elementor_content(), 'Elementor content in default page template missed.' );
	$plugin = false; $template = 'client.php';
	expect( '' === \RadmanCT\Layout\content_attributes(), 'Custom template forced into shared spacing.' );
	expect( str_contains( \RadmanCT\Layout\content_attributes( 'content' ), '1440px' ), 'Explicit custom-page helper failed.' );
	$data = new class { public $update; public function update_with( $value ) { $this->update = $value; } };
	\RadmanCT\Layout\theme_json( $data );
	expect( '760px' === $data->update['settings']['layout']['contentSize'] && '1440px' === $data->update['settings']['layout']['wideSize'], 'Gutenberg wide size did not follow the custom content width.' );
	expect( str_contains( $attrs, '--rdct-content-wide:1000px' ), 'Scoped wide blocks did not follow the resolved content width.' );
	$saved = \RadmanCT\Settings\sanitize( array( 'content_width' => 400 ) );
	\RadmanCT\Layout\theme_json( $data );
	expect( '400px' === $data->update['settings']['layout']['contentSize'] && '400px' === $data->update['settings']['layout']['wideSize'], 'Reading size exceeded a narrow custom canvas.' );
	$saved = $reset;
	$template = 'templates/full-width.php'; $context = array();
	$full = \RadmanCT\Layout\content_attributes();
	expect( str_contains( $full, 'data-rdct-layout="full"' ) && str_contains( $full, '--rdct-content-max:none' ) && str_contains( $full, '--rdct-content-top:0px' ), 'Full-width template lost shared spacing or retained a boxed width.' );
	$plugin = true; $built = 'builder';
	expect( 'class="rdct-lyt-external"' === \RadmanCT\Layout\content_attributes(), 'Full-width template constrained Elementor content.' );
	$plugin = false; $built = ''; $template = '';
	$dimensions = \RadmanCT\Builder\Layout\validate( array( 'customWidth' => 1500, 'gutter' => 0, 'mobileGutter' => null ) );
	$device_dimensions = \RadmanCT\Builder\Layout\validate( array( 'laptopGutter' => 0, 'tabletGutter' => 12 ) );
	$device_css = \RadmanCT\Builder\Layout\attributes( array( 'width' => 'full' ) + $device_dimensions );
	expect( str_contains( $device_css, '--builder-laptop-gutter:0px;' ) && str_contains( $device_css, '--builder-tablet-gutter:12px;' ), 'Four-device gutters missing or zero lost.' );
	foreach ( array( -1, 161, '12', array() ) as $bad_gutter ) {
		expect( null === \RadmanCT\Builder\Layout\validate( array( 'tabletGutter' => $bad_gutter ) ), 'Invalid responsive gutter accepted.' );
	}
	expect( null === \RadmanCT\Builder\Layout\validate( array() )['laptopGutter'], 'Legacy gutter inheritance changed.' );
	expect( 0 === $dimensions['gutter'] && null === \RadmanCT\Builder\Layout\validate( array( 'gutter' => '0' ) ), 'Builder JSON dimensions lost strict typing.' );
	$css = \RadmanCT\Builder\Layout\attributes( array( 'width' => 'custom' ) + $dimensions );
	expect( str_contains( $css, '--builder-content-max:1500px' ) && str_contains( $css, '--builder-gutter:0px' ), 'Independent builder width or zero gutter missing.' );
	expect( str_contains( \RadmanCT\Builder\Layout\attributes( array( 'width' => 'contained' ) ), '--builder-content-max:var(--rdct-container)' ), 'Header/footer width did not inherit the live theme token.' );
	expect( str_contains( \RadmanCT\Builder\Layout\attributes( array( 'width' => 'full' ) ), '--builder-content-max:none' ), 'Full-width header/footer retained a fixed maximum.' );
	$template = 'templates/custom.php'; $plugin = true; $built = 'builder'; $pro = false;
	expect( ! \RadmanCT\Layout\elementor_content() && str_contains( \RadmanCT\Layout\content_attributes(), 'rdct-lyt-content' ), 'Explicit custom page lost its native layout.' );
	$template = 'templates/custom-page.php';
	expect( ! \RadmanCT\Layout\elementor_content() && str_contains( \RadmanCT\Layout\content_attributes(), 'rdct-lyt-content' ), 'Registered child page lost native ownership or shared layout.' );
	$template = 'templates/elementor.php';
	expect( \RadmanCT\Layout\elementor_content() && ! \RadmanCT\Layout\custom_template(), 'Child registration captured the Elementor template.' );
	require __DIR__ . '/../radmanct/inc/admin/page-layout.php';
	$input = array( 'layout' => 'custom', 'width' => '980', 'gutter' => '0', 'tablet_top' => '18', 'mobile_gutter' => '8', 'unsafe' => 'display:none' );
	$page_layout = \RadmanCT\Layout\Page\normalize( $input );
	expect( 0 === $page_layout['gutter'] && ! isset( $page_layout['unsafe'] ), 'Page layout lost zero or accepted unknown keys.' );
	$template = 'templates/custom.php'; $context = array();
	$page_attrs = \RadmanCT\Layout\content_attributes();
	expect( str_contains( $page_attrs, '--rdct-content-max:980px;' ) && str_contains( $page_attrs, '--rdct-content-gutter:0px;' ) && str_contains( $page_attrs, '--rdct-content-tablet-top:18px;' ), 'Page overrides did not reach the shared wrapper.' );
	$template = 'templates/full-width.php'; $plugin = false;
	expect( str_contains( \RadmanCT\Layout\content_attributes(), 'data-rdct-layout="custom"' ), 'Explicit page layout did not override the full-width template default.' );
	$template = 'templates/elementor.php';
	expect( 'class="rdct-lyt-external"' === \RadmanCT\Layout\content_attributes(), 'Page overrides constrained Elementor.' );
	$template = ''; $context = array( 'blog', 'single' ); $page = false;
	expect( ! str_contains( \RadmanCT\Layout\content_attributes(), '--rdct-content-max:980px;' ), 'Page layout leaked to a different content type.' );
	$page = true; $context = array();
	$_POST = array( 'rdct_page_layout_nonce' => 'nonce-rdct_page_layout_10', 'rdct_page_layout' => array( 'width' => 'bad', 'tablet_top' => '', 'mobile_bottom' => '0' ) );
	\RadmanCT\Admin\PageLayout\save( 10 );
	expect( 980 === $page_layout['width'] && ! isset( $page_layout['tablet_top'] ) && 0 === $page_layout['mobile_bottom'], 'Page save failed to preserve invalid values or reset a field.' );
	$writes = $layout_writes;
	$revision = true; \RadmanCT\Admin\PageLayout\save( 10 ); $revision = false;
	$autosave = true; \RadmanCT\Admin\PageLayout\save( 10 ); $autosave = false;
	$layout_caps['edit_theme_options'] = false; \RadmanCT\Admin\PageLayout\save( 10 ); $layout_caps['edit_theme_options'] = true;
	$layout_caps['edit_post'] = false; \RadmanCT\Admin\PageLayout\save( 10 ); $layout_caps['edit_post'] = true;
	$_POST['rdct_page_layout_nonce'] = 'nonce-rdct_page_layout_11'; \RadmanCT\Admin\PageLayout\save( 10 );
	expect( $writes === $layout_writes, 'Autosave/revision, permission or page-specific nonce guard failed.' );
	$page_layout['layout'] = 'inherit';
	expect( ! str_contains( \RadmanCT\Layout\content_attributes(), '--rdct-content-max:980px;' ), 'Global mode applied inactive overrides.' );
	$_POST = array( 'rdct_page_layout_nonce' => 'nonce-rdct_page_layout_10', 'rdct_page_layout' => array_fill_keys( array_keys( $page_layout ), '' ) );
	$_POST['rdct_page_layout']['layout'] = 'inherit'; \RadmanCT\Admin\PageLayout\save( 10 );
	expect( array() === $page_layout, 'Reset page layout left redundant metadata.' );
	echo "Passed: per-page inheritance, responsive spacing, Elementor isolation, safe saves and nonce/capability/autosave guards.\n";
	echo "Passed: layout inheritance, zero/reset, safe values, independent builder dimensions, parent/child Elementor isolation and Gutenberg settings.\n";
}
