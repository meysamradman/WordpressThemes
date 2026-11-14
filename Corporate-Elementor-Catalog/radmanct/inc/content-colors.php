<?php

namespace RadmanCT\ContentColors;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/content-colors-settings.php';

function current(): array {
	if ( \RadmanCT\Layout\elementor_content() ) { return array(); }
	$context = \RadmanCT\Renderers\context();
	if ( 2 !== count( $context ) ) { return array(); }
	$scope = implode( '_', $context ); $settings = \RadmanCT\Settings\values();
	$result = resolve( $scope, $settings );
	if ( 'gradient' === ( $settings[ $scope . '_background' ]['type'] ?? '' ) ) { $result['gradient'] = $settings[ $scope . '_background' ]; }
	return $result;
}

function css( array $colors ): string {
	if ( isset( $colors['gradient'] ) && function_exists( '\\RadmanCTComponents\\Background\\normalize' ) ) {
		$gradient = \RadmanCTComponents\Background\normalize( $colors['gradient'], array( 'color', 'gradient' ) );
		if ( $gradient && 'gradient' === $gradient['type'] ) { return '#main-content{background:' . \RadmanCTComponents\Background\paint( $gradient ) . ';}'; }
	}
	$colors = normalize( $colors );
	return isset( $colors['background'] ) ? '#main-content{background-color:' . $colors['background'] . ';}' : '';
}

function enqueue(): void {
	if ( is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return; }
	$css = css( current() );
	if ( '' !== $css ) { wp_add_inline_style( 'rdct-theme', $css ); }
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 30 );
