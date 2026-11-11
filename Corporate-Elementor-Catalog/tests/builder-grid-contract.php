<?php

require __DIR__ . '/builder-contract.php';
expect( 'flex' === $valid['rows'][0]['appearance']['rowLayout'], 'Legacy row stopped using flex.' );
$flow = $layout;
$flow['rows'][0]['appearance'] = array( 'direction' => 'column', 'tabletDirection' => 'row', 'mobileDirection' => 'column', 'wrap' => 'nowrap' );
$flow_clean = \RadmanCT\Builder\validate( $flow );
expect( null !== $flow_clean, 'Valid container directions were rejected.' );
ob_start(); \RadmanCT\Builder\render_layout( 'header', $flow_clean ); $flow_html = ob_get_clean();
foreach ( array( 'rdct-bld__row--flow-column', 'rdct-bld__row--tablet-flow-row', 'rdct-bld__row--mobile-flow-column', '--builder-wrap:nowrap;' ) as $marker ) { expect( str_contains( $flow_html, $marker ), 'Container flow marker missing: ' . $marker ); }
foreach ( array( 'direction' => 'diagonal', 'tabletDirection' => 'diagonal', 'wrap' => 'anything' ) as $key => $value ) {
	$invalid_flow = $flow; $invalid_flow['rows'][0]['appearance'][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $invalid_flow ), 'Invalid container flow was accepted.' );
}
for ( $columns = 1; $columns <= 6; ++$columns ) {
	$grid = $layout;
	$grid['rows'][0]['appearance'] = array( 'rowLayout' => 'grid', 'columns' => $columns, 'mobileColumns' => 2, 'gap' => 0, 'mobileGap' => 12, 'padding' => 0, 'mobilePadding' => 8 );
	$clean = \RadmanCT\Builder\validate( $grid );
	expect( null !== $clean && 'start' === $clean['rows'][0]['appearance']['align'], 'Grid columns or top alignment rejected.' );
	ob_start(); \RadmanCT\Builder\render_layout( 'footer', $clean ); $html = ob_get_clean();
	foreach ( array( 'rdct-bld__row--grid', '--builder-columns:' . $columns . ';', '--builder-mobile-columns:2;', '--builder-gap:0px;', '--builder-mobile-gap:12px;', '--builder-mobile-padding:8px;' ) as $value ) {
		expect( str_contains( $html, $value ), 'Footer grid lost column count or independent spacing: ' . $value );
	}
}
foreach ( array( array( 'columns', 0 ), array( 'columns', 7 ), array( 'columns', '3' ), array( 'mobileColumns', 4 ), array( 'rowLayout', 'display:none' ) ) as [ $key, $value ] ) {
	$grid['rows'][0]['appearance'][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $grid ), 'Unbounded grid setting accepted.' );
	unset( $grid['rows'][0]['appearance'][ $key ] );
}
echo "Passed: footer grid columns 1-6, mobile columns, independent zero/mobile spacing, bounded schema and flex compatibility.\n";
