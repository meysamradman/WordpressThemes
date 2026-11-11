<?php

require __DIR__ . '/wpml-builder-contract.php';

function is_singular( $type ) { return 'page' === $type && 'page' === $GLOBALS['view']; }
function is_home() { return 'posts-page' === $GLOBALS['view']; }
function get_queried_object_id() { return 200; }

use RadmanCT\Builder\Storage as Storage;
use function RadmanCT\Builder\selection;

$layout = array( 'version' => 1, 'enabled' => true, 'rows' => array() );
$site = Storage\create( 'header', 'Site', $layout );
$home = Storage\create( 'header', 'Home', $layout );
$footer = Storage\create( 'footer', 'Site footer', $layout );
set_theme_mod( 'radman_builder_defaults', array( 'header' => array( 'all' => $site, 'en' => $home ), 'footer' => array( 'all' => $footer ) ) );
$language = 'fa'; $view = 'page';
expect( $site === selection( 'header' ), 'Page did not inherit site header.' );
update_post_meta( 200, '_radman_header_layout', $home );
expect( $home === selection( 'header' ) && $footer === selection( 'footer' ), 'Home override changed another kind or was ignored.' );
$view = 'archive';
expect( $site === selection( 'header' ), 'Page header leaked to the archive.' );
$language = 'en';
expect( $home === selection( 'header' ), 'Language default was ignored.' );
$view = 'posts-page'; $language = 'fa';
expect( $home === selection( 'header' ), 'Assigned native posts Page was ignored.' );
update_post_meta( 200, '_radman_header_layout', 'none' );
expect( 'none' === selection( 'header' ), 'Explicit hidden layout did not win.' );
update_post_meta( 200, '_radman_header_layout', 'lffffffffffffffffffffffffffffffff' );
expect( $site === selection( 'header' ), 'Missing Page layout did not fall back safely.' );
echo "Passed: Page/Home overrides, site/language defaults, independent footer, native posts Page, explicit hiding and missing-layout fallback. Stub WordPress hooks only.\n";
