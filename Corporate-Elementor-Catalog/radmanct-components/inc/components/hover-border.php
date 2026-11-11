<?php

namespace RadmanCTComponents\Components\HoverBorder;

defined( 'ABSPATH' ) || exit;

function fields(): array {
	return array(
		'enabled' => array( 'label' => __( 'Animated hover border', 'radmanct-components' ), 'default' => false ),
		'color_start' => array( 'label' => __( 'Border start color', 'radmanct-components' ), 'default' => '#64e8c6', 'color' => true ),
		'color_end' => array( 'label' => __( 'Border end color', 'radmanct-components' ), 'default' => '#8b5cf6', 'color' => true ),
		'width' => array( 'label' => __( 'Animated border width (px)', 'radmanct-components' ), 'default' => 2, 'min' => .5, 'max' => 12, 'step' => .5 ),
		'duration' => array( 'label' => __( 'Border rotation duration (seconds)', 'radmanct-components' ), 'default' => 4, 'min' => 1, 'max' => 30, 'step' => .1 ),
		'direction' => array( 'label' => __( 'Border rotation direction', 'radmanct-components' ), 'default' => 'auto', 'options' => array( 'auto' => __( 'Automatic', 'radmanct-components' ), 'clockwise' => __( 'Clockwise', 'radmanct-components' ), 'counterclockwise' => __( 'Counterclockwise', 'radmanct-components' ) ) ),
	);
}

function settings( array $input ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( is_bool( $field['default'] ) ) { $value = true === $value || 'yes' === $value; }
		elseif ( isset( $field['min'] ) ) { $value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (float) $value ) ) : $field['default']; }
		elseif ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		else { $value = is_string( $value ) && preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{4}|[a-f0-9]{6}|[a-f0-9]{8})$/iD', $value ) ? $value : $field['default']; }
		$result[ $key ] = $value;
	}
	return $result;
}

function assets( bool $elementor = false ): void {
	\RadmanCTComponents\Components\use_component( 'hover-border' );
	\RadmanCTComponents\Assets\frontend();
	if ( $elementor ) { wp_enqueue_script( 'rdct-elementor-components' ); }
	static $scheduled = false;
	if ( ! $scheduled ) {
		$scheduled = true;
		add_action( 'wp_footer', static function (): void { \RadmanCTComponents\Components\print_late_style( 'hover-border' ); }, 9 );
	}
}

function attributes( array $options ): string {
	$s = settings( $options );
	if ( ! $s['enabled'] ) { return ''; }
	assets();
	return 'data-rdct-border="' . esc_attr( wp_json_encode( $s ) ) . '"';
}
