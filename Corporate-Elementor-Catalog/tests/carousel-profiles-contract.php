<?php

require __DIR__ . '/carousel-contract.php';
function esc_attr__( $value, $domain ) { return esc_attr( $value ); }
function wp_attachment_is_image( $id ) { return 7 === $id; }
function wp_get_attachment_image( $id, $size, $icon, $attributes ) { return '<img data-id="' . $id . '" class="' . $attributes['class'] . '" alt="">'; }
require_once __DIR__ . '/../radmanct-components/inc/components/carousel/profiles.php';
require_once __DIR__ . '/../radmanct-components/inc/components/testimonial/renderer.php';

use RadmanCTComponents\Components\CarouselProfiles as Profiles;
use RadmanCTComponents\Components\Testimonial;

foreach ( Profiles\definitions() as $profile => $definition ) {
	$options = Profiles\options( $profile, array( 'mode' => 'invalid', 'variant' => 'invalid', 'arrows' => false, 'pagination' => 'none' ) );
	foreach ( $definition['locks'] as $key => $value ) { expect( $value === $options[ $key ], 'Profile did not lock its purpose: ' . $profile ); }
	$fields = Profiles\fields( $profile );
	foreach ( Profiles\groups( $profile ) as $group ) { expect( ! empty( $group['fields'] ), 'Empty profile group.' ); foreach ( $group['fields'] as $key ) { expect( empty( $fields[ $key ]['internal'] ), 'Internal field leaked into profile UI.' ); } }
	$items = Profiles\sample_items( $profile, 'en' );
	$html = Profiles\render( $profile, $items, array( 'arrows' => false, 'pagination' => 'none' ) );
	expect( str_contains( $html, 'rdct-crs--profile-' . $profile ), 'Missing independent profile wrapper.' );
	$assets = Profiles\assets( $profile, array() );
	expect( in_array( 'testimonial', $assets, true ) === ( 'testimonials' === $profile ), 'Testimonial CSS leaked into another purpose.' );
	expect( in_array( 'carousel-hero', $assets, true ) === ( 'slider' === $profile ), 'Hero CSS leaked into another purpose.' );
}
expect( 'marquee' === \RadmanCTComponents\Components\Carousel\settings( array( 'mode' => 'marquee' ) )['mode'], 'Legacy carousel mode changed.' );
expect( '' === Profiles\render( 'unknown', array() ), 'Unknown profile rendered.' );
expect( ! in_array( 'navigation', array_keys( Profiles\groups( 'marquee' ) ), true ), 'Marquee exposes discrete navigation.' );
foreach ( array_keys( Testimonial\styles() ) as $style ) {
	$html = Testimonial\render( array( 'title' => '<b>Name</b>', 'content' => '<p>Review <strong>text</strong></p>', 'rating' => 99, 'image_id' => 7, 'client_image_id' => 999, 'source' => 'Source', 'link' => 'javascript:bad' ), array( 'testimonial_style' => $style, 'testimonial_padding' => 'url(bad)', 'testimonial_text_color' => '#000000;display:none' ) );
	expect( str_contains( $html, 'rdct-tst--' . $style ) && str_contains( $html, '<strong>text</strong>' ), 'Testimonial lost its style or permitted content.' );
	$expects_avatar = ! in_array( $style, array( 's15', 's19' ), true );
	expect( str_contains( $html, 'Rated 5 out of 5' ) && str_contains( $html, 'data-id="7"' ) === $expects_avatar && ! str_contains( $html, 'data-id="999"' ), 'Rating, intentional portrait-free layout or attachment validation failed.' );
	$requirements = \RadmanCTComponents\Components\requirements( 'testimonial', array( 'testimonial_style' => $style ) );
	expect( array( 'testimonial', 'testimonial-' . $style ) === $requirements['styles'] && ! $requirements['script'], 'A review preset loads another style or motion engine.' );
	expect( ! str_contains( $html, 'javascript:' ) && ! str_contains( $html, 'url(bad)' ) && ! str_contains( $html, 'display:none' ) && ! str_contains( $html, '<b>Name' ), 'Unsafe content or CSS escaped validation.' );
}
$responsive_review = Testimonial\render( array( 'content' => 'Review', 'source_icon' => 'brand-github', 'link' => 'https://example.com' ), array( 'testimonial_quote_size_responsive' => array( 'size' => 27.5, 'unit' => 'px' ), 'testimonial_quote_size_responsive_tablet' => array( 'size' => 21, 'unit' => 'px' ), 'testimonial_quote_size_responsive_mobile' => array( 'size' => 0, 'unit' => 'px' ), 'testimonial_name_weight_responsive' => '700', 'testimonial_position_color' => '#123456' ) );
expect( str_contains( $responsive_review, 'font-size:27.5px;' ) && str_contains( $responsive_review, 'font-size:21px;' ) && str_contains( $responsive_review, 'font-size:0px;' ) && str_contains( $responsive_review, 'font-weight:700;' ), 'Classic responsive review settings lost desktop/device values or explicit zero.' );
expect( str_contains( $responsive_review, '<svg' ) && str_contains( $responsive_review, 'GitHub' ) && str_contains( $responsive_review, '--rdct-tst-position-color:#123456;' ), 'Local review source icon, accessible fallback or position color is missing.' );
$reviews = Profiles\render( 'testimonials', array( array( 'content' => 'First' ), array( 'content' => 'Second' ) ), array( 'testimonial_quote_size_responsive' => array( 'desktop' => 28, 'mobile' => 18 ) ) );
expect( 1 === substr_count( $reviews, '<style data-rdct-responsive>' ) && 2 === substr_count( $reviews, '<figure ' ) && str_contains( $reviews, 'font-size:18px;' ), 'Review carousel duplicated responsive CSS or lost its shared appearance.' );
expect( '' === Testimonial\render( array( 'title' => 'Name', 'content' => '' ) ), 'Empty review rendered a card.' );
expect( ! \RadmanCTComponents\Components\needs_script( 'testimonial' ), 'Standalone review requires motion JavaScript.' );
$assets = \RadmanCTComponents\Components\requirements( 'testimonial', array( 'testimonial_style' => 'bubble' ) );
expect( array( 'testimonial', 'testimonial-bubble' ) === $assets['styles'] && ! $assets['script'], 'Standalone review loaded carousel or other styles.' );
foreach ( array( array( 'href' => 'https://review.test/', 'target' => '_blank' ), array( 'url' => 'https://review.test/', 'is_external' => true ) ) as $link ) {
    $html = Testimonial\render( array( 'content' => 'Review', 'source' => 'Source', 'link' => $link ) );
    expect( str_contains( $html, 'href="https://review.test/"' ) && str_contains( $html, 'target="_blank" rel="nofollow noopener noreferrer"' ), 'Native Atomic/Classic source link or new-tab protection failed.' );
}
expect( array() === \RadmanCTComponents\Components\script_depends( 'testimonial', array(), true ), 'Static review prototype enqueued a frontend engine.' );
echo "Passed: six purpose profiles, native field boundaries, legacy compatibility, conditional assets and safe independent testimonials.\n";
