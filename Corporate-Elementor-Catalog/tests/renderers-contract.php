<?php

define( 'ABSPATH', '/' );
$settings = array();
$singular = 'post';
$archive = '';
$taxonomy = '';
$home = false;
$protected = false;
$matched = true;
$calls = 0;
$hooks = array();
function get_option( $key, $default ) { return $GLOBALS['settings']; }
function sanitize_text_field( $value ) { return strip_tags( $value ); }
function __( $value, $domain ) { return $value; }
function apply_filters( $hook, $value ) { return $value; }
function is_singular( $types = null ) { return null === $types ? (bool) $GLOBALS['singular'] : in_array( $GLOBALS['singular'], (array) $types, true ); }
function is_post_type_archive( $types ) { return in_array( $GLOBALS['archive'], (array) $types, true ); }
function is_tax( $types = null ) { return null === $types ? (bool) $GLOBALS['taxonomy'] : in_array( $GLOBALS['taxonomy'], (array) $types, true ); }
function is_home() { return $GLOBALS['home']; }
function is_category() { return false; }
function is_tag() { return false; }
function is_author() { return false; }
function is_date() { return false; }
function is_admin() { return false; }
function current_user_can( $cap ) { return false; }
function post_password_required() { return $GLOBALS['protected']; }
function elementor_location_exits( $location, $match ) { ++$GLOBALS['calls']; return $GLOBALS['matched']; }
function elementor_theme_do_location( $location ) { echo 'Pro-' . $location; return true; }
function add_action( $hook, $callback, ...$args ) { $GLOBALS['hooks'][ $hook ][] = $callback; }
function add_filter( $hook, $callback, ...$args ) { $GLOBALS['hooks'][ $hook ][] = $callback; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct/inc/settings.php';
require dirname( __DIR__ ) . '/radmanct/inc/renderers.php';
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor.php';

expect( ! \RadmanCT\Renderers\uses_elementor() && 0 === $calls, 'Default custom design queried Pro documents.' );
$settings['blog_single_renderer'] = 'elementor';
ob_start();
expect( \RadmanCT\Renderers\render(), 'Selected single renderer did not render.' );
expect( 'Pro-single' === ob_get_clean(), 'Incorrect Elementor location.' );
$singular = 'radman_product';
expect( ! \RadmanCT\Renderers\uses_elementor(), 'Blog choice changed product design.' );
$settings['catalog_single_renderer'] = 'elementor';
expect( \RadmanCT\Renderers\uses_elementor(), 'Product single selection was ignored.' );
$protected = true;
expect( ! \RadmanCT\Renderers\uses_elementor(), 'Protected content used Pro rendering.' );
$protected = false;
$singular = '';
$archive = 'radman_product';
expect( ! \RadmanCT\Renderers\uses_elementor(), 'Single selection changed archive design.' );
$settings['catalog_archive_renderer'] = 'elementor';
expect( \RadmanCT\Renderers\uses_elementor(), 'Product archive selection failed.' );
$archive = 'radman_portfolio';
expect( ! \RadmanCT\Renderers\uses_elementor(), 'Product choice changed portfolio design.' );
$settings['portfolio_archive_renderer'] = 'elementor';
$matched = false;
expect( ! \RadmanCT\Renderers\uses_elementor(), 'Missing matched Pro template prevented custom fallback.' );
$matched = true;
$manager = new class { public array $locations = array(); public function register_location( $name, $args ) { $this->locations[ $name ] = $args; } };
foreach ( $hooks['elementor/theme/register_locations'] as $callback ) { $callback( $manager ); }
expect( false === $manager->locations['header']['public'] && false === $manager->locations['footer']['edit_in_content'] && false === $manager->locations['archive']['overwrite'], 'Pro location registration overwrites Radman CT ownership.' );
$filter = $hooks['elementor/theme/get_location_templates/template_id'][0];
expect( -1 === $filter( 17, 'header' ) && 17 === $filter( 17, 'archive' ), 'Location matching did not preserve header or selected content ownership.' );
$settings['portfolio_archive_renderer'] = 'custom';
expect( -1 === $filter( 17, 'archive' ), 'Custom design retained a matching Pro document.' );
$bad = \RadmanCT\Settings\normalize( array( 'catalog_single_renderer' => '../template.php' ), \RadmanCT\Settings\defaults() );
expect( 'custom' === $bad['catalog_single_renderer'], 'Arbitrary template path was accepted.' );
echo "Passed: independent section/single/archive renderers, native fallback, protected content, Pro template filtering and header/footer ownership.\n";
