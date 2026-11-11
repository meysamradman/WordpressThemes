<?php
namespace RadmanCT\Settings {
	function values(): array { return array( 'svg_uploads' => $GLOBALS['svg_enabled'] ); }
}
namespace {
	define( 'ABSPATH', '/' );
	$svg_enabled = false; $svg_caps = true; $svg_path = '';
	function add_filter( ...$args ) {}
	function current_user_can( $capability ) { return $GLOBALS['svg_caps']; }
	function __( $message, $domain ) { return $message; }
	function get_post_mime_type( $id ) { return 'image/svg+xml'; }
	function get_attached_file( $id ) { return $GLOBALS['svg_path']; }
	function wp_get_upload_dir() { return array( 'basedir' => dirname( $GLOBALS['svg_path'] ) ); }
	function wp_normalize_path( $path ) { return str_replace( '\\', '/', $path ); }
	function expect( $condition, $message ) { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	require __DIR__ . '/../radmanct/inc/media-svg.php';
	use RadmanCT\MediaSvg;
	expect( ! isset( MediaSvg\mimes( array() )['svg'] ), 'SVG uploads are enabled by default.' );
	$svg_enabled = true;
	expect( 'image/svg+xml' === MediaSvg\mimes( array() )['svg'], 'SVG MIME missing for an authorized enabled upload.' );
	$svg_caps = false; expect( ! isset( MediaSvg\mimes( array() )['svg'] ), 'SVG MIME granted to an unauthorized user.' ); $svg_caps = true;
	$dirty = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 60" onload="alert(1)"><style>.brand{fill:#123456}</style><script>alert(1)</script><foreignObject><p>bad</p></foreignObject><image href="https://remote.invalid/a.png"/><path class="brand" d="M0 0h240v60z"/></svg>';
	$clean = MediaSvg\sanitize( $dirty );
	expect( is_string( $clean ) && str_contains( $clean, '<path' ) && str_contains( $clean, '#123456' ) && ! preg_match( '/onload|<script|foreignObject|remote.invalid/', $clean ), 'SVG cleaning removed valid artwork or retained active/remote content.' );
	expect( false === MediaSvg\sanitize( '<!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]><svg>&x;</svg>' ) && false === MediaSvg\sanitize( '<html>bad</html>' ) && false === MediaSvg\sanitize( str_repeat( 'x', 1048577 ) ), 'Invalid XML, DTD or oversized SVG accepted.' );
	$svg_path = tempnam( sys_get_temp_dir(), 'rdct-svg-' );
	try {
		file_put_contents( $svg_path, $dirty );
		$file = array( 'name' => 'brand.svg', 'type' => 'image/svg+xml', 'tmp_name' => $svg_path, 'error' => 0, 'size' => strlen( $dirty ) );
		$filtered = MediaSvg\prefilter( $file );
		expect( empty( $filtered['error'] ) && ! str_contains( file_get_contents( $svg_path ), '<script' ), 'Upload did not persist sanitized bytes.' );
		$checked = MediaSvg\filetype( array(), $svg_path, 'brand.svg' );
		expect( 'svg' === $checked['ext'], 'Sanitized upload MIME was not accepted.' );
		$metadata = MediaSvg\metadata( array(), 1 );
		expect( 240 === $metadata['width'] && 60 === $metadata['height'], 'SVG viewBox dimensions missing.' );
		file_put_contents( $svg_path, '<svg onload="bad"/>' );
		expect( array() === MediaSvg\filetype( array(), $svg_path, 'brand.svg' ), 'Modified bytes reused a clean-file decision.' );
		$svg_caps = false; expect( ! empty( MediaSvg\prefilter( $file )['error'] ), 'Unauthorized SVG upload accepted.' ); $svg_caps = true;
		$file['name'] = 'brand.svgz'; expect( ! empty( MediaSvg\prefilter( $file )['error'] ), 'Compressed SVG upload accepted.' );
		$svg_enabled = false;
		expect( MediaSvg\prefilter( $file ) === $file, 'Disabled Radman SVG support overrode another upload provider.' );
	} finally { unlink( $svg_path ); }
	echo "Passed: SVG opt-in, permissions, local sanitizer, active/remote removal, input limits, clean-byte MIME gate and dimensions.\n";
}
