<?php

namespace RadmanCT\Integrations\Elementor\ContainerMotion;

defined( 'ABSPATH' ) || exit;

require_once dirname( __DIR__, 2 ) . '/components/container-motion.php';

function groups(): array {
	return array( 'horizontal' => __( 'Pinned horizontal scroll', 'radmanct' ), 'reveal' => __( 'Scroll reveal', 'radmanct' ), 'settings' => __( 'Settings', 'radmanct' ), 'from' => __( 'Animate from', 'radmanct' ), 'to' => __( 'Animate to', 'radmanct' ), 'parallax' => __( 'Parallax settings', 'radmanct' ), 'parallax_from' => __( 'Parallax from', 'radmanct' ), 'parallax_to' => __( 'Parallax to', 'radmanct' ) );
}

function layout( $element ): bool {
	return in_array( $element->get_name(), array( 'container', 'section', 'column', 'e-flexbox', 'e-grid', 'e-div-block' ), true );
}

function supported( $element ): bool {
	return layout( $element ) || $element instanceof \Elementor\Widget_Base || $element instanceof \Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
}

function field_type( array $field ): string {
	return 'boolean' === ( $field['type'] ?? '' ) ? 'boolean' : ( isset( $field['options'] ) || 'text' === ( $field['type'] ?? '' ) ? 'string' : 'number' );
}

function slider_field( string $key, array $field ): bool {
	return isset( $field['min'] ) && ( in_array( $key, array( 'duration', 'stagger', 'delay', 'parallax_perspective', 'parallax_distance' ), true ) || in_array( $field['group'], array( 'from', 'to', 'parallax_from', 'parallax_to' ), true ) );
}

function classic_field( $element, string $key, array $condition = array() ): void {
	$field = \RadmanCT\Components\ContainerMotion\fields()[ $key ];
	$options = array( 'label' => 'animate' === $key && ! layout( $element ) ? __( 'Animate element', 'radmanct' ) : $field['label'], 'default' => $field['default'], 'condition' => $condition, 'render_type' => 'none' );
	if ( 'animate' === $key ) { $options['separator'] = 'before'; }
	foreach ( $field['condition'] ?? array() as $name => $value ) { $options['condition'][ 'rm_motion_' . $name ] = true === $value ? 'yes' : $value; }
	if ( 'boolean' === ( $field['type'] ?? '' ) ) {
		$options += array( 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes' );
		$options['default'] = $field['default'] ? 'yes' : '';
	} elseif ( 'text' === ( $field['type'] ?? '' ) ) {
		$options += array( 'type' => \Elementor\Controls_Manager::TEXT, 'maxlength' => 120 );
	} elseif ( isset( $field['options'] ) ) {
		$options += array( 'type' => \Elementor\Controls_Manager::SELECT, 'options' => $field['options'] );
	} else {
		$options += array( 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => $field['ui_min'] ?? $field['min'], 'max' => $field['ui_max'] ?? $field['max'], 'step' => 0.01 );
	}
	if ( slider_field( $key, $field ) ) {
		$timing = in_array( $key, array( 'duration', 'stagger', 'delay' ), true );
		$translate = 'parallax_perspective' === $key || (bool) preg_match( '/_[xyz]$/D', $key );
		$rotate = str_contains( $key, '_rotation' );
		$unit = $timing ? 's' : ( $rotate ? 'deg' : 'px' );
		$step = $timing || ! ( $translate || $rotate ) ? 0.01 : 1;
		$step = $field['step'] ?? $step;
		$element->add_control( 'rm_motion_' . $key, array( 'type' => \Elementor\Controls_Manager::HIDDEN, 'default' => $field['default'] ) );
		$options['type'] = \Elementor\Controls_Manager::SLIDER;
		$options['default'] = array( 'size' => '', 'unit' => $unit );
		$options['placeholder'] = array( 'size' => $field['default'] );
		$options['size_units'] = $timing || $translate || $rotate ? array( $unit ) : array();
		$options['range'] = array( $unit => array( 'min' => $field['ui_min'] ?? $field['min'], 'max' => $field['ui_max'] ?? $field['max'], 'step' => $step ) );
		if ( isset( $field['unit_key'] ) ) {
			$unit_field = \RadmanCT\Components\ContainerMotion\fields()[ $field['unit_key'] ];
			$element->add_control( 'rm_motion_' . $field['unit_key'], array( 'type' => \Elementor\Controls_Manager::HIDDEN, 'default' => $unit_field['default'] ) );
			$options['default']['unit'] = $unit_field['default'];
			$options['size_units'] = array_keys( $unit_field['options'] );
			$options['range'] = array();
			$relative_limit = 'parallax_distance' === $key ? 500 : 100;
			foreach ( $options['size_units'] as $value_unit ) { $options['range'][ $value_unit ] = array( 'min' => 'px' === $value_unit ? ( $field['ui_min'] ?? $field['min'] ) : -$relative_limit, 'max' => 'px' === $value_unit ? ( $field['ui_max'] ?? $field['max'] ) : $relative_limit, 'step' => 'px' === $value_unit ? 1 : 0.1 ); }
		} elseif ( str_ends_with( $key, '_originZ' ) ) { $options['size_units'] = array( 'px' ); }
		$options['description'] = __( 'An empty value uses the saved value or its default.', 'radmanct' );
		$element->add_control( 'rm_motion_' . $key . '_slider', $options );
	} else { $element->add_control( 'rm_motion_' . $key, $options ); }
}

function classic_popover( $element, string $group, array $condition, array $exclude = array() ): void {
	$element->add_control( 'rm_motion_ui_' . $group, array( 'label' => groups()[ $group ], 'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE, 'return_value' => 'yes', 'default' => 'yes', 'condition' => $condition, 'render_type' => 'none' ) );
	$element->start_popover();
	foreach ( \RadmanCT\Components\ContainerMotion\fields() as $key => $field ) {
		if ( $group === $field['group'] && ! isset( $field['unit_for'] ) && ! in_array( $key, $exclude, true ) && ( layout( $element ) || ! in_array( $key, array( 'targets', 'inner' ), true ) ) ) { classic_field( $element, $key, 'inner' === $key ? $condition + array( 'rm_motion_targets' => 'contents' ) : $condition ); }
	}
	$element->end_popover();
}

function classic_controls( $element ): void {
	if ( ! supported( $element ) || $element->get_controls( 'radman_motion' ) ) { return; }
	$element->start_controls_section( 'radman_motion', array( 'label' => __( 'Animations & Parallax', 'radmanct' ), 'tab' => \Elementor\Controls_Manager::TAB_ADVANCED ) );
	$element->add_control( 'rm_motion_notice', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => __( 'Native Elementor motion takes priority on the same element. Independently animated children are excluded from parent targets.', 'radmanct' ) ) );
	classic_field( $element, 'parallax' );
	foreach ( array( 'parallax', 'parallax_from', 'parallax_to' ) as $group ) { classic_popover( $element, $group, array( 'rm_motion_parallax' => 'yes' ), array( 'parallax' ) ); }
	classic_field( $element, 'animate' );
	$animate = array( 'rm_motion_animate' => 'yes' );
	if ( layout( $element ) ) { classic_field( $element, 'targets', $animate ); classic_field( $element, 'inner', $animate + array( 'rm_motion_targets' => 'contents' ) ); }
	$element->add_control( 'rm_motion_play', array( 'label' => __( 'Play animations', 'radmanct' ), 'type' => \Elementor\Controls_Manager::BUTTON, 'button_type' => 'success', 'text' => __( 'Play', 'radmanct' ), 'event' => 'rm_motion_play', 'render_type' => 'none', 'condition' => $animate ) );
	classic_popover( $element, 'settings', $animate, array( 'animate', 'targets', 'inner' ) );
	foreach ( array( 'from', 'to' ) as $group ) { classic_popover( $element, $group, $animate + array( 'rm_motion_preset' => 'custom' ) ); }
	$element->add_control( 'rm_motion_parallax_play', array( 'label' => __( 'Play animations', 'radmanct' ), 'type' => \Elementor\Controls_Manager::BUTTON, 'text' => __( 'Play', 'radmanct' ), 'event' => 'rm_motion_play', 'render_type' => 'none', 'condition' => array( 'rm_motion_parallax' => 'yes', 'rm_motion_animate!' => 'yes' ) ) );
	$element->add_control( 'rm_motion_preview_reset', array( 'label' => __( 'Reset preview', 'radmanct' ), 'type' => \Elementor\Controls_Manager::BUTTON, 'text' => __( 'Reset preview', 'radmanct' ), 'event' => 'rm_motion_reset', 'render_type' => 'none', 'conditions' => array( 'relation' => 'or', 'terms' => array( array( 'name' => 'rm_motion_animate', 'operator' => '===', 'value' => 'yes' ), array( 'name' => 'rm_motion_parallax', 'operator' => '===', 'value' => 'yes' ) ) ) ) );
	$element->add_control( 'rm_motion_self_notice', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => __( 'Parallax takes priority over entrance animation on the same element.', 'radmanct' ), 'condition' => array( 'rm_motion_animate' => 'yes', 'rm_motion_parallax' => 'yes' ) + ( layout( $element ) ? array( 'rm_motion_targets' => 'self' ) : array() ) ) );
	$element->end_controls_section();
	if ( ! layout( $element ) ) { return; }
	$element->start_controls_section( 'radman_motion_scroll', array( 'label' => __( 'Advanced scroll', 'radmanct' ), 'tab' => \Elementor\Controls_Manager::TAB_ADVANCED ) );
	foreach ( array( 'horizontal', 'reveal' ) as $group ) {
		classic_field( $element, $group );
		classic_popover( $element, $group, array( 'rm_motion_' . $group => 'yes' ), array( $group ) );
	}
	$element->add_control( 'rm_motion_reveal_play', array( 'label' => __( 'Play animations', 'radmanct' ), 'type' => \Elementor\Controls_Manager::BUTTON, 'text' => __( 'Play', 'radmanct' ), 'event' => 'rm_motion_play', 'condition' => array( 'rm_motion_reveal' => 'yes' ) ) );
	$element->add_control( 'rm_motion_reveal_reset', array( 'label' => __( 'Reset preview', 'radmanct' ), 'type' => \Elementor\Controls_Manager::BUTTON, 'text' => __( 'Reset preview', 'radmanct' ), 'event' => 'rm_motion_reset', 'render_type' => 'none', 'condition' => array( 'rm_motion_reveal' => 'yes' ) ) );
	$element->end_controls_section();
}

function preview_assets(): void {
	$entry = \RadmanCT\Assets\entry( 'src/js/integrations/elementor-motion-preview.js' );
	if ( ! $entry ) { return; }
	wp_enqueue_script( 'rdct-motion-preview', \RadmanCT\Assets\url( $entry['file'] ), array( 'elementor-frontend' ), null, true );
	$fields = \RadmanCT\Components\ContainerMotion\fields();
	$sliders = array_values( array_filter( array_keys( $fields ), static fn( $key ) => slider_field( $key, $fields[ $key ] ) ) );
	$units = array();
	foreach ( $fields as $key => $field ) { if ( isset( $field['unit_for'] ) ) { $units[ $key ] = array( 'source' => $field['unit_for'], 'values' => array_keys( $field['options'] ) ); } }
	wp_add_inline_script( 'rdct-motion-preview', 'window.rdctMotion=' . wp_json_encode( array( 'fields' => array_keys( $fields ), 'types' => array_map( __NAMESPACE__ . '\\field_type', $fields ), 'sliders' => $sliders, 'units' => $units ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}

function atomic_schema( array $schema ): array {
	foreach ( \RadmanCT\Components\ContainerMotion\fields() as $key => $field ) {
		if ( 'boolean' === ( $field['type'] ?? '' ) ) {
			$prop = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type::make();
		} elseif ( 'text' === ( $field['type'] ?? '' ) ) {
			$prop = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make();
		} elseif ( isset( $field['options'] ) ) {
			$prop = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make()->enum( array_keys( $field['options'] ) );
		} else {
			$prop = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::make()->float();
		}
		$schema[ 'rm_motion_' . $key ] = $prop->default( $field['default'] );
		if ( isset( $field['condition'] ) ) {
			$dependencies = \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make( 'and' );
			foreach ( $field['condition'] as $name => $value ) { $dependencies->where( array( 'operator' => 'in', 'path' => array( 'rm_motion_' . $name ), 'value' => is_array( $value ) ? $value : array( $value ), 'effect' => 'hide' ) ); }
			$schema[ 'rm_motion_' . $key ]->set_dependencies( $dependencies->get() );
		}
		if ( in_array( $field['group'], array( 'horizontal', 'reveal' ), true ) && $key !== $field['group'] ) {
			$schema[ 'rm_motion_' . $key ]->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'in', 'path' => array( 'rm_motion_' . $field['group'] ), 'value' => array( true ), 'effect' => 'hide' ) )->get() );
		}
	}
	return $schema;
}

function atomic_controls( array $controls, $element ): array {
	if ( ! supported( $element ) ) { return $controls; }
	require_once __DIR__ . '/motion-control.php';
	foreach ( array( 'motion' => __( 'Animations & Parallax', 'radmanct' ), 'scroll' => __( 'Advanced scroll', 'radmanct' ) ) as $group => $label ) {
		if ( 'scroll' === $group && ! layout( $element ) ) { continue; }
		$control = Motion_Control::make()->configure( $group, layout( $element ) )->set_label( '' )->set_meta( array() );
		$controls[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'radman_motion_' . $group )->set_label( $label )->set_items( array( $control ) );
	}
	return $controls;
}

function property_value( $value, string $type, $fallback = null ) {
	if ( is_array( $value ) && array_key_exists( '$$type', $value ) ) {
		return true === ( $value['disabled'] ?? false ) || $type !== $value['$$type'] ? $fallback : ( $value['value'] ?? $fallback );
	}
	return $value;
}

function native_motion( $element ): bool {
	$settings = $element->get_settings();
	foreach ( array( '_animation', '_animation_tablet', '_animation_mobile', 'animation', 'animation_tablet', 'animation_mobile' ) as $key ) {
		$value = property_value( $settings[ $key ] ?? null, 'string' );
		if ( is_string( $value ) && '' !== $value && 'none' !== $value ) { return true; }
	}
	foreach ( array( 'motion_fx_motion_fx_scrolling', 'motion_fx_motion_fx_mouse' ) as $key ) {
		$value = property_value( $settings[ $key ] ?? null, 'boolean' );
		if ( null === $value ) { $value = property_value( $settings[ $key ] ?? null, 'string' ); }
		if ( true === $value || 'yes' === $value ) { return true; }
	}
	$data = $element->get_data( 'interactions' );
	if ( is_string( $data ) ) { $data = json_decode( $data, true ); }
	$items = is_array( $data ) ? ( $data['value']['items'] ?? $data['items'] ?? array() ) : array();
	return is_array( $data ) && ! ( $data['disabled'] ?? false ) && is_array( $items ) && ! empty( $items );
}

function options( $element ): array {
	$settings = $element->get_settings();
	$result = array();
	foreach ( \RadmanCT\Components\ContainerMotion\fields() as $key => $field ) {
		$value = property_value( $settings[ 'rm_motion_' . $key ] ?? $field['default'], field_type( $field ), $field['default'] );
		$slider = $settings[ 'rm_motion_' . $key . '_slider' ]['size'] ?? null;
		$result[ $key ] = slider_field( $key, $field ) && is_numeric( $slider ) ? $slider : $value;
		if ( isset( $field['unit_for'] ) ) {
			$unit_slider = $settings[ 'rm_motion_' . $field['unit_for'] . '_slider' ] ?? array();
			$unit = $unit_slider['unit'] ?? null;
			if ( is_numeric( $unit_slider['size'] ?? null ) && is_string( $unit ) && isset( $field['options'][ $unit ] ) ) { $result[ $key ] = $unit; }
		}
	}
	if ( ! layout( $element ) ) { $result['targets'] = 'self'; $result['horizontal'] = false; $result['reveal'] = false; }
	return \RadmanCT\Components\ContainerMotion\normalize( $result );
}

function buffers(): \WeakMap { static $buffers; return $buffers ??= new \WeakMap(); }

function before( $element ): void {
	if ( ! supported( $element ) ) { return; }
	$data = options( $element );
	$native = native_motion( $element );
	$attributes = apply_filters( 'radmanct/elementor/render-attributes', array(), $element );
	if ( ! $attributes && ! $native && ! $data['animate'] && ! $data['parallax'] && ! $data['reveal'] && ! $data['horizontal'] ) { return; }
	ob_start();
	buffers()[ $element ] = array( 'level' => ob_get_level(), 'data' => $data, 'native' => $native, 'attributes' => $attributes );
}

function after( $element ): void {
	if ( ! isset( buffers()[ $element ] ) ) { return; }
	$buffer = buffers()[ $element ];
	unset( buffers()[ $element ] );
	if ( ob_get_level() !== $buffer['level'] ) { return; }
	$html = ob_get_clean();
	if ( strlen( $html ) <= 2097152 ) {
		$processor = new \WP_HTML_Tag_Processor( $html );
		if ( $processor->next_tag() ) {
			foreach ( $buffer['attributes'] as $name => $value ) { $processor->set_attribute( $name, $value ); }
			if ( $buffer['native'] ) { $processor->set_attribute( 'data-rdct-native-motion', 'true' ); }
			elseif ( $buffer['data']['animate'] || $buffer['data']['parallax'] || $buffer['data']['reveal'] || $buffer['data']['horizontal'] ) { $processor->set_attribute( 'data-rdct-motion', wp_json_encode( $buffer['data'] ) ); }
			$html = $processor->get_updated_html();
		}
	}
	echo $html;
}

function classic_section_controls( $element, string $section_id ): void {
	if ( in_array( $section_id, array( 'section_advanced', '_section_style' ), true ) || ( 'container' === $element->get_name() && 'section_layout' === $section_id ) ) { classic_controls( $element ); }
}
add_action( 'elementor/element/after_section_end', __NAMESPACE__ . '\\classic_section_controls', 10, 2 );
add_filter( 'elementor/atomic-widgets/props-schema', __NAMESPACE__ . '\\atomic_schema' );
add_filter( 'elementor/atomic-widgets/controls', __NAMESPACE__ . '\\atomic_controls', 20, 2 );
add_action( 'elementor/frontend/before_render', __NAMESPACE__ . '\\before', 20 );
add_action( 'elementor/frontend/after_render', __NAMESPACE__ . '\\after', 5 );
add_action( 'elementor/preview/enqueue_scripts', __NAMESPACE__ . '\\preview_assets' );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array {
	if ( in_array( $attributes['id'] ?? '', array( 'rdct-motion-preview-js', 'rdct-motion-controls-js' ), true ) ) { $attributes['type'] = 'module'; }
	return $attributes;
} );

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	$entry = \RadmanCT\Assets\entry( 'src/js/admin/elementor-motion-controls.js' );
	if ( ! $entry || ! wp_script_is( 'elementor-v2-editor-editing-panel', 'registered' ) ) { return; }
	wp_enqueue_script( 'rdct-motion-controls', \RadmanCT\Assets\url( $entry['file'] ), array( 'elementor-v2-editor-editing-panel', 'wp-element' ), null, true );
	wp_add_inline_script( 'rdct-motion-controls', 'window.rdctMotionControls=' . wp_json_encode( array( 'fields' => \RadmanCT\Components\ContainerMotion\fields(), 'groups' => groups(), 'labels' => array( 'reset' => __( 'Reset', 'radmanct' ), 'animate' => __( 'Animate element', 'radmanct' ), 'play' => __( 'Play animations', 'radmanct' ), 'resetPreview' => __( 'Reset preview', 'radmanct' ), 'self' => __( 'Parallax takes priority over entrance animation on the same element.', 'radmanct' ), 'notice' => __( 'Native Elementor motion takes priority on the same element. Independently animated children are excluded from parent targets.', 'radmanct' ) ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}, 12 );
