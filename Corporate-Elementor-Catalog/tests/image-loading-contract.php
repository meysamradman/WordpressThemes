<?php

require __DIR__ . '/image-contract.php';
function esc_html( $value ) { return esc_attr( $value ); }
function esc_html__( $value, $domain ) { return esc_html( $value ); }
function wp_json_encode( $value ) { return json_encode( $value ); }
function sanitize_hex_color( $value ) { return preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD', $value ) ? $value : null; }
require dirname( __DIR__ ) . '/radmanct-components/inc/components/carousel.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/components/box.php';

use RadmanCTComponents\Components\ImageLoading;
use RadmanCTComponents\Components\Image;
use RadmanCTComponents\Components\Box;
use RadmanCTComponents\Components\Carousel;

expect( array() === ImageLoading\attributes( 'auto' ), 'Automatic mode must leave WordPress heuristics intact.' );
expect( array() === ImageLoading\attributes( 'invalid' ), 'Unknown loading mode must not promote an image.' );
foreach ( array( 'auto', 'lazy', 'high' ) as $policy ) {
	foreach ( array( true, false ) as $initial ) {
		$attrs = ImageLoading\attributes( $policy, $initial );
		expect( !( 'lazy' === ( $attrs['loading'] ?? '' ) && 'high' === ( $attrs['fetchpriority'] ?? '' ) ), 'Conflicting lazy/high attributes.' );
	}
}
foreach ( array( Image\settings( array( 'image_loading' => 'bad' ) ), Box\settings( array( 'image_loading' => array() ) ), Carousel\settings( array( 'image_loading' => 'eager" onclick="bad' ) ) ) as $settings ) { expect( 'auto' === $settings['image_loading'], 'Renderer failed to normalize loading mode.' ); }

foreach ( array( 'auto', 'high', 'lazy' ) as $policy ) {
	foreach ( array( Image\render(...), Box\render(...) ) as $render ) {
		$GLOBALS['image_calls'] = array();
		$render( array( 'image_id' => 1, 'image_loading' => $policy ) );
		expect( 1 === count( $GLOBALS['image_calls'] ), 'Image was rendered more than once.' );
		$attrs = $GLOBALS['image_calls'][0]['attributes'];
		if ( 'auto' === $policy ) { expect( ! isset( $attrs['loading'] ) && ! isset( $attrs['fetchpriority'] ), 'Automatic mode forced an image priority.' ); }
		else { expect( ( 'high' === $policy ? 'eager' : 'lazy' ) === $attrs['loading'], 'Explicit loading mode was lost.' ); }
		expect( ( 'high' === $policy ) === isset( $attrs['fetchpriority'] ), 'High priority escaped explicit selection.' );
	}
}

$items = array( array( 'image_id' => 1, 'link' => '/first/' ), array( 'title' => 'Text only' ), array( 'image_id' => 2, 'link' => '/last/' ), array( 'content_type' => 'template', 'template_id' => 0 ) );
foreach ( array( false, true ) as $reverse ) {
	$GLOBALS['image_calls'] = array();
	$html = Carousel\render( $items, array( 'mode' => 'slider', 'arrows' => false, 'pagination' => 'none', 'autoplay' => $reverse, 'autoplay_direction' => 'reverse', 'image_loading' => 'high' ) );
	$first = $GLOBALS['image_calls'][0]['attributes']; $last = $GLOBALS['image_calls'][1]['attributes'];
	expect( ( $reverse ? 'lazy' : 'eager' ) === $first['loading'] && ( $reverse ? 'eager' : 'lazy' ) === $last['loading'], 'Priority did not follow the actual initial valid slide.' );
	expect( 'high' === ( $reverse ? $last : $first )['fetchpriority'], 'Initial slide lost explicit high priority.' );
	expect( str_contains( $html, 'href="/first/"><div class="rdct-crs__media"' ) && str_contains( $html, 'href="/last/"><div class="rdct-crs__media"' ), 'Image-only slide links were lost.' );
	expect( 3 === substr_count( $html, 'class="swiper-slide ' ), 'Empty template changed valid slide count.' );
}
$GLOBALS['image_calls'] = array();
Carousel\render( $items, array( 'arrows' => false, 'pagination' => 'none' ) );
expect( ! isset( $GLOBALS['image_calls'][0]['attributes']['loading'] ) && 'lazy' === $GLOBALS['image_calls'][1]['attributes']['loading'], 'Default carousel still forces eager on its first image.' );
$rendered = 0;
Carousel\render( array( array( 'render' => static function () use ( &$rendered ) { ++$rendered; return '<p>Client component</p>'; } ), array( 'image_id' => 1 ) ), array( 'arrows' => false, 'pagination' => 'none' ) );
expect( 1 === $rendered, 'Loading policy rendered custom content twice.' );

$native_image = array( 'id' => 1, 'width' => 320, 'height' => 180, 'src' => 'https://ignored.example/remote.jpg' );
$GLOBALS['image_calls'] = array();
Box\render( array( 'title' => 'Box', 'image_id' => 1, 'image' => $native_image ) );
Carousel\render( array( array( 'image_id' => $native_image ) ), array( 'arrows' => false, 'pagination' => 'none' ) );
foreach ( $GLOBALS['image_calls'] as $call ) { expect( array( 320, 180 ) === $call['size'], 'Native image resolution was ignored.' ); }
expect( 2 === count( $GLOBALS['image_calls'] ), 'Native resolution duplicated or lost image rendering.' );
foreach ( array( null, 1, array(), array( 'width' => 320 ), array( 'width' => INF, 'height' => 180 ), array( 'width' => 320, 'height' => -1 ), array( 'width' => 9000, 'height' => 180 ), array( 'width' => array(), 'height' => 180 ) ) as $invalid ) {
	expect( 'medium' === \RadmanCTComponents\Components\ImageMedia\size( $invalid, 'medium' ), 'Malformed resolution replaced the legacy image size.' );
}
echo "Passed: shared image loading, safe native modes, reverse/empty slides, linked image-only content and single rendering.\n";
