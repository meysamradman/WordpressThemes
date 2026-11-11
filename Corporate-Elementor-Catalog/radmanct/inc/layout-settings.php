<?php

namespace RadmanCT\Layout;

defined( 'ABSPATH' ) || exit;

function fields(): array {
	return array( 'width' => array( 320, 2560 ), 'gutter' => array( 0, 160 ), 'tablet_gutter' => array( 0, 160 ), 'mobile_gutter' => array( 0, 160 ), 'top' => array( 0, 320 ), 'bottom' => array( 0, 320 ), 'tablet_top' => array( 0, 320 ), 'tablet_bottom' => array( 0, 320 ), 'mobile_top' => array( 0, 320 ), 'mobile_bottom' => array( 0, 320 ) );
}

function scopes(): array {
	return array( 'content', 'blog_single', 'blog_archive', 'catalog_single', 'catalog_archive', 'portfolio_single', 'portfolio_archive' );
}

function defaults(): array {
	static $defaults = null;
	if ( null !== $defaults ) { return $defaults; }
	$result = array( 'reading_width' => 760 );
	foreach ( scopes() as $scope ) {
		$result[ $scope . '_layout' ] = 'content' === $scope ? 'contained' : 'inherit';
		foreach ( fields() as $field => $range ) { $result[ $scope . '_' . $field ] = null; }
	}
	$defaults = $result;
	return $defaults;
}

function normalize_field( string $field, $value, bool &$valid, bool $inherit = true ) {
	if ( 'layout' === $field ) {
		$valid = in_array( $value, $inherit ? array( 'inherit', 'contained', 'full', 'custom' ) : array( 'contained', 'full', 'custom' ), true );
		return $value;
	}
	$range = 'reading_width' === $field ? array( 320, 1280 ) : ( fields()[ $field ] ?? null );
	if ( $range && 'reading_width' !== $field && ( null === $value || '' === $value ) ) { $valid = true; return null; }
	$valid = $range && ( is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) ) ) && (int) $value >= $range[0] && (int) $value <= $range[1];
	return $valid ? (int) $value : $value;
}

function normalize( string $key, $value, bool &$valid ) {
	if ( 'reading_width' === $key ) { return normalize_field( $key, $value, $valid ); }
	foreach ( scopes() as $scope ) {
		foreach ( array_merge( array( 'layout' ), array_keys( fields() ) ) as $field ) {
			if ( $key === $scope . '_' . $field ) { return normalize_field( $field, $value, $valid, 'content' !== $scope ); }
		}
	}
	$valid = false;
	return $value;
}

function resolve( array $settings, string $scope = 'content' ): array {
	$result = array( 'layout' => $settings['content_layout'] );
	foreach ( fields() as $field => $range ) { $result[ $field ] = $settings[ 'content_' . $field ]; }
	if ( 'content' !== $scope && in_array( $scope, scopes(), true ) && 'inherit' !== $settings[ $scope . '_layout' ] ) {
		$result['layout'] = $settings[ $scope . '_layout' ];
		foreach ( fields() as $field => $range ) { $result[ $field ] = $settings[ $scope . '_' . $field ] ?? $result[ $field ]; }
	}
	$result['width'] = $result['width'] ?? $settings['container_width'];
	return $result;
}

function variables( array $values ): string {
	$wide = 'custom' === $values['layout'] ? $values['width'] . 'px' : 'var(--rdct-container)';
	$result = '--rdct-content-max:' . ( 'full' === $values['layout'] ? 'none' : $wide ) . ';--rdct-content-wide:' . $wide . ';';
	foreach ( array( 'gutter' => 'var(--rdct-gutter)', 'top' => 'var(--rdct-space)', 'bottom' => 'var(--rdct-space)' ) as $field => $fallback ) {
		$result .= '--rdct-content-' . $field . ':' . ( null === $values[ $field ] ? $fallback : $values[ $field ] . 'px' ) . ';';
		foreach ( array( 'tablet', 'mobile' ) as $device ) {
			$value = $values[ $device . '_' . $field ] ?? null;
			$result .= '--rdct-content-' . $device . '-' . $field . ':' . ( null === $value ? 'var(--rdct-content-' . $field . ')' : $value . 'px' ) . ';';
		}
	}
	return $result;
}
