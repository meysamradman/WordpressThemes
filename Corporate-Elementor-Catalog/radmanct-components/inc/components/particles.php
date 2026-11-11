<?php

namespace RadmanCTComponents\Components\Particles;

defined( 'ABSPATH' ) || exit;

function groups(): array {
	return array( 'general' => __( 'Particles', 'radmanct-components' ), 'options' => __( 'Particles options', 'radmanct-components' ), 'interaction' => __( 'Interactivity', 'radmanct-components' ), 'retina' => __( 'Retina', 'radmanct-components' ) );
}

function fields(): array {
	$fields = array(
		'particle_id' => array( 'label' => __( 'Particle ID prefix', 'radmanct-components' ), 'default' => '', 'group' => 'general' ),
		'as_bg' => array( 'label' => __( 'Use as background', 'radmanct-components' ), 'default' => false, 'group' => 'general' ),
		'visible_on_hover' => array( 'label' => __( 'Visible on parent hover', 'radmanct-components' ), 'default' => false, 'group' => 'general' ),
		'height' => array( 'label' => __( 'Height', 'radmanct-components' ), 'default' => '450px', 'group' => 'general' ),
		'height_tablet' => array( 'label' => __( 'Tablet height', 'radmanct-components' ), 'default' => '', 'group' => 'general' ),
		'height_mobile' => array( 'label' => __( 'Mobile height', 'radmanct-components' ), 'default' => '', 'group' => 'general' ),
		'number' => array( 'label' => __( 'Number', 'radmanct-components' ), 'default' => 40, 'min' => 0, 'max' => 200 ),
		'enable_density' => array( 'label' => __( 'Enable density', 'radmanct-components' ), 'default' => false ),
		'density' => array( 'label' => __( 'Density area', 'radmanct-components' ), 'default' => 800, 'min' => 100, 'max' => 10000, 'condition' => array( 'enable_density' => true ) ),
		'color_type' => array( 'label' => __( 'Color variations', 'radmanct-components' ), 'default' => 'single_color', 'options' => array( 'single_color' => __( 'Single color', 'radmanct-components' ), 'multi_color' => __( 'Multiple colors', 'radmanct-components' ), 'random_color' => __( 'Random color', 'radmanct-components' ) ) ),
		'color' => array( 'label' => __( 'Color', 'radmanct-components' ), 'default' => '#ffffff', 'color' => true, 'condition' => array( 'color_type' => 'single_color' ) ),
		'stroke_width' => array( 'label' => __( 'Stroke width', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 20 ),
		'stroke_color' => array( 'label' => __( 'Stroke color', 'radmanct-components' ), 'default' => '#ffffff', 'color' => true ),
		'nb_sides' => array( 'label' => __( 'Polygon sides', 'radmanct-components' ), 'default' => 5, 'min' => 3, 'max' => 12, 'condition' => array( 'shape_polygon' => true ) ),
		'image' => array( 'label' => __( 'Image', 'radmanct-components' ), 'default' => 0, 'media' => true, 'condition' => array( 'shape_image' => true ) ),
		'image_width' => array( 'label' => __( 'Image width', 'radmanct-components' ), 'default' => 100, 'min' => 1, 'max' => 2000, 'condition' => array( 'shape_image' => true ) ),
		'image_height' => array( 'label' => __( 'Image height', 'radmanct-components' ), 'default' => 100, 'min' => 1, 'max' => 2000, 'condition' => array( 'shape_image' => true ) ),
		'opacity' => array( 'label' => __( 'Opacity', 'radmanct-components' ), 'default' => 1.0, 'min' => 0, 'max' => 1, 'step' => .05 ),
		'enable_random_opacity' => array( 'label' => __( 'Random opacity', 'radmanct-components' ), 'default' => false ),
		'enable_anim_opacity' => array( 'label' => __( 'Animate opacity', 'radmanct-components' ), 'default' => false ),
		'anim_opacity_speed' => array( 'label' => __( 'Opacity animation speed', 'radmanct-components' ), 'default' => 2.0, 'min' => 0, 'max' => 50, 'condition' => array( 'enable_anim_opacity' => true ) ),
		'anim_opacity_min' => array( 'label' => __( 'Minimum opacity', 'radmanct-components' ), 'default' => 0.0, 'min' => 0, 'max' => 1, 'step' => .05, 'condition' => array( 'enable_anim_opacity' => true ) ),
		'enable_anim_sync' => array( 'label' => __( 'Synchronize opacity animation', 'radmanct-components' ), 'default' => false, 'condition' => array( 'enable_anim_opacity' => true ) ),
		'size' => array( 'label' => __( 'Size', 'radmanct-components' ), 'default' => 20.0, 'min' => 0, 'max' => 200 ),
		'enable_random_size' => array( 'label' => __( 'Random size', 'radmanct-components' ), 'default' => false ),
		'enable_anim_size' => array( 'label' => __( 'Animate size', 'radmanct-components' ), 'default' => false ),
		'anim_size_speed' => array( 'label' => __( 'Size animation speed', 'radmanct-components' ), 'default' => 20.0, 'min' => 0, 'max' => 100, 'condition' => array( 'enable_anim_size' => true ) ),
		'anim_size_min' => array( 'label' => __( 'Minimum size', 'radmanct-components' ), 'default' => 0.0, 'min' => 0, 'max' => 200, 'condition' => array( 'enable_anim_size' => true ) ),
		'enable_anim_size_sync' => array( 'label' => __( 'Synchronize size animation', 'radmanct-components' ), 'default' => false, 'condition' => array( 'enable_anim_size' => true ) ),
		'enable_line_linked' => array( 'label' => __( 'Linked lines', 'radmanct-components' ), 'default' => false ),
		'line_distance' => array( 'label' => __( 'Line distance', 'radmanct-components' ), 'default' => 100, 'min' => 1, 'max' => 1000, 'condition' => array( 'enable_line_linked' => true ) ),
		'line_color' => array( 'label' => __( 'Line color', 'radmanct-components' ), 'default' => '#ffffff', 'color' => true, 'condition' => array( 'enable_line_linked' => true ) ),
		'line_opacity' => array( 'label' => __( 'Line opacity', 'radmanct-components' ), 'default' => 1.0, 'min' => 0, 'max' => 1, 'step' => .05, 'condition' => array( 'enable_line_linked' => true ) ),
		'line_width' => array( 'label' => __( 'Line width', 'radmanct-components' ), 'default' => 1.0, 'min' => 0, 'max' => 20, 'condition' => array( 'enable_line_linked' => true ) ),
		'enable_move' => array( 'label' => __( 'Move particles', 'radmanct-components' ), 'default' => true ),
		'move_speed' => array( 'label' => __( 'Move speed', 'radmanct-components' ), 'default' => 2.0, 'min' => 0, 'max' => 50, 'condition' => array( 'enable_move' => true ) ),
		'move_direction' => array( 'label' => __( 'Move direction', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'top' => __( 'Top', 'radmanct-components' ), 'top-right' => __( 'Top right', 'radmanct-components' ), 'right' => __( 'Right', 'radmanct-components' ), 'bottom-right' => __( 'Bottom right', 'radmanct-components' ), 'bottom' => __( 'Bottom', 'radmanct-components' ), 'bottom-left' => __( 'Bottom left', 'radmanct-components' ), 'left' => __( 'Left', 'radmanct-components' ), 'top-left' => __( 'Top left', 'radmanct-components' ) ), 'condition' => array( 'enable_move' => true ) ),
		'enable_random_move' => array( 'label' => __( 'Random movement', 'radmanct-components' ), 'default' => false, 'condition' => array( 'enable_move' => true ) ),
		'enable_straight_move' => array( 'label' => __( 'Straight movement', 'radmanct-components' ), 'default' => false, 'condition' => array( 'enable_move' => true ) ),
		'move_out_mode' => array( 'label' => __( 'Out mode', 'radmanct-components' ), 'default' => 'out', 'options' => array( 'out' => __( 'Out', 'radmanct-components' ), 'bounce' => __( 'Bounce', 'radmanct-components' ) ), 'condition' => array( 'enable_move' => true ) ),
		'enable_bounce_move' => array( 'label' => __( 'Particle collisions', 'radmanct-components' ), 'default' => false, 'condition' => array( 'enable_move' => true ) ),
		'enable_attract_move' => array( 'label' => __( 'Attraction', 'radmanct-components' ), 'default' => false, 'condition' => array( 'enable_move' => true ) ),
		'move_attract_rotatex' => array( 'label' => __( 'Attract rotate X', 'radmanct-components' ), 'default' => 3000, 'min' => 1, 'max' => 10000, 'condition' => array( 'enable_attract_move' => true ) ),
		'move_attract_rotatey' => array( 'label' => __( 'Attract rotate Y', 'radmanct-components' ), 'default' => 3000, 'min' => 1, 'max' => 10000, 'condition' => array( 'enable_attract_move' => true ) ),
		'detect_on' => array( 'label' => __( 'Detect on', 'radmanct-components' ), 'default' => '', 'group' => 'interaction', 'options' => array( '' => __( 'None', 'radmanct-components' ), 'canvas' => __( 'Canvas area', 'radmanct-components' ), 'window' => __( 'Window', 'radmanct-components' ) ) ),
		'enable_onhover' => array( 'label' => __( 'Hover interaction', 'radmanct-components' ), 'default' => false, 'group' => 'interaction' ),
		'onhover_mode' => array( 'label' => __( 'Hover mode', 'radmanct-components' ), 'default' => 'grab', 'group' => 'interaction', 'options' => array( 'grab' => __( 'Grab', 'radmanct-components' ), 'bubble' => __( 'Bubble', 'radmanct-components' ), 'repulse' => __( 'Repulse', 'radmanct-components' ) ), 'condition' => array( 'enable_onhover' => true ) ),
		'enable_onclick' => array( 'label' => __( 'Click interaction', 'radmanct-components' ), 'default' => false, 'group' => 'interaction' ),
		'onclick_mode' => array( 'label' => __( 'Click mode', 'radmanct-components' ), 'default' => 'push', 'group' => 'interaction', 'options' => array( 'push' => __( 'Push', 'radmanct-components' ), 'remove' => __( 'Remove', 'radmanct-components' ), 'bubble' => __( 'Bubble', 'radmanct-components' ), 'repulse' => __( 'Repulse', 'radmanct-components' ) ), 'condition' => array( 'enable_onclick' => true ) ),
		'enable_inter_resize' => array( 'label' => __( 'Resize with container', 'radmanct-components' ), 'default' => true, 'group' => 'interaction' ),
		'modes_grab_distance' => array( 'label' => __( 'Grab distance', 'radmanct-components' ), 'default' => 100, 'min' => 1, 'max' => 1000, 'group' => 'interaction' ),
		'modes_grab_opacity' => array( 'label' => __( 'Grab line opacity', 'radmanct-components' ), 'default' => 1.0, 'min' => 0, 'max' => 1, 'step' => .05, 'group' => 'interaction' ),
		'modes_bubble_distance' => array( 'label' => __( 'Bubble distance', 'radmanct-components' ), 'default' => 200, 'min' => 1, 'max' => 1000, 'group' => 'interaction' ),
		'modes_bubble_size' => array( 'label' => __( 'Bubble size', 'radmanct-components' ), 'default' => 80, 'min' => 0, 'max' => 200, 'group' => 'interaction' ),
		'modes_bubble_duration' => array( 'label' => __( 'Bubble duration', 'radmanct-components' ), 'default' => .4, 'min' => .1, 'max' => 10, 'group' => 'interaction' ),
		'modes_repulse_distance' => array( 'label' => __( 'Repulse distance', 'radmanct-components' ), 'default' => 200, 'min' => 1, 'max' => 1000, 'group' => 'interaction' ),
		'modes_repulse_duration' => array( 'label' => __( 'Repulse duration', 'radmanct-components' ), 'default' => .4, 'min' => .1, 'max' => 10, 'group' => 'interaction' ),
		'modes_push_particles_nb' => array( 'label' => __( 'Push count', 'radmanct-components' ), 'default' => 4, 'min' => 0, 'max' => 50, 'group' => 'interaction' ),
		'modes_remove_particles_nb' => array( 'label' => __( 'Remove count', 'radmanct-components' ), 'default' => 2, 'min' => 0, 'max' => 50, 'group' => 'interaction' ),
		'retina_detect' => array( 'label' => __( 'Retina detection', 'radmanct-components' ), 'default' => true, 'group' => 'retina' ),
	);
	foreach ( array( 'circle' => __( 'Circle', 'radmanct-components' ), 'edge' => __( 'Square', 'radmanct-components' ), 'triangle' => __( 'Triangle', 'radmanct-components' ), 'polygon' => __( 'Polygon', 'radmanct-components' ), 'star' => __( 'Star', 'radmanct-components' ), 'image' => __( 'Image', 'radmanct-components' ) ) as $shape => $label ) { $fields[ 'shape_' . $shape ] = array( 'label' => $label, 'default' => 'circle' === $shape, 'shape' => true ); }
	foreach ( $fields as &$field ) { $field += array( 'group' => 'options' ); } unset( $field );
	return $fields;
}

function color_fields(): array { return array( 'scolor' => array( 'label' => __( 'Color', 'radmanct-components' ), 'default' => '#ffffff', 'color' => true ) ); }

function settings( array $input ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( is_bool( $field['default'] ) ) { $value = in_array( $value, array( true, 1, '1', 'yes' ), true ); }
		elseif ( ! empty( $field['media'] ) ) { $value = absint( is_array( $value ) ? ( $value['id'] ?? 0 ) : $value ); }
		elseif ( isset( $field['min'] ) ) { $value = is_array( $value ) ? ( $value['size'] ?? $field['default'] ) : $value; $value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (float) $value ) ) : $field['default']; }
		elseif ( ! empty( $field['color'] ) ) { $value = is_string( $value ) ? sanitize_hex_color( $value ) : ''; $value = $value ?: $field['default']; }
		elseif ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		else { $value = is_string( $value ) ? substr( $value, 0, 100 ) : $field['default']; }
		$result[ $key ] = $value;
	}
	$result['multi_color_values'] = array();
	foreach ( array_slice( is_array( $input['multi_color_values'] ?? null ) ? $input['multi_color_values'] : array(), 0, 16 ) as $row ) { $color = is_array( $row ) && is_string( $row['scolor'] ?? null ) ? sanitize_hex_color( $row['scolor'] ) : ''; if ( $color ) { $result['multi_color_values'][] = $color; } }
	$result['shape_type'] = array();
	foreach ( array( 'circle', 'edge', 'triangle', 'polygon', 'star', 'image' ) as $shape ) { if ( $result[ 'shape_' . $shape ] ) { $result['shape_type'][] = $shape; } }
	if ( isset( $input['shape_type'] ) && is_array( $input['shape_type'] ) ) { $result['shape_type'] = array_values( array_intersect( array( 'circle', 'edge', 'triangle', 'polygon', 'star', 'image' ), array_filter( $input['shape_type'], 'is_string' ) ) ); }
	$result['shape_type'] = $result['shape_type'] ?: array( 'circle' );
	$result['anim_opacity_min'] = min( $result['opacity'], $result['anim_opacity_min'] );
	$result['anim_size_min'] = min( $result['size'], $result['anim_size_min'] );
	return $result;
}

function options( array $s ): array {
	$image = (int) apply_filters( 'wpml_object_id', $s['image'], 'attachment', true );
	$url = $image ? wp_get_attachment_image_url( $image, 'full' ) : '';
	if ( ! $url || wp_parse_url( $url, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) { $url = ''; }
	$shapes = $url ? $s['shape_type'] : array_values( array_diff( $s['shape_type'], array( 'image' ) ) );
	$color = 'random_color' === $s['color_type'] ? 'random' : ( 'multi_color' === $s['color_type'] ? ( $s['multi_color_values'] ?: array( $s['color'] ) ) : $s['color'] );
	return array(
		'particles' => array(
			'number' => array( 'value' => (int) $s['number'], 'density' => array( 'enable' => $s['enable_density'], 'value_area' => $s['density'] ) ),
			'color' => array( 'value' => $color ),
			'shape' => array( 'type' => $shapes ?: array( 'circle' ), 'stroke' => array( 'width' => $s['stroke_width'], 'color' => $s['stroke_color'] ), 'polygon' => array( 'nb_sides' => $s['nb_sides'] ), 'image' => array( 'src' => $url, 'width' => $s['image_width'], 'height' => $s['image_height'] ) ),
			'opacity' => array( 'value' => $s['opacity'], 'random' => $s['enable_random_opacity'], 'anim' => array( 'enable' => $s['enable_anim_opacity'], 'speed' => $s['anim_opacity_speed'], 'opacity_min' => $s['anim_opacity_min'], 'sync' => $s['enable_anim_sync'] ) ),
			'size' => array( 'value' => $s['size'], 'random' => $s['enable_random_size'], 'anim' => array( 'enable' => $s['enable_anim_size'], 'speed' => $s['anim_size_speed'], 'size_min' => $s['anim_size_min'], 'sync' => $s['enable_anim_size_sync'] ) ),
			'line_linked' => array( 'enable' => $s['enable_line_linked'], 'distance' => $s['line_distance'], 'color' => $s['line_color'], 'opacity' => $s['line_opacity'], 'width' => $s['line_width'] ),
			'move' => array( 'enable' => $s['enable_move'], 'speed' => $s['move_speed'], 'direction' => $s['move_direction'], 'random' => $s['enable_random_move'], 'straight' => $s['enable_straight_move'], 'out_mode' => $s['move_out_mode'], 'bounce' => $s['enable_bounce_move'], 'attract' => array( 'enable' => $s['enable_attract_move'], 'rotateX' => $s['move_attract_rotatex'], 'rotateY' => $s['move_attract_rotatey'] ) ),
		),
		'interactivity' => array( 'detect_on' => $s['detect_on'] ?: 'canvas', 'events' => array( 'onhover' => array( 'enable' => '' !== $s['detect_on'] && $s['enable_onhover'], 'mode' => $s['onhover_mode'] ), 'onclick' => array( 'enable' => '' !== $s['detect_on'] && $s['enable_onclick'], 'mode' => $s['onclick_mode'] ), 'resize' => $s['enable_inter_resize'] ), 'modes' => array( 'grab' => array( 'distance' => $s['modes_grab_distance'], 'line_linked' => array( 'opacity' => $s['modes_grab_opacity'] ) ), 'bubble' => array( 'distance' => $s['modes_bubble_distance'], 'size' => $s['modes_bubble_size'], 'duration' => $s['modes_bubble_duration'] ), 'repulse' => array( 'distance' => $s['modes_repulse_distance'], 'duration' => $s['modes_repulse_duration'] ), 'push' => array( 'particles_nb' => (int) $s['modes_push_particles_nb'] ), 'remove' => array( 'particles_nb' => (int) $s['modes_remove_particles_nb'] ) ) ),
		'retina_detect' => $s['retina_detect'],
	);
}

function render( array $input = array() ): string {
	$s = settings( $input );
	$style = '';
	foreach ( array( 'height' => '450px', 'height_tablet' => '', 'height_mobile' => '' ) as $key => $fallback ) { $value = preg_match( '/^\d+(?:\.\d+)?(?:px|vh|svh|dvh|%|em|rem)$/D', $s[ $key ] ) ? $s[ $key ] : $fallback; if ( '' !== $value ) { $style .= '--rdct-pt-' . str_replace( '_', '-', $key ) . ':' . $value . ';'; } }
	$id = wp_unique_id( ( sanitize_html_class( $s['particle_id'] ) ?: 'rdct-particles' ) . '-' );
	return '<div class="rdct-pt" data-rdct-particles data-background="' . ( $s['as_bg'] ? '1' : '0' ) . '" data-hover="' . ( $s['visible_on_hover'] ? '1' : '0' ) . '" data-options="' . esc_attr( wp_json_encode( options( $s ) ) ) . '" style="' . esc_attr( $style ) . '" aria-hidden="true"><div class="rdct-pt__canvas" aria-hidden="true" id="' . esc_attr( $id ) . '"></div></div>';
}
