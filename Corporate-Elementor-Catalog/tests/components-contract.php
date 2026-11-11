<?php

define( 'ABSPATH', '/' );
function __( $text, $domain ) { return $text; }
if ( ! function_exists( 'get_intermediate_image_sizes' ) ) { function get_intermediate_image_sizes() { return array( 'thumbnail', 'medium', 'medium_large', 'large', 'client-banner' ); } }
$registered = array();
$enqueued = array();
$printed = array();
$head = false;
function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_stylesheet_directory() { return get_template_directory(); }
function get_template_directory_uri() { return 'https://radman.test/wp-content/themes/radmanct'; }
function get_stylesheet_directory_uri() { return get_template_directory_uri(); }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function wp_register_style( $handle, ...$args ) { $GLOBALS['registered'][ $handle ] = $args; }
function wp_register_script( ...$args ) {}
function wp_script_is( ...$args ) { return false; }
function wp_enqueue_script( ...$args ) {}
function wp_add_inline_script( ...$args ) {}
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function apply_filters( $hook, $value ) { return $value; }
function wp_enqueue_style( $handle ) { $GLOBALS['enqueued'][ $handle ] = true; }
function wp_style_is( $handle, $state ) { return 'done' === $state ? isset( $GLOBALS['printed'][ $handle ] ) : isset( $GLOBALS['registered'][ $handle ] ); }
function wp_print_styles( $handles ) { foreach ( $handles as $handle ) { $GLOBALS['printed'][ $handle ] = ( $GLOBALS['printed'][ $handle ] ?? 0 ) + 1; } }
function did_action( $hook ) { return $GLOBALS['head'] ? 1 : 0; }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); } }
function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function sanitize_text_field( $text ) { return strip_tags( $text ); }
if ( ! function_exists( 'sanitize_hex_color' ) ) { function sanitize_hex_color( $text ) { return preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD', $text ) ? $text : null; } }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require __DIR__ . '/fixtures/components-url.php';
define( 'RDCT_COMPONENTS_PATH', dirname( __DIR__ ) . '/radmanct-components' );
require dirname( __DIR__ ) . '/radmanct-components/inc/assets.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/components.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/template-functions.php';
expect( ! function_exists( '\RadmanCTComponents\Icons\catalog' ) && ! function_exists( '\RadmanCTComponents\Components\TextReveal\render' ), 'Unused renderers eagerly loaded.' );
\RadmanCTComponents\Components\register_assets();
expect( ! $enqueued && count( $registered ) === count( \RadmanCTComponents\Components\definitions() ) + 1 && isset( $registered['rdct-text-reveal'], $registered['rdct-icons'] ), 'Registration enqueued component CSS or missed declared assets.' );
rdct_use( '../../unknown' );
expect( ! $enqueued, 'Unknown component accepted.' );
$text = "\u{0645}\u{06CC}\u{200C}\u{062E}\u{0648}\u{0627}\u{0647}\u{0645} Word 123\nNext";
ob_start();
rdct_text_reveal( $text, array( 'direction' => 'rtl' ) );
$html = ob_get_clean();
expect( str_contains( $html, $text ) && str_contains( $html, 'dir="rtl"' ) && ! str_contains( $html, '<span' ), 'SSR text, ZWNJ or direction changed.' );
expect( isset( $enqueued['rdct-text-reveal'] ) && ! isset( $enqueued['rdct-icons'] ) && ! $printed, 'Component assets crossed scope or printed before head.' );
$unsafe = \RadmanCTComponents\Components\TextReveal\render( '<script>alert(1)</script>', array( 'tag' => 'script', 'idle' => '#fff;display:none', 'direction' => 'rtl" onload="bad' ) );
expect( str_starts_with( $unsafe, '<p ' ) && ! str_contains( $unsafe, '<script>' ) && ! str_contains( $unsafe, 'onload=' ) && ! str_contains( $unsafe, 'display:none' ), 'Unsafe component options escaped validation.' );
$hover = \RadmanCTComponents\Components\TextReveal\render( 'Hover', array( 'mode' => 'hover' ) );

$custom = \RadmanCTComponents\Components\TextReveal\render( 'Heading', array( 'tag' => 'h2', 'final_opacity' => 0.85, 'start' => 'top 70%', 'end' => 'bottom bottom', 'from' => 'end', 'scrub' => 3, 'stagger' => 0, 'align' => 'start' ) );
foreach ( array( '<h2 ', 'data-final-opacity="0.85"', 'data-reveal-start="top 70%"', 'data-reveal-end="bottom bottom"', 'data-reveal-from="end"', 'data-reveal-scrub="3"', 'data-reveal-stagger="0"', 'text-align:start' ) as $expected ) {
	expect( str_contains( $custom, $expected ), 'Custom reveal option missing: ' . $expected );
}
$invalid = \RadmanCTComponents\Components\TextReveal\render( 'Safe', array( 'final_opacity' => INF, 'start' => array(), 'end' => 'top" onload="bad', 'from' => array(), 'scrub' => -20, 'align' => 'start;display:none' ) );
expect( str_contains( $invalid, 'data-final-opacity="1"' ) && str_contains( $invalid, 'data-reveal-start="top 80%"' ) && str_contains( $invalid, 'data-reveal-end="bottom 35%"' ) && str_contains( $invalid, 'data-reveal-from="start"' ) && str_contains( $invalid, 'data-reveal-scrub="0"' ) && ! str_contains( $invalid, 'display:none' ) && ! str_contains( $invalid, 'onload' ), 'Invalid animation controls escaped normalization.' );
expect( str_contains( $hover, 'tabindex="0"' ) && str_contains( $hover, 'data-rdct-text-reveal="hover"' ), 'Hover keyboard access missing.' );
expect( '' === \RadmanCTComponents\Components\TextReveal\render( '' ) && '' === \RadmanCTComponents\Components\TextReveal\render( str_repeat( 'x', 16001 ) ), 'Empty or unbounded text accepted.' );
expect( str_contains( \RadmanCTComponents\Components\TextReveal\render( str_repeat( 'word ', 401 ) ), 'data-rdct-text-reveal="static"' ), 'Oversized animation did not retain static readable content.' );
$runs = 0;
$prepared = rdct_prepare( static function () use ( &$runs ): void {
	++$runs;
	rdct_text_reveal( 'Prepared once', array( 'mode' => 'static' ) );
	ob_start();
	echo '<span>Nested content</span>';
} );
expect( 1 === $runs && str_contains( $prepared, 'Prepared once' ) && str_contains( $prepared, 'Nested content' ), 'Prepared content lost output or rendered twice.' );
expect( isset( $enqueued['rdct-text-effects'] ) && ! isset( $printed['rdct-text-effects'] ), 'Preparation printed CSS before the head instead of enqueueing.' );
expect( array( 'rdct-text-reveal', 'rdct-text-effects' ) === \RadmanCTComponents\Components\style_depends( 'text-reveal', array( 'mode' => 'static' ) ), 'Shared style selection changed.' );
$level = ob_get_level();
try { rdct_prepare( static function (): void { echo 'Discard'; throw new RuntimeException( 'Render failure' ); } ); } catch ( RuntimeException $error ) {}
expect( ob_get_level() === $level, 'Failed preparation leaked an output buffer.' );
$head = true;
try { rdct_prepare( static function (): void {} ); throw new RuntimeException( 'Late preparation accepted.' ); } catch ( LogicException $error ) {}
ob_start(); rdct_text_reveal( 'After head' ); rdct_text_reveal( 'Another instance' ); ob_end_clean();
expect( 1 === $printed['rdct-text-reveal'], 'Late component stylesheet was lost or printed twice.' );
ob_start(); rdct_icon( 'phone', array( 'label' => 'Call "office"' ) ); $icon = ob_get_clean();
expect( str_contains( $icon, '<svg ' ) && str_contains( $icon, 'Call &quot;office&quot;' ) && ! str_contains( $icon, 'directional' ), 'Local icon rendering or label failed.' );
expect( count( \RadmanCTComponents\Icons\catalog() ) >= 20, 'Base icon set is incomplete.' );
$arrow = \RadmanCTComponents\Icons\render( 'arrow-right' );
expect( str_contains( $arrow, 'rdct-ico--directional' ) && str_contains( $arrow, 'aria-hidden="true"' ), 'Directional or decorative metadata missing.' );
expect( ! str_contains( \RadmanCTComponents\Icons\render( 'arrow-right', array( 'mirror' => false ) ), 'directional' ), 'Explicit mirror opt-out ignored.' );
expect( '' === \RadmanCTComponents\Icons\render( '../../bad' ), 'Arbitrary icon path accepted.' );
\RadmanCTComponents\Components\requirements( 'carousel', array( 'carousel_profile' => 'logos' ) );
expect( ! function_exists( '\\RadmanCTComponents\\Components\\Testimonial\\render' ), 'Logo asset selection eagerly loaded the review renderer.' );
\RadmanCTComponents\Components\requirements( 'carousel', array( 'carousel_profile' => 'testimonials' ) );
expect( ! function_exists( '\\RadmanCTComponents\\Components\\Testimonial\\render' ), 'Style metadata eagerly loaded the review renderer.' );
echo "Passed: isolated assets, late CSS deduplication, SSR Persian text, safe options, trusted SVG, labels and direction.\n";
