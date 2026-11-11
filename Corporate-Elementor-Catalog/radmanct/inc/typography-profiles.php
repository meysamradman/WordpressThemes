<?php

namespace RadmanCT\Typography\Profiles;

defined( 'ABSPATH' ) || exit;

function scopes(): array {
	return array( 'blog', 'portfolio', 'catalog' );
}

function fields( array $settings ): array {
	return array_filter( $settings, static fn( $key ) => (bool) preg_match( '/^(?:(?:(?:body|headings|h[1-6]|link|span)_)?(?:persian|latin)(?:_(?:tablet|mobile))?_(?:font|weight|size|line_height)|(?:body|headings|h[1-6]|link|span)(?:_hover)?_color)$/D', $key ), ARRAY_FILTER_USE_KEY );
}

function resolve( array $base, array $raw ): array {
	$raw = array_filter( array_intersect_key( $raw, fields( $base ) ), static fn( $value ) => '' !== $value );
	foreach ( array( 'persian', 'latin' ) as $group ) {
		if ( isset( $raw[ $group . '_font' ] ) ) {
			foreach ( \RadmanCT\Fonts\roles() as $role ) { $base[ $role . '_' . $group . '_font' ] = ''; }
		} elseif ( isset( $raw[ 'headings_' . $group . '_font' ] ) ) {
			foreach ( range( 1, 6 ) as $level ) { $base[ 'h' . $level . '_' . $group . '_font' ] = ''; }
		}
	}
	$result = array_replace( $base, $raw );
	foreach ( array_merge( array( 'body' ), \RadmanCT\Fonts\roles() ) as $role ) {
		foreach ( array( 'persian', 'latin' ) as $group ) {
			$key = $role . '_' . $group . '_weight';
			$font = \RadmanCT\Fonts\role_font( $result, $role, $group );
			if ( ! isset( $raw[ $key ] ) && ! in_array( (string) $result[ $key ], \RadmanCT\Fonts\weights( $font ), true ) ) { $result[ $key ] = ''; }
		}
	}
	return $result;
}

function normalize( $input, array $base, bool &$valid ): array {
	$valid = is_array( $input );
	if ( ! $valid || ! $input ) { return array(); }
	$base = fields( $base );
	if ( array_diff_key( $input, $base ) || array_filter( $input, static fn( $value ) => ! is_string( $value ) && ! is_numeric( $value ) ) ) { $valid = false; return array(); }
	$raw = array_filter( $input, static fn( $value ) => '' !== $value );
	$effective = resolve( $base, $raw );
	$normalized = \RadmanCT\Settings\normalize( $effective, $base );
	foreach ( $raw as $key => $value ) {
		$comparison = str_ends_with( $key, '_color' ) ? strtolower( (string) $value ) : (string) $value;
		if ( (string) $normalized[ $key ] !== $comparison && ( ! is_numeric( $value ) || (float) $normalized[ $key ] !== round( (float) $value, 2 ) ) ) { $valid = false; return array(); }
		$raw[ $key ] = $normalized[ $key ];
	}
	return $raw;
}

function enqueue(): void {
	if ( is_admin() || \RadmanCT\Renderers\uses_elementor() ) { return; }
	$context = \RadmanCT\Renderers\context();
	if ( ! $context ) { return; }
	$base = \RadmanCT\Settings\values();
	$scope = $context[0];
	$raw = $base[ $scope . '_typography' ] ?? array();
	if ( ! $raw ) { return; }
	$settings = resolve( $base, $raw );
	$fonts = \RadmanCT\Typography\used( \RadmanCT\Typography\selections( $settings ), $settings );
	$font_handles = \RadmanCT\Typography\enqueue_font_faces( $fonts );
	foreach ( fields( $base ) as $key => $unused ) {
		if ( preg_match( '/_(?:size|line_height|color)$/D', $key ) && ! isset( $raw[ $key ] ) ) { $settings[ $key ] = ''; }
	}
	$css = \RadmanCT\Typography\css( $settings, 'body.rdct-site #main-content', false, $base );
	if ( '' === $css ) { return; }
	wp_register_style( 'rdct-content-typography', false, array_merge( array( 'rdct-theme' ), $font_handles ), null );
	wp_enqueue_style( 'rdct-content-typography' );
	wp_add_inline_style( 'rdct-content-typography', $css );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 30 );
