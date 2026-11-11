<?php

namespace RadmanCT\Components\ContainerMotion;

defined( 'ABSPATH' ) || exit;

function fields(): array {
	static $fields;
	if ( is_array( $fields ) ) { return $fields; }
	$easings = json_decode( file_get_contents( __DIR__ . '/motion-easings.json' ), true );
	$easings = is_array( $easings ) && $easings ? array_combine( $easings, $easings ) : array( 'none' => 'none', 'power4.out' => 'power4.out' );
	$fields = array(
		'horizontal' => array( 'label' => __( 'Pinned horizontal scroll', 'radmanct' ), 'type' => 'boolean', 'default' => false, 'group' => 'horizontal' ),
		'horizontal_direction' => array( 'label' => __( 'Scroll direction', 'radmanct' ), 'default' => 'auto', 'options' => array( 'auto' => __( 'Automatic', 'radmanct' ), 'left' => __( 'Move left', 'radmanct' ), 'right' => __( 'Move right', 'radmanct' ) ), 'group' => 'horizontal' ),
		'horizontal_width' => array( 'label' => __( 'Panel width (%)', 'radmanct' ), 'default' => 90, 'min' => 10, 'max' => 100, 'group' => 'horizontal' ),
		'horizontal_gap' => array( 'label' => __( 'Panel gap (px)', 'radmanct' ), 'default' => 24, 'min' => 0, 'max' => 200, 'group' => 'horizontal' ),
		'horizontal_align' => array( 'label' => __( 'Panel alignment', 'radmanct' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct' ), 'center' => __( 'Center', 'radmanct' ), 'end' => __( 'End', 'radmanct' ), 'stretch' => __( 'Stretch', 'radmanct' ) ), 'group' => 'horizontal' ),
		'horizontal_offset' => array( 'label' => __( 'Top offset (px)', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => 300, 'group' => 'horizontal' ),
		'horizontal_min_width' => array( 'label' => __( 'Minimum screen width (px)', 'radmanct' ), 'default' => 1024, 'min' => 320, 'max' => 1920, 'group' => 'horizontal' ),
		'horizontal_scrub' => array( 'label' => __( 'Horizontal smoothing', 'radmanct' ), 'default' => 1, 'min' => 0.1, 'max' => 5, 'group' => 'horizontal' ),
		'reveal' => array( 'label' => __( 'Scroll reveal', 'radmanct' ), 'type' => 'boolean', 'default' => false, 'group' => 'reveal' ),
		'reveal_direction' => array( 'label' => __( 'Reveal direction', 'radmanct' ), 'default' => 'start', 'options' => array( 'start' => __( 'Reading start', 'radmanct' ), 'end' => __( 'Reading end', 'radmanct' ), 'left' => __( 'Left', 'radmanct' ), 'right' => __( 'Right', 'radmanct' ) ), 'group' => 'reveal' ),
		'reveal_start' => array( 'label' => __( 'Reveal start', 'radmanct' ), 'default' => 90, 'min' => 50, 'max' => 100, 'group' => 'reveal' ),
		'reveal_end' => array( 'label' => __( 'Reveal end', 'radmanct' ), 'default' => 50, 'min' => 0, 'max' => 80, 'group' => 'reveal' ),
		'reveal_scrub' => array( 'label' => __( 'Reveal smoothing', 'radmanct' ), 'default' => 1, 'min' => 0.1, 'max' => 5, 'group' => 'reveal' ),
		'animate' => array( 'label' => __( 'Animate contents', 'radmanct' ), 'type' => 'boolean', 'default' => false, 'group' => 'settings' ),
		'parallax' => array( 'label' => __( 'Parallax', 'radmanct' ), 'type' => 'boolean', 'default' => false, 'group' => 'parallax' ),
		'targets' => array( 'label' => __( 'Animation targets', 'radmanct' ), 'default' => 'contents', 'options' => array( 'contents' => __( 'All contents', 'radmanct' ), 'containers' => __( 'Direct inner containers', 'radmanct' ), 'self' => __( 'This element', 'radmanct' ) ), 'group' => 'settings' ),
		'inner' => array( 'label' => __( 'Include inner containers content', 'radmanct' ), 'type' => 'boolean', 'default' => true, 'group' => 'settings' ),
		'preset' => array( 'label' => __( 'Animation preset', 'radmanct' ), 'default' => 'custom', 'options' => array( 'custom' => __( 'Custom', 'radmanct' ), 'fade' => __( 'Fade in', 'radmanct' ), 'fade-up' => __( 'Fade in up', 'radmanct' ), 'fade-down' => __( 'Fade in down', 'radmanct' ), 'fade-left' => __( 'Fade in left', 'radmanct' ), 'fade-right' => __( 'Fade in right', 'radmanct' ), 'flip-x' => __( 'Flip in X', 'radmanct' ), 'flip-y' => __( 'Flip in Y', 'radmanct' ), 'scale-up' => __( 'Scale up', 'radmanct' ), 'scale-down' => __( 'Scale down', 'radmanct' ) ), 'group' => 'settings' ),
		'ease' => array( 'label' => __( 'Easing', 'radmanct' ), 'default' => 'power4.out', 'options' => $easings, 'group' => 'settings' ),
		'order' => array( 'label' => __( 'Animation order', 'radmanct' ), 'default' => 'start', 'options' => array( 'start' => __( 'Reading start', 'radmanct' ), 'end' => __( 'Reading end', 'radmanct' ), 'center' => __( 'Center', 'radmanct' ), 'edges' => __( 'Edges', 'radmanct' ), 'random' => __( 'Random', 'radmanct' ) ), 'group' => 'settings' ),
		'duration' => array( 'label' => __( 'Duration', 'radmanct' ), 'default' => 1.6, 'min' => 0, 'max' => 10, 'group' => 'settings' ),
		'stagger' => array( 'label' => __( 'Stagger', 'radmanct' ), 'default' => 0.16, 'min' => 0, 'max' => 10, 'group' => 'settings' ),
		'delay' => array( 'label' => __( 'Start delay', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => 10, 'group' => 'settings' ),
		'scrub' => array( 'label' => __( 'Parallax smoothing', 'radmanct' ), 'default' => 1, 'min' => 0.1, 'max' => 5, 'group' => 'parallax' ),
		'parallax_perspective' => array( 'label' => __( 'Perspective', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => 2000, 'group' => 'parallax' ),
		'parallax_ease' => array( 'label' => __( 'Parallax easing', 'radmanct' ), 'default' => 'linear', 'options' => $easings, 'group' => 'parallax' ),
		'parallax_start' => array( 'label' => __( 'Parallax start', 'radmanct' ), 'default' => 'top bottom', 'options' => array( 'top bottom' => __( 'On enter', 'radmanct' ), 'top top' => __( 'On leave', 'radmanct' ), 'center center' => __( 'On center', 'radmanct' ) ), 'group' => 'parallax' ),
		'parallax_end' => array( 'label' => __( 'Parallax end', 'radmanct' ), 'default' => 'bottom top', 'options' => array( 'bottom top' => __( 'On leave', 'radmanct' ), 'bottom bottom' => __( 'On enter', 'radmanct' ) ), 'group' => 'parallax' ),
	);
	$axes = array( 'x' => __( 'Translate X', 'radmanct' ), 'y' => __( 'Translate Y', 'radmanct' ), 'z' => __( 'Translate Z', 'radmanct' ), 'scaleX' => __( 'Scale X', 'radmanct' ), 'scaleY' => __( 'Scale Y', 'radmanct' ), 'rotationX' => __( 'Rotate X', 'radmanct' ), 'rotationY' => __( 'Rotate Y', 'radmanct' ), 'rotation' => __( 'Rotate Z', 'radmanct' ), 'opacity' => __( 'Opacity', 'radmanct' ) );
	foreach ( array( 'from', 'to', 'parallax_from', 'parallax_to' ) as $group ) {
		$condition = array( $group . '_origin' => true );
		$fields[ $group . '_origin' ] = array( 'label' => __( 'Custom transform origin', 'radmanct' ), 'type' => 'boolean', 'default' => false, 'group' => $group );
		foreach ( array( 'X', 'Y', 'Z' ) as $axis ) {
			$key = $group . '_origin' . $axis;
			$fields[ $key ] = array( 'label' => sprintf( __( 'Transform origin %s', 'radmanct' ), $axis ), 'default' => 'Z' === $axis ? 0 : 50, 'min' => -500, 'max' => 500, 'step' => 'Z' === $axis ? 1 : 0.1, 'group' => $group, 'condition' => $condition );
			if ( 'Z' !== $axis ) {
				$fields[ $key ]['unit_key'] = $key . '_unit';
				$fields[ $key . '_unit' ] = array( 'label' => sprintf( __( 'Origin %s unit', 'radmanct' ), $axis ), 'default' => '%', 'options' => array( '%' => '%', 'px' => 'px' ), 'unit_for' => $key, 'group' => $group, 'condition' => $condition );
			}
		}
		foreach ( $axes as $axis => $label ) {
			$scale = str_starts_with( $axis, 'scale' );
			$opacity = 'opacity' === $axis;
			$default = $scale || $opacity ? 1 : 0;
			$fields[ $group . '_' . $axis ] = array( 'label' => $label, 'default' => $default, 'min' => $scale || $opacity ? 0 : -1000, 'max' => $opacity ? 1 : ( $scale ? 5 : 1000 ), 'group' => $group );
			$fields[ $group . '_' . $axis ] += array( 'ui_min' => $scale || $opacity ? 0 : ( str_starts_with( $axis, 'rotation' ) ? -360 : -500 ), 'ui_max' => $opacity ? 1 : ( $scale ? 5 : ( str_starts_with( $axis, 'rotation' ) ? 360 : 500 ) ) );
			if ( in_array( $axis, array( 'x', 'y' ), true ) ) {
				$key = $group . '_' . $axis;
				$fields[ $key ]['unit_key'] = $key . '_unit';
				$fields[ $key . '_unit' ] = array( 'label' => sprintf( __( '%s unit', 'radmanct' ), $label ), 'default' => 'px', 'options' => array( 'px' => 'px', '%' => '%', 'vw' => 'vw', 'vh' => 'vh' ), 'unit_for' => $key, 'group' => $group );
			}
		}
	}
	$fields['parallax_start']['options']['custom'] = __( 'Custom', 'radmanct' );
	$fields['parallax_end']['options']['custom'] = __( 'Custom', 'radmanct' );
	$fields['parallax_start_custom'] = array( 'label' => __( 'Custom parallax start', 'radmanct' ), 'type' => 'text', 'default' => 'top bottom', 'group' => 'parallax', 'condition' => array( 'parallax_start' => 'custom' ) );
	$fields['parallax_end_custom'] = array( 'label' => __( 'Custom parallax end', 'radmanct' ), 'type' => 'text', 'default' => 'bottom top', 'group' => 'parallax', 'condition' => array( 'parallax_end' => 'custom' ) );
	$distance_condition = array( 'parallax_end' => array( 'bottom top', 'bottom bottom' ) );
	$fields['parallax_distance'] = array( 'label' => __( 'Extend or shorten parallax distance', 'radmanct' ), 'default' => 0, 'min' => -500, 'max' => 500, 'unit_key' => 'parallax_distance_unit', 'group' => 'parallax', 'condition' => $distance_condition );
	$fields['parallax_distance_unit'] = array( 'label' => __( 'Parallax distance unit', 'radmanct' ), 'default' => 'px', 'options' => array( 'px' => 'px', '%' => '%' ), 'unit_for' => 'parallax_distance', 'group' => 'parallax', 'condition' => $distance_condition );
	return $fields;
}

function scroll_position( $value, string $fallback ): string {
	if ( ! is_string( $value ) || strlen( $value ) > 120 ) { return $fallback; }
	$value = strtolower( trim( preg_replace( '/\s+/', ' ', $value ) ) );
	$number = '[+-]?(?:\d+(?:\.\d+)?|\.\d+)(?:%|px)?';
	$anchor = '(?:top|center|bottom|left|right|' . $number . ')(?:[+-]=' . $number . ')?';
	return preg_match( '~^(?:' . $anchor . ' ' . $anchor . '|[+-]=' . $number . '|max|' . $number . ')$~D', $value ) ? $value : $fallback;
}

function normalize( array $input ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( 'boolean' === ( $field['type'] ?? '' ) ) {
			$result[ $key ] = in_array( $value, array( true, 'yes' ), true );
		} elseif ( 'text' === ( $field['type'] ?? '' ) ) {
			$result[ $key ] = scroll_position( $value, $field['default'] );
		} elseif ( isset( $field['options'] ) ) {
			$result[ $key ] = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default'];
		} else {
			$result[ $key ] = ( is_int( $value ) || is_float( $value ) || is_string( $value ) ) && is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (float) $value ) ) : $field['default'];
		}
	}
	$result['reveal_end'] = min( $result['reveal_end'], $result['reveal_start'] - 5 );
	foreach ( fields() as $key => $field ) {
		if ( isset( $field['unit_key'] ) && 'px' !== $result[ $field['unit_key'] ] ) { $limit = 'parallax_distance' === $key ? 500 : 100; $result[ $key ] = max( -$limit, min( $limit, $result[ $key ] ) ); }
	}
	return $result;
}

function attributes( array $options ): string {
	$data = normalize( $options );
	return $data['animate'] || $data['parallax'] || $data['reveal'] || $data['horizontal'] ? 'data-rdct-motion="' . esc_attr( wp_json_encode( $data ) ) . '"' : '';
}
