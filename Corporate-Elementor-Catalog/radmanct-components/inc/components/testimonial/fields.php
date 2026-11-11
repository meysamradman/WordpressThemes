<?php

namespace RadmanCTComponents\Components\Testimonial;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/appearance.php';

function styles(): array {
	$styles = array( 'card' => __( 'Card', 'radmanct-components' ), 'bubble' => __( 'Speech bubble', 'radmanct-components' ), 'centered' => __( 'Centered', 'radmanct-components' ), 'split' => __( 'Side by side', 'radmanct-components' ), 'outline' => __( 'Outlined', 'radmanct-components' ), 'minimal' => __( 'Minimal', 'radmanct-components' ) );
	$styles['s01'] = __( '01 - Author first', 'radmanct-components' );
	$styles['s02'] = __( '02 - Full speech bubble', 'radmanct-components' );
	$styles['s03'] = __( '03 - Divided review card', 'radmanct-components' );
	$styles['s04'] = __( '04 - Compact social review', 'radmanct-components' );
	$styles['s05'] = __( '05 - Client logo above', 'radmanct-components' );
	$styles['s06'] = __( '06 - Centered spacious quote', 'radmanct-components' );
	$styles['s07'] = __( '07 - Portrait beside review', 'radmanct-components' );
	$styles['s08'] = __( '08 - Compact author first', 'radmanct-components' );
	$styles['s09'] = __( '09 - Bordered author first', 'radmanct-components' );
	$styles['s10'] = __( '10 - Centered quote badge', 'radmanct-components' );
	$styles['s11'] = __( '11 - Centered rating card', 'radmanct-components' );
	$styles['s12'] = __( '12 - Quote mark at start', 'radmanct-components' );
	$styles['s13'] = __( '13 - Large editorial quote', 'radmanct-components' );
	$styles['s14'] = __( '14 - Large author first', 'radmanct-components' );
	$styles['s15'] = __( '15 - Large speech bubble', 'radmanct-components' );
	$styles['s16'] = __( '16 - Portrait and client logo', 'radmanct-components' );
	$styles['s17'] = __( '17 - Centered speech bubble', 'radmanct-components' );
	$styles['s18'] = __( '18 - Accent and client logo', 'radmanct-components' );
	$styles['s19'] = __( '19 - Inline author and rating', 'radmanct-components' );
	return $styles;
}
function style( $value ): string { return is_string( $value ) && isset( styles()[ $value ] ) ? $value : 'card'; }
function fields(): array {
	$fields = array(
		'testimonial_style' => array( 'label' => __( 'Testimonial style', 'radmanct-components' ), 'default' => 'card', 'options' => styles() ),
		'testimonial_align' => array( 'label' => __( 'Text alignment', 'radmanct-components' ), 'default' => 'auto', 'options' => array( 'auto' => __( 'Automatic', 'radmanct-components' ), 'start' => __( 'Start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ) ),
		'testimonial_padding' => array( 'label' => __( 'Content padding', 'radmanct-components' ), 'default' => 28, 'min' => 0, 'max' => 96 ),
		'testimonial_radius' => array( 'label' => __( 'Border radius', 'radmanct-components' ), 'default' => 12, 'min' => 0, 'max' => 64 ),
	);
	foreach ( array( 'background' => __( 'Background color', 'radmanct-components' ), 'text' => __( 'Content color', 'radmanct-components' ), 'name' => __( 'Name color', 'radmanct-components' ), 'rating' => __( 'Rating color', 'radmanct-components' ) ) as $key => $label ) { $fields[ 'testimonial_' . $key . '_color' ] = array( 'label' => $label, 'default' => '', 'color' => true ); }
	foreach ( array( 'position' => __( 'Position color', 'radmanct-components' ), 'source' => __( 'Review source color', 'radmanct-components' ), 'border' => __( 'Border color', 'radmanct-components' ), 'accent' => __( 'Accent color', 'radmanct-components' ), 'quote' => __( 'Quote mark color', 'radmanct-components' ) ) as $key => $label ) { $fields[ 'testimonial_' . $key . '_color' ] = array( 'label' => $label, 'default' => '', 'color' => true ); }
	foreach ( presentation() as $key => $spec ) { $fields[ $key ] = array( 'label' => $spec['label'], 'default' => isset( $spec['options'] ) ? '' : -1, 'options' => $spec['options'] ?? null, 'min' => $spec['min'] ?? null, 'max' => $spec['max'] ?? null ); $fields[ $key ] = array_filter( $fields[ $key ], static fn( $value ) => null !== $value ); }
	return $fields;
}
function item_fields(): array {
	require_once dirname( __DIR__, 2 ) . '/icons.php';
	$brands = array_filter( \RadmanCTComponents\Icons\labels(), static fn( $key ) => str_starts_with( $key, 'brand-' ), ARRAY_FILTER_USE_KEY );
	return array(
		'source_icon' => array( 'label' => __( 'Review source icon', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'None', 'radmanct-components' ) ) + $brands ),
		'title' => array( 'label' => __( 'Name', 'radmanct-components' ), 'default' => '' ),
		'position' => array( 'label' => __( 'Position or company', 'radmanct-components' ), 'default' => '' ),
		'content' => array( 'label' => __( 'Review text', 'radmanct-components' ), 'default' => '', 'rich' => true ),
		'image_id' => array( 'label' => __( 'Avatar', 'radmanct-components' ), 'default' => 0, 'media' => true ),
		'client_image_id' => array( 'label' => __( 'Client logo', 'radmanct-components' ), 'default' => 0, 'media' => true ),
		'rating' => array( 'label' => __( 'Rating', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 5 ),
		'date' => array( 'label' => __( 'Review date', 'radmanct-components' ), 'default' => '' ),
		'source' => array( 'label' => __( 'Review source', 'radmanct-components' ), 'default' => '' ),
		'link' => array( 'label' => __( 'Source link', 'radmanct-components' ), 'default' => '', 'url' => true ),
	);
}

function widget_fields(): array {
	$fields = item_fields() + fields();
	$samples = json_decode( file_get_contents( dirname( __DIR__, 3 ) . '/languages/testimonial-samples.json' ), true );
	$locale = function_exists( 'determine_locale' ) ? determine_locale() : 'en_US';
	$sample = $samples[ str_starts_with( $locale, 'fa' ) ? 'fa' : 'en' ][0] ?? array();
	foreach ( $sample as $key => $value ) { if ( isset( $fields[ $key ] ) ) { $fields[ $key ]['default'] = $value; } }
	return $fields;
}
