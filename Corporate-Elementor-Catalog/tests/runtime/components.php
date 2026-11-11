<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['SERVER_NAME'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
wp_set_current_user( 1 );
function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }

$with_elementor = did_action( 'elementor/loaded' ) > 0;
wp_enqueue_scripts();
expect( ! wp_style_is( 'rdct-icons', 'enqueued' ) && ! wp_style_is( 'rdct-text-reveal', 'enqueued' ), 'Unused component assets were enqueued.' );
expect( wp_style_is( 'rdct-icons', 'registered' ) && wp_style_is( 'rdct-text-reveal', 'registered' ), 'Component styles not registered.' );
expect( wp_script_is( 'rdct-elementor-components', 'registered' ) && ! wp_script_is( 'rdct-elementor-components', 'enqueued' ), 'Elementor bridge was loaded globally.' );
$font_css = implode( '', wp_styles()->get_data( 'rdct-theme', 'after' ) ?: array() );
expect( str_contains( $font_css, '/fonts/persian/IRANSans/IRANSansXV.woff2' ) && str_contains( $font_css, 'font-display:swap' ) && ! str_contains( $font_css, 'Pesteh' ), 'Head font declarations not scoped to selected families.' );
$preloads = apply_filters( 'wp_preload_resources', array() );
expect( ! array_filter( $preloads, static fn( $item ) => is_array( $item ) && 'font' === ( $item['as'] ?? '' ) ), 'Unrequested font preloads added.' );
add_filter( 'radmanct/typography/preload', static fn() => array( 'persian' ) );
$preloads = apply_filters( 'wp_preload_resources', array() );
expect( count( $preloads ) === 1 && 'font/woff2' === $preloads[0]['type'] && 'anonymous' === $preloads[0]['crossorigin'], 'Critical font preload contract failed.' );
rdct_use( 'text-reveal' );
ob_start(); wp_head(); $head = ob_get_clean();
expect( str_contains( $head, 'rdct-text-reveal-css' ) && ! str_contains( $head, 'rdct-icons-css' ), 'Head component asset scope failed.' );
$text = "\u{0645}\u{06CC}\u{200C}\u{062E}\u{0648}\u{0627}\u{0647}\u{0645} English 123";
ob_start(); rdct_text_reveal( $text ); $native = ob_get_clean();
expect( str_contains( $native, $text ) && str_contains( $native, 'data-rdct-text-reveal="scroll"' ), 'Native text renderer failed.' );
ob_start(); rdct_icon( 'phone', array( 'label' => 'Call office' ) ); rdct_icon( 'arrow-right' ); $icons = ob_get_clean();
expect( substr_count( $icons, "id='rdct-icons-css'" ) === 1 && substr_count( $icons, '<svg ' ) === 2 && str_contains( $icons, 'aria-label="Call office"' ), 'Late icon assets or markup duplicated.' );
if ( $with_elementor ) {
	$plugin = \Elementor\Plugin::$instance;
	$widgets = $plugin->widgets_manager->get_widget_types();
	expect( isset( $widgets['rdct-icon'], $widgets['rdct-text-reveal'] ), 'Actual Elementor widgets not registered.' );
	expect( array( 'rdct-text-reveal' ) === $widgets['rdct-text-reveal']->get_style_depends(), 'Text stylesheet dependency mismatch.' );
	expect( array( 'rdct-elementor-components' ) === $widgets['rdct-text-reveal']->get_script_depends() && array() === $widgets['rdct-icon']->get_script_depends(), 'Widget scripts crossed boundaries.' );
	$document = $plugin->documents->create( 'wp-page', array( 'post_title' => 'Radman CT components', 'post_status' => 'publish' ) );
	expect( (bool) $document, 'Actual Elementor document creation failed.' );
	$document->save( array( 'elements' => array(
		array( 'id' => 'qatxt', 'elType' => 'widget', 'widgetType' => 'rdct-text-reveal', 'settings' => array( 'text' => $text, 'direction' => 'rtl', 'mode' => 'scroll' ), 'elements' => array() ),
		array( 'id' => 'qaico', 'elType' => 'widget', 'widgetType' => 'rdct-icon', 'settings' => array( 'name' => 'mail', 'label' => 'Contact us' ), 'elements' => array() ),
	) ) );
	$html = $plugin->frontend->get_builder_content( $document->get_main_id(), true );
	expect( str_contains( $html, $text ) && str_contains( $html, 'data-rdct-text-reveal="scroll"' ) && str_contains( $html, 'dir="rtl"' ) && str_contains( $html, 'aria-label="Contact us"' ), 'Actual Elementor did not use shared component renderers.' );
	expect( wp_script_is( 'rdct-elementor-components', 'enqueued' ), 'Actual Elementor did not request widget bridge.' );
	$attributes = apply_filters( 'wp_script_attributes', array( 'id' => 'rdct-elementor-components-js' ) );
	expect( 'module' === $attributes['type'], 'Widget bridge is not a module script.' );
}
echo 'Passed: actual WordPress fonts/head and shared PHP components' . ( $with_elementor ? ', real Elementor widget document and dependency rendering' : ' without Elementor' ) . ".\n";
