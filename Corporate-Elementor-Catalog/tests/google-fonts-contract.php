<?php

define( 'ABSPATH', '/usr/src/wordpress/' );
$hooks = $saved = array();
function add_filter( $hook, $callback, $priority = 10, $accepted = 1 ) { $GLOBALS['hooks'][ $hook ][ $priority ][] = array( $callback, $accepted ); }
function add_action( ...$args ) {}
function do_action( ...$args ) {}
function do_action_ref_array( ...$args ) {}
function apply_filters( $hook, $value, ...$args ) {
	$groups = $GLOBALS['hooks'][ $hook ] ?? array(); ksort( $groups );
	foreach ( $groups as $callbacks ) { foreach ( $callbacks as list( $callback, $accepted ) ) { $value = $callback( ...array_slice( array_merge( array( $value ), $args ), 0, $accepted ) ); } }
	return $value;
}
function get_option( $key, $default = false ) { return $GLOBALS['saved']; }
function __( $text, $domain ) { return $text; }
function sanitize_text_field( $text ) { return strip_tags( $text ); }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function esc_url( $url ) { return htmlspecialchars( $url, ENT_QUOTES ); }
function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require ABSPATH . 'wp-includes/class-wp-error.php';
require ABSPATH . 'wp-includes/class-wp-dependency.php';
require ABSPATH . 'wp-includes/class-wp-dependencies.php';
require ABSPATH . 'wp-includes/class-wp-styles.php';
require dirname( __DIR__ ) . '/radmanct/inc/settings.php';
require dirname( __DIR__ ) . '/radmanct/inc/google-fonts.php';

expect( \RadmanCT\Settings\defaults()['block_google_fonts'], 'Google Fonts blocking must default on.' );
foreach ( array( 'https://fonts.googleapis.com/css2?family=Test', '//fonts.gstatic.com/font.woff2', 'fonts.googleapis.com', 'https://FONTS.GOOGLEAPIS.COM.:443/css' ) as $url ) {
	expect( \RadmanCT\GoogleFonts\blocked( $url ), 'Unblocked Google Fonts URL: ' . $url );
}
foreach ( array( '/fonts/local.woff2', 'https://example.test/fonts.googleapis.com', 'https://fonts.googleapis.com.example.test/a.css', 'https://google.com', 'https://www.gstatic.com/recaptcha/' ) as $url ) {
	expect( ! \RadmanCT\GoogleFonts\blocked( $url ), 'Unrelated URL blocked: ' . $url );
}
$styles = new WP_Styles();
$styles->base_url = 'https://example.test';
$styles->add( 'google', 'https://fonts.googleapis.com/css2?family=Test', array(), null );
$styles->add( 'local', '/theme.min.css', array( 'google' ), null );
$styles->enqueue( 'local' );
ob_start(); $styles->do_items(); $html = ob_get_clean();
expect( str_contains( $html, '/theme.min.css' ) && ! str_contains( $html, 'googleapis.com' ), 'Blocking removed dependent local CSS or kept remote font CSS.' );
expect( array( 'google' ) === $styles->registered['local']->deps && isset( $styles->registered['google'] ), 'Stylesheet registrations or dependencies were mutated.' );
$hints = array( '//fonts.googleapis.com', array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' ), array( 'href' => '/fonts/local.woff2', 'as' => 'font' ) );
expect( array( $hints[2] ) === apply_filters( 'wp_resource_hints', $hints, 'preconnect' ), 'Google hints not removed or local hint modified.' );
expect( array( $hints[2] ) === apply_filters( 'wp_preload_resources', $hints ), 'Google preloads not removed.' );
$response = apply_filters( 'pre_http_request', false, array(), 'https://fonts.gstatic.com/a.woff2' );
expect( $response instanceof WP_Error && 'rdct_google_fonts_blocked' === $response->get_error_code(), 'Google HTTP request not short-circuited.' );
$cached = array( 'body' => 'cached' );
expect( $cached === apply_filters( 'pre_http_request', $cached, array(), 'https://fonts.gstatic.com/a.woff2' ), 'Existing HTTP response changed.' );
expect( false === apply_filters( 'pre_http_request', false, array(), 'https://example.test/api' ), 'Unrelated HTTP blocked.' );
expect( '' === apply_filters( 'script_loader_tag', '<script src="remote"></script>', 'remote', 'https://fonts.googleapis.com/a.js' ), 'Google script tag not blocked.' );
$saved['block_google_fonts'] = false;
expect( 'https://fonts.googleapis.com/a.css' === apply_filters( 'style_loader_src', 'https://fonts.googleapis.com/a.css' ), 'Disabled switch still blocks sources.' );
expect( $hints === apply_filters( 'wp_preload_resources', $hints ), 'Disabled switch still blocks preloads.' );
expect( false === apply_filters( 'pre_http_request', false, array(), 'https://fonts.gstatic.com/a.woff2' ), 'Disabled switch still blocks HTTP.' );
expect( true === \RadmanCT\Settings\normalize( array( 'block_google_fonts' => 'bad' ), \RadmanCT\Settings\defaults() )['block_google_fonts'], 'Invalid switch value escaped validation.' );
echo "Passed: Google Fonts switch, exact hosts, real WP_Styles dependency rendering, local assets, hints/preloads and HTTP short circuit without network.\n";
