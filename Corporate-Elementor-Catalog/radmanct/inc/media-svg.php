<?php

namespace RadmanCT\MediaSvg;

defined( 'ABSPATH' ) || exit;

function enabled(): bool {
	return ! empty( \RadmanCT\Settings\values()['svg_uploads'] );
}

function allowed(): bool {
	return enabled() && current_user_can( 'edit_theme_options' ) && current_user_can( 'upload_files' ) && class_exists( '\\DOMDocument' );
}

function sanitize( string $source ) {
	if ( strlen( $source ) > 1048576 || ! class_exists( '\\DOMDocument' ) || preg_match( '/<!DOCTYPE|<!ENTITY|<\?php/i', $source ) ) { return false; }
	if ( ! class_exists( '\\RadmanCT\\Vendor\\SvgSanitize\\Sanitizer', false ) ) {
		spl_autoload_register( static function ( $class ): void {
			$prefix = 'RadmanCT\\Vendor\\SvgSanitize\\';
			if ( ! str_starts_with( $class, $prefix ) ) { return; }
			$relative = substr( $class, strlen( $prefix ) );
			if ( ! preg_match( '/^[a-zA-Z0-9_\\\\]+$/D', $relative ) ) { return; }
			$file = dirname( __DIR__ ) . '/vendor/svg-sanitize/src/' . str_replace( '\\', '/', $relative ) . '.php';
			if ( is_file( $file ) ) { require_once $file; }
		} );
	}
	$sanitizer = new \RadmanCT\Vendor\SvgSanitize\Sanitizer();
	$sanitizer->removeRemoteReferences( true );
	$sanitizer->minify( true );
	try { return $sanitizer->sanitize( $source ); } catch ( \Throwable $exception ) { return false; }
}

function mimes( array $mimes ): array {
	if ( allowed() ) { $mimes['svg'] = 'image/svg+xml'; }
	return $mimes;
}

function clean_files( ?string $file = null ): array {
	static $files = array();
	if ( null !== $file ) { $files[ $file ] = hash_file( 'sha256', $file ); }
	return $files;
}

function prefilter( array $file ): array {
	if ( ! enabled() ) { return $file; }
	$extension = strtolower( pathinfo( (string) ( $file['name'] ?? '' ), PATHINFO_EXTENSION ) );
	if ( ! in_array( $extension, array( 'svg', 'svgz' ), true ) && 'image/svg+xml' !== ( $file['type'] ?? '' ) ) { return $file; }
	$error = __( 'SVG upload was rejected. Use a valid SVG under 1 MB and an administrator account with media access.', 'radmanct' );
	if ( ! allowed() || 'svg' !== $extension || ! empty( $file['error'] ) ) { $file['error'] = $error; return $file; }
	$path = $file['tmp_name'] ?? '';
	if ( ! is_string( $path ) || ! is_file( $path ) || is_link( $path ) || ! is_readable( $path ) || ! is_writable( $path ) || filesize( $path ) > 1048576 ) { $file['error'] = $error; return $file; }
	$source = file_get_contents( $path );
	try { $clean = is_string( $source ) ? sanitize( $source ) : false; } catch ( \Throwable $exception ) { $clean = false; }
	if ( ! is_string( $clean ) || '' === trim( $clean ) || file_put_contents( $path, $clean ) !== strlen( $clean ) ) { $file['error'] = $error; return $file; }
	$file['type'] = 'image/svg+xml';
	$file['size'] = strlen( $clean );
	clean_files( realpath( $path ) );
	return $file;
}

function filetype( array $data, string $file, string $filename ): array {
	$path = realpath( $file );
	$files = clean_files();
	if ( allowed() && 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) && $path && isset( $files[ $path ] ) && hash_equals( $files[ $path ], hash_file( 'sha256', $path ) ) ) { return array( 'ext' => 'svg', 'type' => 'image/svg+xml', 'proper_filename' => false ); }
	return $data;
}

function metadata( $data, int $id ) {
	if ( 'image/svg+xml' !== get_post_mime_type( $id ) || ! class_exists( '\\DOMDocument' ) ) { return $data; }
	$file = get_attached_file( $id );
	$base = realpath( wp_get_upload_dir()['basedir'] );
	$path = is_string( $file ) ? realpath( $file ) : false;
	if ( ! $base || ! $path || ! str_starts_with( wp_normalize_path( $path ), rtrim( wp_normalize_path( $base ), '/' ) . '/' ) || ! is_readable( $path ) || filesize( $path ) > 1048576 ) { return $data; }
	$source = file_get_contents( $path );
	if ( ! is_string( $source ) || preg_match( '/<!DOCTYPE|<!ENTITY/i', $source ) ) { return $data; }
	$xml = new \DOMDocument();
	$previous = libxml_use_internal_errors( true );
	try { $valid = $xml->loadXML( $source, LIBXML_NONET ); } finally { libxml_clear_errors(); libxml_use_internal_errors( $previous ); }
	$svg = $xml->documentElement;
	if ( ! $valid || ! $svg || 'svg' !== $svg->localName ) { return $data; }
	$width = $svg->getAttribute( 'width' ); $height = $svg->getAttribute( 'height' );
	if ( ! preg_match( '/^\d+(?:\.\d+)?(?:px)?$/D', $width ) || ! preg_match( '/^\d+(?:\.\d+)?(?:px)?$/D', $height ) ) {
		$box = preg_split( '/[\s,]+/', trim( $svg->getAttribute( 'viewBox' ) ) );
		$width = $box[2] ?? 0; $height = $box[3] ?? 0;
	}
	if ( ! is_numeric( rtrim( (string) $width, 'px' ) ) || ! is_numeric( rtrim( (string) $height, 'px' ) ) ) { return $data; }
	$width = (float) $width; $height = (float) $height;
	if ( ! is_finite( $width ) || ! is_finite( $height ) || $width <= 0 || $height <= 0 || $width > 100000 || $height > 100000 ) { return $data; }
	$data = is_array( $data ) ? $data : array();
	$data['width'] = (int) ceil( $width ); $data['height'] = (int) ceil( $height );
	return $data;
}

add_filter( 'upload_mimes', __NAMESPACE__ . '\\mimes' );
add_filter( 'wp_handle_upload_prefilter', __NAMESPACE__ . '\\prefilter', PHP_INT_MAX );
add_filter( 'wp_handle_sideload_prefilter', __NAMESPACE__ . '\\prefilter', PHP_INT_MAX );
add_filter( 'wp_check_filetype_and_ext', __NAMESPACE__ . '\\filetype', PHP_INT_MAX, 3 );
add_filter( 'wp_generate_attachment_metadata', __NAMESPACE__ . '\\metadata', 20, 2 );
