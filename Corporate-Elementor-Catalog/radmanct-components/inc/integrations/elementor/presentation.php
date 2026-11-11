<?php

namespace RadmanCTComponents\Integrations\Elementor\Presentation;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 2 ) . '/components/presentation.php';

function control( $widget, string $type, string $key, array $field, array $extra = array() ): void {
	$spec = \RadmanCTComponents\Components\Presentation\controls( $type )[ $key ];
	$unit = $spec['unit'] ?? 'px';
	$control = array( 'label' => $field['label'], 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( $unit ), 'range' => array( $unit => array( 'min' => $spec['min'] ?? 0, 'max' => $spec['max'] ?? 100, 'step' => $spec['step'] ?? 1 ) ), 'default' => array( 'unit' => $unit ), 'render_type' => 'template', 'devices' => array( 'desktop', 'tablet', 'mobile' ) ) + $extra;
	if ( 'button' === $type ) { $control['devices'] = array( 'desktop', 'laptop', 'tablet', 'mobile' ); }
	if ( isset( $spec['unit_field'] ) ) { $control['size_units'] = array(); }
	if ( isset( $spec['units'] ) ) {
		$control['size_units'] = $spec['units'];
		$control['default'] = array( 'unit' => $spec['default_unit'] ?? $spec['units'][0] );
		foreach ( $spec['units'] as $allowed ) { $control['range'][ $allowed ] = ( $spec['range'][ $allowed ] ?? array( 'min' => $spec['min'], 'max' => $spec['max'] ) ) + array( 'step' => .1 ); }
	}
	if ( 'button' === $type && isset( $spec['allowed_units'] ) ) {
		$control['size_units'] = $spec['allowed_units'];
		foreach ( $spec['allowed_units'] as $allowed ) { $control['range'][ $allowed ] = ( $spec['range'][ $allowed ] ?? array( 'min' => $spec['min'], 'max' => $spec['max'] ) ) + array( 'step' => .1 ); }
	}
	if ( isset( $spec['options'] ) ) {
		if ( 'select' === ( $spec['options_ui'] ?? '' ) ) { $control['type'] = \Elementor\Controls_Manager::SELECT; $control['options'] = $spec['options']; $control['default'] = ''; $widget->add_responsive_control( $key . '_responsive', $control ); return; }
		$control['type'] = \Elementor\Controls_Manager::CHOOSE; $control['toggle'] = true; $control['default'] = '';
		$icons = 'layout' === $key ? array( 'top' => 'eicon-v-align-top', 'inline' => 'eicon-h-align-stretch', 'side' => 'eicon-h-align-left' ) : array( 'start' => 'eicon-text-align-' . ( is_rtl() ? 'right' : 'left' ), 'center' => 'eicon-text-align-center', 'end' => 'eicon-text-align-' . ( is_rtl() ? 'left' : 'right' ) );
		$icons['justify'] = 'eicon-text-align-justify';
		if ( 'vertical_align' === $key ) { $icons = array( 'auto' => 'eicon-ban', 'start' => 'eicon-v-align-top', 'center' => 'eicon-v-align-middle', 'end' => 'eicon-v-align-bottom' ); }
		if ( 'icon_side' === $key ) { $icons = array( 'start' => 'eicon-h-align-' . ( is_rtl() ? 'right' : 'left' ), 'end' => 'eicon-h-align-' . ( is_rtl() ? 'left' : 'right' ) ); }
		foreach ( $spec['options'] as $value => $label ) { $control['options'][ $value ] = array( 'title' => $label, 'icon' => $icons[ $value ] ); }
	}
	$widget->add_responsive_control( $key . '_responsive', $control );
}
