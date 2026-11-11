<?php

require __DIR__ . '/carousel-profiles-contract.php';
use RadmanCTComponents\Components as Components;
function esc_url_raw( $url, $protocols = array() ) { return esc_url( $url ); }
require_once __DIR__ . '/../radmanct-components/inc/components/box.php';

foreach ( array_keys( \RadmanCTComponents\Components\Box\styles() ) as $style ) {
	$styles = Components\requirements( 'box', array( 'template' => $style ) )['styles'];
	expect( array( 'box', 'box-' . $style ) === $styles, 'Box loaded another preset.' );
}
foreach ( array( 's02', 's03', 's08' ) as $style ) {
	expect( ! str_contains( \RadmanCTComponents\Components\Box\render( array( 'template' => $style ) ), 'data-rdct-box-motion' ), 'CSS-only Box still requests a JavaScript engine.' );
}
expect( str_contains( \RadmanCTComponents\Components\Box\render( array( 'template' => 's06' ) ), 'data-rdct-box-motion="tilt"' ), 'Tilt Box lost its interactive engine.' );
foreach ( array( 'grid', 'striped', 'minimal' ) as $style ) { expect( array( 'table', 'table-' . $style ) === Components\requirements( 'table', array( 'variant' => $style ) )['styles'], 'Table loaded another preset.' ); }
foreach ( array( 'lr', 'rl', 'tb', 'bt' ) as $style ) { expect( array( 'flipbox', 'flipbox-' . $style ) === Components\requirements( 'flipbox', array( 'direction' => $style ) )['styles'], 'Flip Box loaded another direction.' ); }
foreach ( array( 'top', 'inline', 'side' ) as $style ) { expect( array( 'icons', 'icon-box', 'icon-box-' . $style ) === Components\requirements( 'icon-box', array( 'layout' => $style ) )['styles'], 'Icon Box loaded another layout.' ); }
foreach ( array_keys( \RadmanCTComponents\Components\Testimonial\styles() ) as $style ) {
	expect( array( 'testimonial', 'testimonial-' . $style ) === Components\requirements( 'testimonial', array( 'testimonial_style' => $style ) )['styles'], 'Review loaded another style.' );
}
$enqueued = array();
ob_start(); rdct_testimonial( array( 'content' => 'A review' ), array( 'testimonial_style' => 'minimal' ) ); ob_end_clean();
expect( array( 'rdct-testimonial', 'rdct-testimonial-minimal' ) === array_keys( $enqueued ), 'Static review enqueued carousel or unrelated styles.' );
echo "Passed: each Box/Table/Flip Box/Icon Box/Testimonial preset loads only its base and selected stylesheet.\n";
foreach ( array( 'fill', 'wave', 'double', 'circle', 'brush' ) as $shape ) {
	$styles = Components\requirements( 'text-reveal', array( 'mode' => 'static', 'highlight' => $shape ) )['styles'];
	expect( array( 'text-reveal', 'text-effects', 'text-highlight', 'text-highlight-' . $shape ) === $styles, 'Text highlight loads another shape or motion mode.' );
}
expect( array( 'text-reveal', 'text-effects', 'text-fill' ) === Components\requirements( 'text-reveal', array( 'mode' => 'fill' ) )['styles'], 'Text fill loads rotator or highlight presets.' );
expect( array( 'text-reveal', 'text-effects', 'text-rotate', 'text-rotate-list' ) === Components\requirements( 'text-reveal', array( 'mode' => 'rotate', 'rotate_style' => 'list' ) )['styles'], 'List rotator loads unrelated modes.' );

foreach ( array( 'float' => 'float', 'tilt' => 'tilt', 'reveal' => 'reveal', 'animate_shadow' => 'shadow', 'lightbox' => 'lightbox' ) as $option => $feature ) {
    expect( array( 'image', 'image-' . $feature ) === Components\requirements( 'image', array( $option => true ) )['styles'], 'Image loads an unrelated feature: ' . $option );
}
expect( array( 'image' ) === Components\requirements( 'image' )['styles'] && ! Components\needs_script( 'image' ), 'Static image loads motion assets.' );

foreach ( array( 'text-inline-image' => array( 'image' => array( 'id' => 7 ) ), 'text-segment-gradient' => array( 'paint' => 'gradient' ), 'text-segment-mask' => array( 'paint' => 'image' ) ) as $asset => $row ) {
    expect( array( 'text-reveal', 'text-effects', 'text-segments', $asset ) === Components\requirements( 'text-reveal', array( 'mode' => 'rich', 'segments' => array( $row, $row ) ) )['styles'], 'Rich text loads unrelated or duplicate assets: ' . $asset );
}

expect( array( 'image', 'image-label' ) === Components\requirements( 'image', array( 'label_enabled' => true, 'label' => 'Label' ) )['styles'], 'Image label loads motion assets.' );

foreach ( array( 'solid', 'outline', 'plain', 'underline' ) as $style ) {
	$requirements = Components\requirements( 'button', array( 'style' => $style ) );
	expect( array( 'button', 'button-' . $style ) === $requirements['styles'] && ! $requirements['script'], 'Plain button loads another style or JavaScript.' );
}
expect( array( 'button', 'button-solid' ) === Components\requirements( 'button', array( 'action' => 'scroll' ) )['styles'], 'Scroll button loads dialog styles.' );
foreach ( array( 'lined', 'boxed' ) as $style ) {
	$requirements = Components\requirements( 'accordion', array( 'style' => $style ) );
	expect( array( 'accordion', 'accordion-' . $style ) === $requirements['styles'] && ! $requirements['script'], 'Accordion loads another style or JavaScript.' );
}
$timeline = Components\requirements( 'timeline' );
expect( array( 'timeline', 'timeline-alternating' ) === $timeline['styles'] && ! $timeline['script'], 'Timeline loads unrelated component assets.' );
