<?php

namespace RadmanCTComponents\Components\FlipBox;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/custom-content.php';
require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/image-media.php';

function fields(): array {
	$fields = array(
		'direction' => array( 'label' => __( 'Flip direction', 'radmanct-components' ), 'default' => 'lr', 'options' => array( 'lr' => __( 'Left to right', 'radmanct-components' ), 'rl' => __( 'Right to left', 'radmanct-components' ), 'tb' => __( 'Top to bottom', 'radmanct-components' ), 'bt' => __( 'Bottom to top', 'radmanct-components' ) ) ),
		'shadow' => array( 'label' => __( 'Shadow', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'always' => __( 'Default', 'radmanct-components' ), 'hover' => __( 'On hover', 'radmanct-components' ) ) ),
		'radius' => array( 'label' => __( 'Border radius', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'semi-round' => __( 'Semi round', 'radmanct-components' ), 'round' => __( 'Round', 'radmanct-components' ), 'circle' => __( 'Circle', 'radmanct-components' ) ) ),
		'height' => array( 'label' => __( 'Minimum height', 'radmanct-components' ), 'default' => 270, 'min' => 100, 'max' => 1600 ),
		'duration' => array( 'label' => __( 'Duration', 'radmanct-components' ), 'default' => 600, 'min' => 0, 'max' => 2000 ),
	);
	foreach ( array( 'front', 'back' ) as $side ) {
		$fields[ $side . '_type' ] = array( 'label' => __( 'Content type', 'radmanct-components' ), 'default' => 'html', 'options' => array( 'html' => __( 'Text editor', 'radmanct-components' ), 'template' => __( 'Elementor template', 'radmanct-components' ) ) );
		$fields[ $side . '_content' ] = array( 'label' => __( 'Content', 'radmanct-components' ), 'default' => 'front' === $side ? __( 'Front side', 'radmanct-components' ) : __( 'Back side', 'radmanct-components' ), 'multiline' => true, 'rich' => true );
		$fields[ $side . '_template' ] = array( 'label' => __( 'Select template', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX, 'template' => true );
		$fields[ $side . '_background_type' ] = array( 'label' => __( 'Background', 'radmanct-components' ), 'default' => 'classic', 'options' => array( 'classic' => __( 'Classic', 'radmanct-components' ), 'gradient' => __( 'Gradient', 'radmanct-components' ) ) );
		$fields[ $side . '_color' ] = array( 'label' => __( 'Background color', 'radmanct-components' ), 'default' => 'front' === $side ? '#176a6b' : '#172b3a', 'color' => true );
		$fields[ $side . '_text_color' ] = array( 'label' => __( 'Text color', 'radmanct-components' ), 'default' => '#ffffff', 'color' => true );
		$fields[ $side . '_second_color' ] = array( 'label' => __( 'Second color', 'radmanct-components' ), 'default' => '#172b3a', 'color' => true );
		$fields[ $side . '_gradient_type' ] = array( 'label' => __( 'Gradient type', 'radmanct-components' ), 'default' => 'linear', 'options' => array( 'linear' => __( 'Linear', 'radmanct-components' ), 'radial' => __( 'Radial', 'radmanct-components' ) ) );
		$fields[ $side . '_angle' ] = array( 'label' => __( 'Angle', 'radmanct-components' ), 'default' => 180, 'min' => 0, 'max' => 360 );
		$fields[ $side . '_position' ] = array( 'label' => __( 'Image position', 'radmanct-components' ), 'default' => 'center center', 'options' => array_combine( array( 'left top', 'center top', 'right top', 'left center', 'center center', 'right center', 'left bottom', 'center bottom', 'right bottom' ), array( 'Left top', 'Center top', 'Right top', 'Left center', 'Center center', 'Right center', 'Left bottom', 'Center bottom', 'Right bottom' ) ) );
		$fields[ $side . '_image_id' ] = array( 'label' => __( 'Background image', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX );
		$fields[ $side . '_overlay_type' ] = array( 'label' => __( 'Background overlay', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'classic' => __( 'Classic', 'radmanct-components' ), 'gradient' => __( 'Gradient', 'radmanct-components' ) ) );
		foreach ( array( 'overlay_color' => '#000000', 'overlay_second_color' => '#172b3a' ) as $key => $default ) { $fields[ $side . '_' . $key ] = array( 'label' => 'overlay_color' === $key ? __( 'Overlay color', 'radmanct-components' ) : __( 'Overlay second color', 'radmanct-components' ), 'default' => $default, 'color' => true ); }
		$fields[ $side . '_overlay_opacity' ] = array( 'label' => __( 'Overlay opacity', 'radmanct-components' ), 'default' => 0.4, 'min' => 0, 'max' => 1 );
		$fields[ $side . '_overlay_angle' ] = array( 'label' => __( 'Overlay angle', 'radmanct-components' ), 'default' => 180, 'min' => 0, 'max' => 360 );
		foreach ( array( 'top' => 40, 'right' => 50, 'bottom' => 40, 'left' => 50 ) as $edge => $default ) { $fields[ $side . '_padding_' . $edge ] = array( 'label' => __( 'Padding', 'radmanct-components' ) . ' ' . $edge, 'default' => $default, 'min' => 0, 'max' => 300 ); }
	}
	return $fields;
}

function settings( array $input ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( ! empty( $field['color'] ) ) { $value = is_string( $value ) && preg_match( '/^#(?:[0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/iD', $value ) ? $value : $field['default']; }
		elseif ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( isset( $field['min'] ) ) { $value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (float) $value ) ) : $field['default']; }
		else { $value = is_string( $value ) ? wp_kses_post( substr( $value, 0, 20000 ) ) : $field['default']; }
		$result[ $key ] = $value;
	}
	return $result;
}

function templates(): array {
	require_once __DIR__ . '/template-library.php';
	return \RadmanCTComponents\Components\TemplateLibrary\options();
}

function template( int $id ): string {
	require_once __DIR__ . '/template-library.php';
	return \RadmanCTComponents\Components\TemplateLibrary\render( $id );
}

function face_style( array $s, string $side ): string {
	$prefix = $side . '_';
	$background = $s[ $prefix . 'color' ];
	if ( 'gradient' === $s[ $prefix . 'background_type' ] ) { $background = 'radial' === $s[ $prefix . 'gradient_type' ] ? 'radial-gradient(circle,' . $background . ',' . $s[ $prefix . 'second_color' ] . ')' : 'linear-gradient(' . $s[ $prefix . 'angle' ] . 'deg,' . $background . ',' . $s[ $prefix . 'second_color' ] . ')'; }
	$overlay = 'none' === $s[ $prefix . 'overlay_type' ] ? 'transparent' : $s[ $prefix . 'overlay_color' ];
	if ( 'gradient' === $s[ $prefix . 'overlay_type' ] ) { $overlay = 'linear-gradient(' . $s[ $prefix . 'overlay_angle' ] . 'deg,' . $overlay . ',' . $s[ $prefix . 'overlay_second_color' ] . ')'; }
	$padding = implode( ' ', array_map( static fn( $edge ) => $s[ $prefix . 'padding_' . $edge ] . 'px', array( 'top', 'right', 'bottom', 'left' ) ) );
	return '--rdct-fb-background:' . $background . ';--rdct-fb-text-color:' . $s[ $prefix . 'text_color' ] . ';--rdct-fb-padding:' . $padding . ';--rdct-fb-overlay:' . $overlay . ';--rdct-fb-opacity:' . $s[ $prefix . 'overlay_opacity' ] . ';--rdct-fb-position:' . $s[ $prefix . 'position' ] . ';';
}

function render( array $input ): string {
	$s = settings( $input );
	$id = wp_unique_id( 'rdct-fb-' );
	$html = '<div class="rdct-fb rdct-fb--' . $s['direction'] . ' rdct-fb--shadow-' . $s['shadow'] . ' rdct-fb--' . $s['radius'] . '" data-rdct-flipbox style="--rdct-fb-min-height:' . $s['height'] . 'px;--rdct-fb-duration:' . $s['duration'] . 'ms">';
	$html .= '<button type="button" class="rdct-fb__toggle" aria-expanded="false" aria-controls="' . esc_attr( $id ) . '" data-rdct-fb-toggle hidden><span>' . esc_html__( 'Flip card', 'radmanct-components' ) . '</span></button><div class="rdct-fb__wrap" id="' . esc_attr( $id ) . '">';
	foreach ( array( 'front', 'back' ) as $side ) {
		$content = $s[ $side . '_content' ];
		$custom_key = \RadmanCTComponents\Components\CustomContent\key( array( 'custom_content' => $input[ $side . '_custom_content' ] ?? '' ) );
		if ( $custom_key ) { $content = \RadmanCTComponents\Components\CustomContent\render( $custom_key, array( 'slot' => 'flipbox-' . $side ) ); }
		elseif ( 'template' === $s[ $side . '_type' ] ) { $content = template( (int) $s[ $side . '_template' ] ) ?: $content; }
		$image = '';
		$image_id = (int) apply_filters( 'wpml_object_id', (int) $s[ $side . '_image_id' ], 'attachment', true );
		if ( $image_id && wp_attachment_is_image( $image_id ) ) { $image = wp_get_attachment_image( $image_id, \RadmanCTComponents\Components\ImageMedia\size( $input[ $side . '_image' ] ?? null ), false, array( 'class' => 'rdct-fb__image', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ); }
		$html .= '<div class="rdct-fb__face rdct-fb__' . $side . '" data-rdct-fb-face="' . $side . '" style="' . esc_attr( face_style( $s, $side ) ) . '">' . $image . '<span class="rdct-fb__overlay" aria-hidden="true"></span><div class="rdct-fb__inner">' . $content . '</div></div>';
	}
	return \RadmanCTComponents\Components\Presentation\render( $html . '</div></div>', $input, 'flipbox' );
}
