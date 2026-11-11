<?php

require __DIR__ . '/builder-contract.php';

$row_appearance = \RadmanCT\Builder\appearance( array( 'background' => '#102030', 'color' => '#FAFBFC', 'mobileLayout' => 'inline' ), true );
$item_appearance = \RadmanCT\Builder\appearance( array( 'color' => '#AABBCC', 'fontSize' => 22, 'mobileFontSize' => 16 ), false );
expect( '#fafbfc' === $row_appearance['color'] && 'inline' === $row_appearance['mobileLayout'], 'Row color or mobile layout failed to normalize.' );
$row_attributes = \RadmanCT\Builder\appearance_attributes( $row_appearance, true );
expect( str_contains( $row_attributes, 'rdct-bld__row--mobile-inline' ) && str_contains( $row_attributes, '--builder-text-color:#fafbfc;' ), 'Responsive row or color was not emitted.' );
$item_attributes = \RadmanCT\Builder\appearance_attributes( $item_appearance, false );
expect( str_contains( $item_attributes, 'rdct-bld--font-custom' ) && str_contains( $item_attributes, 'rdct-bld--mobile-font-custom' ) && str_contains( $item_attributes, '--builder-font-size:22px;' ) && str_contains( $item_attributes, '--builder-mobile-font-size:16px;' ), 'Independent text sizes did not reach PHP output.' );
$default_row = \RadmanCT\Builder\appearance( array(), true );
$default_item = \RadmanCT\Builder\appearance( array(), false );
expect( ! str_contains( \RadmanCT\Builder\appearance_attributes( $default_item, false ), '--builder-custom-width:' ), 'Inactive custom sizing leaked into the default item.' );
$custom_item = \RadmanCT\Builder\appearance( array( 'width' => 'custom', 'customWidth' => 20 ), false );
expect( str_contains( \RadmanCT\Builder\appearance_attributes( $custom_item, false ), '--builder-custom-width:20%;' ), 'Active custom width was dropped.' );
expect( 'stack' === $default_row['mobileLayout'], 'Existing mobile layout changed without a selection.' );
expect( null === $default_row['mobileGap'] && null === $default_row['mobilePadding'], 'Old layouts must inherit desktop spacing on mobile.' );
expect( ! str_contains( \RadmanCT\Builder\appearance_attributes( $default_row, true ), '--builder-mobile-' ), 'Inherited mobile spacing emitted an override.' );
$mobile_spacing = \RadmanCT\Builder\appearance( array( 'gap' => 24, 'padding' => 32, 'mobileGap' => 0, 'mobilePadding' => 12 ), true );
$spacing_attributes = \RadmanCT\Builder\appearance_attributes( $mobile_spacing, true );
expect( str_contains( $spacing_attributes, '--builder-gap:24px;' ) && str_contains( $spacing_attributes, '--builder-padding:32px;' ) && str_contains( $spacing_attributes, '--builder-mobile-gap:0px;' ) && str_contains( $spacing_attributes, '--builder-mobile-padding:12px;' ), 'Mobile spacing zero or independent desktop values were lost.' );
foreach ( array( 'mobileGap' => 64, 'mobilePadding' => 120 ) as $property => $maximum ) {
	expect( $maximum === \RadmanCT\Builder\appearance( array( $property => $maximum ), true )[ $property ], 'Maximum mobile spacing was rejected.' );
	foreach ( array( -1, $maximum + 1, '12', 12.5, false, array(), '0;display:none' ) as $invalid ) {
		expect( null === \RadmanCT\Builder\appearance( array( $property => $invalid ), true ), 'Invalid mobile spacing accepted.' );
	}
}
expect( ! str_contains( \RadmanCT\Builder\appearance_attributes( $default_item, false ), '--colored' ) && ! str_contains( \RadmanCT\Builder\appearance_attributes( $default_item, false ), '-font-custom' ), 'Inherited appearance emitted explicit overrides.' );
foreach ( array( '#fff;position:fixed', 'url(https://example.test/a)', array() ) as $color ) {
	expect( null === \RadmanCT\Builder\appearance( array( 'color' => $color ), true ), 'Unsafe row color was accepted.' );
	expect( null === \RadmanCT\Builder\appearance( array( 'color' => $color ), false ), 'Unsafe item color was accepted.' );
}
foreach ( array( -1, 97, '22', 22.5, array() ) as $size ) {
	expect( null === \RadmanCT\Builder\appearance( array( 'fontSize' => $size ), false ), 'Invalid desktop size accepted.' );
	expect( null === \RadmanCT\Builder\appearance( array( 'mobileFontSize' => $size ), false ), 'Invalid mobile size accepted.' );
}
expect( null === \RadmanCT\Builder\appearance( array( 'mobileLayout' => 'display:none' ), true ), 'Unsupported mobile layout was accepted.' );
$styled_layout = $layout;
$styled_layout['rows'][0]['appearance'] = $row_appearance;
$styled_layout['rows'][0]['appearance']['mobileGap'] = 0;
$styled_layout['rows'][0]['appearance']['mobilePadding'] = 12;
$styled_layout['rows'][0]['items'][0]['appearance'] = $item_appearance;
$clean_layout = \RadmanCT\Builder\validate( $styled_layout );
expect( null !== $clean_layout && $item_appearance === $clean_layout['rows'][0]['items'][0]['appearance'], 'Appearance did not survive complete layout validation.' );
ob_start();
\RadmanCT\Builder\render_layout( 'header', $clean_layout, 'default', true );
$html = ob_get_clean();
expect( str_contains( $html, '--builder-mobile-font-size:16px;' ) && str_contains( $html, 'rdct-bld__row--mobile-inline' ), 'Preview renderer diverged from appearance settings.' );
expect( str_contains( $html, '--builder-mobile-gap:0px;' ) && str_contains( $html, '--builder-mobile-padding:12px;' ), 'Validated mobile spacing did not reach the shared PHP renderer.' );
echo "Passed: inherited appearance defaults, safe row/item color, bounded desktop/mobile text sizes and responsive row markers in PHP preview.\n";
