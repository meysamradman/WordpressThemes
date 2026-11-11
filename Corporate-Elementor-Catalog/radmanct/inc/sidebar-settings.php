<?php

namespace RadmanCT\Sidebars;

defined( 'ABSPATH' ) || exit;

function scopes(): array {
	return array( 'blog_archive', 'blog_single', 'catalog_archive', 'catalog_single', 'portfolio_archive', 'portfolio_single' );
}

function defaults(): array {
	$result = array();
	foreach ( scopes() as $scope ) {
		$result[ $scope . '_sidebar_enabled' ] = false;
		$result[ $scope . '_sidebar_area' ] = 'rdct-' . str_replace( '_', '-', $scope );
		$result[ $scope . '_sidebar_position' ] = 'end';
		$result[ $scope . '_sidebar_width' ] = 320;
	}
	return $result;
}

function normalize( string $key, $value, bool &$valid ) {
	$valid = false;
	if ( str_ends_with( $key, '_sidebar_enabled' ) ) {
		$valid = in_array( $value, array( true, false, 0, 1, '0', '1' ), true );
		return $valid ? in_array( $value, array( true, 1, '1' ), true ) : $value;
	}
	if ( str_ends_with( $key, '_sidebar_area' ) ) {
		$valid = is_string( $value ) && 1 === preg_match( '/^[a-z0-9_-]{1,80}$/D', $value );
	} elseif ( str_ends_with( $key, '_sidebar_position' ) ) {
		$valid = in_array( $value, array( 'start', 'end' ), true );
	} elseif ( str_ends_with( $key, '_sidebar_width' ) ) {
		$valid = ( is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) ) ) && (int) $value >= 240 && (int) $value <= 480;
		return $valid ? (int) $value : $value;
	}
	return $value;
}
