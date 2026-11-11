<?php

require __DIR__ . '/settings-contract.php';
use RadmanCT\Analytics;

$analytics_context = array(); $analytics_scripts = array(); $analytics_inline = array();
function is_admin() { return ! empty( $GLOBALS['analytics_context']['admin'] ); }
function is_feed() { return ! empty( $GLOBALS['analytics_context']['feed'] ); }
function is_preview() { return ! empty( $GLOBALS['analytics_context']['preview'] ); }
function is_customize_preview() { return ! empty( $GLOBALS['analytics_context']['customizer'] ); }
function wp_script_is( $handle, $status ) { return isset( $GLOBALS['analytics_scripts'][ $handle ] ); }
function wp_enqueue_script( $handle, ...$args ) { $GLOBALS['analytics_scripts'][ $handle ] = $args; }
function wp_add_inline_script( ...$args ) { $GLOBALS['analytics_inline'][] = $args; }
function wp_json_encode( $value ) { return json_encode( $value ); }

expect( '' === Analytics\measurement_id( '' ) && null === Analytics\measurement_id( array() ), 'Invalid input types were accepted.' );
expect( 'G-ABC1234567' === Analytics\measurement_id( 'g-abc1234567' ), 'GA4 ID was not normalized.' );
$snippet = '<script async src="https://www.googletagmanager.com/gtag/js?id=G-ABC1234567"></script><script>gtag("config","G-ABC1234567");</script>';
expect( 'G-ABC1234567' === Analytics\measurement_id( $snippet ), 'Standard tag snippet was not reduced to its ID.' );
expect( null === Analytics\measurement_id( 'G-ABC1234567 G-ZYX9876543' ) && null === Analytics\measurement_id( 'UA-12345678-1' ), 'Ambiguous or obsolete tags were accepted.' );
expect( null === Analytics\measurement_id( str_repeat( 'x', 8193 ) ), 'Oversized tag input was accepted.' );
$saved['analytics_enabled'] = false; $saved['analytics_measurement_id'] = 'G-ABC1234567';
Analytics\enqueue(); expect( ! $analytics_scripts && ! $analytics_inline, 'Disabled tracking emitted assets.' );
$normalized = \RadmanCT\Settings\sanitize( array( 'analytics_measurement_id' => $snippet, 'analytics_enabled' => '1' ) );
expect( true === $normalized['analytics_enabled'] && 'G-ABC1234567' === $normalized['analytics_measurement_id'], 'Tag settings were not normalized.' );
$saved = $normalized;
foreach ( array( 'admin', 'feed', 'preview', 'customizer' ) as $context ) {
	$analytics_context = array( $context => true ); Analytics\enqueue();
	expect( ! $analytics_scripts, 'Tracking leaked into an excluded context.' );
}
$analytics_context = array(); $settings_caps['edit_theme_options'] = true;
Analytics\enqueue(); expect( ! $analytics_scripts, 'Administrator visits were tracked.' );
$settings_caps = array(); $_GET['elementor-preview'] = '42';
Analytics\enqueue(); expect( ! $analytics_scripts, 'Elementor preview was tracked.' ); unset( $_GET['elementor-preview'] );
Analytics\enqueue(); Analytics\enqueue();
$script = $analytics_scripts['rdct-google-analytics'];
expect( 'https://www.googletagmanager.com/gtag/js?id=G-ABC1234567' === $script[0] && array( 'strategy' => 'async', 'in_footer' => false ) === $script[3], 'Tracking was not async in head.' );
expect( 1 === count( $analytics_inline ) && 'before' === $analytics_inline[0][2] && str_contains( $analytics_inline[0][1], 'gtag("config","G-ABC1234567")' ), 'Initialization order or deduplication failed.' );
echo "Analytics contracts passed.\n";
