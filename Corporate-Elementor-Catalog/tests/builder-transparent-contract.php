<?php

require __DIR__ . '/builder-sticky-contract.php';
expect( false === $valid['transparent'], 'Legacy header became transparent.' );
$overlay = $layout; $overlay['transparent'] = true; $overlay['sticky'] = 'all';
$clean = \RadmanCT\Builder\validate( $overlay );
expect( true === $clean['transparent'], 'Transparent header setting was lost.' );
foreach ( array( 'yes', 1, array() ) as $bad ) {
	$overlay['transparent'] = $bad;
	expect( null === \RadmanCT\Builder\validate( $overlay ), 'Invalid transparency accepted.' );
}
foreach ( array( array( 'header', false, true ), array( 'header', true, false ), array( 'footer', false, false ) ) as [ $kind, $preview, $expected ] ) {
	ob_start(); \RadmanCT\Builder\render_layout( $kind, $clean, 'default', $preview ); $html = ob_get_clean();
	expect( str_contains( $html, 'data-rdct-transparent-header' ) === $expected, 'Transparency leaked into footer/isolated preview or public marker was lost.' );
}
$saved['radman_builder_header'] = $clean; $styles = array();
\RadmanCT\Builder\enqueue();
expect( in_array( 'rdct-transparent-header', $styles, true ), 'Transparent header CSS missing.' );
$saved['radman_builder_header']['transparent'] = false; $styles = array();
\RadmanCT\Builder\enqueue();
expect( ! in_array( 'rdct-transparent-header', $styles, true ), 'Transparency off loaded its CSS.' );
echo "Passed: transparent header schema, default-off compatibility, isolated preview/footer and conditional CSS.\n";
