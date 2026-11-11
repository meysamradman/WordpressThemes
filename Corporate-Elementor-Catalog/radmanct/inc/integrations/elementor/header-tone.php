<?php

namespace RadmanCT\Integrations\Elementor\HeaderTone;

defined( 'ABSPATH' ) || exit;

function choices(): array {
	return array( '' => __( 'Unspecified', 'radmanct' ), 'light' => __( 'Light section', 'radmanct' ), 'dark' => __( 'Dark section', 'radmanct' ), 'auto' => __( 'Follow site color scheme', 'radmanct' ) );
}

function classic_controls( $element, string $section_id ): void {
	if ( ! in_array( $element->get_name(), array( 'container', 'section', 'column' ), true ) || ! in_array( $section_id, array( 'section_layout', 'section_advanced', '_section_style' ), true ) || $element->get_controls( 'rdct_header_contrast' ) ) { return; }
	$element->start_controls_section( 'rdct_header_contrast', array( 'label' => __( 'Header contrast', 'radmanct' ), 'tab' => \Elementor\Controls_Manager::TAB_ADVANCED ) );
	$element->add_control( 'rdct_header_tone', array( 'label' => __( 'Section color tone', 'radmanct' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '', 'options' => choices() ) );
	$element->end_controls_section();
}

function atomic_schema( array $schema ): array {
	if ( class_exists( '\\Elementor\\Modules\\AtomicWidgets\\PropTypes\\Primitives\\String_Prop_Type' ) ) {
		$schema['rdct_header_tone'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make()->enum( array_keys( choices() ) )->default( '' );
	}
	return $schema;
}

function atomic_controls( array $controls, $element ): array {
	if ( ! in_array( $element->get_name(), array( 'e-flexbox', 'e-grid', 'e-div-block' ), true ) ) { return $controls; }
	$options = array();
	foreach ( choices() as $value => $label ) { $options[] = array( 'value' => $value, 'label' => $label ); }
	$control = \Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control::bind_to( 'rdct_header_tone' )->set_label( __( 'Section color tone', 'radmanct' ) )->set_options( $options );
	$controls[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_header_contrast' )->set_label( __( 'Header contrast', 'radmanct' ) )->set_items( array( $control ) );
	return $controls;
}

function attributes( array $attributes, $element ): array {
	if ( ! \RadmanCT\Integrations\Elementor\ContainerMotion\layout( $element ) ) { return $attributes; }
	$value = $element->get_settings( 'rdct_header_tone' );
	$value = \RadmanCT\Integrations\Elementor\ContainerMotion\property_value( $value, 'string', '' );
	if ( in_array( $value, array( 'light', 'dark', 'auto' ), true ) ) { $attributes['data-rdct-header-tone'] = $value; }
	return $attributes;
}

add_action( 'elementor/element/after_section_end', __NAMESPACE__ . '\\classic_controls', 20, 2 );
add_filter( 'elementor/atomic-widgets/props-schema', __NAMESPACE__ . '\\atomic_schema' );
add_filter( 'elementor/atomic-widgets/controls', __NAMESPACE__ . '\\atomic_controls', 25, 2 );
add_filter( 'radmanct/elementor/render-attributes', __NAMESPACE__ . '\\attributes', 10, 2 );
