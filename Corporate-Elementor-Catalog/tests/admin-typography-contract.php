<?php

namespace RadmanCT\Assets {
	function style( $handle, $key, $scope, $dependencies ) {
		$GLOBALS['styles'][ $handle ] = array( $key, $scope, $dependencies );
		return true;
	}
}
namespace {
	define( 'ABSPATH', '/' );
	function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	function add_action( ...$args ) { $GLOBALS['hooks'][] = $args[0]; }
	function add_filter( ...$args ) {}
	function wp_style_is( $handle, $status ) { return isset( $GLOBALS['styles'][ $handle ] ); }
	function wp_add_inline_style( $handle, $css ) { $GLOBALS['inline'][ $handle ] = $css; }
	function get_template_directory_uri() { return 'https://local.test/wp-content/themes/radmanct'; }
	function esc_url_raw( $url ) { return $url; }
	require __DIR__ . '/../radmanct/inc/typography.php';
	require __DIR__ . '/../radmanct/inc/admin/typography.php';
	$styles = array();
	$inline = array();
	\RadmanCT\Admin\Typography\enqueue();
	expect( empty( $styles ) && empty( $inline ), 'Admin font loaded on an unrelated screen.' );
	foreach ( array( 'rdct-admin-settings', 'rdct-admin-builder', 'rdct-admin-requirements' ) as $handle ) {
		$styles = array( $handle => true );
		\RadmanCT\Admin\Typography\enqueue();
		expect( array( $handle ) === $styles['rdct-admin-typography'][2], 'Admin typography lost its screen stylesheet dependency.' );
		expect( str_contains( \RadmanCT\Admin\Typography\body_class( 'existing' ), 'existing rdct-adm-interface' ), 'Typography scope lost existing body classes.' );
	}
	$css = $inline['rdct-admin-typography'];
	expect( str_contains( $css, '/fonts/persian/Peyda/PeydaWebVF.woff2' ) && str_contains( $css, 'unicode-range:U+0600-06FF' ) && str_contains( $css, 'font-weight:100 1000' ), 'Local Peyda metadata or Persian-only coverage missing.' );
	preg_match_all( '/url\("([^"]+)"\)/', $css, $urls );
	expect( 1 === count( $urls[1] ) && str_starts_with( $urls[1][0], get_template_directory_uri() . '/fonts/' ), 'Admin typography references a font outside the theme.' );
	$styles = array();
	expect( 'existing' === \RadmanCT\Admin\Typography\body_class( 'existing' ), 'Admin font scope leaked to other screens.' );
	expect( ! in_array( 'elementor/editor/after_enqueue_styles', $hooks, true ), 'Radman admin font must not override Elementor UI.' );
	expect( ! function_exists( '\\RadmanCT\\Typography\\editor' ), 'Site fonts must not be injected into Gutenberg.' );
	echo "Passed: local Persian admin font, system Latin fallback, screen-specific loading and body scope.\n";
}
