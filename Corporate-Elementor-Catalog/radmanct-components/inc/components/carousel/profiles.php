<?php

namespace RadmanCTComponents\Components\CarouselProfiles;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/carousel.php';

function definitions(): array {
	return array(
		'cards' => array( 'title' => __( 'Card Carousel', 'radmanct-components' ), 'class' => 'CardCarousel', 'locks' => array( 'mode' => 'carousel', 'variant' => 'cards' ), 'defaults' => array() ),
		'logos' => array( 'title' => __( 'Logo Carousel', 'radmanct-components' ), 'class' => 'LogoCarousel', 'locks' => array( 'mode' => 'carousel', 'variant' => 'brands' ), 'defaults' => array( 'slides' => 5, 'slides_tablet' => 3, 'slides_mobile' => 2, 'label' => __( 'Brand logos', 'radmanct-components' ) ) ),
		'slider' => array( 'title' => __( 'Hero Slider', 'radmanct-components' ), 'class' => 'HeroSlider', 'locks' => array( 'mode' => 'slider', 'variant' => 'images' ), 'defaults' => array( 'height' => 560, 'height_tablet' => 440, 'height_mobile' => 360, 'label' => __( 'Slider', 'radmanct-components' ) ) ),
		'marquee' => array( 'title' => __( 'Marquee', 'radmanct-components' ), 'class' => 'MarqueeCarousel', 'locks' => array( 'mode' => 'marquee' ), 'defaults' => array( 'label' => __( 'Marquee', 'radmanct-components' ) ) ),
		'stack' => array( 'title' => __( 'Stacked Cards', 'radmanct-components' ), 'class' => 'StackCarousel', 'locks' => array( 'mode' => 'stack', 'variant' => 'cards' ), 'defaults' => array( 'height_mode' => 'adaptive', 'duration' => 600 ) ),
		'testimonials' => array( 'title' => __( 'Testimonials', 'radmanct-components' ), 'class' => 'Testimonials', 'locks' => array( 'mode' => 'carousel', 'variant' => 'cards' ), 'defaults' => array( 'slides' => 2, 'height_mode' => 'natural', 'label' => __( 'Testimonials', 'radmanct-components' ) ) ),
	);
}

function options( string $profile, array $input ): array {
	$definition = definitions()[ $profile ] ?? null;
	return $definition ? array_replace( $definition['defaults'], $input, $definition['locks'], array( 'carousel_profile' => $profile ) ) : $input;
}

function fields( string $profile ): array {
	$definition = definitions()[ $profile ] ?? null;
	$fields = \RadmanCTComponents\Components\Carousel\fields();
	if ( ! $definition ) { return $fields; }
	foreach ( $fields as $key => &$field ) {
		if ( array_key_exists( $key, $definition['defaults'] ) ) { $field['default'] = $definition['defaults'][ $key ]; }
		if ( array_key_exists( $key, $definition['locks'] ) ) {
			$field['default'] = $definition['locks'][ $key ]; $field['options'] = array( $field['default'] => $field['label'] ); $field['internal'] = true;
		}
		foreach ( $field['condition'] ?? array() as $dependency => $allowed ) {
			if ( ! array_key_exists( $dependency, $definition['locks'] ) ) { continue; }
			if ( ! in_array( $definition['locks'][ $dependency ], (array) $allowed, true ) ) { $field['internal'] = true; }
			unset( $field['condition'][ $dependency ] );
		}
	}
	unset( $field );
	if ( in_array( $profile, array( 'logos', 'testimonials' ), true ) ) { foreach ( array( 'media_motion', 'motion_duration', 'motion_scale' ) as $key ) { $fields[ $key ]['internal'] = true; } }
	if ( 'testimonials' === $profile ) { $fields['image_loading']['internal'] = true; }
	$fields['carousel_profile'] = array( 'label' => '', 'default' => $profile, 'options' => array( $profile => $profile ), 'internal' => true );
	if ( 'testimonials' === $profile ) { require_once dirname( __DIR__ ) . '/testimonial/fields.php'; $fields += \RadmanCTComponents\Components\Testimonial\fields(); }
	return $fields;
}

function item_fields( string $profile ): array {
	if ( 'testimonials' === $profile ) { require_once dirname( __DIR__ ) . '/testimonial/fields.php'; return \RadmanCTComponents\Components\Testimonial\item_fields(); }
	$fields = \RadmanCTComponents\Components\Carousel\item_fields();
	if ( in_array( $profile, array( 'slider', 'stack', 'marquee' ), true ) ) { unset( $fields['width'], $fields['width_tablet'], $fields['width_mobile'] ); }
	if ( 'logos' === $profile ) { unset( $fields['content'] ); }
	return $fields;
}

function sample_items( string $profile, ?string $language = null ): array {
	if ( 'testimonials' === $profile ) {
		$language = $language ?? ( function_exists( 'determine_locale' ) && str_starts_with( determine_locale(), 'fa' ) ? 'fa' : 'en' );
		$samples = json_decode( file_get_contents( dirname( __DIR__, 3 ) . '/languages/testimonial-samples.json' ), true );
		return $samples[ 'fa' === $language ? 'fa' : 'en' ];
	}
	return array( array( 'title' => __( 'First item', 'radmanct-components' ) ), array( 'title' => __( 'Second item', 'radmanct-components' ) ), array( 'title' => __( 'Third item', 'radmanct-components' ) ) );
}

function groups( string $profile ): array {
	$groups = \RadmanCTComponents\Components\Carousel\groups();
	if ( 'testimonials' === $profile ) { require_once dirname( __DIR__ ) . '/testimonial/fields.php'; $groups['testimonial'] = array( 'label' => __( 'Testimonial appearance', 'radmanct-components' ), 'tab' => 'style', 'fields' => array_keys( \RadmanCTComponents\Components\Testimonial\fields() ) ); }
	$fields = fields( $profile );
	foreach ( $groups as $key => &$group ) {
		$group['fields'] = array_values( array_filter( $group['fields'], static fn( $field ) => empty( $fields[ $field ]['internal'] ) ) );
		if ( ! $group['fields'] ) { unset( $groups[ $key ] ); }
	}
	unset( $group );
	return $groups;
}

function assets( string $profile, array $input ): array {
	$input = options( $profile, $input );
	$assets = \RadmanCTComponents\Components\Carousel\assets( $input );
	if ( 'slider' === $profile ) { $assets[] = 'carousel-hero'; }
	if ( 'testimonials' === $profile ) {
		require_once dirname( __DIR__ ) . '/testimonial/fields.php';
		$assets = array_values( array_diff( $assets, array( 'carousel-cards' ) ) );
		$assets[] = 'testimonial'; $assets[] = 'testimonial-' . \RadmanCTComponents\Components\Testimonial\style( $input['testimonial_style'] ?? '' );
	}
	return $assets;
}

function render( string $profile, array $items, array $input = array() ): string {
	if ( ! isset( definitions()[ $profile ] ) ) { return ''; }
	$input = options( $profile, $input );
	if ( 'testimonials' === $profile ) {
		require_once dirname( __DIR__ ) . '/testimonial/renderer.php';
		$styles = array();
		$items = array_map( static function ( $item ) use ( $input, &$styles ) { return array( 'render' => static function () use ( $item, $input, &$styles ) { return \RadmanCTComponents\Components\Testimonial\render( $item, $input, $styles ); } ); }, array_values( array_filter( array_slice( $items, 0, 50 ), 'is_array' ) ) );
	}
	$html = \RadmanCTComponents\Components\Carousel\render( $items, $input );
	if ( isset( $styles ) ) { $html = implode( '', $styles ) . $html; }
	return preg_replace( '/\brdct-crs--(cards|images|brands)\b/', 'rdct-crs--profile-' . $profile . ' $0', $html, 1 );
}
