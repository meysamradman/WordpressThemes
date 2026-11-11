<?php

define( 'ABSPATH', __DIR__ );
define( 'RDCT_COMPONENTS_FILE', '/plugins/radmanct-components/plugin.php' );
$GLOBALS['hooks'] = array();
$GLOBALS['purges'] = 0;
function add_filter( $hook, $callback, ...$args ) { $GLOBALS['hooks'][ $hook ][ $callback ] = true; }
function add_action( $hook, $callback, ...$args ) { add_filter( $hook, $callback ); }
function has_action( $hook, $callback ) { return isset( $GLOBALS['hooks'][ $hook ][ $callback ] ); }
function get_template_directory_uri() { return 'https://example.test/custom/themes/radmanct'; }
function get_stylesheet_directory_uri() { return 'https://example.test/custom/themes/client'; }
function get_stylesheet() { return 'client'; }
function plugins_url( $path, $file ) { return 'https://example.test/custom/plugins/radmanct-components/' . $path; }
function wp_parse_url( $url, $component ) { return parse_url( $url, $component ); }
function rocket_clean_domain() { ++$GLOBALS['purges']; }
function get_current_screen() { return $GLOBALS['screen'] ?? null; }
require __DIR__ . '/../radmanct/inc/integrations/rocket.php';
function check( $condition ) { if ( ! $condition ) { throw new RuntimeException( 'Rocket contract failed.' ); } }
$excluded = \RadmanCT\Integrations\Rocket\delay_exclusions( array( 'existing' ) );
check( count( $excluded ) === 5 && $excluded[0] === 'existing' && in_array( 'rdct-color-scheme-init', $excluded, true ) );
check( $excluded === \RadmanCT\Integrations\Rocket\delay_exclusions( $excluded ) );
check( preg_match( '#' . $excluded[1] . '#', '/custom/themes/radmanct/dist/js/frontend-hash.min.js' ) === 1 );
check( preg_match( '#' . $excluded[1] . '#', '/custom/plugins/other/main.js' ) === 0 );
\RadmanCT\Integrations\Rocket\option_changed( 'unrelated_option' );
check( empty( $GLOBALS['hooks']['shutdown'] ) );
foreach ( array( 'radman_settings', 'radman_builder_header__l123', 'theme_mods_client' ) as $option ) { \RadmanCT\Integrations\Rocket\option_changed( $option ); }
\RadmanCT\Integrations\Rocket\queue_purge();
check( count( $GLOBALS['hooks']['shutdown'] ) === 1 );
foreach ( array_keys( $GLOBALS['hooks']['shutdown'] ) as $callback ) { $callback(); }
check( $GLOBALS['purges'] === 1 );
check( \RadmanCT\Integrations\Rocket\activation_notice( true ) );
$GLOBALS['screen'] = (object) array( 'id' => 'toplevel_page_rdct-settings' );
check( ! \RadmanCT\Integrations\Rocket\activation_notice( true ) );
$GLOBALS['screen']->id = 'plugins';
check( \RadmanCT\Integrations\Rocket\activation_notice( true ) );
check( ! \RadmanCT\Integrations\Rocket\activation_notice( false ) );
echo "Passed: bounded delay exclusions and one cache purge per relevant save request.\n";
