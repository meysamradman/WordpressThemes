<?php
namespace RadmanCTComponents\Components\Library;
defined( 'ABSPATH' ) || exit;

function enhance_catalog( array $catalog ): array {
    require_once __DIR__ . '/box.php';
    foreach ( \RadmanCTComponents\Components\Box\styles() as $style => $label ) {
        if ( in_array( $style, array( 'cover', 's01' ), true ) ) { continue; }
        $catalog[ 'box-' . $style ] = array_replace( $catalog['box-image'], array( 'variant' => $label, 'settings' => array( 'template' => $style, 'title' => __( 'Thoughtful design', 'radmanct-components' ), 'label' => __( 'Local sample', 'radmanct-components' ) ) ) );
    }
    foreach ( array( 'static', 'hover', 'fill', 'rotate', 'marquee' ) as $mode ) {
        $catalog[ 'text-' . $mode ] = array_replace( $catalog['text-reveal-scroll'], array( 'variant' => array( 'static' => __( 'Static text', 'radmanct-components' ), 'hover' => __( 'Hover highlight', 'radmanct-components' ), 'fill' => __( 'Scroll fill', 'radmanct-components' ), 'rotate' => __( 'Rotating words', 'radmanct-components' ), 'marquee' => __( 'Marquee', 'radmanct-components' ) )[ $mode ], 'settings' => array( 'mode' => $mode, 'tag' => 'h2', 'items' => array( array( 'title' => __( 'Design', 'radmanct-components' ) ), array( 'title' => __( 'Development', 'radmanct-components' ) ) ) ) ) );
    }
    foreach ( array( 'inline', 'top' ) as $layout ) { $catalog[ 'icon-box-' . $layout ] = array_replace( $catalog['icon-box-service'], array( 'variant' => 'top' === $layout ? __( 'Icon above content', 'radmanct-components' ) : __( 'Inline icon', 'radmanct-components' ), 'settings' => array( 'layout' => $layout, 'icon' => 'check', 'shape' => 'circle' ) ) ); }
    foreach ( array( 'rl', 'tb', 'bt' ) as $direction ) { $catalog[ 'flipbox-' . $direction ] = array_replace( $catalog['flipbox-card'], array( 'variant' => array( 'rl' => __( 'Flip from end', 'radmanct-components' ), 'tb' => __( 'Flip from top', 'radmanct-components' ), 'bt' => __( 'Flip from bottom', 'radmanct-components' ) )[ $direction ], 'settings' => array_replace( $catalog['flipbox-card']['settings'], array( 'direction' => $direction ) ) ) ); }
    foreach ( array( 'plain' => array(), 'tilt' => array( 'tilt' => true ), 'parallax' => array( 'parallax' => true ) ) as $variant => $settings ) { $catalog[ 'image-' . $variant ] = array_replace( $catalog['image-reveal'], array( 'variant' => array( 'plain' => __( 'Natural image', 'radmanct-components' ), 'tilt' => __( 'Hover tilt', 'radmanct-components' ), 'parallax' => __( 'Image parallax', 'radmanct-components' ) )[ $variant ], 'settings' => $settings ) ); }
    $catalog['image-gallery'] = array_replace( $catalog['image-reveal'], array( 'gallery' => true, 'name' => __( 'Image gallery', 'radmanct-components' ), 'variant' => __( 'Linked lightbox', 'radmanct-components' ), 'settings' => array( 'lightbox' => true, 'lightbox_group' => 'rdct-library-gallery', 'roundness' => '12px' ) ) );
    foreach ( $catalog as $id => &$item ) {
        $item['family'] = ! empty( $item['gallery'] ) ? 'gallery' : ( 'carousel' === $item['component'] ? 'carousel-' . ( $item['profile'] ?? 'cards' ) : $item['component'] );
        $item['description'] = descriptions()[ $item['family'] ] ?? descriptions()['box'];
        $item['id'] = $id;
    }
    unset( $item );
    return $catalog;
}

function descriptions(): array {
    return array(
        'button' => __( 'Links and actions with distinct styles, icons and hover effects.', 'radmanct-components' ),
        'carousel-slider' => __( 'A full-width image slider with navigation and motion.', 'radmanct-components' ),
        'gallery' => __( 'A responsive image grid with a shared, interactive lightbox.', 'radmanct-components' ),
        'carousel-testimonials' => __( 'Customer review layouts inside the shared carousel.', 'radmanct-components' ),
        'testimonial' => __( 'Individual review cards with multiple visual styles.', 'radmanct-components' ),
        'image' => __( 'Responsive images with reveal, tilt and parallax options.', 'radmanct-components' ),
        'text-reveal' => __( 'Readable text with scroll, hover and rotating-word effects.', 'radmanct-components' ),
        'carousel-cards' => __( 'Content cards with responsive carousel navigation.', 'radmanct-components' ),
        'carousel-logos' => __( 'A compact carousel for brand logos.', 'radmanct-components' ),
        'carousel-stack' => __( 'Overlapping cards controlled by the shared slider.', 'radmanct-components' ),
        'carousel-marquee' => __( 'A continuous content strip with reduced-motion support.', 'radmanct-components' ),
        'accordion' => __( 'Expandable content with lined and boxed styles.', 'radmanct-components' ),
        'timeline' => __( 'Alternating image and text steps in a responsive timeline.', 'radmanct-components' ),
        'sticky-sections' => __( 'Scroll-driven panels, stacked cards and split sections.', 'radmanct-components' ),
        'curtain' => __( 'Expandable content panels and image accordions.', 'radmanct-components' ),
        'icon-box' => __( 'An icon, heading and description in selectable layouts.', 'radmanct-components' ),
        'flipbox' => __( 'Two-sided content cards with directional interaction.', 'radmanct-components' ),
        'table' => __( 'A structured, responsive table for general content.', 'radmanct-components' ),
        'icons' => __( 'Local SVG icons from the shared Radman catalog.', 'radmanct-components' ),
        'particles' => __( 'A lightweight interactive particle background.', 'radmanct-components' ),
        'box' => __( 'Image and content cards with distinct layout options.', 'radmanct-components' ),
    );
}

function design_description( string $id, array $item ): string {
    $buttons = array(
        'button-solid' => __( 'A filled button with a label and optional icon.', 'radmanct-components' ),
        'button-outline' => __( 'A bordered button with a transparent background.', 'radmanct-components' ),
        'button-plain' => __( 'A minimal action without a filled background.', 'radmanct-components' ),
        'button-underline' => __( 'An underlined text link with hover interaction.', 'radmanct-components' ),
        'button-switch-x' => __( 'The label moves horizontally on hover.', 'radmanct-components' ),
        'button-switch-y' => __( 'The label moves vertically on hover.', 'radmanct-components' ),
        'button-change-y' => __( 'An alternate label appears on hover.', 'radmanct-components' ),
        'button-marquee-x' => __( 'The label scrolls continuously in a horizontal direction.', 'radmanct-components' ),
        'button-marquee-y' => __( 'The label scrolls continuously in a vertical direction.', 'radmanct-components' ),
        'button-liquid-x' => __( 'A horizontal liquid transition on hover.', 'radmanct-components' ),
        'button-liquid-y' => __( 'A vertical liquid transition on hover.', 'radmanct-components' ),
        'button-liquid-x-alt' => __( 'A reversed horizontal liquid transition on hover.', 'radmanct-components' ),
        'button-liquid-y-alt' => __( 'A reversed vertical liquid transition on hover.', 'radmanct-components' ),
    );
    return $buttons[ $id ] ?? $item['description'];
}

function groups(): array {
    $names = array(
        'button' => __( 'Buttons', 'radmanct-components' ),
        'carousel-slider' => __( 'Image slider', 'radmanct-components' ),
        'gallery' => __( 'Image gallery', 'radmanct-components' ),
        'carousel-testimonials' => __( 'Review carousel', 'radmanct-components' ),
        'carousel-cards' => __( 'Card carousel', 'radmanct-components' ),
        'carousel-logos' => __( 'Logo carousel', 'radmanct-components' ),
        'carousel-stack' => __( 'Stacked cards', 'radmanct-components' ),
        'carousel-marquee' => __( 'Marquee', 'radmanct-components' ),
    );
    $groups = array();
    foreach ( catalog() as $id => $item ) {
        if ( ! empty( $item['hidden'] ) ) { continue; }
        $key = $item['family'];
        if ( ! isset( $groups[ $key ] ) ) { $groups[ $key ] = array_replace( $item, array( 'name' => $names[ $key ] ?? $item['name'], 'variants' => array() ) ); }
        $groups[ $key ]['variants'][ $id ] = $item;
    }
    $ordered = array();
    foreach ( array( 'button', 'carousel-slider', 'gallery', 'carousel-testimonials' ) as $key ) { if ( isset( $groups[ $key ] ) ) { $ordered[ $key ] = $groups[ $key ]; } }
    return $ordered + $groups;
}

function thumbnail( string $id ): string {
    static $manifest = null, $status = null;
    $base = dirname( __DIR__, 2 ) . '/images/components/';
    $manifest ??= is_file( $base . 'previews.json' ) ? json_decode( file_get_contents( $base . 'previews.json' ), true ) : array();
    $status ??= is_file( $base . 'preview-status.json' ) ? json_decode( file_get_contents( $base . 'preview-status.json' ), true ) : array();
    $item = $manifest['items'][ $id ] ?? array();
    $file = $item['file'] ?? '';
    if ( ! preg_match( '/^[a-z0-9-]+\.webp$/D', $file ) || ! is_file( $base . $file ) || ( $status[ $id ] ?? '' ) !== ( $item['fingerprint'] ?? null ) ) { return ''; }
    return \RadmanCTComponents\url( 'images/components/' . $file );
}
