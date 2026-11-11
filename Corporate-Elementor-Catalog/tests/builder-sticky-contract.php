<?php

require __DIR__ . '/builder-contract.php';
require dirname( __DIR__ ) . '/radmanct/inc/settings.php';
require dirname( __DIR__ ) . '/radmanct/inc/page-effects.php';

function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_template_directory_uri() { return 'https://radman.test/wp-content/themes/radmanct'; }
function wp_enqueue_style( $handle, ...$args ) { $GLOBALS['styles'][] = $handle; }
function is_admin_bar_showing() { return false; }

expect( 'off' === $valid['sticky'], 'Old layouts did not default to sticky off.' );
$sticky = $layout;
$sticky['sticky'] = 'desktop';
$clean = \RadmanCT\Builder\validate( $sticky );
expect( 'desktop' === $clean['sticky'], 'Desktop sticky setting was not retained.' );
foreach ( array( true, 1, array(), 'position:fixed', 'unknown' ) as $value ) {
	$bad = $layout;
	$bad['sticky'] = $value;
	expect( null === \RadmanCT\Builder\validate( $bad ), 'Invalid sticky setting was accepted.' );
}
$allow = true;
$active = false;
$before = $queries;
expect( ! \RadmanCT\Builder\sticky_header() && $before === $queries, 'Inactive sticky check read layout data.' );
$saved['radman_settings'] = array( 'smooth_scroll' => true );
expect( \RadmanCT\PageEffects\values()['smooth_scroll'], 'Normal header suppressed smooth scrolling.' );
$active = true;
$saved['radman_builder_header'] = $clean + array( 'revision' => 1 );
expect( \RadmanCT\Builder\sticky_header(), 'Selected sticky header was not recognized.' );
expect( ! \RadmanCT\PageEffects\values()['smooth_scroll'] && $saved['radman_settings']['smooth_scroll'], 'Native-scroll guard did not preserve saved user settings.' );
$styles = array();
\RadmanCT\Builder\enqueue();
expect( in_array( 'rdct-sticky-header', $styles, true ), 'Active sticky header did not enqueue its independent CSS.' );
ob_start();
\RadmanCT\Builder\render_layout( 'header', $clean );
expect( str_contains( ob_get_clean(), 'rdct-bld--sticky-desktop' ), 'Public header lacks sticky class.' );
ob_start();
\RadmanCT\Builder\render_layout( 'header', $clean, 'default', true );
expect( ! str_contains( ob_get_clean(), 'rdct-bld--sticky-' ), 'Editor preview incorrectly applied sticky behavior.' );
ob_start();
\RadmanCT\Builder\render_layout( 'footer', $clean );
expect( ! str_contains( ob_get_clean(), 'rdct-bld--sticky-' ), 'Sticky behavior leaked into the footer.' );
$saved['radman_builder_header']['sticky'] = 'off';
$styles = array();
\RadmanCT\Builder\enqueue();
expect( ! in_array( 'rdct-sticky-header', $styles, true ) && \RadmanCT\PageEffects\values()['smooth_scroll'], 'Sticky off left extra CSS or suppressed scrolling.' );
echo "Passed: sticky schema, inactive isolation, independent CSS, public-only header behavior and native scrolling fallback without changing saved settings.\n";
