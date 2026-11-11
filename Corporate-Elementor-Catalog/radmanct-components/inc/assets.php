<?php

namespace RadmanCTComponents\Assets;

defined( 'ABSPATH' ) || exit;

function entry( string $key ): array {
	static $manifest = null;
	if ( null === $manifest ) {
		$path = RDCT_COMPONENTS_PATH . '/dist/manifest.json';
		$data = is_readable( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
		$manifest = is_array( $data ) ? $data : array();
	}
	$asset = $manifest[ $key ] ?? array();
	$file = $asset['file'] ?? '';
	if ( ! is_string( $file ) || ! preg_match( '~^(css|js)/[a-zA-Z0-9_./-]+\.min\.(css|js)$~D', $file ) || str_contains( $file, '..' ) ) { return array(); }
	return is_file( RDCT_COMPONENTS_PATH . '/dist/' . $file ) ? $asset : array();
}

function url( string $file ): string { return \RadmanCTComponents\url( 'dist/' . $file ); }

function style( string $handle, string $key ): bool {
	$asset = entry( $key );
	if ( ! $asset || ! str_ends_with( $asset['file'], '.min.css' ) ) { return false; }
	wp_enqueue_style( $handle, url( $asset['file'] ), array(), null );
	return true;
}

function register_frontend(): void {
	$asset = entry( 'src/js/frontend.js' );
	if ( ! $asset || wp_script_is( 'rdct-components', 'registered' ) ) { return; }
	wp_register_script( 'rdct-components', url( $asset['file'] ), array(), null, true );
	$vendor = entry( 'src/js/animation-vendor.js' );
	$animation = apply_filters( 'radmanct_components/animation_url', $vendor ? url( $vendor['file'] ) : '' );
	$gsap = entry( 'src/js/gsap-vendor.js' );
	$gsap_url = apply_filters( 'radmanct_components/gsap_url', $gsap ? url( $gsap['file'] ) : '' );
	wp_add_inline_script( 'rdct-components', 'window.rdctComponents=' . wp_json_encode( array( 'animationUrl' => $animation, 'gsapUrl' => $gsap_url, 'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}

function frontend(): void { register_frontend(); wp_enqueue_script( 'rdct-components' ); }

add_filter( 'wp_script_attributes', static function ( array $attributes ): array {
	if ( 'rdct-components-js' === ( $attributes['id'] ?? '' ) ) { $attributes['type'] = 'module'; }
	return $attributes;
} );
