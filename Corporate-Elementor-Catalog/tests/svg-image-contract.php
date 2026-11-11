<?php

define( 'ABSPATH', '/' );
function wp_get_upload_dir() { return array( 'basedir' => sys_get_temp_dir() . '/rdct-svg-qa' ); }
function get_post_mime_type( $id ) { return 'image/svg+xml'; }
function get_attached_file( $id ) { return 99 === $id ? __FILE__ : wp_get_upload_dir()['basedir'] . '/icon.svg'; }
function wp_normalize_path( $value ) { return str_replace( '\\', '/', $value ); }
function wp_unique_id( $prefix ) { static $id = 0; return $prefix . ++$id; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$directory = wp_get_upload_dir()['basedir'];
mkdir( $directory );
file_put_contents( $directory . '/icon.svg', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" onload="alert(1)"><script>alert(1)</script><style>body{display:none}</style><foreignObject><p>unsafe</p></foreignObject><image href="https://example.invalid/track"/><defs><linearGradient id="paint"><stop offset="0" stop-color="#fff"/></linearGradient></defs><g transform="translate(1 1)"><path d="M0 0 L20 20" stroke="url(#paint)" onclick="alert(1)"/><circle cx="10" cy="10" r="5" fill="url(https://example.invalid/image)"/><animateTransform attributeName="transform"/></g></svg>' );
require __DIR__ . '/../radmanct-components/inc/components/svg-image.php';
try {
	$first = \RadmanCTComponents\Components\SvgImage\render( 1 );
	$second = \RadmanCTComponents\Components\SvgImage\render( 1 );
	expect( str_contains( $first, '<path' ) && str_contains( $first, '<circle' ) && str_contains( $first, 'linearGradient' ), 'Safe SVG geometry lost.' );
	foreach ( array( 'onload', 'onclick', '<script', '<style', 'foreignObject', '<image', 'example.invalid', 'animateTransform' ) as $unsafe ) { expect( ! str_contains( $first, $unsafe ), 'Unsafe SVG content retained: ' . $unsafe ); }
	expect( str_contains( $first, 'url(#rdct-svg-1-paint)' ) && str_contains( $second, 'url(#rdct-svg-2-paint)' ), 'Local gradient references or instance isolation failed.' );
	expect( '' === \RadmanCTComponents\Components\SvgImage\render( 99 ), 'File outside uploads accepted.' );
	echo "Passed: bounded local SVG, preserved geometry/gradients, stripped active/external content and isolated IDs.\n";
} finally { unlink( $directory . '/icon.svg' ); rmdir( $directory ); }
