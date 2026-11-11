<?php

define( 'ABSPATH', '/usr/src/wordpress/' );
require ABSPATH . 'wp-includes/plugin.php';
require __DIR__ . '/../radmanct/inc/navigation.php';
$fail = false;
function wp_nav_menu( $args ) {
	if ( $GLOBALS['fail'] ) { throw new RuntimeException( 'Menu renderer failed.' ); }
	$id = apply_filters( 'nav_menu_item_id', 'menu-item-42', (object) array( 'ID' => 42 ), (object) $args, 0 );
	echo '<ul id="' . htmlspecialchars( $args['menu_id'] ) . '"><li id="' . htmlspecialchars( $id ) . '">Item</li></ul>';
	if ( 'menu-item-42' !== apply_filters( 'nav_menu_item_id', 'menu-item-42', (object) array( 'ID' => 42 ), (object) array(), 0 ) ) { throw new RuntimeException( 'Filter affected another menu.' ); }
}
ob_start();
foreach ( array( 'rdct-primary-menu-list', 'rdct-footer-menu-list', 'rdct-header-main-desktop-list', 'rdct-header-main-mobile-list' ) as $scope ) {
	\RadmanCT\Navigation\menu( array( 'menu_id' => $scope, 'fallback_cb' => false ) );
}
$html = ob_get_clean();
preg_match_all( '/ id="([^"]+)"/', $html, $ids );
if ( count( $ids[1] ) !== count( array_unique( $ids[1] ) ) || has_filter( 'nav_menu_item_id' ) ) { throw new RuntimeException( 'Duplicate IDs or lingering filter.' ); }
$fail = true;
try { \RadmanCT\Navigation\menu( array( 'menu_id' => 'rdct-error' ) ); } catch ( RuntimeException $error ) {}
if ( has_filter( 'nav_menu_item_id' ) ) { throw new RuntimeException( 'Filter leaked after renderer failure.' ); }
echo "Passed: real WordPress hook isolation, repeated native menu IDs, scoped header/footer identifiers and exception cleanup.\n";
