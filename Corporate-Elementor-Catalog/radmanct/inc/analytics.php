<?php

namespace RadmanCT\Analytics;

defined( 'ABSPATH' ) || exit;

function measurement_id( $input ): ?string {
	if ( ! is_string( $input ) || strlen( $input ) > 8192 ) { return null; }
	if ( '' === trim( $input ) ) { return ''; }
	preg_match_all( '/\bG-[A-Z0-9]{4,20}\b/i', $input, $matches );
	$ids = array_values( array_unique( array_map( 'strtoupper', $matches[0] ) ) );
	return 1 === count( $ids ) ? $ids[0] : null;
}

function enqueue(): void {
	if ( is_admin() || is_feed() || is_preview() || is_customize_preview() || isset( $_GET['elementor-preview'] ) || current_user_can( 'edit_theme_options' ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return; }
	$settings = \RadmanCT\Settings\values();
	$id = measurement_id( $settings['analytics_measurement_id'] );
	if ( ! $settings['analytics_enabled'] || ! $id ) { return; }
	if ( wp_script_is( 'rdct-google-analytics', 'enqueued' ) ) { return; }
	wp_enqueue_script( 'rdct-google-analytics', 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $id ), array(), null, array( 'strategy' => 'async', 'in_footer' => false ) );
	wp_add_inline_script( 'rdct-google-analytics', 'window.dataLayer=window.dataLayer||[];window.gtag=window.gtag||function(){window.dataLayer.push(arguments);};gtag("js",new Date());gtag("config",' . wp_json_encode( $id ) . ');', 'before' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 20 );
