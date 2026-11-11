<?php
namespace RadmanCTComponents\Components\Library;
defined( 'ABSPATH' ) || exit;

function preview_media(): array {
    $saved = get_option( 'rdct_library_sample_media', array() );
    $saved = is_array( $saved ) ? $saved : array();
    $names = array( 'form', 'balance', 'horizon' );
    $valid = array();
    foreach ( $names as $name ) { $id = absint( $saved[ $name ] ?? 0 ); if ( $id && wp_attachment_is_image( $id ) ) { $valid[ $name ] = $id; } }
    return array_values( $valid );
}

function preview_settings( string $id, string $language, array $media ): array {
    $item = catalog()[ $id ];
    $model = model( $id, $language );
    $settings = $model['elements'][0]['settings'] ?? $item['settings'];
    $first = $media[0] ?? 0;
    if ( in_array( $item['component'], array( 'image', 'box' ), true ) ) { $settings['image_id'] = $first; }
    if ( 'box' === $item['component'] ) { $settings += array( 'content' => __( 'A local demonstration of the actual component.', 'radmanct-components' ) ); }
    if ( 'flipbox' === $item['component'] ) { $settings['front_image_id'] = $first; $settings['back_image_id'] = $media[1] ?? $first; }
    if ( in_array( $item['component'], array( 'carousel', 'curtain', 'timeline', 'sticky-sections' ), true ) ) {
        foreach ( $settings['items'] ?? array() as $index => $row ) {
            $settings['items'][ $index ]['image_id'] = $media[ $index % max( 1, count( $media ) ) ] ?? 0;
        }
    }
    if ( 'carousel' === $item['component'] && 'testimonials' === ( $item['profile'] ?? '' ) ) {
        foreach ( $settings['items'] as &$row ) { $row['rating'] = 5; }
        unset( $row );
    }
    if ( 'testimonial' === $item['component'] ) { $settings['rating'] = 5; }
    if ( 'button' === $item['component'] ) { $settings['size'] = 'lg'; $settings['radius'] = 80; }
    if ( 'particles' === $item['component'] ) { $settings['height'] = '400px'; }
    return $settings;
}

function preview_markup( array $item, array $settings, string $language, array $media ): string {
    $type = $item['component'];
    if ( empty( $media ) && 'image' === $type ) { return '<p class="rdct-demo-missing">' . esc_html__( 'Sample media has not been supplied yet.', 'radmanct-components' ) . '</p>'; }
    if ( 'icons' === $type ) { return \RadmanCTComponents\Icons\render( $settings['name'], $settings ); }
    require_once __DIR__ . '/' . ( 'testimonial' === $type ? 'testimonial/renderer' : $type ) . '.php';
    if ( ! empty( $item['gallery'] ) ) {
        $html = '<div class="rdct-demo-gallery">';
        foreach ( $media as $image ) { $html .= \RadmanCTComponents\Components\Image\render( array_replace( $settings, array( 'image_id' => $image ) ) ); }
        return $html . '</div>';
    }
    if ( 'carousel' === $type ) { return \RadmanCTComponents\Components\CarouselProfiles\render( $item['profile'] ?? '', $settings['items'], $settings ); }
    if ( 'text-reveal' === $type ) { return \RadmanCTComponents\Components\TextReveal\render( 'rotate' === $settings['mode'] ? '' : sample_text( $language ), $settings ); }
    $renderers = array( 'button' => 'Button', 'image' => 'Image', 'box' => 'Box', 'flipbox' => 'FlipBox', 'icon-box' => 'IconBox', 'table' => 'Table', 'testimonial' => 'Testimonial', 'particles' => 'Particles', 'accordion' => 'Accordion', 'timeline' => 'Timeline', 'curtain' => 'Curtain', 'sticky-sections' => 'StickySections' );
    $renderer = '\\RadmanCTComponents\\Components\\' . $renderers[ $type ] . '\\render';
    if ( 'table' === $type ) { return $renderer( $settings['data'], $settings ); }
    if ( in_array( $type, array( 'accordion', 'timeline', 'curtain', 'sticky-sections' ), true ) ) { return $renderer( $settings['items'], $settings ); }
    if ( 'testimonial' === $type ) { require_once __DIR__ . '/testimonial/renderer.php'; return $renderer( $settings, $settings ); }
    return $renderer( $settings );
}

function preview_document( string $id, string $language ): void {
    $item = catalog()[ $id ];
    $media = preview_media();
    $settings = preview_settings( $id, $language, $media );
    $requirements = \RadmanCTComponents\Components\requirements( $item['component'], $settings );
    $entries = array( 'src/css/base.css', 'src/css/admin/component-preview.css' );
    $definitions = \RadmanCTComponents\Components\definitions();
    foreach ( $requirements['styles'] as $key ) { if ( isset( $definitions[ $key ] ) ) { $entries[] = $definitions[ $key ]['entry']; } }
    echo '<!doctype html><html data-rdct-preview="true" lang="' . esc_attr( $language ) . '" dir="' . ( 'fa' === $language ? 'rtl' : 'ltr' ) . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . esc_html( $item['name'] ) . '</title>';
    foreach ( array_unique( $entries ) as $key ) { $asset = \RadmanCTComponents\Assets\entry( $key ); if ( $asset ) { echo '<link rel="stylesheet" href="' . esc_url( \RadmanCTComponents\Assets\url( $asset['file'] ) ) . '">'; } }
    echo '<style>' . apply_filters( 'radmanct_components/preview_css', '' ) . '</style></head><body class="rdct-site rdct-library-preview" data-preview-family="' . esc_attr( $item['family'] ) . '"><main class="rdct-cmp-demo" data-preview-stage>' . preview_markup( $item, $settings, $language, $media ) . '</main>';
    if ( $requirements['script'] ) { $asset = \RadmanCTComponents\Assets\entry( 'src/js/admin/component-preview.js' ); if ( $asset ) { echo '<script type="module" src="' . esc_url( \RadmanCTComponents\Assets\url( $asset['file'] ) ) . '"></script>'; } }
    echo '</body></html>';
}
