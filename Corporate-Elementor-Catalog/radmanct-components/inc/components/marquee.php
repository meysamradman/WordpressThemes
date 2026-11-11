<?php

namespace RadmanCTComponents\Components\Marquee;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/presentation.php';

function fields(): array {
	return array(
		'marquee_speed' => array( 'label' => __( 'Marquee speed (px/second)', 'radmanct-components' ), 'description' => __( 'Marquee animates static content. Linked or interactive content keeps manual scrolling.', 'radmanct-components' ), 'default' => 60, 'min' => 5, 'max' => 500, 'step' => 1 ),
		'marquee_speed_tablet' => array( 'label' => __( 'Tablet marquee speed (-1 inherits)', 'radmanct-components' ), 'default' => -1, 'min' => -1, 'max' => 500 ),
		'marquee_speed_mobile' => array( 'label' => __( 'Mobile marquee speed (-1 inherits)', 'radmanct-components' ), 'default' => -1, 'min' => -1, 'max' => 500 ),
		'marquee_gap' => array( 'label' => __( 'Marquee gap (px)', 'radmanct-components' ), 'default' => 40, 'min' => 0, 'max' => 300 ),
		'marquee_direction' => array( 'label' => __( 'Marquee direction', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Toward reading start', 'radmanct-components' ), 'end' => __( 'Toward reading end', 'radmanct-components' ), 'left' => __( 'Left', 'radmanct-components' ), 'right' => __( 'Right', 'radmanct-components' ) ) ),
		'marquee_pause' => array( 'label' => __( 'Pause marquee on hover', 'radmanct-components' ), 'default' => true ),
		'marquee_fade' => array( 'label' => __( 'Fade marquee edges', 'radmanct-components' ), 'default' => false ),
	);
}

function settings( array $input ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( is_bool( $field['default'] ) ) { $value = true === $value || 'yes' === $value; }
		elseif ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		else { $value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (float) $value ) ) : $field['default']; }
		$result[ $key ] = $value;
	}
	foreach ( array( 'marquee_speed_tablet', 'marquee_speed_mobile' ) as $key ) { if ( $result[ $key ] >= 0 ) { $result[ $key ] = max( 5, $result[ $key ] ); } }
	return $result;
}

function render( array $slides, array $options, string $class = '', string $style = '', bool $text = false ): string {
	$slides = array_filter( array_slice( $slides, 0, 50 ), static fn( $html ) => is_string( $html ) && '' !== trim( $html ) );
	if ( ! $slides ) { return ''; }
	$s = settings( $options );
	$breakpoints = \RadmanCTComponents\Components\Presentation\breakpoints( array( 'tablet' => 1023, 'mobile' => 767 ) );
	$s['breakpoints'] = array( 'tablet' => $breakpoints['mobile'] + 1, 'desktop' => $breakpoints['tablet'] + 1 );
	if ( ! $text ) { $s['columns'] = array( (float) ( $options['slides'] ?? 3 ), (float) ( $options['slides_tablet'] ?? 2 ), (float) ( $options['slides_mobile'] ?? 1 ) ); }
	$direction = in_array( $options['direction'] ?? '', array( 'rtl', 'ltr', 'auto' ), true ) ? ' dir="' . $options['direction'] . '"' : '';
	$units = implode( '', array_map( static fn( $html ) => '<div class="rdct-marquee__unit">' . $html . '</div>', $slides ) );
	$html = '<div class="rdct-marquee ' . esc_attr( $class ) . '"' . $direction . ' data-rdct-marquee="' . esc_attr( wp_json_encode( $s ) ) . '"' . ( $text ? ' data-rdct-text-reveal="marquee"' : ' data-rdct-carousel data-mode="marquee"' ) . ' style="' . esc_attr( $style . '--rdct-marquee-gap:' . $s['marquee_gap'] . 'px;' ) . '" aria-label="' . esc_attr( is_string( $options['label'] ?? null ) ? $options['label'] : __( 'Marquee', 'radmanct-components' ) ) . '"><div class="rdct-marquee__viewport"><div class="rdct-marquee__track"><div class="rdct-marquee__group">' . $units . '</div></div></div><button type="button" class="rdct-marquee__play" data-marquee-play hidden aria-pressed="true" aria-label="' . esc_attr__( 'Play or pause marquee', 'radmanct-components' ) . '">&#10074;&#10074;</button></div>';
	if ( ! $text && array( 'tablet' => 1023, 'mobile' => 767 ) !== $breakpoints ) {
		$token = 'rdct-mrq-r-' . substr( md5( serialize( $breakpoints ) ), 0, 12 );
		$selector = '.' . $token . '.' . $token . ' .rdct-marquee__unit';
		$height = 'var(--rdct-carousel-height-desktop,auto)';
		$css = $selector . '{block-size:' . $height . ';}';
		foreach ( array( 'tablet', 'mobile' ) as $device ) {
			$height = 'var(--rdct-carousel-height-' . $device . ',' . $height . ')';
			$css .= '@media(max-width:' . $breakpoints[ $device ] . 'px){' . $selector . '{block-size:' . $height . ';}}';
		}
		$html = '<style data-rdct-responsive>' . $css . '</style>' . preg_replace( '/class="/', 'class="' . $token . ' ', $html, 1 );
	}
	return $html;
}
