<?php

namespace RadmanCT\Integrations\Components;

defined( 'ABSPATH' ) || exit;

add_filter( 'radmanct_components/text_fonts', static function ( array $fonts ): array {
	$registry = \RadmanCT\Typography\registry();
	foreach ( \RadmanCT\Typography\used() as $key ) { $fonts[ $registry[ $key ]['family'] ] = $registry[ $key ]['family']; }
	return $fonts;
} );

add_filter( 'radmanct_components/animation_url', static function ( string $fallback ): string {
	$asset = \RadmanCT\Assets\entry( '../radmanct-components/src/js/animation-vendor.js' );
	return $asset ? \RadmanCT\Assets\url( $asset['file'] ) : $fallback;
} );
add_filter( 'radmanct_components/gsap_url', static function ( string $fallback ): string {
	$asset = \RadmanCT\Assets\entry( '../radmanct-components/src/js/gsap-vendor.js' );
	return $asset ? \RadmanCT\Assets\url( $asset['file'] ) : $fallback;
} );
add_filter( 'radmanct_components/admin_css', static function ( string $css ): string {
	return function_exists( '\\RadmanCT\\Admin\\Typography\\css' ) ? $css . \RadmanCT\Admin\Typography\css() : $css;
} );
add_filter( 'radmanct_components/preview_css', static function ( string $css ): string {
	return $css . \RadmanCT\Typography\css() . \RadmanCT\Settings\tokens();
} );
