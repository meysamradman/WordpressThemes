<?php

require getenv( 'RDCT_WP_ROOT' ) . '/wp-load.php';

function content_expect( bool $condition, string $message ): void {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}

$asset_calls = 0;
$instances = array();
rdct_register_content( 'contract-design', array(
	'title' => 'Contract design',
	'assets' => static function () use ( &$asset_calls ): void { ++$asset_calls; },
	'render' => static function ( array $context ) use ( &$instances ): string {
		$instances[] = $context['instance_id'];
		return '<p data-content-contract="' . esc_attr( $context['slot'] ?? '' ) . '">Shared PHP design</p>';
	},
) );
content_expect( 0 === $asset_calls, 'Registration loaded unused assets.' );
content_expect( '' === rdct_render_content( '../wp-config.php' ), 'An unregistered path was accepted.' );
content_expect( '' === rdct_render_content( 'missing-design' ), 'Missing design produced content.' );
rdct_prepare_content( 'contract-design' );
rdct_prepare_content( 'contract-design' );
content_expect( 1 === $asset_calls, 'Assets were loaded more than once.' );
content_expect( str_contains( rdct_render_content( 'contract-design' ), 'Shared PHP design' ), 'Registered PHP design did not render.' );
rdct_register_content( 'contract-cycle', array( 'title' => 'Cycle', 'render' => static fn() => rdct_render_content( 'contract-cycle' ) ) );
content_expect( '' === rdct_render_content( 'contract-cycle' ), 'Recursive content was not bounded.' );
rdct_register_content( 'contract-error', array( 'title' => 'Error', 'render' => static function (): void { echo 'Discard me'; throw new RuntimeException( 'Expected callback error' ); } ) );
$level = ob_get_level();
for ( $i = 0; $i < 2; ++$i ) {
	try { rdct_render_content( 'contract-error' ); throw new LogicException( 'Callback did not run again.' ); }
	catch ( RuntimeException $error ) { content_expect( 'Expected callback error' === $error->getMessage(), 'Unexpected exception.' ); }
}
content_expect( $level === ob_get_level(), 'Callback error leaked an output buffer.' );

$base = WP_PLUGIN_DIR . '/radmanct-components/inc/components/';
foreach ( array( 'carousel', 'accordion', 'curtain', 'sticky-sections', 'flipbox' ) as $component ) { require_once $base . $component . '.php'; }
$item = array( 'title' => 'Design', 'custom_content' => 'contract-design', 'content' => '<p>Fallback</p>' );
$outputs = array(
	'carousel' => \RadmanCTComponents\Components\Carousel\render( array( $item ) ),
	'accordion' => \RadmanCTComponents\Components\Accordion\render( array( $item ) ),
	'curtain' => \RadmanCTComponents\Components\Curtain\render( array( $item ) ),
	'curtain-images' => \RadmanCTComponents\Components\Curtain\render( array( $item ), array( 'layout' => 'images' ) ),
	'sticky-item' => \RadmanCTComponents\Components\StickySections\render( array( $item ) ),
	'sticky-aside' => \RadmanCTComponents\Components\StickySections\render( array( array( 'title' => 'Item' ) ), array( 'mode' => 'split', 'aside_custom_content' => 'contract-design' ) ),
	'flipbox-front' => \RadmanCTComponents\Components\FlipBox\render( array( 'front_custom_content' => 'contract-design', 'back_custom_content' => 'contract-design' ) ),
);
foreach ( $outputs as $slot => $html ) { content_expect( str_contains( $html, 'data-content-contract="' . $slot . '"' ), 'Custom content missing in ' . $slot ); }
content_expect( str_contains( $outputs['flipbox-front'], 'data-content-contract="flipbox-back"' ), 'Back face was not integrated.' );
content_expect( str_contains( \RadmanCTComponents\Components\Carousel\render( array( array( 'render' => static fn() => '<p>Legacy callback</p>' ) ) ), 'Legacy callback' ), 'Existing carousel callback was broken.' );
content_expect( count( $instances ) === count( array_unique( $instances ) ), 'Repeated fragments reused HTML IDs.' );
content_expect( 1 === $asset_calls, 'Rendering repeated the assets callback.' );

require_once get_template_directory() . '/inc/navigation/panel.php';
$panel = array( 'panelContentType' => 'custom', 'panelCustomContent' => 'contract-design' );
content_expect( null !== \RadmanCT\Navigation\Panel\settings( $panel ), 'Builder custom source was rejected.' );
content_expect( null !== \RadmanCT\Navigation\Panel\settings( array( 'panelContentType' => 'custom', 'panelCustomContent' => 'missing-design' ) ), 'Temporarily missing Child design was erased.' );
content_expect( null === \RadmanCT\Navigation\Panel\settings( array( 'panelContentType' => 'custom', 'panelCustomContent' => '../file' ) ), 'Builder accepted a file path.' );
ob_start();
\RadmanCT\Navigation\Panel\render( $panel );
$html = ob_get_clean();
content_expect( str_contains( $html, 'data-content-contract="panel"' ), 'Panel did not use the shared renderer.' );
content_expect( isset( \RadmanCT\CustomContent\options()['site-navigation'] ), 'Child example was not registered.' );
content_expect( ! wp_style_is( 'rdct-client-navigation', 'enqueued' ), 'Unused Child design CSS was loaded.' );
$example = rdct_render_content( 'site-navigation' );
content_expect( str_contains( $example, 'rdct-client-navigation' ) && wp_style_is( 'rdct-client-navigation', 'enqueued' ), 'Selected Child partial or its CSS did not load.' );
require_once get_template_directory() . '/inc/builder/schema.php';
$ids = array();
$saved = \RadmanCT\Builder\validate_items( array( $panel + array( 'id' => 'contract-panel', 'type' => 'panel' ) ), $ids );
content_expect( 'contract-design' === ( $saved[0]['panelCustomContent'] ?? null ), 'Builder validation lost the custom design selection.' );
echo "Passed: PHP content slots, registry guards, isolated assets, unique instances and panel selection; no stored site data changed.\n";
