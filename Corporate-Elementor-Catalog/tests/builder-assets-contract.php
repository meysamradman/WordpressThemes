<?php

namespace RadmanCT\Assets {

function style( $handle, ...$args ) {
	$GLOBALS['builder_styles'][] = $handle;
	return true;
}

}

namespace {

require __DIR__ . '/wpml-builder-contract.php';

function is_singular( $type ) { return 'page' === $type && $GLOBALS['builder_page']; }
function is_home() { return false; }
function get_queried_object_id() { return 700; }
function get_page_template_slug( $id = null ) { return $GLOBALS['builder_template']; }

use RadmanCT\Builder\Storage as Storage;
use function RadmanCT\Builder\enqueue;

$builder_page = true; $builder_template = ''; $language = 'fa';
$plain = RadmanCT\Builder\validate( array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'row1', 'items' => array( array( 'id' => 'item1', 'type' => 'text', 'text' => 'Header content' ) ) ) ) ) );
$home = Storage\create( 'header', 'Home effects', array_replace( $plain, array( 'sticky' => 'all', 'transparent' => true ) ) );
$site = Storage\create( 'header', 'Other pages', $plain );
$footer = Storage\create( 'footer', 'Shared footer', $plain );
set_theme_mod( 'radman_builder_defaults', array( 'header' => array( 'all' => $site, 'en' => $home ), 'footer' => array( 'all' => $footer ) ) );
$collect = static function () { $GLOBALS['builder_styles'] = array(); enqueue(); return $GLOBALS['builder_styles']; };

expect( array( 'rdct-builder' ) === $collect(), 'Unselected Home effects leaked into the site header, or shared base duplicated.' );
update_post_meta( 700, '_radman_header_layout', $home );
expect( array( 'rdct-builder', 'rdct-sticky-header', 'rdct-transparent-header' ) === $collect(), 'Page-specific header assets did not follow the selected layout.' );
$builder_template = 'elementor_canvas';
expect( array() === $collect(), 'Canvas received unused Theme header/footer assets.' );
expect( $home === RadmanCT\Builder\selection( 'header' ), 'Canvas changed the saved Page assignment.' );
$builder_template = 'elementor_header_footer';
expect( in_array( 'rdct-sticky-header', $collect(), true ), 'Elementor full-width lost the selected Theme header.' );
$meta[700]['_radman_header_layout'] = 'none';
$meta[700]['_radman_footer_layout'] = 'none';
expect( array() === $collect(), 'Explicitly hidden header/footer received builder assets.' );
unset( $meta[700]['_radman_header_layout'], $meta[700]['_radman_footer_layout'] );
$language = 'en';
expect( in_array( 'rdct-transparent-header', $collect(), true ), 'Language-specific asset selection was ignored.' );
$builder_page = false; $language = 'fa';
expect( array( 'rdct-builder' ) === $collect(), 'Page-only assets leaked into other views.' );
echo "Passed: selected Page/site/language assets, shared base once, unselected layout isolation, Canvas omission, Elementor full-width and hidden-layout omission. Stub WordPress hooks only.\n";

}
