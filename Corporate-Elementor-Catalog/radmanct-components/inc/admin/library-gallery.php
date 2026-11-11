<?php
namespace RadmanCTComponents\Admin\ComponentsPage;
defined( 'ABSPATH' ) || exit;

function family_icon( string $family ): string {
    static $catalog = null;
    $catalog ??= require dirname( __DIR__ ) . '/icons/catalog.php';
    require_once dirname( __DIR__ ) . '/icons/engine.php';
    $icons = array( 'button' => 'external-link', 'carousel-slider' => 'image', 'gallery' => 'images', 'carousel-testimonials' => 'message-circle', 'testimonial' => 'message-square', 'accordion' => 'list', 'timeline' => 'calendar-days', 'carousel-stack' => 'copy', 'particles' => 'share-2', 'carousel-cards' => 'layout-grid', 'carousel-logos' => 'building-2', 'box' => 'square-menu', 'table' => 'layout-grid', 'image' => 'image', 'icon-box' => 'list', 'flipbox' => 'rotate-ccw', 'curtain' => 'chevrons-right', 'text-reveal' => 'file-text', 'icons' => 'phone', 'carousel-marquee' => 'move-right', 'sticky-sections' => 'copy' );
    return \RadmanCTShared\Icons\render( $catalog[ $icons[ $family ] ?? 'layout-grid' ] );
}

function gallery_card( array $group, bool $editor ): void {
    $variants = array();
    foreach ( $group['variants'] as $id => $item ) { $variants[] = array( 'id' => $id, 'label' => $item['variant'], 'description' => \RadmanCTComponents\Components\Library\design_description( $id, $item ), 'url' => preview_url( $id ), 'image' => \RadmanCTComponents\Components\Library\thumbnail( $id ) ); }
    $first = $variants[0];
    $search = implode( ' ', array_merge( array( $group['name'], $group['description'], $group['keywords'] ?? '' ), array_column( $variants, 'label' ) ) );
    echo '<article class="rdct-lib__card" data-library-card="' . esc_attr( $group['family'] ) . '" data-library-category="' . esc_attr( $group['category'] ) . '" data-library-search-text="' . esc_attr( $search ) . '" data-library-name="' . esc_attr( $group['name'] ) . '" data-library-description="' . esc_attr( $group['description'] ) . '" data-library-variants="' . esc_attr( wp_json_encode( $variants ) ) . '">';
    echo '<button type="button" class="rdct-lib__family ' . ( $editor ? 'e-btn e-btn-txt-border' : 'button' ) . '" data-library-family="' . esc_attr( $group['family'] ) . '" aria-expanded="false">';
    if ( $first['image'] ) { echo '<img src="' . esc_url( $first['image'] ) . '" width="960" height="600" alt="" loading="lazy" decoding="async">'; }
    echo '<span class="rdct-lib__icon" data-library-icon>' . family_icon( $group['family'] ) . '</span><strong>' . esc_html( $group['name'] ) . '</strong><span class="rdct-lib__meta">' . esc_html( (string) count( $variants ) ) . ' ' . esc_html__( 'designs', 'radmanct-components' ) . '</span></button></article>';
}

function gallery( bool $editor ): void {
    $groups = \RadmanCTComponents\Components\Library\groups();
    $categories = array_values( array_unique( array_column( $groups, 'category' ) ) );
    $button = $editor ? 'e-btn e-btn-txt-border' : 'button';
    echo '<div class="rdct-lib" data-rdct-library' . ( $editor ? ' data-editor="true" data-library-model-url="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" data-library-model-nonce="' . esc_attr( wp_create_nonce( 'radman_component_library' ) ) . '"' : '' ) . '>';
    echo '<header class="rdct-lib__heading"><p>' . esc_html__( 'Browse real components. Open a design to interact with it.', 'radmanct-components' ) . '</p><span>' . esc_html( (string) count( $groups ) ) . ' ' . esc_html__( 'component families', 'radmanct-components' ) . '</span></header>';
    echo '<div class="rdct-lib__filters"><label class="rdct-lib__search">' . esc_html__( 'Search components', 'radmanct-components' ) . '<input type="search" name="library-search" data-library-search autocomplete="off" placeholder="' . esc_attr__( 'Search by component, design or feature…', 'radmanct-components' ) . '"></label><label>' . esc_html__( 'Sample language', 'radmanct-components' ) . '<select data-library-language name="library-language"><option value="fa"' . ( is_rtl() ? ' selected' : '' ) . '>' . esc_html__( 'Persian', 'radmanct-components' ) . '</option><option value="en"' . ( is_rtl() ? '' : ' selected' ) . '>' . esc_html__( 'English', 'radmanct-components' ) . '</option></select></label></div>';
    echo '<div class="rdct-lib__categories" role="group" aria-label="' . esc_attr__( 'Component category', 'radmanct-components' ) . '"><button type="button" class="' . esc_attr( $button ) . '" data-library-category="" aria-pressed="true">' . esc_html__( 'All categories', 'radmanct-components' ) . '</button>';
    foreach ( $categories as $category ) { echo '<button type="button" class="' . esc_attr( $button ) . '" data-library-category="' . esc_attr( $category ) . '" aria-pressed="false">' . esc_html( $category ) . '</button>'; }
    echo '</div><p class="rdct-lib__results" role="status" aria-live="polite" data-library-results></p><div class="rdct-lib__grid" data-library-grid>';
    $index = 0;
    foreach ( $groups as $group ) { if ( 24 === $index++ ) { echo '</div><template data-library-overflow>'; } gallery_card( $group, $editor ); }
    echo count( $groups ) > 24 ? '</template>' : '</div>';
    echo '<p class="rdct-lib__empty" data-library-empty hidden>' . esc_html__( 'No matching components. Try another word or clear the category.', 'radmanct-components' ) . '</p><nav class="rdct-lib__pagination" aria-label="' . esc_attr__( 'Component pages', 'radmanct-components' ) . '"><button type="button" class="' . esc_attr( $button ) . '" data-library-page="previous">' . esc_html__( 'Previous', 'radmanct-components' ) . '</button><span data-library-page-label></span><button type="button" class="' . esc_attr( $button ) . '" data-library-page="next">' . esc_html__( 'Next', 'radmanct-components' ) . '</button></nav>';
    echo '<section data-library-designs hidden aria-labelledby="rdct-library-family-title"><header class="rdct-lib__viewer-header"><div><h2 id="rdct-library-family-title" data-library-family-title tabindex="-1"></h2><p data-library-family-description></p></div><button type="button" class="' . esc_attr( $button ) . '" data-library-back>' . esc_html__( 'Back to components', 'radmanct-components' ) . '</button></header><div class="rdct-lib__grid" data-library-design-grid></div></section>';
    $tag = $editor ? 'section' : 'dialog';
    echo '<' . $tag . ' class="rdct-lib__viewer" data-library-preview-panel aria-labelledby="rdct-library-preview-title"' . ( $editor ? ' hidden' : '' ) . '><header class="rdct-lib__viewer-header"><div><h2 id="rdct-library-preview-title" data-library-preview-title></h2><p data-library-preview-description></p></div><button type="button" class="' . esc_attr( $button ) . '" data-library-close-preview>' . esc_html__( 'Back to designs', 'radmanct-components' ) . '</button></header>';
    echo '<div class="rdct-lib__tools"><label>' . esc_html__( 'Design', 'radmanct-components' ) . '<select data-library-variant name="library-variant"></select></label><div role="group" aria-label="' . esc_attr__( 'Preview device', 'radmanct-components' ) . '">';
    foreach ( array( 'desktop' => __( 'Desktop', 'radmanct-components' ), 'tablet' => __( 'Tablet', 'radmanct-components' ), 'mobile' => __( 'Mobile', 'radmanct-components' ) ) as $device => $label ) { echo '<button class="' . esc_attr( $button ) . '" type="button" data-library-width="' . esc_attr( $device ) . '" aria-pressed="' . ( 'desktop' === $device ? 'true' : 'false' ) . '">' . esc_html( $label ) . '</button> '; }
    echo '</div></div><p class="rdct-loading" role="status" aria-live="polite" data-library-preview-status data-loading="' . esc_attr__( 'Loading preview…', 'radmanct-components' ) . '" data-ready="' . esc_attr__( 'Ready. Hover, click or scroll to explore this design.', 'radmanct-components' ) . '" data-error="' . esc_attr__( 'The preview could not load. Retry or choose another design.', 'radmanct-components' ) . '"></p><div class="rdct-lib__frame"><iframe title="' . esc_attr__( 'Component preview', 'radmanct-components' ) . '" referrerpolicy="same-origin"></iframe></div><footer class="rdct-lib__viewer-footer"><button class="' . esc_attr( $button ) . '" type="button" data-library-retry>' . esc_html__( 'Reload preview', 'radmanct-components' ) . '</button>';
    if ( $editor ) {
        if ( \RadmanCTComponents\Components\Library\atomic_available() ) { echo '<label>' . esc_html__( 'Element type', 'radmanct-components' ) . '<select data-library-mode><option value="classic">V3</option><option value="atomic">Atomic</option></select></label>'; }
        echo '<button class="e-btn e-primary" type="button" data-library-insert>' . esc_html__( 'Insert sample', 'radmanct-components' ) . '</button>';
    }
    echo '<p role="status" aria-live="polite" data-library-status data-inserted="' . esc_attr__( 'Sample added at the end of the page. Edit it in Elementor and save your page.', 'radmanct-components' ) . '" data-error="' . esc_attr__( 'Insertion could not be confirmed. Check the page before trying again.', 'radmanct-components' ) . '"></p></footer></' . $tag . '></div>';
}
