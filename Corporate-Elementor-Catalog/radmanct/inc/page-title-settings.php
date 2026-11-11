<?php
namespace RadmanCT\PageTitle;
defined( 'ABSPATH' ) || exit;

const META = '_rdct_page_title';

function scopes(): array { return array( 'blog_archive', 'blog_single', 'catalog_archive', 'catalog_single', 'portfolio_archive', 'portfolio_single' ); }

function labels(): array {
	return array_map( static fn( $label ) => __( $label, 'radmanct' ), array( 'page_title' => 'Page title', 'page_title_typography' => 'Page title typography', 'titleDisplay' => 'Page title display', 'titleShape' => 'Title area shape', 'titlePlain' => 'Straight edge', 'titleNotched' => 'Curved edge', 'titleShow' => 'Show', 'titleHide' => 'Hide', 'titleCustom' => 'Customize this title area', 'titleHeight' => 'Title area height (px)', 'titleBreadcrumbs' => 'Show breadcrumbs', 'titleOverlay' => 'Background overlay', 'titleOverlayOpacity' => 'Overlay opacity' ) ) + array( 'backgroundFields' => background_labels() );
}

function background_labels(): array {
	return array_map( static fn( $label ) => __( $label, 'radmanct' ), array( 'type' => 'Background type', 'color' => 'Background color', 'gradient' => 'Gradient', 'second' => 'Second color', 'linear' => 'Linear', 'radial' => 'Radial', 'angle' => 'Gradient angle', 'start' => 'First color position', 'end' => 'Second color position', 'image' => 'Background image', 'video' => 'Background video', 'poster' => 'Video poster image', 'choose' => 'Choose media', 'remove' => 'Remove', 'inherit' => 'Inherit / clear' ) );
}

function options(): array {
	return array( 'mode' => 'inherit', 'custom' => false, 'shape' => 'plain', 'background' => array(), 'height' => 280, 'breadcrumbs' => true, 'breadcrumbMode' => 'inherit', 'overlay' => '#000000', 'overlayOpacity' => 0 );
}

function defaults(): array {
	$result = array( 'page_title' => array_replace( options(), array( 'mode' => 'hide', 'custom' => true ) ), 'page_title_typography' => array() );
	foreach ( scopes() as $scope ) { $result[ $scope . '_page_title' ] = options(); }
	return $result;
}

function normalize( $input ): ?array {
	if ( ! is_array( $input ) ) { return null; }
	$result = options();
	foreach ( $input as $key => $value ) {
		if ( ! array_key_exists( $key, $result ) ) { return null; }
		if ( in_array( $key, array( 'mode', 'breadcrumbMode' ), true ) ) { if ( ! in_array( $value, array( 'inherit', 'show', 'hide' ), true ) ) { return null; } }
		elseif ( 'shape' === $key ) { if ( ! in_array( $value, array( 'plain', 'notched' ), true ) ) { return null; } }
		elseif ( in_array( $key, array( 'custom', 'breadcrumbs' ), true ) ) { if ( ! is_bool( $value ) ) { return null; } }
		elseif ( 'background' === $key ) {
			if ( ! function_exists( '\\RadmanCTComponents\\Background\\normalize' ) ) { if ( array() !== $value ) { return null; } }
			else { $value = \RadmanCTComponents\Background\normalize( $value ); if ( null === $value ) { return null; } }
		} elseif ( 'height' === $key ) { if ( ! is_int( $value ) || $value < 120 || $value > 900 ) { return null; } }
		elseif ( 'overlay' === $key ) { if ( ! is_string( $value ) || ! preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ) { return null; } $value = strtolower( $value ); }
		elseif ( ! is_numeric( $value ) || ! is_finite( (float) $value ) || $value < 0 || $value > 1 ) { return null; }
		$result[ $key ] = $value;
	}
	if ( ! array_key_exists( 'breadcrumbMode', $input ) && ! empty( $input['custom'] ) ) { $result['breadcrumbMode'] = $result['breadcrumbs'] ? 'show' : 'hide'; }
	return $result;
}

function merge( array $base, array $override ): array {
	if ( $override['custom'] ) { foreach ( array( 'shape', 'background', 'height', 'overlay', 'overlayOpacity' ) as $key ) { $base[ $key ] = $override[ $key ]; } }
	if ( 'inherit' !== ( $override['breadcrumbMode'] ?? 'inherit' ) ) { $base['breadcrumbs'] = 'show' === $override['breadcrumbMode']; }
	if ( 'inherit' !== $override['mode'] ) { $base['mode'] = $override['mode']; }
	return $base;
}
