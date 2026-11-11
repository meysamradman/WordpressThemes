<?php

namespace RadmanCT\Assets;

defined( 'ABSPATH' ) || exit;

function entry( string $key, string $scope = 'parent' ): array {
	static $manifests = array();
	$directory = 'child' === $scope ? get_stylesheet_directory() : get_template_directory();
	if ( ! isset( $manifests[ $directory ] ) ) {
		$path = $directory . '/dist/manifest.json';
		$data = is_readable( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
		$manifests[ $directory ] = is_array( $data ) ? $data : array();
	}
	$asset = $manifests[ $directory ][ $key ] ?? array();
	$file = $asset['file'] ?? '';
	if ( ! is_string( $file ) || ! preg_match( '~^(css|js)/[a-zA-Z0-9_./-]+\.min\.(css|js)$~', $file ) || str_contains( $file, '..' ) ) {
		return array();
	}
	return is_file( $directory . '/dist/' . $file ) ? $asset : array();
}

function url( string $file, string $scope = 'parent' ): string {
	$base = 'child' === $scope ? get_stylesheet_directory_uri() : get_template_directory_uri();
	return $base . '/dist/' . $file;
}

function style( string $handle, string $key, string $scope = 'parent', array $dependencies = array(), string $media = 'all' ): bool {
	$asset = entry( $key, $scope );
	if ( ! $asset || ! str_ends_with( $asset['file'], '.min.css' ) ) {
		return false;
	}
	wp_enqueue_style( $handle, url( $asset['file'], $scope ), $dependencies, null, $media );
	return true;
}

function enqueue(): void {
	style( 'rdct-theme', 'src/css/theme.css' );
	if ( is_404() && function_exists( '\\RadmanCTComponents\\Components\\enqueue' ) ) { \RadmanCTComponents\Components\enqueue( 'archive-search' ); }
	if ( is_page() && '' === get_page_template_slug() ) {
		style( 'rdct-page', 'src/css/page.css', 'parent', array( 'rdct-theme' ) );
	}
	$context = \RadmanCT\Renderers\context();
	$custom_listing = 'archive' === ( $context[1] ?? '' ) && in_array( $context[0] ?? '', array( 'catalog', 'portfolio' ), true );
	$elementor_location = function_exists( '\\RadmanCT\\Renderers\\uses_elementor' ) && \RadmanCT\Renderers\uses_elementor();
	if ( ( is_home() || is_archive() || is_search() ) && ! $custom_listing && ! $elementor_location ) {
		style( 'rdct-listing', 'src/css/listing.css', 'parent', array( 'rdct-theme' ) );
	}
	if ( is_singular( 'post' ) && ! $elementor_location ) {
		style( 'rdct-blog', 'src/css/blog.css', 'parent', array( 'rdct-theme' ) );
	}
	$script = entry( 'src/js/frontend.js' );
	if ( $script ) {
		wp_enqueue_script_module( 'rdct-frontend', url( $script['file'] ), array(), null );
	}
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );

add_filter( 'wp_script_attributes', static function ( array $attributes ): array {
	if ( 'rdct-frontend-js-module' === ( $attributes['id'] ?? '' ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$attributes['data-rdct-debug'] = '1';
	}
	return $attributes;
} );
