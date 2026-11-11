<?php

require __DIR__ . '/content-blocks-contract.php';
$navigation_meta = array( 100 => 80 );
function get_post_meta( $id, $key, $single ) { return '_rdct_mega_block' === $key ? ( $GLOBALS['navigation_meta'][ $id ] ?? 0 ) : ''; }
require dirname( __DIR__ ) . '/radmanct/inc/navigation.php';
pattern( 80, '<!-- wp:image --><figure><img id="photo" src="/logo.png" fetchpriority="high"></figure><!-- /wp:image -->' );
$items = array( (object) array( 'ID' => 100, 'menu_item_parent' => 0 ), (object) array( 'ID' => 101, 'menu_item_parent' => 100 ), (object) array( 'ID' => 102, 'menu_item_parent' => 0 ), (object) array( 'ID' => 103, 'menu_item_parent' => 101 ) );
$args = (object) array( 'rdct_menu_scope' => 'rdct-test' );
$scope = \RadmanCT\Navigation\MegaContent\scope( 'rdct-test', 'mega' );
$prepare = $scope['wp_nav_menu_objects'][0];
expect( $items === $prepare( $items, (object) array() ), 'Foreign menu objects were changed.' );
$prepared = $prepare( $items, $args );
expect( array( 100, 102 ) === array_column( $prepared, 'ID' ), 'Only descendants replaced by a valid block should be removed.' );
$content = $scope['walker_nav_menu_start_el'][0];
$html = $content( '<a>Item</a>', $items[0], 0, $args );
expect( str_contains( $html, 'rdct-test-block-100-photo' ) && str_contains( $html, 'loading="lazy"' ) && ! str_contains( $html, 'fetchpriority' ), 'Mega content lost scoped IDs or requested eager image loading.' );
expect( '<a>Item</a>' === $content( '<a>Item</a>', $items[0], 0, (object) array() ), 'Foreign menu markup gained a Radman block.' );
$navigation_meta[100] = 3;
$fallback = \RadmanCT\Navigation\MegaContent\scope( 'rdct-test', 'mega' );
expect( $items === $fallback['wp_nav_menu_objects'][0]( $items, $args ), 'Invalid or unpublished content removed fallback navigation.' );
echo "Passed: scoped mega content, branch replacement, lazy images and unpublished-block fallback.\n";
