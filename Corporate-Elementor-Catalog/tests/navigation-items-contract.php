<?php

define( 'ABSPATH', '/usr/src/wordpress/' );
require ABSPATH . 'wp-includes/plugin.php';
$metadata = array(); $allowed = true; $post_type = 'nav_menu_item'; $styles = array(); $observed = array(); $fail = false;
function get_posts( $args ) { return array(); }
function current_user_can( ...$args ) { return $GLOBALS['allowed']; }
function get_post_type( $id ) { return $GLOBALS['post_type']; }
function get_post_meta( $id, $key, $single ) { return $GLOBALS['metadata'][ $id ][ $key ] ?? ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['metadata'][ $id ][ $key ] = $value; }
function delete_post_meta( $id, $key ) { unset( $GLOBALS['metadata'][ $id ][ $key ] ); }
function wp_verify_nonce( $nonce, $action ) { return 'nonce-' . $action === $nonce; }
function wp_unslash( $value ) { return stripslashes( $value ); }
function __( $value, $domain ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
function esc_html( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
function esc_html__( $value, $domain ) { return esc_html( $value ); }
function selected( $value, $key, $echo ) { return $value === $key ? ' selected' : ''; }
function wp_nonce_field( $action, $name, $referer ) { echo '<input name="' . esc_attr( $name ) . '" value="nonce-' . esc_attr( $action ) . '">'; }
function wp_style_is( $handle, $state ) { return 'registered' === $state; }
function wp_enqueue_style( $handle ) { $GLOBALS['styles'][] = $handle; }
function expect( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
function wp_get_nav_menu_items( $id, $args ) { return $id === 11 ? array( (object) array( 'ID' => 1, 'menu_item_parent' => 0 ), (object) array( 'ID' => 2, 'menu_item_parent' => 0 ), (object) array( 'ID' => 3, 'menu_item_parent' => 2 ) ) : false; }
function wp_nav_menu( $args ) {
	if ( $GLOBALS['fail'] ) { throw new RuntimeException( 'Menu failed.' ); }
	$base = array( 'menu-item', 'menu-item-has-children' );
	foreach ( array( 1 => 0, 2 => 0, 3 => 1, 4 => 0 ) as $id => $depth ) {
		$GLOBALS['observed'][ $id ] = apply_filters( 'nav_menu_css_class', $base, (object) array( 'ID' => $id ), (object) $args, $depth );
	}
	expect( $base === apply_filters( 'nav_menu_css_class', $base, (object) array( 'ID' => 2 ), (object) array(), 0 ), 'Unscoped Elementor/native menu was changed.' );
}
require dirname( __DIR__ ) . '/radmanct/inc/navigation.php';
require dirname( __DIR__ ) . '/radmanct/inc/components.php';
require dirname( __DIR__ ) . '/radmanct/inc/admin/navigation-items.php';

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array( 'rdct_submenu_type' => array( 2 => 'mega' ), 'rdct_menu_item_nonce' => array( 2 => 'nonce-rdct-menu-item-2' ) );
do_action( 'wp_update_nav_menu_item', 11, 2, array() );
expect( 'mega' === \RadmanCT\Navigation\submenu_type( 2 ), 'Valid item choice was not saved.' );
foreach ( array( 'invalid', array( 'mega' ) ) as $bad ) {
	$_POST['rdct_submenu_type'][2] = $bad; do_action( 'wp_update_nav_menu_item', 11, 2, array() );
	expect( 'mega' === \RadmanCT\Navigation\submenu_type( 2 ), 'Malformed item choice replaced stored data.' );
}
$_POST['rdct_submenu_type'][2] = 'dropdown';
$allowed = false; do_action( 'wp_update_nav_menu_item', 11, 2, array() ); $allowed = true;
$post_type = 'page'; do_action( 'wp_update_nav_menu_item', 11, 2, array() ); $post_type = 'nav_menu_item';
$_POST['rdct_menu_item_nonce'][2] = 'wrong'; do_action( 'wp_update_nav_menu_item', 11, 2, array() );
expect( 'mega' === \RadmanCT\Navigation\submenu_type( 2 ), 'Unauthorized, foreign post or bad nonce changed metadata.' );
$_POST = array(); do_action( 'wp_update_nav_menu_item', 11, 2, array() );
expect( 'mega' === \RadmanCT\Navigation\submenu_type( 2 ), 'Missing customizer/API fields erased metadata.' );
$metadata[1][ \RadmanCT\Navigation\SUBMENU_META ] = 'dropdown';
\RadmanCT\Navigation\menu( array( 'menu_id' => 'rdct-mixed', 'rdct_submenu_style' => 'mega' ) );
expect( in_array( 'rdct-submenu--dropdown', $observed[1], true ) && in_array( 'rdct-submenu--mega', $observed[2], true ) && in_array( 'rdct-submenu--mega', $observed[4], true ), 'Item overrides or global inheritance failed.' );
expect( ! in_array( 'rdct-submenu--mega', $observed[3], true ), 'Top-level decoration changed a nested item.' );
expect( array( 'rdct-menu-mega' ) === $styles, 'Mega CSS was not conditionally enqueued.' );
expect( \RadmanCT\Navigation\menu_has_mega( 11 ) && ! \RadmanCT\Navigation\menu_has_mega( 99 ), 'Scoped head asset detection failed.' );
expect( ! has_filter( 'nav_menu_css_class' ) && ! has_filter( 'nav_menu_item_id' ), 'Temporary menu filters leaked.' );
$fail = true;
try { \RadmanCT\Navigation\menu( array( 'menu_id' => 'rdct-failure' ) ); } catch ( RuntimeException $error ) {}
expect( ! has_filter( 'nav_menu_css_class' ) && ! has_filter( 'nav_menu_item_id' ), 'Failed render leaked its filters.' );
$_POST = array( 'rdct_submenu_type' => array( 2 => '' ), 'rdct_menu_item_nonce' => array( 2 => 'nonce-rdct-menu-item-2' ) );
do_action( 'wp_update_nav_menu_item', 11, 2, array() );
expect( 'dropdown' === \RadmanCT\Navigation\submenu_type( 2 ), 'Inherit did not clear the override.' );
ob_start(); do_action( 'wp_nav_menu_item_custom_fields', '2', (object) array( 'ID' => 2 ), 0, null, 11 ); $html = ob_get_clean();
expect( str_contains( $html, 'name="rdct_submenu_type[2]"' ) && str_contains( $html, 'rdct_menu_item_nonce[2]' ), 'Native field lacks item identity or nonce.' );
echo "Passed: native hooks, protected item metadata, inheritance, mixed branches, renderer isolation, conditional styles and filter cleanup.\n";
