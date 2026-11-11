<?php

namespace RadmanCT\ContentColors;

defined( 'ABSPATH' ) || exit;

const KEYS = array( 'background' );

function fields(): array {
	return array(
		'background' => __( 'Content background', 'radmanct' ),
	);
}

function scopes(): array {
	return array( 'blog_archive', 'blog_single', 'catalog_archive', 'catalog_single', 'portfolio_archive', 'portfolio_single' );
}

function defaults(): array {
	$result = array();
	foreach ( scopes() as $scope ) {
		foreach ( KEYS as $key ) { $result[ $scope . '_color_' . $key ] = ''; }
	}
	return $result;
}

function valid( $value ): bool {
	return is_string( $value ) && ( '' === $value || 1 === preg_match( '/^#[a-f0-9]{6}$/iD', $value ) );
}

function normalize( $input, $previous = array() ): array {
	$input = is_array( $input ) ? $input : array();
	$previous = is_array( $previous ) ? $previous : array();
	$result = array();
	foreach ( KEYS as $key ) {
		$value = array_key_exists( $key, $input ) ? $input[ $key ] : ( $previous[ $key ] ?? '' );
		if ( ! valid( $value ) ) { $value = $previous[ $key ] ?? ''; }
		if ( valid( $value ) && '' !== $value ) { $result[ $key ] = strtolower( $value ); }
	}
	return $result;
}

function resolve( string $scope, array $settings ): array {
	if ( ! in_array( $scope, scopes(), true ) ) { return array(); }
	$base = array();
	foreach ( KEYS as $key ) { $base[ $key ] = $settings[ $scope . '_color_' . $key ] ?? ''; }
	return normalize( $base );
}
