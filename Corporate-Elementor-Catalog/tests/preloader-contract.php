<?php

namespace RadmanCT\Settings {
	function values(): array { return $GLOBALS['settings']; }
}
namespace RadmanCT\Assets {
	function entry( $key ): array { return $GLOBALS['missing'] === $key ? array() : ( $GLOBALS['manifest'][ $key ] ?? array() ); }
	function style( ...$args ): void { $GLOBALS['styles'][] = $args; }
}
namespace {
	define( 'ABSPATH', __DIR__ );
	function add_action( ...$args ) {}
	function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
	function get_template_directory_uri() { return 'https://local.test/wp-content/themes/radmanct'; }
	function esc_attr( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
	function esc_url( $value ) { return esc_attr( $value ); }
	function wp_print_inline_script_tag( $script, $attributes ) { echo '<script id="' . esc_attr( $attributes['id'] ) . '">' . $script . '</script>'; }
	function expect( $condition, $message ) { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	$manifest = json_decode( file_get_contents( get_template_directory() . '/dist/manifest.json' ), true );
	$missing = ''; $styles = array();
	require get_template_directory() . '/inc/preloader.php';
	\RadmanCT\Preloader\enqueue();
	expect( 'src/css/motion/preloader.css' === $styles[0][1], 'Preloader CSS entry missing.' );
	foreach ( array( 'curtain', 'fade', 'sliding', 'spinner', 'spinner-classic', 'dissolve', 'image' ) as $variant ) {
		$settings = array( 'preloader_style' => $variant, 'preloader_background' => '#ffffff', 'preloader_background_secondary' => '#112233', 'preloader_color' => '#235a50' );
		ob_start(); \RadmanCT\Preloader\markup(); $html = ob_get_clean();
		expect( str_contains( $html, 'class="rdct-pre--' . $variant . '"' ), 'Selected style lost.' );
		expect( str_contains( $html, 'inert hidden' ) && str_contains( $html, 'id="rdct-preloader-init"' ), 'Safe markup or early bootstrap missing.' );
		expect( str_contains( $html, '--rdct-preloader-color:#235a50' ), 'Selected colors lost.' );
		expect( str_contains( $html, '--rdct-preloader-back:#112233' ), 'Independent curtain back color was lost.' );
		expect( str_contains( $html, '<svg ' ) === ( 'spinner' === $variant ), 'Circular dash spinner was not isolated to the spinner style.' );
		expect( str_contains( $html, 'class="rdct-pre__indicator"' ) === ( 'spinner-classic' === $variant ), 'Classic spinner or unused decorative markup is incorrect.' );
		$count = array( 'curtain' => 2, 'sliding' => 1, 'dissolve' => 27 )[ $variant ] ?? 0;
		expect( $count === substr_count( $html, 'class="rdct-pre__panel"' ) && str_contains( $html, 'data-exit-duration=' ), 'Selected transition lost its independent panels or duration.' );
		expect( str_contains( $html, '<img ' ) === ( 'image' === $variant ), 'Image requested for an unrelated style.' );
		if ( 'image' === $variant ) { expect( is_file( get_template_directory() . '/images/preloader/img-preloader.gif' ), 'Local image missing.' ); }
	}
	$settings['preloader_style'] = 'curtain'; unset( $settings['preloader_background_secondary'] );
	ob_start(); \RadmanCT\Preloader\markup(); $html = ob_get_clean();
	expect( str_contains( $html, '--rdct-preloader-back:#235a50' ), 'Legacy curtain color fallback changed.' );
	foreach ( array( 'src/js/preloader.js', 'src/css/motion/preloader.css' ) as $missing ) {
		ob_start(); \RadmanCT\Preloader\markup(); $html = ob_get_clean();
		expect( '' === $html, 'Incomplete assets must skip the overlay.' );
	}
	echo "Passed: seven preloader variants, selected colors, local image, early bootstrap and missing-asset fallback.\n";
}
