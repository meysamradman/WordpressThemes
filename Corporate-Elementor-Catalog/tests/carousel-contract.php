<?php

require __DIR__ . '/components-contract.php';
function esc_html__( $value, $domain ) { return esc_html( $value ); }
function esc_url( $value ) { return preg_match( '~^(https?://|/)~', $value ) ? esc_attr( $value ) : ''; }
function wp_kses_post( $value ) { return strip_tags( $value, '<p><strong><em>' ); }

require_once dirname( __DIR__ ) . '/radmanct-components/inc/components/carousel.php';
use RadmanCTComponents\Components\Carousel;

$surface = Carousel\render( array( array( 'title' => 'One' ), array( 'title' => 'Two' ) ), array( 'arrow_fill' => 'solid', 'arrow_shadow' => 'hover', 'arrow_background' => '#123456', 'arrow_hover_background' => '#abcdef', 'arrow_border_color' => 'url(bad)' ) );
expect( str_contains( $surface, 'data-fill="solid"' ) && str_contains( $surface, 'data-shadow="hover"' ) && str_contains( $surface, '--rdct-crs-arrow-background:#123456;' ) && ! str_contains( $surface, 'url(bad)' ), 'Arrow surface state or color validation failed.' );
expect( in_array( 'carousel-navigation-surface', Carousel\assets( array( 'arrow_fill' => 'solid' ) ), true ) && in_array( 'carousel-navigation-shadow', Carousel\assets( array( 'arrow_shadow' => 'hover' ) ), true ), 'Selected arrow features are missing CSS.' );
foreach ( array( array(), array( 'arrows' => false, 'arrow_fill' => 'solid', 'arrow_shadow' => 'hover' ), array( 'mode' => 'marquee', 'arrow_fill' => 'solid', 'arrow_shadow' => 'normal' ) ) as $plain ) { expect( ! in_array( 'carousel-navigation-surface', Carousel\assets( $plain ), true ) && ! in_array( 'carousel-navigation-shadow', Carousel\assets( $plain ), true ), 'Unused arrow feature CSS leaked into a plain carousel.' ); }

$sync = Carousel\settings( array( 'controller_id' => ' hero-images ', 'controlled_ids' => '#hero-text, hero-text,details,.unsafe,body > div' ) );
$external = Carousel\settings( array( 'external_previous_class' => ' .hero-previous ', 'external_next_class' => 'body > button' ) );
expect( 'hero-previous' === $external['external_previous_class'] && '' === $external['external_next_class'], 'External controls accepted selectors or lost the single class name.' );
expect( 'hero-images' === $sync['controller_id'] && 'hero-text,details' === $sync['controlled_ids'], 'Carousel synchronization IDs were not normalized or deduplicated.' );
foreach ( array( array(), '1invalid', 'bad id', '#hero', 'id" onclick="bad', str_repeat( 'a', 65 ) ) as $invalid ) { expect( '' === Carousel\controller_id( $invalid ), 'Unsafe carousel ID was accepted.' ); }
expect( 16 === count( explode( ',', Carousel\controlled_ids( implode( ',', array_map( static fn( $index ) => 'slide-' . $index, range( 1, 20 ) ) ) ) ) ), 'Synchronization targets were not bounded.' );
expect( '' === Carousel\controlled_ids( str_repeat( 'a', 1041 ) ) && '' === Carousel\controlled_ids( array() ), 'Malformed synchronization input was accepted.' );
$sync_html = Carousel\render( array( array( 'title' => 'First' ), array( 'title' => 'Second' ) ), array( 'controller_id' => 'hero', 'controlled_ids' => 'details' ) );
expect( str_contains( html_entity_decode( $sync_html ), '"controlled_ids":"details"' ) && str_contains( html_entity_decode( $sync_html ), '"controller_id":"hero"' ), 'Shared SSR omitted synchronization settings.' );

$stack = Carousel\settings( array( 'mode' => 'stack', 'transition' => 'fade', 'variable_width' => true, 'slides' => 5, 'slides_mobile' => 2, 'group_slides' => true, 'gap' => 80, 'alignment' => 'center', 'arrow_color' => '#123456', 'arrow_hover_color' => 'url(javascript:bad)' ) );
expect( 1 === $stack['slides'] && 1 === $stack['slides_mobile'] && 0 === $stack['gap'] && ! $stack['variable_width'] && ! $stack['group_slides'] && 'start' === $stack['alignment'] && 'slide' === $stack['transition'], 'Stack must use a single shared slide in every device.' );
expect( '#123456' === $stack['arrow_color'] && '' === $stack['arrow_hover_color'], 'Arrow colors must reject CSS injection.' );
expect( in_array( 'carousel-stack', Carousel\assets( $stack ), true ) && ! in_array( 'carousel-stack', Carousel\assets( array() ), true ), 'Stack stylesheet must stay optional.' );
expect( in_array( 'carousel-navigation', Carousel\assets( $stack ), true ), 'Explicit arrow colors require the shared navigation stylesheet.' );

$gallery = Carousel\gallery_items( array( 9, array( 'id' => '7', 'url' => 'https://untrusted.test/ignored.jpg' ), 9, 0, -1, 1.5, array( 'url' => '/no-id.jpg' ), 'invalid' ) );
expect( array( 9, 7 ) === array_column( $gallery, 'image_id' ), 'Gallery selection lost order, IDs or deduplication.' );
expect( count( Carousel\gallery_items( range( 1, 100 ) ) ) === 50 && array() === Carousel\gallery_items( 'invalid' ), 'Gallery selection was not bounded.' );
expect( ! isset( $gallery[1]['image_url'] ), 'Gallery trusted a supplied image URL.' );

$settings = Carousel\settings( array( 'slides' => 3.2, 'height' => array( 'size' => 70, 'unit' => 'vh' ), 'height_mobile' => array( 'size' => 360, 'unit' => 'px' ), 'duration' => -20, 'variant' => '../bad', 'previous_icon' => '<bad>' ) );
expect( 3.2 === $settings['slides'] && 0 === $settings['duration'] && 'cards' === $settings['variant'], 'Carousel bounds or fractional slides failed.' );
$css = Carousel\height_css( $settings );
expect( str_contains( $css, 'height-desktop:70vh;' ) && str_contains( $css, 'height-mobile:360px;' ) && ! str_contains( $css, 'height-tablet:' ), 'Responsive heights lost units or inheritance.' );
$auto = Carousel\height_css( Carousel\settings( array( 'height' => array( 'size' => null, 'unit' => 'auto' ) ) ) );
expect( str_contains( $auto, 'height-desktop:auto;' ), 'Native auto height was lost.' );
expect( str_contains( Carousel\height_css( Carousel\settings( array( 'height' => '70vh' ) ) ), 'height-desktop:70vh;' ), 'Resolved Atomic size string was lost.' );
expect( ! str_contains( Carousel\height_css( Carousel\settings( array( 'height' => '1px;display:none' ) ) ), 'display' ), 'Unsafe size reached CSS.' );
$items = array( array( 'title' => '<b>One</b>', 'content' => '<strong>Text</strong>', 'link' => 'javascript:bad()' ), array( 'render' => static fn() => '<article data-card="portfolio">Project</article>' ) );
$html = Carousel\render( $items, array( 'variant' => 'content' ) );
expect( str_contains( $html, '&lt;b&gt;One&lt;/b&gt;' ) && ! str_contains( $html, 'javascript:' ), 'Content title or URL escaped incorrectly.' );
expect( str_contains( $html, 'data-card="portfolio"' ) && ! str_contains( $html, 'id=' ), 'Independent PHP content was duplicated or rewritten.' );
expect( 50 === substr_count( Carousel\render( array_fill( 0, 60, array( 'title' => 'Slide' ) ) ), 'class="swiper-slide ' ), 'Unbounded slide count.' );
expect( '' === Carousel\render( array( array( 'content_type' => 'template', 'template_id' => 0, 'title' => 'Hidden fallback' ) ) ), 'Missing template used unintended content.' );
$fullscreen = Carousel\settings( array( 'mode' => 'slider', 'slides' => 4, 'gap' => 24, 'height' => '100vh', 'position' => 'middle', 'pagination_position' => 'bottom' ) );
expect( 1 === $fullscreen['slides'] && 0 === $fullscreen['gap'] && $fullscreen['controls_overlay'], 'Single-slide mode lost its viewport.' );
$html = Carousel\render( $items, $fullscreen );
expect( str_contains( $html, 'height-desktop:100vh;' ) && 2 === substr_count( $html, 'controls--overlay' ) && str_contains( $html, 'data-position="middle"' ) && str_contains( $html, 'data-position="bottom"' ), 'Full-height controls are not independently overlaid.' );
$legacy = Carousel\settings( array( 'position' => 'top' ) );
expect( 'top' === $legacy['position'] && 'top' === $legacy['pagination_position'], 'Saved control position was lost.' );
$independent = Carousel\settings( array( 'position' => 'top', 'pagination_position' => 'bottom', 'arrow_alignment' => 'end', 'pagination_alignment' => 'start' ) );
expect( 'bottom' === $independent['pagination_position'] && 'end' === $independent['arrow_alignment'], 'Independent controls were lost.' );
echo "Passed: bounded generic slides, fractional view, native responsive height units, auto/inheritance and shared PHP content; WordPress escaping is stubbed.\n";

$fade = \RadmanCTComponents\Components\Carousel\settings( array( 'mode' => 'slider', 'transition' => 'fade', 'slides' => 6 ) );
expect( 'fade' === $fade['transition'] && 1 === $fade['slides'], 'Fade escaped the single-slide layout.' );
expect( in_array( 'carousel-fade', \RadmanCTComponents\Components\Carousel\assets( $fade ), true ), 'Fade did not request its independent CSS.' );
foreach ( array( 'carousel', 'marquee' ) as $mode ) {
    expect( 'slide' === \RadmanCTComponents\Components\Carousel\settings( array( 'mode' => $mode, 'transition' => 'fade' ) )['transition'], 'Incompatible stored effect was applied to another layout.' );
    expect( ! in_array( 'carousel-fade', \RadmanCTComponents\Components\Carousel\assets( array( 'mode' => $mode, 'transition' => 'fade' ) ), true ), 'Unused fade CSS leaked into another layout.' );
}
$fade_html = \RadmanCTComponents\Components\Carousel\render( array( array( 'title' => 'First' ), array( 'title' => 'Second' ) ), $fade );
expect( str_contains( $fade_html, 'data-transition="fade"' ) && 2 === substr_count( $fade_html, 'class="swiper-slide ' ), 'Fade lost its loader marker or cloned template content.' );
