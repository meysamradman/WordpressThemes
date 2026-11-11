<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;

function field_control( string $key, array $field ): array {
	require_once dirname( __DIR__, 3 ) . '/components/template-library.php';
	$default = $field['default'];
	$control = array( 'label' => $field['label'], 'default' => $default, 'type' => Controls::TEXT );
	if ( ! empty( $field['media'] ) ) { $control['type'] = Controls::MEDIA; $control['default'] = array( 'id' => 0, 'url' => '' ); }
	elseif ( ! empty( $field['template'] ) ) { $control['type'] = Controls::SELECT2; $control['options'] = \RadmanCTComponents\Components\TemplateLibrary\options(); }
	elseif ( isset( $field['options'] ) ) { $control['type'] = Controls::SELECT; $control['options'] = $field['options']; }
	elseif ( 'icon' === $key ) { $control['type'] = Controls::SELECT; $control['options'] = array( '' => __( 'None', 'radmanct-components' ) ) + \RadmanCTComponents\Icons\labels(); }
	elseif ( is_bool( $default ) ) { $control['type'] = Controls::SWITCHER; $control['default'] = $default ? 'yes' : ''; $control['return_value'] = 'yes'; }
	elseif ( ! empty( $field['color'] ) ) { $control['type'] = Controls::COLOR; $control['alpha'] = false; }
	elseif ( ! empty( $field['rich'] ) ) { $control['type'] = Controls::WYSIWYG; }
	elseif ( ! empty( $field['multiline'] ) ) { $control['type'] = Controls::TEXTAREA; }
	elseif ( isset( $field['min'] ) ) {
		$control['type'] = Controls::NUMBER;
		foreach ( array( 'min', 'max', 'step' ) as $property ) { if ( isset( $field[ $property ] ) ) { $control[ $property ] = $field[ $property ]; } }
	}
	if ( isset( $field['condition'] ) ) { $control['condition'] = $field['condition']; }
	if ( isset( $field['description'] ) ) { $control['description'] = $field['description']; }
	return $control;
}

function add_repeater_fields( \Elementor\Repeater $repeater, array $fields ): void {
	foreach ( $fields as $key => $field ) { $repeater->add_control( $key, field_control( $key, $field ) ); }
}
