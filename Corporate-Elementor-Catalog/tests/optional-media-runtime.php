<?php

require getenv( 'RDCT_WP_ROOT' ) . '/wp-load.php';
require_once WP_PLUGIN_DIR . '/radmanct-components/inc/components/button.php';
require_once WP_PLUGIN_DIR . '/radmanct-components/inc/components/icon-box.php';
require_once WP_PLUGIN_DIR . '/radmanct-components/inc/components/box.php';

function media_expect( bool $condition, string $message ): void {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}

use RadmanCTComponents\Components as Components;
use RadmanCTComponents\Components\Button as Button;
use RadmanCTComponents\Components\IconBox as IconBox;
use RadmanCTComponents\Components\Box as Box;

foreach ( array( '', 'missing-icon' ) as $icon ) {
	$options = array( 'label' => 'Text only', 'icon' => $icon, 'icon_effect' => 'swap', 'icon_magnetic' => true, 'icon_ripple' => true );
	$html = Button\render( $options );
	media_expect( ! str_contains( $html, 'rdct-button__icon' ) && ! str_contains( $html, 'rdct-button__text' ) && ! str_contains( $html, 'data-rdct-button' ), 'Absent button icon left motion/markup.' );
	media_expect( str_contains( $html, 'data-icon-effect="none"' ), 'Absent button icon retained its swap effect.' );
	media_expect( ! Components\needs_script( 'button', $options ) && ! in_array( 'button-icon-motion', Button\assets( $options ), true ), 'Absent icon loaded motion assets.' );
	media_expect( 'swap' === Button\settings( $options )['icon_effect'] && $icon === Button\settings( $options )['icon'], 'Optional output state altered saved settings.' );
	$liquid = $options + array( 'text_effect' => 'liquid-x' );
	media_expect( Components\needs_script( 'button', $liquid ), 'Removing the icon disabled independent text motion.' );
}

foreach ( array( 'top', 'inline', 'side' ) as $layout ) {
	foreach ( array( array( 'icon_type' => 'none' ), array( 'icon' => '' ), array( 'icon' => 'missing-icon' ), array( 'icon_type' => 'image', 'image_id' => 0 ), array( 'icon_type' => 'image', 'image_id' => 999999999 ) ) as $media ) {
		$options = $media + array( 'layout' => $layout, 'title' => 'Text card', 'animation' => 'draw', 'shape' => 'circle' );
		$html = IconBox\render( $options );
		media_expect( str_contains( $html, '>Text card</h3>' ) && ! str_contains( $html, 'rdct-ib__icon' ) && ! str_contains( $html, 'rdct-ib__content' ), 'Empty icon/content wrapper remained in ' . $layout );
		$styles = Components\requirements( 'icon-box', $options )['styles'];
		media_expect( ! in_array( 'icons', $styles, true ) && ! in_array( 'icon-box-animation', $styles, true ), 'Absent icon loaded icon/animation styles.' );
		$html = IconBox\render( $options + array( 'text' => '<p>Description</p>' ) );
		media_expect( str_contains( $html, 'rdct-ib__content' ) && str_contains( $html, 'Description' ), 'Text-only card lost its content.' );
		$rules = Components\Presentation\declarations( 'layout', $layout, array() );
		foreach ( array_keys( $rules ) as $selector ) { media_expect( str_contains( $selector, ':has(> .rdct-ib__icon)' ), 'Responsive layout reintroduced empty icon rows/columns.' ); }
	}
}

foreach ( Box\styles() as $style => $label ) {
	foreach ( array( 0, 999999999 ) as $id ) {
		$html = Box\render( array( 'template' => $style, 'title' => 'Text card', 'text' => 'Description', 'icon' => '', 'image_id' => $id ) );
		media_expect( str_contains( $html, 'Text card' ) && str_contains( $html, 'Description' ), 'Text-only Box lost content in ' . $style );
		media_expect( ! str_contains( $html, 'rdct-box__media' ) && ! str_contains( $html, 'rdct-box__overlay' ) && ! str_contains( $html, 'rdct-box__icon' ), 'Empty media DOM remained in ' . $style );
	}
}
echo "Passed: optional/missing media across Button, all Icon Box layouts and all Box styles; independent motion and stored options preserved.\n";
