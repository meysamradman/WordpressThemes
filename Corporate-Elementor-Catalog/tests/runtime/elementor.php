<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
wp_set_current_user( 1 );
function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }

if ( 'activate' === ( $argv[1] ?? '' ) ) {
	foreach ( array( 'elementor/elementor.php', 'elementor-pro/elementor-pro.php' ) as $plugin ) {
		$result = activate_plugin( $plugin );
		expect( ! is_wp_error( $result ), is_wp_error( $result ) ? $result->get_error_message() : '' );
	}
	echo "Activated supplied Elementor and Pro in isolated database.\n";
	exit;
}

$with_elementor = did_action( 'elementor/loaded' ) > 0;
if ( 'renderer-create' === ( $argv[1] ?? '' ) ) {
	$document = \Elementor\Plugin::$instance->documents->create( 'single-post', array( 'post_title' => 'QA single template', 'post_status' => 'publish' ) );
	expect( (bool) $document, 'Pro single template could not be created.' );
	$document->save( array( 'elements' => array( array( 'id' => 'qasingle', 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => array( 'title' => 'Radman CT Pro design QA' ), 'elements' => array() ) ) ) );
	\ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->save_conditions( $document->get_main_id(), array( array( 'type' => 'include', 'name' => 'singular', 'sub_name' => 'post', 'sub_id' => '' ) ) );
	$post_id = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Native blog post', 'post_content' => 'Native blog body' ) );
	update_option( 'qa_renderer_post', $post_id );
	update_option( 'radman_settings', array( 'blog_single_renderer' => 'elementor' ) );
	update_option( 'radman_builder_header', array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'qaRow', 'items' => array( array( 'id' => 'qaText', 'type' => 'text', 'text' => 'QA shared header' ) ) ) ) ), false );
	set_theme_mod( 'radman_builder_header', true );
	echo "Created actual Pro single template and matching display condition.\n";
	exit;
}
if ( 'renderer' === ( $argv[1] ?? '' ) ) {
	$post_id = (int) get_option( 'qa_renderer_post' );
	$query = new WP_Query();
	$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'] = $query;
	$query->query( array( 'p' => $post_id, 'post_type' => 'post' ) );
	$GLOBALS['post'] = get_post( $post_id );
	setup_postdata( $GLOBALS['post'] );
	$manager = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager();
	$manager->register_locations();
	expect( \RadmanCT\Renderers\uses_elementor(), 'Matching published Pro single was not detected.' );
	ob_start();
	expect( \RadmanCT\Renderers\render(), 'Actual Pro location failed to render.' );
	$html = ob_get_clean();
	expect( str_contains( $html, 'Radman CT Pro design QA' ), 'Pro design content is missing.' );
	ob_start();
	\RadmanCT\Builder\render( 'header' );
	expect( str_contains( ob_get_clean(), 'QA shared header' ), 'Pro single replaced Radman CT header.' );
	update_option( 'radman_settings', array( 'blog_single_renderer' => 'custom' ) );
	expect( ! \RadmanCT\Renderers\uses_elementor(), 'Custom choice still selected Pro.' );
	echo "Passed: actual Pro single location, matching condition, PHP header ownership and independent custom selection.\n";
	exit;
}
$layout = array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'qaRow', 'appearance' => array( 'gap' => 20, 'background' => '#123456' ), 'items' => array( array( 'id' => 'qaText', 'type' => 'text', 'text' => 'QA shared header' ) ) ) ) );
update_option( 'radman_builder_header', $layout, false );
set_theme_mod( 'radman_builder_header', true );
ob_start();
expect( \RadmanCT\Builder\render( 'header' ), 'Actual WordPress builder did not render.' );
$header = ob_get_clean();
expect( str_contains( $header, 'QA shared header' ) && str_contains( $header, '--builder-gap:20px' ), 'Actual builder content or appearance missing.' );
if ( ! $with_elementor ) {
	expect( ! function_exists( '\\RadmanCT\\Integrations\\Elementor\\enqueue' ), 'Elementor integration loaded without plugin.' );
	echo "Passed: WordPress theme and styled header render without Elementor.\n";
	exit;
}

expect( false === apply_filters( 'elementor/frontend/print_google_fonts', true ), 'Google font enqueue was not blocked.' );
$hints = apply_filters( 'wp_resource_hints', array( '//fonts.googleapis.com', array( 'href' => 'https://fonts.gstatic.com' ), '//example.invalid' ), 'preconnect' );
expect( count( $hints ) === 1, 'Font preconnect hints were not scoped.' );
$templates = wp_get_theme()->get_page_templates();
expect( isset( $templates['templates/elementor.php'] ), 'Elementor page template missing.' );
$module = \ElementorPro\Modules\AssetsManager\Module::instance();
$manager = $module->get_assets_manager( 'font' );
expect( false === has_action( 'elementor/css-file/post/parse', array( $manager, 'enqueue_fonts' ) ), 'Pro custom font injection remains attached.' );

$document = \Elementor\Plugin::$instance->documents->create( 'wp-page', array( 'post_title' => 'QA Elementor page', 'post_status' => 'publish' ) );
expect( (bool) $document, 'Elementor document creation failed.' );
$document->save( array( 'elements' => array( array( 'id' => 'qahead1', 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => array( 'title' => 'Radman CT Elementor QA', 'typography_typography' => 'custom', 'typography_font_family' => 'Roboto' ), 'elements' => array() ) ) ) );
update_post_meta( $document->get_main_id(), '_wp_page_template', 'templates/elementor.php' );
$GLOBALS['post'] = get_post( $document->get_main_id() );
setup_postdata( $GLOBALS['post'] );
ob_start();
$content = \Elementor\Plugin::$instance->frontend->get_builder_content( $document->get_main_id(), true );
$extra = ob_get_clean();
expect( str_contains( $content, 'Radman CT Elementor QA' ), 'Elementor heading did not render.' );
expect( ! str_contains( $extra . $content, 'fonts.googleapis.com' ), 'Rendered page includes Google font stylesheet.' );
$local_fonts = apply_filters( 'elementor/fonts/additional_fonts', array( 'Other Font' => 'custom' ) );
expect( isset( $local_fonts['Radman CT IRANSansX'] ) && ! isset( $local_fonts['Other Font'] ), 'Elementor font source did not follow theme.' );
global $wp_version;
echo 'Passed: WordPress ' . $wp_version . ', PHP ' . PHP_VERSION . ', Elementor ' . ELEMENTOR_VERSION . ', Pro ' . ELEMENTOR_PRO_VERSION . "; document save/render, theme header, local font hooks and optional template.\n";
