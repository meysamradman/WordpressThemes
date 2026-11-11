<?php

require __DIR__ . '/builder-contract.php';
$responsive = $layout;
$responsive['rows'][0]['appearance'] = array( 'rowLayout' => 'grid', 'columns' => 4, 'laptopColumns' => 3, 'tabletColumns' => 2, 'mobileColumns' => 1, 'laptopGap' => 0, 'tabletPadding' => 0, 'hiddenDevices' => array( 'mobile' ) );
$responsive['rows'][0]['items'][0]['appearance'] = array( 'hiddenDevices' => array( 'desktop', 'tablet' ), 'laptopFontSize' => 18, 'tabletFontSize' => 22, 'laptopWidth' => 'full', 'tabletWidth' => 'auto', 'mobileWidth' => 'equal' );
$clean = \RadmanCT\Builder\validate( $responsive );
expect( null !== $clean, 'Responsive layout rejected.' );
$responsive['rows'][0]['appearance'] += array( 'tabletAlign' => 'stretch', 'laptopJustify' => 'space-between', 'tabletLayout' => 'stack' );
$responsive['rows'][0]['items'][0]['appearance']['mobileAlign'] = 'center';
$clean = \RadmanCT\Builder\validate( $responsive );
expect( null !== $clean, 'Responsive alignment/flow rejected.' );
ob_start(); \RadmanCT\Builder\render_layout( 'footer', $clean ); $aligned_html = ob_get_clean();
foreach ( array( 'rdct-bld--tablet-align', '--builder-tablet-align:stretch;', '--builder-laptop-justify:space-between;', 'rdct-bld__row--tablet-stack', '--builder-mobile-align:center;' ) as $marker ) {
	expect( str_contains( $aligned_html, $marker ), 'Responsive alignment marker missing: ' . $marker );
}
foreach ( array( 'header', 'footer' ) as $kind ) {
	ob_start(); \RadmanCT\Builder\render_layout( $kind, $clean ); $html = ob_get_clean();
	foreach ( array( '--builder-laptop-columns:3;', '--builder-tablet-columns:2;', '--builder-laptop-gap:0px;', '--builder-tablet-padding:0px;', 'rdct-bld--hide-mobile', 'rdct-bld--hide-desktop', 'rdct-bld--hide-tablet', '--builder-laptop-font-size:18px;', '--builder-tablet-font-size:22px;', 'rdct-bld__item--laptop-full', 'rdct-bld__item--tablet-auto', 'rdct-bld__item--mobile-equal' ) as $marker ) {
		expect( str_contains( $html, $marker ), $kind . ' lost responsive marker: ' . $marker );
	}
}
$responsive['rows'][0]['appearance']['laptopColumns'] = null;
expect( null === \RadmanCT\Builder\validate( $responsive )['rows'][0]['appearance']['laptopColumns'], 'Inherited columns changed.' );
foreach ( array( array( 'mobile', 'mobile' ), array( array( 'tablet' ) ), array( 'watch' ), array( 'x' => 'tablet' ) ) as $invalid ) {
	$copy = $responsive; $copy['rows'][0]['appearance']['hiddenDevices'] = $invalid;
	expect( null === \RadmanCT\Builder\validate( $copy ), 'Malformed hidden devices accepted.' );
}
foreach ( array( array( 'tabletColumns', 7 ), array( 'laptopGap', -1 ), array( 'tabletPadding', '12' ) ) as [ $key, $value ] ) {
	$copy = $responsive; $copy['rows'][0]['appearance'][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $copy ), 'Unbounded responsive row accepted.' );
}
foreach ( array( array( 'tabletAlign', 'left' ), array( 'laptopJustify', 'script' ), array( 'tabletLayout', 'absolute' ) ) as [ $key, $value ] ) {
	$copy = $responsive; $copy['rows'][0]['appearance'][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $copy ), 'Invalid alignment/flow accepted.' );
}
foreach ( array( array( 'tabletFontSize', 97 ), array( 'laptopWidth', 'fixed' ) ) as [ $key, $value ] ) {
	$copy = $responsive; $copy['rows'][0]['items'][0]['appearance'][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $copy ), 'Unbounded responsive item accepted.' );
}
echo "Passed: header/footer responsive output, inheritance, zero spacing, hidden devices and schema bounds.\n";

$row = array( 'hiddenDevices' => array( 'tablet' ) );
$items = array( array( 'visibility' => 'desktop', 'hiddenDevices' => array( 'laptop' ) ), array( 'visibility' => 'all', 'hiddenDevices' => array( 'laptop', 'mobile' ) ) );
$collapsed = \RadmanCT\Builder\Responsive\collapse( $row, $items );
expect( array( 'tablet', 'laptop', 'mobile' ) === $collapsed['hiddenDevices'], 'Empty responsive row retained spacing or overwrote explicit hiding.' );
$items[] = array( 'visibility' => 'mobile' );
expect( array( 'tablet', 'laptop' ) === \RadmanCT\Builder\Responsive\collapse( $row, $items )['hiddenDevices'], 'Visible mobile child was collapsed.' );
$empty = \RadmanCT\Builder\validate( array( 'version' => 1, 'enabled' => false, 'rows' => array( array( 'id' => 'empty-row', 'items' => array() ) ) ) );
ob_start(); \RadmanCT\Builder\render_layout( 'footer', $empty ); $html = ob_get_clean();
expect( ! str_contains( $html, 'rdct-bld__row' ), 'Empty public row retained padding.' );
ob_start(); \RadmanCT\Builder\render_layout( 'footer', $empty, 'default', true ); $html = ob_get_clean();
expect( str_contains( $html, 'data-builder-preview-row="empty-row"' ) && ! str_contains( $html, 'rdct-bld--hide-' ), 'Empty editor drop target disappeared.' );
echo "Passed: responsive empty-row collapse, explicit visibility and editable empty preview.\n";
