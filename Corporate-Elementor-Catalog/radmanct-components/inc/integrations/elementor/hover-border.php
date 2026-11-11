<?php

namespace RadmanCTComponents\Elementor\HoverBorder;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 2 ) . '/components/hover-border.php';

function classic_controls( $element ): void {
	if ( $element->get_controls( 'rdct_hover_border' ) ) { return; }
	$element->start_controls_section( 'rdct_hover_border', array( 'label' => __( 'Animated hover border', 'radmanct-components' ), 'tab' => \Elementor\Controls_Manager::TAB_ADVANCED ) );
	foreach ( \RadmanCTComponents\Components\HoverBorder\fields() as $key => $field ) {
		$control = $field + array( 'type' => is_bool( $field['default'] ) ? \Elementor\Controls_Manager::SWITCHER : ( isset( $field['options'] ) ? \Elementor\Controls_Manager::SELECT : ( ! empty( $field['color'] ) ? \Elementor\Controls_Manager::COLOR : \Elementor\Controls_Manager::NUMBER ) ) );
		if ( is_bool( $field['default'] ) ) { $control['default'] = ''; }
		else { $control['condition'] = array( 'rdct_border_enabled' => 'yes' ); }
		if ( ! empty( $field['color'] ) ) { $control['alpha'] = false; }
		$element->add_control( 'rdct_border_' . $key, $control );
	}
	$element->end_controls_section();
}

function schema( array $schema ): array {
	foreach ( \RadmanCTComponents\Components\HoverBorder\fields() as $key => $field ) {
		$prop = is_bool( $field['default'] ) ? \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type::make() : ( isset( $field['min'] ) ? \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::make()->float() : \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make() );
		if ( isset( $field['options'] ) ) { $prop->enum( array_keys( $field['options'] ) ); }
		$schema[ 'rdct_border_' . $key ] = $prop->default( $field['default'] );
		if ( 'enabled' !== $key ) { $schema[ 'rdct_border_' . $key ]->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'eq', 'path' => array( 'rdct_border_enabled' ), 'value' => true, 'effect' => 'hide' ) )->get() ); }
	}
	return $schema;
}

function controls( array $controls, $element ): array {
	require_once __DIR__ . '/atomic-components/widget.php';
	$items = array();
	foreach ( \RadmanCTComponents\Components\HoverBorder\fields() as $key => $field ) {
		$bind = 'rdct_border_' . $key;
		if ( is_bool( $field['default'] ) ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control::bind_to( $bind ); }
		elseif ( ! empty( $field['color'] ) ) { $control = \RadmanCTComponents\Elementor\AtomicComponents\ColorControl::bind_to( $bind ); }
		elseif ( isset( $field['options'] ) ) { $options = array(); foreach ( $field['options'] as $value => $label ) { $options[] = array( 'value' => $value, 'label' => $label ); } $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control::bind_to( $bind )->set_options( $options ); }
		else { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control::bind_to( $bind )->set_should_force_int( false )->set_min( (int) floor( $field['min'] ) )->set_max( (int) ceil( $field['max'] ) ); }
		$items[] = $control->set_label( $field['label'] );
	}
	$controls[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_hover_border' )->set_label( __( 'Animated hover border', 'radmanct-components' ) )->set_items( $items );
	return $controls;
}

function values( $element ): array {
	$settings = $element->get_settings(); $values = array();
	foreach ( \RadmanCTComponents\Components\HoverBorder\fields() as $key => $field ) {
		$value = $settings[ 'rdct_border_' . $key ] ?? $field['default'];
		if ( is_array( $value ) && isset( $value['$$type'] ) ) { $value = empty( $value['disabled'] ) ? ( $value['value'] ?? $field['default'] ) : $field['default']; }
		$values[ $key ] = $value;
	}
	return \RadmanCTComponents\Components\HoverBorder\settings( $values );
}

function buffers(): \WeakMap { static $buffers; return $buffers ??= new \WeakMap(); }

add_action( 'elementor/frontend/before_render', static function ( $element ): void {
	$s = values( $element ); if ( ! $s['enabled'] ) { return; }
	\RadmanCTComponents\Components\HoverBorder\assets( true );
	ob_start(); buffers()[ $element ] = array( 'level' => ob_get_level(), 'settings' => $s );
}, 25 );
add_action( 'elementor/frontend/after_render', static function ( $element ): void {
	if ( ! isset( buffers()[ $element ] ) ) { return; }
	$entry = buffers()[ $element ]; unset( buffers()[ $element ] );
	if ( ob_get_level() !== $entry['level'] ) { return; }
	$html = ob_get_clean();
	if ( strlen( $html ) <= 2097152 ) { $processor = new \WP_HTML_Tag_Processor( $html ); if ( $processor->next_tag() ) { $processor->set_attribute( 'data-rdct-border', wp_json_encode( $entry['settings'] ) ); $html = $processor->get_updated_html(); } }
	echo $html;
}, 3 );
foreach ( array( 'container/section_layout', 'section/section_advanced', 'column/section_advanced', 'common/_section_style' ) as $section ) { add_action( 'elementor/element/' . $section . '/after_section_end', __NAMESPACE__ . '\\classic_controls' ); }
add_filter( 'elementor/atomic-widgets/props-schema', __NAMESPACE__ . '\\schema' );
add_filter( 'elementor/atomic-widgets/controls', __NAMESPACE__ . '\\controls', 30, 2 );
add_action( 'elementor/preview/enqueue_scripts', static function (): void {
	$entry = \RadmanCTComponents\Assets\entry( 'src/js/integrations/elementor-border-preview.js' ); if ( ! $entry ) { return; }
	wp_enqueue_script( 'rdct-border-preview', \RadmanCTComponents\Assets\url( $entry['file'] ), array( 'elementor-frontend' ), null, true );
	wp_add_inline_script( 'rdct-border-preview', 'window.rdctBorderFields=' . wp_json_encode( array_keys( \RadmanCTComponents\Components\HoverBorder\fields() ) ) . ';', 'before' );
	\RadmanCTComponents\Components\use_component( 'hover-border' );
} );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array { if ( 'rdct-border-preview-js' === ( $attributes['id'] ?? '' ) ) { $attributes['type'] = 'module'; } return $attributes; } );
