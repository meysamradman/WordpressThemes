<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;

function fields( string $type ): array {
	if ( 'taxonomy-list' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/taxonomy-list.php'; return \RadmanCTComponents\Components\TaxonomyList\fields(); }
	if ( 'archive-search' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/archive-search.php'; return \RadmanCTComponents\Components\ArchiveSearch\fields(); }
	if ( 'button' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/button.php'; $fields = \RadmanCTComponents\Components\Button\fields(); $fields['icon']['options'] = array( '' => __( 'None', 'radmanct-components' ) ) + \RadmanCTComponents\Icons\labels(); return $fields; }
	if ( 'sticky-sections' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/sticky-sections.php'; return \RadmanCTComponents\Components\StickySections\fields(); }
	if ( 'testimonial' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/testimonial/fields.php'; return \RadmanCTComponents\Components\Testimonial\widget_fields(); }
	if ( str_starts_with( $type, 'carousel-' ) ) { require_once dirname( __DIR__, 2 ) . '/components/carousel/profiles.php'; return \RadmanCTComponents\Components\CarouselProfiles\fields( substr( $type, 9 ) ); }
	if ( 'particles' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/particles.php'; return \RadmanCTComponents\Components\Particles\fields(); }
	if ( 'carousel' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/carousel.php'; return \RadmanCTComponents\Components\Carousel\fields(); }
	if ( 'table' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/table.php'; return \RadmanCTComponents\Components\Table\fields(); }
 if ( 'image' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/image.php'; return \RadmanCTComponents\Components\Image\fields(); }
 if ( 'icon-box' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/icon-box.php'; $fields = \RadmanCTComponents\Components\IconBox\fields(); $fields['icon']['options'] = \RadmanCTComponents\Icons\labels(); return $fields; }
 if ( 'flipbox' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/flipbox.php'; return \RadmanCTComponents\Components\FlipBox\fields(); }
	if ( 'box' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/box.php'; $fields = \RadmanCTComponents\Components\Box\fields(); $fields['icon']['options'] = array( '' => __( 'None', 'radmanct-components' ) ) + \RadmanCTComponents\Icons\labels(); return $fields; }
	if ( 'accordion' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/accordion.php'; return \RadmanCTComponents\Components\Accordion\fields(); }
	if ( 'timeline' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/timeline.php'; return \RadmanCTComponents\Components\Timeline\fields(); }
	if ( 'curtain' === $type ) { require_once dirname( __DIR__, 2 ) . '/components/curtain.php'; return \RadmanCTComponents\Components\Curtain\fields(); }
	if ( 'icon' === $type ) {
		return array(
			'name' => array( 'label' => __( 'Icon', 'radmanct-components' ), 'default' => 'phone', 'options' => \RadmanCTComponents\Icons\labels() ),
			'size' => array( 'label' => __( 'Icon size', 'radmanct-components' ), 'default' => 24, 'min' => 8, 'max' => 128 ),
			'stroke' => array( 'label' => __( 'Stroke width', 'radmanct-components' ), 'default' => 1, 'min' => 1, 'max' => 3 ),
			'icon_align' => array( 'label' => __( 'Alignment', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ) ),
			'label' => array( 'label' => __( 'Accessible label', 'radmanct-components' ), 'default' => '' ),
			'mirror' => array( 'label' => __( 'Mirror directional icons in RTL', 'radmanct-components' ), 'default' => true ),
		);
	}
	require_once dirname( __DIR__, 2 ) . '/components/text-reveal.php';
	return \RadmanCTComponents\Components\TextReveal\fields();
}

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() || ! wp_script_is( 'rdct-curtain-controls', 'enqueued' ) ) { return; }
	require_once dirname( __DIR__, 2 ) . '/components/particles.php';
	$config = array( 'controlKey' => 'rdct-particle-colors', 'itemType' => 'rdct-particle-color', 'maximum' => 16, 'groups' => false, 'fields' => \RadmanCTComponents\Components\Particles\color_fields(), 'labels' => array( 'item' => __( 'Color', 'radmanct-components' ), 'remove' => __( 'Remove', 'radmanct-components' ) ) );
	wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctParticleColors=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}, 20 );

function register( $manager ): void {
	if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() || ! class_exists( '\\Elementor\\Modules\\AtomicWidgets\\Elements\\Base\\Atomic_Widget_Base' ) ) { return; }
	require_once dirname( __DIR__, 2 ) . '/icons.php';
	require_once __DIR__ . '/atomic-components/widget.php';
	require_once __DIR__ . '/atomic-components/curtain.php';
	require_once __DIR__ . '/atomic-components/timeline.php';
	require_once __DIR__ . '/atomic-components/accordion.php';
	require_once __DIR__ . '/atomic-components/sticky-sections.php';
	require_once __DIR__ . '/atomic-components/box.php';
	require_once __DIR__ . '/atomic-components/flipbox.php';
	require_once __DIR__ . '/atomic-components/icon-box.php';
	require_once __DIR__ . '/atomic-components/button.php';
	require_once __DIR__ . '/atomic-components/image.php';
	require_once __DIR__ . '/atomic-components/table.php';
	require_once __DIR__ . '/atomic-components/carousel.php';
	require_once __DIR__ . '/atomic-components/testimonial.php';
	require_once __DIR__ . '/atomic-components/particles.php';
	require_once __DIR__ . '/atomic-components/content-ui.php';
	$manager->register( new TaxonomyList() );
	$manager->register( new ArchiveSearch() );
	$manager->register( new Icon() );
	$manager->register( new TextReveal() );
	$manager->register( new Curtain() );
	$manager->register( new Timeline() );
	$manager->register( new Accordion() );
	$manager->register( new StickySections() );
	foreach ( array( StickyStack::class, StickyOverlap::class, ServicePanels::class, ZoomCards::class, StickySplit::class ) as $class ) { $manager->register( new $class() ); }
	$manager->register( new Box() );
	$manager->register( new FlipBox() );
	$manager->register( new IconBox() );
	$manager->register( new Button() );
	$manager->register( new Image() );
	$manager->register( new Table() );
	$manager->register( new Carousel() );
	$manager->register( new Testimonial() );
	foreach ( array( CardCarousel::class, LogoCarousel::class, HeroSlider::class, MarqueeCarousel::class, StackCarousel::class, Testimonials::class ) as $class ) { $manager->register( new $class() ); }
	$manager->register( new Particles() );
}

function preview_assets(): void {
	if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() ) { return; }
	$entry = \RadmanCTComponents\Assets\entry( 'src/js/integrations/elementor-atomic-components.js' );
	if ( ! $entry ) { return; }
	require_once dirname( __DIR__, 2 ) . '/icons.php';
	require_once dirname( __DIR__, 2 ) . '/components/box.php';
	$icons = array();
	foreach ( array_keys( \RadmanCTComponents\Icons\catalog() ) as $name ) { $icons[ $name ] = \RadmanCTComponents\Icons\render( $name ); }
	wp_enqueue_script( 'rdct-atomic-components-preview', \RadmanCTComponents\Assets\url( $entry['file'] ), array( 'elementor-frontend', 'rdct-components' ), null, true );
	wp_add_inline_script( 'rdct-atomic-components-preview', 'window.rdctAtomicComponents=' . wp_json_encode( array( 'fields' => array( 'icon' => fields( 'icon' ), 'text-reveal' => fields( 'text-reveal' ) ), 'icons' => $icons, 'renderUrl' => admin_url( 'admin-ajax.php' ), 'renderNonce' => wp_create_nonce( 'rdct_component_render' ), 'renderError' => __( 'Preview could not be loaded.', 'radmanct-components' ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}

add_action( 'elementor/widgets/register', __NAMESPACE__ . '\\register', 20 );
add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() || ! wp_script_is( 'elementor-v2-editor-editing-panel', 'registered' ) ) { return; }
	$entry = \RadmanCTComponents\Assets\entry( 'src/js/admin/atomic-colors.js' );
	if ( ! $entry ) { return; }
	wp_enqueue_script( 'rdct-atomic-colors', \RadmanCTComponents\Assets\url( $entry['file'] ), array( 'elementor-v2-editor-editing-panel', 'wp-element' ), null, true );
	$config = array();
	foreach ( array( 'button', 'icon', 'icon-box', 'box', 'flipbox', 'table', 'curtain', 'text-reveal', 'testimonial', 'carousel-testimonials' ) as $type ) {
		$fields = fields( $type );
		foreach ( \RadmanCTComponents\Components\Presentation\controls( $type ) as $key => $spec ) { $config[ 'rdct-responsive-' . $type . '-' . str_replace( '_', '-', $key ) ] = $spec + array( 'label' => $fields[ $key ]['label'] ?? $key, 'default' => $fields[ $key ]['default'] ?? 0 ); }
	}
	foreach ( $config as $key => &$spec ) { if ( str_starts_with( $key, 'rdct-responsive-button-' ) ) { $spec['devices'] = array( 'desktop', 'laptop', 'tablet', 'mobile' ); } }
	unset( $spec );
	wp_add_inline_script( 'rdct-atomic-colors', 'window.rdctResponsiveControls=' . wp_json_encode( array( 'controls' => $config, 'labels' => array( 'desktop' => __( 'Desktop', 'radmanct-components' ), 'laptop' => __( 'Laptop', 'radmanct-components' ), 'tablet' => __( 'Tablet', 'radmanct-components' ), 'mobile' => __( 'Mobile', 'radmanct-components' ), 'device' => __( 'Device', 'radmanct-components' ), 'inherit' => __( 'Reset to inherited value', 'radmanct-components' ) ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}, 9 );
add_action( 'elementor/preview/enqueue_scripts', __NAMESPACE__ . '\\preview_assets' );
add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() ) { return; }
	require_once dirname( __DIR__, 2 ) . '/components/curtain.php';
	require_once dirname( __DIR__, 2 ) . '/icons.php';
	$entry = \RadmanCTComponents\Assets\entry( 'src/js/admin/atomic-curtain-controls.js' );
	if ( ! $entry || ! wp_script_is( 'elementor-v2-editor-editing-panel', 'registered' ) ) { return; }
	wp_enqueue_media();
	wp_enqueue_script( 'rdct-curtain-controls', \RadmanCTComponents\Assets\url( $entry['file'] ), array( 'elementor-v2-editor-editing-panel', 'wp-element', 'media-editor' ), null, true );
	$image_props = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( 'image' )->get_props();
	wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctImageControlProps=' . wp_json_encode( $image_props, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	require_once dirname( __DIR__, 2 ) . '/components/template-library.php';
	$fields = \RadmanCTComponents\Components\Curtain\item_fields();
	foreach ( array( 'image_url', 'background_url' ) as $key ) { $fields[ $key ] = array( 'default' => '', 'internal' => true ); }
	wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctCurtainControls=' . wp_json_encode( array( 'fields' => $fields, 'templates' => \RadmanCTComponents\Components\TemplateLibrary\options(), 'icons' => array( '' => __( 'None', 'radmanct-components' ) ) + \RadmanCTComponents\Icons\labels(), 'labels' => array( 'normal' => __( 'Normal', 'radmanct-components' ), 'active' => __( 'Active', 'radmanct-components' ), 'inherit' => __( 'Inherit', 'radmanct-components' ), 'item' => __( 'Title', 'radmanct-components' ), 'add' => __( 'Add item', 'radmanct-components' ), 'remove' => __( 'Remove', 'radmanct-components' ), 'duplicate' => __( 'Duplicate', 'radmanct-components' ), 'up' => __( 'Move up', 'radmanct-components' ), 'down' => __( 'Move down', 'radmanct-components' ) ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
} );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array {
	if ( in_array( $attributes['id'] ?? '', array( 'rdct-atomic-components-preview-js', 'rdct-curtain-controls-js', 'rdct-atomic-colors-js' ), true ) ) { $attributes['type'] = 'module'; }
	return $attributes;
} );

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() || ! wp_script_is( 'rdct-curtain-controls', 'enqueued' ) ) { return; }
	require_once dirname( __DIR__, 2 ) . '/components/carousel.php';
	$config = array( 'controlKey' => 'rdct-carousel-items', 'itemType' => 'rdct-carousel-item', 'maximum' => 50, 'batchImages' => true, 'groups' => false, 'fields' => \RadmanCTComponents\Components\Carousel\item_fields(), 'templates' => \RadmanCTComponents\Components\TemplateLibrary\options(), 'icons' => array(), 'labels' => array( 'item' => __( 'Slide', 'radmanct-components' ), 'addImages' => __( 'Add images', 'radmanct-components' ), 'remove' => __( 'Remove', 'radmanct-components' ) ) );
	wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctCarouselControls=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	require_once dirname( __DIR__, 2 ) . '/components/carousel/profiles.php';
	$profiles = array();
	foreach ( array( 'cards', 'logos', 'slider', 'marquee', 'stack' ) as $profile ) {
		$profile_config = array( 'controlKey' => 'rdct-carousel-items-' . $profile, 'propTypeKey' => 'rdct-carousel-items', 'fields' => array_keys( \RadmanCTComponents\Components\CarouselProfiles\item_fields( $profile ) ) );
		$profiles[] = $profile_config;
	}
	wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctCarouselProfileControls=' . wp_json_encode( $profiles, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	require_once dirname( __DIR__, 2 ) . '/components/testimonial/fields.php';
	$config = array( 'controlKey' => 'rdct-testimonial-items', 'propTypeKey' => 'rdct-carousel-items', 'itemType' => 'rdct-carousel-item', 'maximum' => 50, 'groups' => false, 'fields' => \RadmanCTComponents\Components\Testimonial\item_fields(), 'labels' => array( 'item' => __( 'Customer review', 'radmanct-components' ) ) );
	wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctTestimonialControls=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}, 20 );

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
    if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() || ! wp_script_is( 'rdct-curtain-controls', 'enqueued' ) ) { return; }
    require_once dirname( __DIR__, 2 ) . '/components/text-reveal.php';
    $config = array( 'controlKey' => 'rdct-text-words', 'itemType' => 'rdct-text-word', 'maximum' => 30, 'groups' => false, 'fields' => \RadmanCTComponents\Components\TextReveal\word_fields(), 'labels' => array( 'item' => __( 'Word or phrase', 'radmanct-components' ), 'remove' => __( 'Remove', 'radmanct-components' ) ) );
    wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctTextControls=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
    $segments = array( 'controlKey' => 'rdct-text-segments', 'itemType' => 'rdct-text-segment', 'maximum' => 30, 'initialGroup' => 'content', 'groupOptions' => \RadmanCTComponents\Components\TextReveal\segment_groups(), 'fields' => \RadmanCTComponents\Components\TextReveal\segment_fields(), 'labels' => array( 'item' => __( 'Text segment', 'radmanct-components' ), 'remove' => __( 'Remove', 'radmanct-components' ) ) );
    wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctTextSegmentsControls=' . wp_json_encode( $segments, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}, 20 );

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
 if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() || ! wp_script_is( 'rdct-curtain-controls', 'enqueued' ) ) { return; }
 require_once dirname( __DIR__, 2 ) . '/components/sticky-sections.php';
 $config = array( 'controlKey' => 'rdct-sticky-section-items', 'itemType' => 'rdct-sticky-section-item', 'maximum' => 12, 'groups' => false, 'fields' => \RadmanCTComponents\Components\StickySections\item_fields(), 'templates' => \RadmanCTComponents\Components\TemplateLibrary\options(), 'labels' => array( 'item' => __( 'Section title', 'radmanct-components' ) ) );
 wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctStickySectionControls=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}, 20 );

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
 if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() || ! wp_script_is( 'rdct-curtain-controls', 'enqueued' ) ) { return; }
 require_once dirname( __DIR__, 2 ) . '/components/timeline.php';
 $config = array( 'controlKey' => 'rdct-timeline-items', 'itemType' => 'rdct-timeline-item', 'maximum' => 100, 'groups' => false, 'fields' => \RadmanCTComponents\Components\Timeline\item_fields(), 'labels' => array( 'item' => __( 'Timeline', 'radmanct-components' ), 'add' => __( 'Add item', 'radmanct-components' ), 'remove' => __( 'Remove', 'radmanct-components' ), 'duplicate' => __( 'Duplicate', 'radmanct-components' ), 'up' => __( 'Move up', 'radmanct-components' ), 'down' => __( 'Move down', 'radmanct-components' ) ) );
 wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctTimelineControls=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}, 20 );

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
 if ( ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() || ! wp_script_is( 'rdct-curtain-controls', 'enqueued' ) ) { return; }
 require_once dirname( __DIR__, 2 ) . '/components/accordion.php';
 $config = array( 'controlKey' => 'rdct-accordion-items', 'itemType' => 'rdct-accordion-item', 'maximum' => 100, 'groups' => false, 'fields' => \RadmanCTComponents\Components\Accordion\item_fields(), 'labels' => array( 'item' => __( 'Accordion', 'radmanct-components' ), 'add' => __( 'Add item', 'radmanct-components' ), 'remove' => __( 'Remove', 'radmanct-components' ), 'duplicate' => __( 'Duplicate', 'radmanct-components' ), 'up' => __( 'Move up', 'radmanct-components' ), 'down' => __( 'Move down', 'radmanct-components' ) ) );
 wp_add_inline_script( 'rdct-curtain-controls', 'window.rdctAccordionControls=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}, 20 );
