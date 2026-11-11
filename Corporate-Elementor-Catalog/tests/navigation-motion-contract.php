<?php

require __DIR__ . '/builder-menu-contract.php';

use RadmanCT\Navigation\Component as Menu;
use RadmanCT\Navigation\Presentation as Presentation;
expect( null !== Menu\settings( array( 'menuGap' => 0, 'laptopMenuGap' => 40, 'mobileMenuGap' => 12 ) ), 'Responsive menu spacing rejected explicit zero.' );
expect( null === Menu\settings( array( 'menuGap' => '32' ) ) && null === Menu\settings( array( 'tabletMenuGap' => 129 ) ), 'Menu spacing accepted invalid types or out-of-range values.' );
expect( '--rdct-menu-gap-desktop:0px;--rdct-menu-gap-mobile:12px;' === Menu\spacing_style( array( 'menuGap' => 0, 'mobileMenuGap' => 12, 'tabletMenuGap' => null ) ), 'Menu spacing did not preserve inheritance or zero.' );
expect( array( 'panelMode' => array( 'sidebar' ) ) === Presentation\fields()['panelWidth']['condition'], 'Sidebar width control is visible for layouts that do not use it.' );

foreach ( array( 'sidebar', 'fullscreen', 'split' ) as $layout ) {
	foreach ( array( 'standard', 'curtain', 'wave' ) as $effect ) {
		$options = Menu\settings( array( 'mobileDisplay' => 'drawer', 'panelMode' => $layout, 'panelMotion' => $effect ) );
		expect( null !== $options, 'Valid panel effect/layout combination was rejected.' );
		$assets = Menu\asset_names( $options );
		expect( in_array( 'menu-panel-' . $layout, $assets, true ), 'Selected panel layout is missing.' );
		expect( in_array( 'menu-panel-motion', $assets, true ) === ( 'standard' !== $effect ), 'Motion CSS leaks into the standard panel.' );
		expect( in_array( 'menu-panel-wave', $assets, true ) === ( 'wave' === $effect ), 'Wave CSS leaks into another effect.' );
		foreach ( array_diff( array( 'sidebar', 'fullscreen', 'split' ), array( $layout ) ) as $other ) { expect( ! in_array( 'menu-panel-' . $other, $assets, true ), 'Unrelated layout CSS was requested.' ); }
	}
}
$inline = Menu\settings( array( 'panelMotion' => 'wave' ) );
expect( array( 'menu-dropdown' ) === Menu\asset_names( $inline ), 'A menu without a panel requests panel effects.' );
foreach ( array( 'panelMotion' => 'raw-script', 'panelMotionDuration' => 20, 'panelMotionColor' => 'red;display:none', 'panelLinkLayout' => 'raw-html' ) as $key => $value ) { expect( null === Presentation\validate( array( $key => $value ) ), 'Unsafe panel option was accepted: ' . $key ); }
$options = Menu\settings( array( 'mobileDisplay' => 'drawer', 'panelMotion' => 'wave', 'panelLinkLayout' => 'vertical', 'panelMotionColor' => '#123456' ) );
ob_start(); Menu\render( 'header', 'motion', 11, $options ); $html = ob_get_clean();
foreach ( array( 'data-panel-motion="wave"', 'data-panel-duration="800"', 'data-panel-link-layout="vertical"', 'data-panel-motion-color="#123456"' ) as $attribute ) { expect( str_contains( $html, $attribute ), 'Shared menu rendering omitted: ' . $attribute ); }
echo "Passed: all panel layout/effect combinations, conditional styles, safe settings and shared PHP markup.\n";
