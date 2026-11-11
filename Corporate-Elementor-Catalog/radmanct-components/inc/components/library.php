<?php

namespace RadmanCTComponents\Components\Library;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/library-catalog.php';

function catalog(): array {
	require_once __DIR__ . '/carousel/profiles.php';
	require_once __DIR__ . '/testimonial/fields.php';
	require_once __DIR__ . '/button.php';
	$catalog = array(
		'accordion-lined' => array( 'component' => 'accordion', 'name' => __( 'Accordion', 'radmanct-components' ), 'variant' => __( 'Lined', 'radmanct-components' ), 'category' => __( 'Content', 'radmanct-components' ), 'image' => 'accordion.svg', 'widget' => 'rdct-accordion', 'settings' => array( 'style' => 'lined' ) ),
		'accordion-boxed' => array( 'component' => 'accordion', 'name' => __( 'Accordion', 'radmanct-components' ), 'variant' => __( 'Boxed', 'radmanct-components' ), 'category' => __( 'Content', 'radmanct-components' ), 'image' => 'accordion.svg', 'widget' => 'rdct-accordion', 'settings' => array( 'style' => 'boxed' ) ),
		'timeline-alternating' => array( 'component' => 'timeline', 'name' => __( 'Timeline', 'radmanct-components' ), 'variant' => __( 'Alternating image and text', 'radmanct-components' ), 'category' => __( 'Content', 'radmanct-components' ), 'image' => 'timeline.svg', 'widget' => 'rdct-timeline', 'settings' => array() ),
		'button-solid' => array( 'component' => 'button', 'name' => __( 'Button', 'radmanct-components' ), 'variant' => __( 'Solid', 'radmanct-components' ), 'category' => __( 'Content', 'radmanct-components' ), 'image' => 'button.svg', 'widget' => 'rdct-button', 'settings' => array( 'label' => __( 'Learn more', 'radmanct-components' ), 'style' => 'solid', 'icon' => 'arrow-right' ) ),
		'carousel-stack' => array( 'component' => 'carousel', 'name' => __( 'Carousel', 'radmanct-components' ), 'variant' => __( 'Stacked cards', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'carousel.svg', 'widget' => 'rdct-carousel', 'settings' => array( 'variant' => 'cards', 'mode' => 'stack', 'height_mode' => 'adaptive', 'duration' => 600 ) ),
		'particles-background' => array( 'component' => 'particles', 'name' => __( 'Particles', 'radmanct-components' ), 'variant' => __( 'Linked particles', 'radmanct-components' ), 'category' => __( 'Effects', 'radmanct-components' ), 'image' => 'particles.svg', 'widget' => 'rdct-particles', 'settings' => array( 'color' => '#4263eb', 'enable_line_linked' => true, 'line_color' => '#4263eb', 'size' => 3, 'height' => '360px' ) ),
		'carousel-cards' => array( 'component' => 'carousel', 'name' => __( 'Carousel', 'radmanct-components' ), 'variant' => __( 'Cards', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'carousel.svg', 'widget' => 'rdct-carousel', 'settings' => array( 'variant' => 'cards', 'slides' => 3.2 ) ),
		'carousel-brands' => array( 'component' => 'carousel', 'name' => __( 'Carousel', 'radmanct-components' ), 'variant' => __( 'Brand logos', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'carousel.svg', 'widget' => 'rdct-carousel', 'settings' => array( 'variant' => 'brands', 'slides' => 5 ) ),
		'box-cover' => array( 'component' => 'box', 'name' => __( 'Content Box', 'radmanct-components' ), 'variant' => __( 'Tall image cover', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'box.svg', 'widget' => 'rdct-box', 'settings' => array( 'template' => 'cover', 'title' => __( 'Title', 'radmanct-components' ), 'height' => 640, 'height_percent' => 125 ) ),
		'table-general' => array( 'component' => 'table', 'name' => __( 'Table', 'radmanct-components' ), 'variant' => __( 'General content table', 'radmanct-components' ), 'category' => __( 'Content', 'radmanct-components' ), 'image' => 'table.svg', 'widget' => 'rdct-table', 'settings' => array( 'variant' => 'grid' ) ),
		'image-reveal' => array( 'component' => 'image', 'name' => __( 'Image', 'radmanct-components' ), 'variant' => __( 'Reveal effect', 'radmanct-components' ), 'category' => __( 'Image', 'radmanct-components' ), 'image' => 'image.svg', 'widget' => 'rdct-image', 'settings' => array( 'reveal' => true, 'reveal_direction' => 'start', 'roundness' => '8px', 'shadow' => 'depth2' ) ),
		'icon-box-service' => array( 'component' => 'icon-box', 'name' => __( 'Icon Box', 'radmanct-components' ), 'variant' => __( 'Icon and content inline', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'icon-box.svg', 'widget' => 'rdct-icon-box', 'settings' => array( 'layout' => 'side', 'shape' => 'circle', 'icon' => 'check' ) ),
		'flipbox-card' => array( 'component' => 'flipbox', 'name' => __( 'Flip Box', 'radmanct-components' ), 'variant' => __( 'Front and back', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'flipbox.svg', 'widget' => 'rdct-flipbox', 'settings' => array( 'direction' => 'lr', 'front_content' => __( 'Front side', 'radmanct-components' ), 'back_content' => __( 'Back side', 'radmanct-components' ) ) ),
		'box-image' => array( 'component' => 'box', 'name' => __( 'Content Box', 'radmanct-components' ), 'variant' => __( 'Image and content', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'box.svg', 'widget' => 'rdct-box', 'settings' => array( 'template' => 's01', 'title' => __( 'Title', 'radmanct-components' ), 'label' => __( 'Label', 'radmanct-components' ) ) ),
		'curtain-panels' => array( 'component' => 'curtain', 'name' => __( 'Expandable Panels', 'radmanct-components' ), 'variant' => __( 'Expandable panels', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'curtain.svg', 'widget' => 'rdct-curtain', 'settings' => array( 'active_item' => 1, 'trigger' => 'click', 'heading_tag' => 'h3', 'gap' => 20, 'height' => 480 ) ),
		'curtain-images' => array( 'component' => 'curtain', 'name' => __( 'Expandable Panels', 'radmanct-components' ), 'variant' => __( 'Image accordion', 'radmanct-components' ), 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'curtain.svg', 'widget' => 'rdct-curtain', 'settings' => array( 'layout' => 'images', 'active_item' => 1, 'trigger' => 'hover', 'gap' => 12, 'height' => 640, 'duration' => 400, 'expanded_ratio' => 3, 'image_radius' => 26 ) ),
		'text-reveal-scroll' => array( 'component' => 'text-reveal', 'name' => __( 'Text', 'radmanct-components' ), 'variant' => __( 'Text effects', 'radmanct-components' ), 'keywords' => __( 'Marquee Rotating words Text fill', 'radmanct-components' ), 'category' => __( 'Text', 'radmanct-components' ), 'image' => 'text-reveal.svg', 'widget' => 'radman-text-reveal', 'settings' => array( 'tag' => 'h2', 'mode' => 'scroll', 'opacity' => 0.25, 'final_opacity' => 1, 'from' => 'start' ) ),
		'icon-phone' => array( 'component' => 'icons', 'name' => __( 'Icon', 'radmanct-components' ), 'variant' => __( 'Phone', 'radmanct-components' ), 'category' => __( 'Icon', 'radmanct-components' ), 'image' => 'icons.svg', 'widget' => 'radman-icon', 'settings' => array( 'name' => 'phone', 'size' => 48, 'stroke' => 1, 'mirror' => 'yes' ) ),
		'icon-arrow' => array( 'hidden' => true, 'component' => 'icons', 'name' => __( 'Icon', 'radmanct-components' ), 'variant' => __( 'Arrow right', 'radmanct-components' ), 'category' => __( 'Icon', 'radmanct-components' ), 'image' => 'icons.svg', 'widget' => 'radman-icon', 'settings' => array( 'name' => 'arrow-right', 'size' => 48, 'stroke' => 1, 'mirror' => 'yes' ) ),
	);
	foreach ( array( 'carousel-cards' => 'cards', 'carousel-brands' => 'logos', 'carousel-stack' => 'stack' ) as $id => $profile ) { $catalog[ $id ]['profile'] = $profile; $catalog[ $id ]['widget'] = 'rdct-carousel-' . $profile; $catalog[ $id ]['name'] = \RadmanCTComponents\Components\CarouselProfiles\definitions()[ $profile ]['title']; }
	foreach ( array( 'slider', 'marquee', 'testimonials' ) as $profile ) {
		$definition = \RadmanCTComponents\Components\CarouselProfiles\definitions()[ $profile ];
		$catalog[ 'carousel-' . $profile ] = array( 'component' => 'carousel', 'profile' => $profile, 'name' => $definition['title'], 'variant' => $definition['title'], 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'testimonials' === $profile ? 'testimonial.svg' : 'carousel.svg', 'widget' => 'rdct-carousel-' . $profile, 'settings' => array() );
	}
	foreach ( array( 'bubble', 'centered', 'split', 'outline', 'minimal' ) as $style ) { $item = $catalog['carousel-testimonials']; $item['settings']['testimonial_style'] = $style; $item['variant'] = \RadmanCTComponents\Components\Testimonial\styles()[ $style ]; $catalog[ 'testimonial-' . $style ] = $item; }
	foreach ( \RadmanCTComponents\Components\Testimonial\styles() as $style => $label ) { $catalog[ 'review-' . $style ] = array( 'component' => 'testimonial', 'name' => __( 'Customer review', 'radmanct-components' ), 'variant' => $label, 'category' => __( 'Content', 'radmanct-components' ), 'image' => 'testimonial.svg', 'widget' => 'rdct-testimonial', 'settings' => array( 'testimonial_style' => $style ) ); }
	require_once __DIR__ . '/sticky-sections.php';
	foreach ( array( 'stack' => 'StickyStack', 'overlap' => 'StickyOverlap', 'services' => 'ServicePanels', 'zoom' => 'ZoomCards', 'split' => 'StickySplit' ) as $mode => $class ) {
		$catalog[ 'sticky-' . $mode ] = array( 'component' => 'sticky-sections', 'name' => __( 'Sticky Sections', 'radmanct-components' ), 'variant' => \RadmanCTComponents\Components\StickySections\fields()['mode']['options'][ $mode ], 'category' => __( 'Layout', 'radmanct-components' ), 'image' => 'curtain.svg', 'widget' => 'rdct-sticky-' . $mode, 'atomic_class' => $class, 'settings' => array( 'mode' => $mode ) );
	}
	foreach ( array( 'outline', 'plain', 'underline' ) as $style ) { $item = $catalog['button-solid']; $item['settings']['style'] = $style; $item['variant'] = \RadmanCTComponents\Components\Button\fields()['style']['options'][ $style ]; $catalog[ 'button-' . $style ] = $item; }
	foreach ( array( 'switch-x', 'switch-y', 'change-y', 'marquee-x', 'marquee-y', 'liquid-x', 'liquid-y', 'liquid-x-alt', 'liquid-y-alt' ) as $effect ) { $item = $catalog['button-solid']; $item['settings']['text_effect'] = $effect; $item['variant'] = \RadmanCTComponents\Components\Button\fields()['text_effect']['options'][ $effect ]; $catalog[ 'button-' . $effect ] = $item; }
	return enhance_catalog( $catalog );
}

function sample_text( string $language ): string {
	static $samples = null;
	if ( null === $samples ) {
		$samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/component-samples.json' ), true );
	}
	return $samples[ 'fa' === $language ? 'fa' : 'en' ] ?? 'Thoughtful design brings your ideas to life.';
}

function atomic_available(): bool {
	return function_exists( '\\RadmanCTComponents\\Integrations\\Elementor\\Atomic\\enabled' )
		&& \RadmanCTComponents\Integrations\Elementor\Atomic\enabled()
		&& class_exists( '\\RadmanCTComponents\\Elementor\\AtomicComponents\\Icon', false )
		&& class_exists( '\\RadmanCTComponents\\Elementor\\AtomicComponents\\TextReveal', false );
}

function curtain_sample( string $language ): array {
	$samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/curtain-samples.json' ), true );
	return $samples[ 'fa' === $language ? 'fa' : 'en' ] ?? array();
}

function accordion_sample( string $language ): array {
	$samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/accordion-samples.json' ), true );
	return $samples[ 'fa' === $language ? 'fa' : 'en' ] ?? array();
}

function timeline_sample( string $language ): array {
	$samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/timeline-samples.json' ), true );
	return $samples[ 'fa' === $language ? 'fa' : 'en' ] ?? array();
}

function table_sample( string $language ): array {
	$samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/table-samples.json' ), true );
	return $samples[ 'fa' === $language ? 'fa' : 'en' ] ?? array();
}

function model( string $id, string $language, string $mode = 'classic' ): array {
	$item = catalog()[ $id ] ?? null;
	if ( ! $item || ! in_array( $mode, array( 'classic', 'atomic' ), true ) || ( 'atomic' === $mode && ! atomic_available() ) ) {
		return array();
	}
	$settings = $item['settings'];
	if ( 'accordion' === $item['component'] ) { $settings['items'] = accordion_sample( $language ); }
	if ( 'timeline' === $item['component'] ) { $settings['items'] = timeline_sample( $language ); }
	if ( 'carousel' === $item['component'] ) { $profile = $item['profile'] ?? ''; $settings = \RadmanCTComponents\Components\CarouselProfiles\options( $profile, $settings ); $settings['items'] = 'testimonials' === $profile ? \RadmanCTComponents\Components\CarouselProfiles\sample_items( $profile, $language ) : carousel_sample( $language ); }
	if ( 'table' === $item['component'] ) { $settings['data'] = table_sample( $language ); }
	if ( 'button' === $item['component'] ) { $settings = array_replace( $settings, button_sample( $language ) ); }
	if ( 'icon-box' === $item['component'] ) { $samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/icon-box-samples.json' ), true ); $settings += $samples[ 'fa' === $language ? 'fa' : 'en' ]; }
	if ( 'testimonial' === $item['component'] ) { $samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/testimonial-samples.json' ), true ); $settings += $samples[ 'fa' === $language ? 'fa' : 'en' ][0]; }
	if ( in_array( $item['component'], array( 'curtain', 'sticky-sections' ), true ) ) { $settings['items'] = curtain_sample( $language ); if ( 'sticky-sections' === $item['component'] ) { $settings['aside_content'] = $settings['items'][0]['content'] ?? ''; } }
	if ( 'text-reveal' === $item['component'] ) {
		$settings['text'] = sample_text( $language );
		$settings['direction'] = 'fa' === $language ? 'rtl' : 'ltr';
	}
	if ( 'atomic' === $mode ) {
		$class = array( 'accordion' => \RadmanCTComponents\Elementor\AtomicComponents\Accordion::class, 'timeline' => \RadmanCTComponents\Elementor\AtomicComponents\Timeline::class, 'sticky-sections' => \RadmanCTComponents\Elementor\AtomicComponents\StickySections::class, 'testimonial' => \RadmanCTComponents\Elementor\AtomicComponents\Testimonial::class, 'particles' => \RadmanCTComponents\Elementor\AtomicComponents\Particles::class, 'carousel' => \RadmanCTComponents\Elementor\AtomicComponents\Carousel::class, 'table' => \RadmanCTComponents\Elementor\AtomicComponents\Table::class, 'image' => \RadmanCTComponents\Elementor\AtomicComponents\Image::class, 'icons' => \RadmanCTComponents\Elementor\AtomicComponents\Icon::class, 'text-reveal' => \RadmanCTComponents\Elementor\AtomicComponents\TextReveal::class, 'curtain' => \RadmanCTComponents\Elementor\AtomicComponents\Curtain::class, 'box' => \RadmanCTComponents\Elementor\AtomicComponents\Box::class, 'flipbox' => \RadmanCTComponents\Elementor\AtomicComponents\FlipBox::class, 'button' => \RadmanCTComponents\Elementor\AtomicComponents\Button::class, 'icon-box' => \RadmanCTComponents\Elementor\AtomicComponents\IconBox::class )[ $item['component'] ];
		if ( 'sticky-sections' === $item['component'] ) { $class = '\\RadmanCTComponents\\Elementor\\AtomicComponents\\' . $item['atomic_class']; }
		if ( 'carousel' === $item['component'] && ! empty( $item['profile'] ) ) { $class = '\\RadmanCTComponents\\Elementor\\AtomicComponents\\' . \RadmanCTComponents\Components\CarouselProfiles\definitions()[ $item['profile'] ]['class']; }
		if ( ! class_exists( $class, false ) ) { return array(); }
		$schema = $class::get_props_schema();
		$typed = array();
		foreach ( $settings as $key => $value ) {
			if ( 'data' === $key && 'table' === $item['component'] ) { $value = $class::typed_data( $value ); }
			if ( 'items' === $key && in_array( $item['component'], array( 'accordion', 'timeline', 'curtain', 'carousel', 'sticky-sections' ), true ) ) { $value = $class::typed_items( $value ); }
			if ( 'carousel' === $item['component'] && in_array( $key, array( 'height', 'height_tablet', 'height_mobile' ), true ) && is_numeric( $value ) ) { $value = array( 'size' => (float) $value, 'unit' => 'px' ); }
			if ( 'mirror' === $key ) { $value = 'yes' === $value; }
			if ( ! isset( $schema[ $key ] ) ) { return array(); }
			$prop = $schema[ $key ];
			if ( $prop instanceof \Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type ) {
				if ( 'data' === $key && 'table' === $item['component'] ) { $prop = $prop->get_prop_type( 'rdct-table-data' ); }
				elseif ( 'items' === $key && in_array( $item['component'], array( 'accordion', 'timeline', 'curtain', 'carousel', 'sticky-sections' ), true ) ) { $prop = $prop->get_prop_type( 'rdct-' . $item['component'] . '-items' ); }
				elseif ( 'carousel' === $item['component'] && in_array( $key, array( 'height', 'height_tablet', 'height_mobile' ), true ) ) { $prop = $prop->get_prop_type( 'size' ); }
				else { $prop = $prop->get_prop_type_from_value( $value ); }
			}
			if ( ! is_callable( array( $prop, 'generate' ) ) ) { return array(); }
			$typed[ $key ] = $prop::generate( $value );
			if ( ! $schema[ $key ]->validate( $typed[ $key ] ) ) { return array(); }
		}
		return array( 'version' => '0.0', 'elType' => 'e-flexbox', 'settings' => array(), 'styles' => array(), 'elements' => array_fill( 0, ! empty( $item['gallery'] ) ? 3 : 1, array( 'version' => '0.0', 'elType' => 'widget', 'widgetType' => $class::get_element_type(), 'settings' => $typed, 'styles' => array(), 'elements' => array() ) ) );
	}
	$element = array( 'elType' => 'widget', 'widgetType' => $item['widget'], 'settings' => $settings, 'elements' => array() );
	return array( 'elType' => 'container', 'settings' => ! empty( $item['gallery'] ) ? array( 'flex_direction' => 'row', 'flex_wrap' => 'wrap' ) : array(), 'elements' => ! empty( $item['gallery'] ) ? array_fill( 0, 3, $element ) : array( $element ) );
}

function carousel_sample( string $language ): array {
	$samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/carousel-samples.json' ), true );
	return $samples[ 'fa' === $language ? 'fa' : 'en' ];
}

function button_sample( string $language ): array {
	$samples = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/languages/button-samples.json' ), true );
	return $samples[ 'fa' === $language ? 'fa' : 'en' ];
}
