<?php

define( 'ABSPATH', '/' );
$removed = array();
$admin = false;
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function remove_action( $hook, $callback ) { $GLOBALS['removed'][] = $hook; }
function is_admin() { return $GLOBALS['admin']; }
function wp_parse_url( $url, $component ) { return parse_url( $url, $component ); }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor.php';
$hints = array( '//fonts.googleapis.com', 'fonts.gstatic.com', array( 'href' => 'https://use.typekit.net' ), 'https://local.test' );
expect( \RadmanCT\Integrations\Elementor\hints( $hints, 'preconnect' ) === array( 'https://local.test' ), 'Font hints were not blocked across URL formats.' );
expect( \RadmanCT\Integrations\Elementor\hints( $hints, 'preload' ) === $hints, 'Unrelated hint types were modified.' );
$control = array( 'name' => 'typography_font_family', 'selectors' => array( '{{WRAPPER}} h2' => 'font-family: "{{VALUE}}";font-size:24px;' ) );
$result = \RadmanCT\Integrations\Elementor\selectors( $control );
expect( str_contains( $result['selectors']['{{WRAPPER}} h2'], 'var(--rdct-elementor-font,var(--rdct-font-body))' ) && str_contains( $result['selectors']['{{WRAPPER}} h2'], 'font-size:24px' ), 'Text family ownership lost unrelated typography.' );
$control['name'] = 'selected_icon';
expect( \RadmanCT\Integrations\Elementor\selectors( $control ) === $control, 'Unrelated icon control changed.' );
\RadmanCT\Integrations\Elementor\pro_fonts( new stdClass() );
expect( count( $removed ) === 5 && ! in_array( 'elementor/icons/register', $removed, true ), 'Pro text-font boundaries changed icon hooks.' );
$admin = true;
\RadmanCT\Integrations\Elementor\pro_fonts( new stdClass() );
expect( count( $removed ) === 5, 'Admin font-management actions were detached.' );
echo "Passed: local font hints, scoped CSS family control and Pro frontend-only font boundaries.\n";
