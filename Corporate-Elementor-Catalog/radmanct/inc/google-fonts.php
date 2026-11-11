<?php

namespace RadmanCT\GoogleFonts;

defined( 'ABSPATH' ) || exit;

function blocked( string $url ): bool {
	if ( ! \RadmanCT\Settings\values()['block_google_fonts'] ) { return false; }
	$url = html_entity_decode( trim( $url ), ENT_QUOTES, 'UTF-8' );
	$url = str_starts_with( $url, '//' ) ? 'https:' . $url : ( str_contains( $url, '://' ) ? $url : 'https://' . $url );
	$host = strtolower( rtrim( (string) wp_parse_url( $url, PHP_URL_HOST ), '.' ) );
	return in_array( $host, array( 'fonts.googleapis.com', 'fonts.gstatic.com', 'fonts.google.com' ), true );
}

function source( string $url ): string {
	return blocked( $url ) ? '' : $url;
}

function script( string $tag, string $handle, string $url ): string {
	return blocked( $url ) ? '' : $tag;
}

function resources( array $resources ): array {
	return array_values( array_filter( $resources, static function ( $entry ): bool {
		$url = is_array( $entry ) ? ( $entry['href'] ?? '' ) : $entry;
		return ! is_string( $url ) || ! blocked( $url );
	} ) );
}

function request( $response, array $args, string $url ) {
	if ( false !== $response || ! blocked( $url ) ) { return $response; }
	return new \WP_Error( 'rdct_google_fonts_blocked', __( 'Google Fonts requests are disabled in Radman CT settings.', 'radmanct' ) );
}

add_filter( 'style_loader_src', __NAMESPACE__ . '\\source', PHP_INT_MAX );
add_filter( 'script_loader_tag', __NAMESPACE__ . '\\script', PHP_INT_MAX, 3 );
add_filter( 'wp_resource_hints', __NAMESPACE__ . '\\resources', PHP_INT_MAX );
add_filter( 'wp_preload_resources', __NAMESPACE__ . '\\resources', PHP_INT_MAX );
add_filter( 'pre_http_request', __NAMESPACE__ . '\\request', PHP_INT_MAX, 3 );
