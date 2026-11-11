<?php

require __DIR__ . '/builder-contract.php';

function wp_style_is( $handle, $state ) { return 'registered' === $state; }
function wp_enqueue_style( $handle, ...$args ) { $GLOBALS['styles'][] = $handle; }
function did_action( $hook ) { return false; }
require dirname( __DIR__ ) . '/radmanct/inc/components.php';

$menu_calls = array();
function remove_filter( ...$args ) {}
$menu_objects = array( 11, 22 );
function has_nav_menu( $location ) { return true; }
function wp_get_nav_menu_object( $id ) { return in_array( $id, $GLOBALS['menu_objects'], true ) ? (object) array( 'term_id' => $id ) : false; }
function get_nav_menu_locations() { return array(); }
function wp_get_nav_menu_items( $id, $args = array() ) { return array(); }
function wp_nav_menu( $args ) { $GLOBALS['menu_calls'][] = $args; echo '<ul id="' . esc_attr( $args['menu_id'] ) . '"><li><a href="/">Home</a></li></ul>'; }
$with_menu = $layout;
$with_menu['rows'][0]['items'][0] = array( 'id' => 'main-menu', 'type' => 'menu', 'menuId' => 11, 'mobileMenuId' => 22, 'mobileDisplay' => 'drawer', 'drawerSide' => 'end' );
$clean = \RadmanCT\Builder\validate( $with_menu );
expect( null !== $clean, 'Separate mobile menu schema rejected.' );
ob_start();
\RadmanCT\Builder\render_layout( 'header', $clean );
$html = ob_get_clean();
expect( array( 11, 22 ) === array_column( $menu_calls, 'menu' ), 'Independent device menus did not render their selected menu.' );
expect( str_contains( $html, 'rdct-bld--desktop' ) && str_contains( $html, 'rdct-bld--mobile' ) && 1 === substr_count( $html, 'data-rdct-drawer ' ), 'Responsive menu wrappers or drawer marker were incorrect.' );
preg_match_all( '/ id="([^"]+)"/', $html, $ids );
expect( count( $ids[1] ) === count( array_unique( $ids[1] ) ), 'Separate device menus produced duplicate DOM identifiers.' );
$menu_calls = array();
ob_start();
\RadmanCT\Builder\menu( 'footer', 'same-menu', 11, array( 'mobileMenuId' => 11 ) );
ob_end_clean();
expect( 1 === count( $menu_calls ), 'Shared device menu was rendered twice.' );
$menu_calls = array();
ob_start();
\RadmanCT\Builder\menu( 'footer', 'missing-menu', 11, array( 'mobileMenuId' => 999 ) );
ob_end_clean();
expect( array( 11 ) === array_column( $menu_calls, 'menu' ), 'Missing explicit mobile menu fell back to an unrelated menu.' );
foreach ( array( 'mobileMenuId' => '22', 'mobileDisplay' => 'raw-html', 'drawerSide' => 'left;display:none' ) as $key => $value ) {
	$invalid = $with_menu;
	$invalid['rows'][0]['items'][0][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $invalid ), 'Invalid mobile menu setting accepted.' );
}
$expanded = $with_menu;
$expanded['rows'][0]['items'][0]['mobileDisplay'] = 'expanded';
$expanded['rows'][0]['items'][0]['menuLayout'] = 'vertical';
$clean = \RadmanCT\Builder\validate( $expanded );
expect( null !== $clean, 'Expanded vertical menu rejected.' );
ob_start();
\RadmanCT\Builder\render_layout( 'footer', $clean );
$html = ob_get_clean();
expect( str_contains( $html, 'data-mobile-expanded' ) && str_contains( $html, 'rdct-bld-menu--vertical' ) && ! str_contains( $html, 'data-rdct-drawer' ), 'Expanded footer menu included a drawer or lost its layout.' );
$expanded['rows'][0]['items'][0]['menuLayout'] = 'column;display:none';
expect( null === \RadmanCT\Builder\validate( $expanded ), 'Unbounded menu layout accepted.' );

$missing = $with_menu;
$missing['rows'][0]['items'][0]['menuId'] = 999;
$missing['rows'][0]['items'][0]['mobileMenuId'] = 999;
$clean = \RadmanCT\Builder\validate( $missing );
ob_start(); \RadmanCT\Builder\render_layout( 'footer', $clean ); $html = ob_get_clean();
expect( ! str_contains( $html, 'rdct-bld__row' ) && ! str_contains( $html, 'rdct-menu-toggle' ), 'Deleted menu retained an empty footer column or toggle.' );
ob_start(); \RadmanCT\Builder\render_layout( 'footer', $clean, 'default', true ); $html = ob_get_clean();
expect( str_contains( $html, 'data-builder-preview-item="main-menu"' ), 'Missing menu could no longer be selected in the editor.' );
echo "Passed: independent desktop/mobile header and footer menus, shared-menu reuse, unique IDs, missing-menu isolation and bounded drawer settings.\n";

$mega = $with_menu;
$mega['rows'][0]['items'][0] += array( 'submenuStyle' => 'mega', 'megaColumns' => 4, 'megaWidth' => 1100, 'menuSkin' => 'soft', 'drawerDevices' => array( 'desktop', 'tablet', 'mobile' ) );
$clean = \RadmanCT\Builder\validate( $mega );
expect( null !== $clean, 'Mega menu or device drawer schema rejected.' );
$styles = array(); $menu_calls = array();
ob_start(); \RadmanCT\Builder\render_layout( 'header', $clean ); $html = ob_get_clean();
expect( str_contains( $html, 'rdct-menu--skin-soft' ) && str_contains( $html, 'data-submenu-style="mega"' ) && str_contains( $html, '--rdct-mega-columns:4;--rdct-mega-width:1100px' ), 'Mega menu choices did not reach public markup.' );
expect( str_contains( $html, 'data-drawer-devices="desktop tablet"' ) && str_contains( $html, 'data-drawer-devices="mobile"' ), 'Separate menu wrappers lost their drawer device ownership.' );
expect( in_array( 'rdct-menu-mega', $styles, true ) && in_array( 'rdct-menu-drawer', $styles, true ) && in_array( 'rdct-menu-dropdown', $styles, true ), 'Used menu components were not enqueued.' );
expect( 0 === $menu_calls[0]['depth'] && str_contains( $menu_calls[0]['menu_class'], 'rdct-menu--mega' ), 'Native menu lost deeper mega links.' );
$styles = array();
ob_start(); \RadmanCT\Builder\menu( 'header', 'plain-menu', 11 ); ob_end_clean();
expect( array( 'rdct-menu-dropdown' ) === $styles, 'Unused mega/drawer CSS leaked into a plain menu.' );
foreach ( array( 'submenuStyle' => 'html', 'megaWidth' => 99999, 'megaColumns' => '3', 'menuSkin' => 'remote', 'drawerDevices' => array( 'tv' ) ) as $key => $value ) {
	$invalid = $mega; $invalid['rows'][0]['items'][0][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $invalid ), 'Invalid menu component setting accepted: ' . $key );
}
echo "Passed: mega columns and native depth, device-specific drawers, appearance, conditional components and strict validation.\n";
require dirname( __DIR__ ) . '/radmanct/inc/template-functions.php';
ob_start(); rdct_menu( 11, array( 'submenuStyle' => 'mega' ) ); rdct_menu( 11 ); $html = ob_get_clean();
preg_match_all( '/ id="([^"]+)"/', $html, $ids );
expect( count( $ids[1] ) === count( array_unique( $ids[1] ) ) && str_contains( $html, 'custom-menu-1' ) && str_contains( $html, 'custom-menu-2' ), 'PHP component helper reused DOM identifiers.' );
