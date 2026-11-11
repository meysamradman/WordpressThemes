<?php

define( 'ABSPATH', '/' );
$hooks = $registered = $enqueued = $printed = array();
function add_action( ...$args ) {}
function add_filter( $hook, $callback ) { $GLOBALS['hooks'][ $hook ][] = $callback; }
function apply_filters( $hook, $value ) { foreach ( $GLOBALS['hooks'][ $hook ] ?? array() as $callback ) { $value = $callback( $value ); } return $value; }
function __( $text, $domain ) { return $text; }
function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $text ) { return esc_attr( $text ); }
function esc_url_raw( $text, $protocols = array() ) { return $text; }
function sanitize_text_field( $text ) { return strip_tags( $text ); }
function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_template_directory_uri() { return 'https://example.test/themes/radmanct'; }
function plugins_url( $path, $file ) { return 'https://example.test/plugins/' . basename( dirname( $file ) ) . '/' . $path; }
function wp_register_style( $handle, $src, $deps = array() ) { $GLOBALS['registered'][ $handle ] = array( $src, $deps ); }
function wp_enqueue_style( $handle ) { $GLOBALS['enqueued'][ $handle ] = true; }
function wp_register_script( ...$args ) {}
function wp_script_is( ...$args ) { return false; }
function wp_add_inline_script( ...$args ) {}
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function wp_style_is( $handle, $state ) { return isset( $GLOBALS[ 'done' === $state ? 'printed' : 'registered' ][ $handle ] ); }
function wp_print_styles( $handles ) { foreach ( $handles as $handle ) { $GLOBALS['printed'][ $handle ] = ( $GLOBALS['printed'][ $handle ] ?? 0 ) + 1; } }
function did_action( $name ) { return 1; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

$mode = $argv[1] ?? 'theme';
$root = dirname( __DIR__ );
if ( 'theme' !== $mode ) {
	require $root . '/tests/fixtures/components-url.php';
	define( 'RDCT_COMPONENTS_PATH', $root . '/radmanct-components' );
	require $root . '/radmanct-components/inc/assets.php';
	require $root . '/radmanct-components/inc/components.php';
	require $root . '/radmanct-components/inc/icons.php';
	require $root . '/radmanct-components/inc/template-functions.php';
}
if ( 'plugin' !== $mode ) {
	require $root . '/radmanct/inc/assets.php';
	require $root . '/radmanct/inc/components.php';
	require $root . '/radmanct/inc/icons.php';
	require $root . '/radmanct/inc/template-functions.php';
}
$owner = 'plugin' === $mode ? '\\RadmanCTComponents\\Icons\\' : '\\RadmanCT\\Icons\\';
$catalog = ( $owner . 'catalog' )();
expect( count( $catalog ) === count( json_decode( file_get_contents( $root . '/tools/icons.json' ), true )['icons'] ), 'Base catalog incomplete.' );
foreach ( array( 'arrow-left', 'menu', 'smartphone', 'phone', 'brand-instagram', 'brand-telegram', 'brand-whatsapp', 'brand-linkedin' ) as $name ) {
	expect( isset( $catalog[ $name ] ), 'Required icon missing: ' . $name );
}
expect( ! $enqueued, 'Catalog read enqueued frontend assets.' );
$render = $owner . 'render';
expect( str_contains( $render( 'phone' ), 'stroke-width="1"' ), 'Default stroke is not 1.' );
expect( str_contains( $render( 'phone', array( 'stroke' => 2 ) ), 'stroke-width="2"' ), 'Stored explicit stroke changed.' );
expect( str_contains( $render( 'phone', array( 'stroke' => NAN ) ), 'stroke-width="1"' ), 'Invalid stroke fallback failed.' );
expect( 'general' === $catalog['phone']['pack'] && 'social' === $catalog['brand-telegram']['pack'], 'General/social pack metadata is missing.' );
$pack_loads = 0;
add_filter( 'radmanct/icons/packs', static function ( $packs ) use ( &$pack_loads, $catalog ) {
	$packs['real-estate'] = static function () use ( &$pack_loads, $catalog ) {
		++$pack_loads;
		return array( 'real-estate-building' => $catalog['home'], 'phone' => $catalog['home'] );
	};
	$packs['disabled'] = false;
	return $packs;
} );
expect( 0 === $pack_loads, 'Optional pack was loaded before catalog consumption.' );
$extended = ( $owner . 'catalog' )();
expect( isset( $extended['real-estate-building'] ) && 'real-estate' === $extended['real-estate-building']['pack'], 'Active client pack was not merged.' );
expect( $catalog['phone'] === $extended['phone'], 'Optional pack overwrote a base icon.' );
( $owner . 'catalog' )();
expect( 1 === $pack_loads, 'Optional pack was loaded more than once per request.' );
expect( ! str_contains( $render( 'phone' ), $catalog['home']['body'] ), 'Unselected pack SVG leaked into frontend output.' );
expect( '' === $render( '../../bad' ), 'Arbitrary path accepted.' );
expect( str_contains( $render( 'arrow-right' ), 'rdct-ico--directional' ), 'RTL arrow metadata missing.' );
expect( ! str_contains( $render( 'brand-telegram' ), 'rdct-ico--directional' ), 'Brand mirrored in RTL.' );
expect( str_contains( $render( 'phone', array( 'label' => '<b>Call "office"</b>' ) ), 'Call &quot;office&quot;' ), 'Accessible label not escaped.' );
add_filter( 'radmanct/icons/catalog', static function ( $icons ) { $icons['client-factory'] = $icons['phone']; return $icons; } );
expect( str_contains( $render( 'client-factory' ), '<svg' ), 'Project pack unavailable after earlier catalog read.' );
if ( 'plugin' !== $mode ) {
	expect( isset( \RadmanCT\Icons\labels()['client-factory'] ), 'Project pack missing from builder picker.' );
	require $root . '/radmanct/inc/builder/elements.php';
	$html = \RadmanCT\Builder\Elements\render( 'header', array( 'id' => 'test', 'type' => 'icon', 'icon' => 'brand-whatsapp', 'size' => 24, 'text' => 'WhatsApp', 'url' => '' ), 'default', true );
	expect( str_contains( $html, '<svg' ) && str_contains( $html, 'WhatsApp' ), 'Header/footer icon rendering failed.' );
}
if ( 'both' === $mode ) {
	expect( array_keys( \RadmanCT\Icons\catalog() ) === array_keys( \RadmanCTComponents\Icons\catalog() ), 'Theme and widget catalogs differ.' );
}
ob_start(); rdct_icon( 'phone' ); rdct_icon( 'phone' ); $html = ob_get_clean();
expect( 2 === substr_count( $html, '<svg' ), 'PHP helper missing icons.' );
expect( ! str_contains( $html, $catalog['brand-instagram']['body'] ) && ! str_contains( $html, '<script' ), 'Unused icons or picker data leaked into public output.' );
expect( 1 === $printed['rdct-icons'] && isset( $enqueued['rdct-icons'] ), 'Icon CSS missing or printed twice.' );
expect( empty( $registered['rdct-icons'][1] ), 'Icon requires unrelated component base CSS.' );
echo "Passed icons: $mode, base SVG, project pack, stroke, RTL, labels and conditional CSS.\n";
