<?php

namespace RadmanCTComponents\Components\ImageMedia;

defined( 'ABSPATH' ) || exit;

function attachment_id( $image ): int {
	if ( is_array( $image ) && isset( $image['id'] ) && is_numeric( $image['id'] ) && (int) $image['id'] > 0 ) { return (int) $image['id']; }
	if ( is_numeric( $image ) ) { return max( 0, (int) $image ); }
	$url = is_array( $image ) ? ( $image['src'] ?? $image['url'] ?? '' ) : '';
	if ( ! is_string( $url ) || ! function_exists( 'wp_upload_dir' ) || ! function_exists( 'attachment_url_to_postid' ) ) { return 0; }
	$url = preg_replace( '/[?#].*$/', '', esc_url_raw( $url ) );
	$uploads = wp_upload_dir( null, false );
	$base = rtrim( $uploads['baseurl'] ?? '', '/' ) . '/';
	if ( '/' === $base || ! str_starts_with( $url, $base ) ) { return 0; }
	static $cache = array();
	if ( array_key_exists( $url, $cache ) ) { return $cache[ $url ]; }
	$id = (int) attachment_url_to_postid( $url );
	if ( ! $id ) {
		$original = preg_replace( '/-\d+x\d+(?=\.[a-zA-Z0-9]+$)/', '', $url );
		if ( $original !== $url ) { $id = (int) attachment_url_to_postid( $original ); }
	}
	$cache[ $url ] = max( 0, $id );
	return $cache[ $url ];
}

function editor_images( array $values, int $depth = 0 ): array {
	if ( $depth > 12 ) { return $values; }
	if ( isset( $values['src'] ) && is_string( $values['src'] ) ) {
		$values['id'] = attachment_id( $values );
		return $values;
	}
	foreach ( $values as $key => $value ) { if ( is_array( $value ) ) { $values[ $key ] = editor_images( $value, $depth + 1 ); } }
	return $values;
}

function size( $image, string $fallback = 'large' ): array|string {
	if ( ! is_array( $image ) ) { return $fallback; }
	$dimensions = array();
	foreach ( array( 'width', 'height' ) as $key ) {
		$value = $image[ $key ] ?? null;
		if ( ! is_numeric( $value ) || ! is_finite( (float) $value ) || $value < 1 || $value > 8192 ) { return $fallback; }
		$dimensions[] = (int) $value;
	}
	return $dimensions;
}
