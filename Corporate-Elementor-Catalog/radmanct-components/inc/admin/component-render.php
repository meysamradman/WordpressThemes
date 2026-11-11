<?php

namespace RadmanCTComponents\Admin\ComponentRender;

defined( 'ABSPATH' ) || exit;

add_action( 'wp_ajax_rdct_component_render', static function (): void {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_theme_options' ) ) { wp_send_json_error( null, 403 ); }
	$nonce = $_POST['_ajax_nonce'] ?? null;
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! is_string( $nonce ) || '' === $nonce || strlen( $nonce ) > 128 || ! check_ajax_referer( 'rdct_component_render', '_ajax_nonce', false ) ) { wp_send_json_error( null, 403 ); }
	$raw = isset( $_POST['settings'] ) && is_string( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : '';
	if ( strlen( $raw ) > 262144 ) { wp_send_json_error( null, 400 ); }
	$settings = json_decode( $raw, true, 32 );
	if ( ! is_array( $settings ) ) { wp_send_json_error( null, 400 ); }
	require_once dirname( __DIR__ ) . '/components/image-media.php';
	$settings = \RadmanCTComponents\Components\ImageMedia\editor_images( $settings );
	$type = isset( $_POST['component'] ) && is_string( $_POST['component'] ) ? $_POST['component'] : '';
	if ( ! in_array( $type, array( 'accordion', 'timeline', 'curtain', 'table', 'sticky-sections' ), true ) && strlen( $raw ) > 60000 ) { wp_send_json_error( null, 400 ); }
	$keys = array( 'src/css/base.css' );
	if ( in_array( $type, array( 'taxonomy-list', 'archive-search' ), true ) ) {
		require_once dirname( __DIR__ ) . '/components/' . $type . '.php';
		$html = 'taxonomy-list' === $type ? \RadmanCTComponents\Components\TaxonomyList\render( $settings ) : \RadmanCTComponents\Components\ArchiveSearch\render( $settings );
		foreach ( \RadmanCTComponents\Components\requirements( $type, $settings )['styles'] as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} elseif ( 'button' === $type ) {
		require_once dirname( __DIR__ ) . '/components/button.php';
		$html = \RadmanCTComponents\Components\Button\render( $settings );
		foreach ( \RadmanCTComponents\Components\requirements( 'button', $settings )['styles'] as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} elseif ( 'testimonial' === $type ) {
		require_once dirname( __DIR__ ) . '/components/testimonial/renderer.php';
		$html = \RadmanCTComponents\Components\Testimonial\render( $settings, $settings );
		foreach ( \RadmanCTComponents\Components\requirements( 'testimonial', $settings )['styles'] as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} elseif ( 'sticky-sections' === $type ) {
		require_once dirname( __DIR__ ) . '/components/sticky-sections.php';
		$html = \RadmanCTComponents\Components\StickySections\render( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		foreach ( \RadmanCTComponents\Components\requirements( 'sticky-sections', $settings )['styles'] as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} elseif ( 'particles' === $type ) {
		require_once dirname( __DIR__ ) . '/components/particles.php';
		$html = \RadmanCTComponents\Components\Particles\render( $settings );
		$keys[] = 'src/css/components/particles.css';
	} elseif ( 'text-reveal' === $type ) {
		require_once dirname( __DIR__ ) . '/components/text-reveal.php';
		$html = \RadmanCTComponents\Components\TextReveal\render( is_string( $settings['text'] ?? null ) ? $settings['text'] : '', $settings );
        foreach ( \RadmanCTComponents\Components\TextReveal\assets( $settings ) as $asset ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $asset ]['entry']; }
    } elseif ( 'carousel' === $type ) {
        require_once dirname( __DIR__ ) . '/components/carousel.php';
        require_once dirname( __DIR__ ) . '/components/carousel/profiles.php';
        $profile = is_string( $settings['carousel_profile'] ?? null ) ? $settings['carousel_profile'] : '';
        $items = is_array( $settings['items'] ?? null ) ? $settings['items'] : array();
        $html = $profile ? \RadmanCTComponents\Components\CarouselProfiles\render( $profile, $items, $settings ) : \RadmanCTComponents\Components\Carousel\render( $items, $settings );
        $assets = $profile ? \RadmanCTComponents\Components\CarouselProfiles\assets( $profile, $settings ) : \RadmanCTComponents\Components\Carousel\assets( $settings );
        foreach ( $assets as $asset ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $asset ]['entry']; }
	} elseif ( 'icon' === $type ) {
		$html = \RadmanCTComponents\Icons\render( is_string( $settings['name'] ?? null ) ? $settings['name'] : '', $settings );
		$keys[] = 'src/css/components/icons.css';
	} elseif ( 'table' === $type ) {
		require_once dirname( __DIR__ ) . '/components/table.php';
		$s = \RadmanCTComponents\Components\Table\settings( $settings );
		$html = \RadmanCTComponents\Components\Table\render( is_array( $settings['data'] ?? null ) ? $settings['data'] : array(), $settings );
		$keys[] = 'src/css/components/table/base.css';
		$keys[] = 'src/css/components/table/' . $s['variant'] . '.css';
	} elseif ( 'image' === $type ) {
		require_once dirname( __DIR__ ) . '/components/image.php';
		$settings['image_id'] = absint( $settings['image']['id'] ?? 0 );
		$settings['overlay_image_id'] = absint( $settings['overlay_image']['id'] ?? 0 );
		$html = \RadmanCTComponents\Components\Image\render( $settings );
		foreach ( \RadmanCTComponents\Components\Image\assets( $settings ) as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} elseif ( 'flipbox' === $type ) {
		require_once dirname( __DIR__ ) . '/components/flipbox.php';
		foreach ( array( 'front', 'back' ) as $side ) { $settings[ $side . '_image_id' ] = absint( $settings[ $side . '_image' ]['id'] ?? 0 ); }
		$s = \RadmanCTComponents\Components\FlipBox\settings( $settings );
		$html = \RadmanCTComponents\Components\FlipBox\render( $settings );
		$keys[] = 'src/css/components/flipbox/base.css';
		$keys[] = 'src/css/components/flipbox/' . $s['direction'] . '.css';
	} elseif ( 'icon-box' === $type ) {
		require_once dirname( __DIR__ ) . '/components/icon-box.php';
		$settings['image_id'] = absint( $settings['image']['id'] ?? 0 );
		$s = \RadmanCTComponents\Components\IconBox\settings( $settings );
		$html = \RadmanCTComponents\Components\IconBox\render( $settings );
		$keys[] = 'src/css/components/icons.css';
		$keys[] = 'src/css/components/icon-box/base.css';
		$keys[] = 'src/css/components/icon-box/' . $s['layout'] . '.css';
		if ( 'none' !== $s['animation'] ) { $keys[] = 'src/css/components/icon-box/animation.css'; }
	} elseif ( 'box' === $type ) {
		require_once dirname( __DIR__ ) . '/components/box.php';
		$settings['image_id'] = absint( $settings['image']['id'] ?? 0 );
		$html = \RadmanCTComponents\Components\Box\render( $settings );
		$keys[] = 'src/css/components/icons.css';
		foreach ( \RadmanCTComponents\Components\requirements( 'box', $settings )['styles'] as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} elseif ( 'accordion' === $type ) {
		require_once dirname( __DIR__ ) . '/components/accordion.php';
		$html = \RadmanCTComponents\Components\Accordion\render( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		foreach ( \RadmanCTComponents\Components\requirements( 'accordion', $settings )['styles'] as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} elseif ( 'timeline' === $type ) {
		require_once dirname( __DIR__ ) . '/components/timeline.php';
		$html = \RadmanCTComponents\Components\Timeline\render( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		foreach ( \RadmanCTComponents\Components\requirements( 'timeline', $settings )['styles'] as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} elseif ( 'curtain' === $type ) {
		require_once dirname( __DIR__ ) . '/components/curtain.php';
		$html = \RadmanCTComponents\Components\Curtain\render( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		$keys[] = 'src/css/components/icons.css';
		foreach ( \RadmanCTComponents\Components\requirements( 'curtain', $settings )['styles'] as $style ) { $keys[] = \RadmanCTComponents\Components\definitions()[ $style ]['entry']; }
	} else { wp_send_json_error( null, 400 ); }
	$styles = array();
	foreach ( $keys as $key ) { $asset = \RadmanCTComponents\Assets\entry( $key ); if ( $asset ) { $styles[] = \RadmanCTComponents\Assets\url( $asset['file'] ); } }
	wp_send_json_success( array( 'html' => $html, 'styles' => $styles ) );
} );
