<?php

namespace RadmanCTContent\Admin\Assets;

defined( 'ABSPATH' ) || exit;

function entry( string $key ): array {
	static $manifest;
	$base = dirname( __DIR__, 2 );
	if ( null === $manifest ) {
		$path = $base . '/dist/manifest.json';
		$manifest = is_readable( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
		$manifest = is_array( $manifest ) ? $manifest : array();
	}
	$asset = $manifest[ $key ] ?? array();
	$file = $asset['file'] ?? '';
	return is_string( $file ) && preg_match( '~^(js|css)/[a-zA-Z0-9_-]+\.min\.(js|css)$~D', $file ) && is_file( $base . '/dist/' . $file ) ? $asset : array();
}

function style( string $handle, string $key, array $dependencies = array() ): bool {
	$asset = entry( $key );
	if ( ! $asset || ! str_ends_with( $asset['file'], '.min.css' ) ) { return false; }
	wp_enqueue_style( $handle, plugin_dir_url( \RadmanCTContent\FILE ) . 'dist/' . $asset['file'], $dependencies, null );
	return true;
}

function script( string $handle, string $key, array $dependencies = array() ): bool {
	$asset = entry( $key );
	if ( ! $asset || ! str_ends_with( $asset['file'], '.min.js' ) ) { return false; }
	wp_enqueue_script( $handle, plugin_dir_url( \RadmanCTContent\FILE ) . 'dist/' . $asset['file'], $dependencies, null, true );
	return true;
}
