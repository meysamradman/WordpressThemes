<?php

namespace RadmanCTComponents\Components\Image;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/values.php';
require_once __DIR__ . '/image-loading.php';

function groups(): array {
	return array( 'general' => __( 'Image', 'radmanct-components' ), 'opacity' => __( 'Opacity', 'radmanct-components' ), 'lightbox' => __( 'Lightbox', 'radmanct-components' ), 'roundness' => __( 'Roundness', 'radmanct-components' ), 'shadow' => __( 'Shadow', 'radmanct-components' ), 'parallax' => __( 'Image parallax', 'radmanct-components' ), 'reveal' => __( 'Reveal effect', 'radmanct-components' ), 'float' => __( 'Floating effect', 'radmanct-components' ), 'tilt' => __( 'Hover 3D', 'radmanct-components' ), 'lines' => __( 'Overlay lines', 'radmanct-components' ), 'overlay' => __( 'Overlay background', 'radmanct-components' ), 'label' => __( 'Side label', 'radmanct-components' ), 'laptop' => __( 'Laptop', 'radmanct-components' ), 'tablet' => __( 'Tablet', 'radmanct-components' ), 'mobile' => __( 'Mobile', 'radmanct-components' ) );
}

function fields(): array {
	static $cache;
	if ( is_array( $cache ) ) { return $cache; }
	$fields = array(
		'image_id' => array( 'label' => __( 'Image', 'radmanct-components' ), 'default' => 0, 'media' => true, 'min' => 0, 'max' => PHP_INT_MAX, 'group' => 'general' ),
		'image_size' => array( 'label' => __( 'Image resolution', 'radmanct-components' ), 'default' => 'full', 'options' => array( 'thumbnail' => 'Thumbnail', 'medium' => 'Medium', 'medium_large' => 'Medium large', 'large' => 'Large', 'full' => 'Full', 'custom' => 'Custom' ), 'group' => 'general' ),
		'custom_width' => array( 'label' => __( 'Source width (px)', 'radmanct-components' ), 'default' => 1200, 'min' => 1, 'max' => 4096, 'group' => 'general', 'condition' => array( 'image_size' => 'custom' ) ),
		'image_loading' => \RadmanCTComponents\Components\ImageLoading\field() + array( 'group' => 'general' ),
		'custom_height' => array( 'label' => __( 'Source height (px)', 'radmanct-components' ), 'default' => 800, 'min' => 1, 'max' => 4096, 'group' => 'general', 'condition' => array( 'image_size' => 'custom' ) ),
		'url' => array( 'label' => __( 'Link', 'radmanct-components' ), 'default' => '', 'group' => 'general' ),
		'new_tab' => array( 'label' => __( 'Open in new window', 'radmanct-components' ), 'default' => false, 'group' => 'general' ),
		'nofollow' => array( 'label' => __( 'Add nofollow', 'radmanct-components' ), 'default' => false, 'group' => 'general' ),
		'lightbox' => array( 'label' => __( 'Enable lightbox', 'radmanct-components' ), 'default' => false, 'group' => 'lightbox' ),
		'lightbox_group' => array( 'label' => __( 'Lightbox group', 'radmanct-components' ), 'default' => '', 'group' => 'lightbox', 'condition' => array( 'lightbox' => true ) ),
		'roundness' => array( 'label' => __( 'Roundness', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => 'None', '2px' => '2px', '4px' => '4px', '6px' => '6px', '8px' => '8px', '50%' => 'Circle', 'custom' => 'Custom' ), 'group' => 'roundness' ),
		'custom_radius' => array( 'label' => __( 'Custom roundness', 'radmanct-components' ), 'default' => '20px', 'lengths' => true, 'group' => 'roundness', 'condition' => array( 'roundness' => 'custom' ) ),
		'shadow' => array( 'label' => __( 'Shadow', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => 'None', 'depth1' => 'Depth 1', 'depth2' => 'Depth 2', 'depth3' => 'Depth 3', 'depth4' => 'Depth 4', 'custom' => 'Custom' ), 'group' => 'shadow' ),
		'animate_shadow' => array( 'label' => __( 'Animate shadow', 'radmanct-components' ), 'default' => false, 'group' => 'shadow' ),
		'parallax' => array( 'label' => __( 'Parallax inside a fixed frame', 'radmanct-components' ), 'default' => false, 'group' => 'parallax' ),
		'parallax_amount' => array( 'label' => __( 'Parallax travel (%)', 'radmanct-components' ), 'default' => 12, 'min' => 1, 'max' => 30, 'group' => 'parallax' ),
		'parallax_direction' => array( 'label' => __( 'Direction', 'radmanct-components' ), 'default' => 'up', 'options' => array( 'up' => __( 'Up', 'radmanct-components' ), 'down' => __( 'Down', 'radmanct-components' ) ), 'group' => 'parallax' ),
		'parallax_min_width' => array( 'label' => __( 'Minimum screen width', 'radmanct-components' ), 'default' => 768, 'min' => 0, 'max' => 1920, 'group' => 'parallax' ),
		'reveal' => array( 'label' => __( 'Reveal effect', 'radmanct-components' ), 'default' => false, 'group' => 'reveal' ),
		'reveal_direction' => array( 'label' => __( 'Direction', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => 'Start', 'end' => 'End', 'lr' => 'Left - Right', 'rl' => 'Right - Left', 'tb' => 'Top - Bottom', 'bt' => 'Bottom - Top' ), 'group' => 'reveal' ),
		'float' => array( 'label' => __( 'Floating effect', 'radmanct-components' ), 'default' => false, 'group' => 'float' ),
		'float_ease' => array( 'label' => __( 'Float easing', 'radmanct-components' ), 'default' => 'ease', 'options' => array_combine( array( 'ease', 'ease-in', 'ease-out', 'ease-in-out', 'linear', 'custom' ), array( 'ease', 'ease-in', 'ease-out', 'ease-in-out', 'linear', 'Custom' ) ), 'group' => 'float' ),
		'float_custom_ease' => array( 'label' => __( 'Custom float easing', 'radmanct-components' ), 'default' => 'cubic-bezier(0.25,0.1,0.25,1)', 'group' => 'float', 'condition' => array( 'float_ease' => 'custom' ) ),
		'tilt' => array( 'label' => __( 'Hover 3D', 'radmanct-components' ), 'default' => false, 'group' => 'tilt' ),
		'lines' => array( 'label' => __( 'Overlay lines', 'radmanct-components' ), 'default' => false, 'group' => 'lines' ),
		'overlay' => array( 'label' => __( 'Overlay background', 'radmanct-components' ), 'default' => false, 'group' => 'overlay' ),
		'overlay_image_id' => array( 'label' => __( 'Overlay image', 'radmanct-components' ), 'default' => 0, 'media' => true, 'min' => 0, 'max' => PHP_INT_MAX, 'group' => 'overlay' ),
		'label_enabled' => array( 'label' => __( 'Add side label', 'radmanct-components' ), 'default' => false, 'group' => 'label' ),
		'label' => array( 'label' => __( 'Side label', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'group' => 'label' ),
		'label_side' => array( 'label' => __( 'Label side', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => 'Start', 'end' => 'End', 'left' => 'Left', 'right' => 'Right' ), 'group' => 'label' ),
		'label_position' => array( 'label' => __( 'Label position', 'radmanct-components' ), 'default' => 'center', 'options' => array( 'start' => 'Start', 'center' => 'Center', 'end' => 'End' ), 'group' => 'label' ),
		'label_overlay' => array( 'label' => __( 'Side label overlay', 'radmanct-components' ), 'default' => false, 'group' => 'label' ),
	);
	foreach ( array(
		'opacity' => array( __( 'Opacity', 'radmanct-components' ), 1.0, 0, 1, 'opacity' ),
		'hover_opacity' => array( __( 'Hover opacity', 'radmanct-components' ), 1.0, 0, 1, 'opacity' ),
		'blur' => array( __( 'Blur', 'radmanct-components' ), 0, 0, 30, 'general' ),
		'brightness' => array( __( 'Brightness', 'radmanct-components' ), 100, 0, 200, 'general' ),
		'contrast' => array( __( 'Contrast', 'radmanct-components' ), 100, 0, 200, 'general' ),
		'saturation' => array( __( 'Saturation', 'radmanct-components' ), 100, 0, 200, 'general' ),
		'hue' => array( __( 'Hue', 'radmanct-components' ), 0, 0, 360, 'general' ),
		'shadow_delay' => array( __( 'Shadow delay (ms)', 'radmanct-components' ), 0, 0, 10000, 'shadow' ),
		'shadow_x' => array( __( 'Shadow X', 'radmanct-components' ), 0, -200, 200, 'shadow' ),
		'shadow_y' => array( __( 'Shadow Y', 'radmanct-components' ), 20, -200, 200, 'shadow' ),
		'shadow_blur' => array( __( 'Shadow blur', 'radmanct-components' ), 40, 0, 200, 'shadow' ),
		'shadow_spread' => array( __( 'Shadow spread', 'radmanct-components' ), 0, -100, 100, 'shadow' ),
		'reveal_delay' => array( __( 'Reveal delay (ms)', 'radmanct-components' ), 0, 0, 10000, 'reveal' ),
		'float_from' => array( __( 'Float from (%)', 'radmanct-components' ), 0.0, -100, 100, 'float' ),
		'float_to' => array( __( 'Float to (%)', 'radmanct-components' ), 3.0, -100, 100, 'float' ),
		'float_duration' => array( __( 'Float duration (s)', 'radmanct-components' ), 1.0, 0.2, 20, 'float' ),
		'float_delay' => array( __( 'Float delay (s)', 'radmanct-components' ), 0.0, 0, 20, 'float' ),
		'stacking_factor' => array( __( 'Stacking factor', 'radmanct-components' ), 1.0, 0, 10, 'tilt' ),
		'lines_count' => array( __( 'Lines count', 'radmanct-components' ), 3, 1, 20, 'lines' ),
		'label_size' => array( __( 'Label size', 'radmanct-components' ), 14, 8, 120, 'label' ),
		'label_weight' => array( __( 'Label weight', 'radmanct-components' ), 400, 100, 900, 'label' ),
		'label_spacing' => array( __( 'Label letter spacing', 'radmanct-components' ), 0.0, -5, 20, 'label' ),
		'label_line_height' => array( __( 'Label line height', 'radmanct-components' ), 1.5, 0.8, 4, 'label' ),
	) as $key => $value ) { $fields[ $key ] = array( 'label' => $value[0], 'default' => $value[1], 'min' => $value[2], 'max' => $value[3], 'group' => $value[4] ); }
	foreach ( array( 'reveal' => '#235a50', 'overlay' => '#00000080' ) as $prefix => $default ) {
		$fields[ $prefix . '_background' ] = array( 'label' => __( 'Background type', 'radmanct-components' ), 'default' => 'solid', 'options' => array( 'solid' => 'Solid', 'linear' => 'Linear gradient', 'radial' => 'Radial gradient' ), 'group' => $prefix );
		foreach ( array( 'color' => $default, 'second_color' => '#00000000' ) as $name => $color ) { $fields[ $prefix . '_' . $name ] = array( 'label' => 'color' === $name ? __( 'Background color', 'radmanct-components' ) : __( 'Gradient second color', 'radmanct-components' ), 'default' => $color, 'color' => true, 'group' => $prefix ); }
		foreach ( array( 'angle' => 180, 'stop' => 0, 'second_stop' => 100 ) as $name => $value ) { $fields[ $prefix . '_' . $name ] = array( 'label' => 'angle' === $name ? __( 'Gradient angle', 'radmanct-components' ) : ( 'stop' === $name ? __( 'Gradient first stop', 'radmanct-components' ) : __( 'Gradient second stop', 'radmanct-components' ) ), 'default' => $value, 'min' => 0, 'max' => 'angle' === $name ? 360 : 100, 'group' => $prefix ); }
	}
	foreach ( array( 'shadow_color' => array( __( 'Shadow color', 'radmanct-components' ), '#00000040', 'shadow' ), 'lines_color' => array( __( 'Lines color', 'radmanct-components' ), '#ffffff80', 'lines' ), 'label_color' => array( __( 'Label color', 'radmanct-components' ), '#172b3a', 'label' ), 'label_background' => array( __( 'Label background', 'radmanct-components' ), '#ffffff', 'label' ) ) as $key => $value ) { $fields[ $key ] = array( 'label' => $value[0], 'default' => $value[1], 'color' => true, 'group' => $value[2] ); }
	foreach ( array( '' => 'general', '_laptop' => 'laptop', '_tablet' => 'tablet', '_mobile' => 'mobile' ) as $suffix => $group ) {
		foreach ( array( 'width' => __( 'Width', 'radmanct-components' ), 'max_width' => __( 'Max width', 'radmanct-components' ), 'height' => __( 'Height', 'radmanct-components' ), 'container_width' => __( 'Percentage image size', 'radmanct-components' ) ) as $key => $label ) { $fields[ $key . $suffix ] = array( 'label' => $label, 'default' => '' === $suffix ? ( in_array( $key, array( 'width', 'max_width', 'container_width' ), true ) ? '100%' : 'auto' ) : '', 'length' => true, 'group' => $group ); }
		$fields[ 'align' . $suffix ] = array( 'label' => __( 'Alignment', 'radmanct-components' ), 'default' => '' === $suffix ? 'start' : 'inherit', 'options' => array_combine( array( 'inherit', 'start', 'center', 'end', 'left', 'right' ), array( 'Inherit', 'Start', 'Center', 'End', 'Left', 'Right' ) ), 'group' => $group );
		$fields[ 'fit' . $suffix ] = array( 'label' => __( 'Object fit', 'radmanct-components' ), 'default' => '' === $suffix ? 'cover' : 'inherit', 'options' => array_combine( array( 'inherit', 'cover', 'contain', 'fill', 'none' ), array( 'Inherit', 'Cover', 'Contain', 'Fill', 'None' ) ), 'group' => $group );
	}
	$labels = array( 'None' => __( 'None', 'radmanct-components' ), 'Custom' => __( 'Custom', 'radmanct-components' ), 'Start' => __( 'Start', 'radmanct-components' ), 'End' => __( 'End', 'radmanct-components' ), 'Center' => __( 'Center', 'radmanct-components' ), 'Left' => __( 'Left', 'radmanct-components' ), 'Right' => __( 'Right', 'radmanct-components' ), 'Inherit' => __( 'Inherit', 'radmanct-components' ), 'Cover' => __( 'Cover', 'radmanct-components' ), 'Contain' => __( 'Contain', 'radmanct-components' ), 'Fill' => __( 'Fill', 'radmanct-components' ), 'Circle' => __( 'Circle', 'radmanct-components' ), 'Thumbnail' => __( 'Thumbnail', 'radmanct-components' ), 'Medium' => __( 'Medium', 'radmanct-components' ), 'Medium large' => __( 'Medium large', 'radmanct-components' ), 'Large' => __( 'Large', 'radmanct-components' ), 'Full' => __( 'Full', 'radmanct-components' ), 'Solid' => __( 'Solid', 'radmanct-components' ), 'Linear gradient' => __( 'Linear gradient', 'radmanct-components' ), 'Radial gradient' => __( 'Radial gradient', 'radmanct-components' ), 'Depth 1' => __( 'Depth 1', 'radmanct-components' ), 'Depth 2' => __( 'Depth 2', 'radmanct-components' ), 'Depth 3' => __( 'Depth 3', 'radmanct-components' ), 'Depth 4' => __( 'Depth 4', 'radmanct-components' ), 'Left - Right' => __( 'Left - Right', 'radmanct-components' ), 'Right - Left' => __( 'Right - Left', 'radmanct-components' ), 'Top - Bottom' => __( 'Top - Bottom', 'radmanct-components' ), 'Bottom - Top' => __( 'Bottom - Top', 'radmanct-components' ) );
	$fields['shadow_inset'] = array( 'label' => __( 'Inset shadow', 'radmanct-components' ), 'default' => false, 'group' => 'shadow', 'condition' => array( 'shadow' => 'custom' ) );
	$fields['label_font'] = array( 'label' => __( 'Label font family', 'radmanct-components' ), 'default' => 'inherit', 'group' => 'label' );
	foreach ( array( 'from', 'to' ) as $side ) { $fields[ 'float_' . $side . '_unit' ] = array( 'label' => 'from' === $side ? __( 'Float start unit', 'radmanct-components' ) : __( 'Float end unit', 'radmanct-components' ), 'default' => '%', 'options' => array( '%' => '%', 'px' => 'px' ), 'group' => 'float' ); }
	$fields['overlay_image_fit'] = array( 'label' => __( 'Overlay image size', 'radmanct-components' ), 'default' => 'cover', 'options' => array( 'cover' => 'Cover', 'contain' => 'Contain', 'auto' => 'Automatic' ), 'group' => 'overlay' );
	$fields['overlay_image_repeat'] = array( 'label' => __( 'Image repeat', 'radmanct-components' ), 'default' => 'no-repeat', 'options' => array_combine( array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ), array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ) ), 'group' => 'overlay' );
	$positions = array( 'left top' => __( 'Left top', 'radmanct-components' ), 'center top' => __( 'Center top', 'radmanct-components' ), 'right top' => __( 'Right top', 'radmanct-components' ), 'left center' => __( 'Left center', 'radmanct-components' ), 'center center' => __( 'Center', 'radmanct-components' ), 'right center' => __( 'Right center', 'radmanct-components' ), 'left bottom' => __( 'Left bottom', 'radmanct-components' ), 'center bottom' => __( 'Center bottom', 'radmanct-components' ), 'right bottom' => __( 'Right bottom', 'radmanct-components' ) );
	foreach ( array( 'reveal', 'overlay' ) as $prefix ) { $fields[ $prefix . '_position' ] = array( 'label' => __( 'Background position', 'radmanct-components' ), 'default' => 'center center', 'options' => $positions, 'group' => $prefix ); }
	$toggles = array( 'parallax' => 'parallax', 'lightbox' => 'lightbox', 'reveal' => 'reveal', 'float' => 'float', 'tilt' => 'tilt', 'lines' => 'lines', 'overlay' => 'overlay', 'label' => 'label_enabled' );
	foreach ( $fields as $key => &$field ) {
		if ( isset( $toggles[ $field['group'] ] ) && $key !== $toggles[ $field['group'] ] ) { $field['condition'][ $toggles[ $field['group'] ] ] = true; }
		if ( in_array( $key, array( 'shadow_x', 'shadow_y', 'shadow_blur', 'shadow_spread', 'shadow_color' ), true ) ) { $field['condition']['shadow'] = 'custom'; }
		if ( 'shadow_delay' === $key ) { $field['condition']['animate_shadow'] = true; }
		if ( isset( $field['options'] ) ) { foreach ( $field['options'] as &$label ) { $label = $labels[ $label ] ?? $label; } unset( $label ); }
	} unset( $field );
	$cache = $fields;
	return $cache;
}

function settings( array $input ): array {
	$s = \RadmanCTComponents\Components\Values\fields( $input, fields() );
	foreach ( fields() as $key => $field ) {
		if ( ! empty( $field['length'] ) || ! empty( $field['lengths'] ) ) {
			$parts = preg_split( '/\s+/', trim( $s[ $key ] ) );
			$valid = count( $parts ) <= ( ! empty( $field['lengths'] ) ? 4 : 1 );
			foreach ( $parts as $part ) { if ( ! preg_match( '/^(?:|auto|(?:\d{1,4}(?:\.\d{1,2})?|\.\d{1,2})(?:px|%|vw|vh|rem|em))$/D', $part ) ) { $valid = false; } }
			if ( ! $valid ) { $s[ $key ] = $field['default']; }
		}
	}
	$s['url'] = preg_match( '~^(?:https?://|/(?!/)|#)~i', $s['url'] ) ? esc_url_raw( $s['url'], array( 'http', 'https' ) ) : '';
	if ( ! preg_match( '/^[\p{L}\p{N} ,"\x27-]{1,100}$/uD', $s['label_font'] ) ) { $s['label_font'] = 'inherit'; }
	if ( ! preg_match( '/^cubic-bezier\(\s*(?:0(?:\.\d+)?|1(?:\.0+)?|\.\d+)\s*,\s*-?\d*(?:\.\d+)?\s*,\s*(?:0(?:\.\d+)?|1(?:\.0+)?|\.\d+)\s*,\s*-?\d*(?:\.\d+)?\s*\)$/D', $s['float_custom_ease'] ) ) { $s['float_custom_ease'] = 'ease'; }
	return $s;
}

function background( array $s, string $prefix ): string {
	$color = $s[ $prefix . '_color' ];
	$type = $s[ $prefix . '_background' ];
	return 'solid' === $type ? $color : ( 'radial' === $type ? 'radial-gradient(circle at ' . $s[ $prefix . '_position' ] . ',' : 'linear-gradient(' . $s[ $prefix . '_angle' ] . 'deg,' ) . $color . ' ' . $s[ $prefix . '_stop' ] . '%,' . $s[ $prefix . '_second_color' ] . ' ' . max( $s[ $prefix . '_stop' ], $s[ $prefix . '_second_stop' ] ) . '%)';
}

function css( array $s ): string {
	$css = '';
	foreach ( array( 'opacity', 'hover_opacity', 'shadow_delay', 'reveal_delay', 'float_from', 'float_to', 'float_duration', 'float_delay', 'lines_count', 'label_size', 'label_weight', 'label_spacing', 'label_line_height', 'lines_color', 'label_color', 'label_background' ) as $key ) {
		$unit = in_array( $key, array( 'shadow_delay', 'reveal_delay' ), true ) ? 'ms' : ( in_array( $key, array( 'float_delay', 'float_duration' ), true ) ? 's' : ( in_array( $key, array( 'float_from', 'float_to' ), true ) ? '%' : ( in_array( $key, array( 'label_size', 'label_spacing' ), true ) ? 'px' : '' ) ) );
		$css .= '--rdct-im-' . str_replace( '_', '-', $key ) . ':' . $s[ $key ] . $unit . ';';
	}
	$css .= '--rdct-im-float-from:' . $s['float_from'] . $s['float_from_unit'] . ';--rdct-im-float-to:' . $s['float_to'] . $s['float_to_unit'] . ';--rdct-im-label-font:' . $s['label_font'] . ';';
	$radius = 'none' === $s['roundness'] ? '0' : ( 'custom' === $s['roundness'] ? $s['custom_radius'] : $s['roundness'] );
	$shadows = array( 'none' => 'none', 'depth1' => '0 25px 30px #00000012', 'depth2' => '0 30px 50px #00000026', 'depth3' => '0 30px 70px #00000026', 'depth4' => '0 30px 100px #0000002b', 'custom' => $s['shadow_x'] . 'px ' . $s['shadow_y'] . 'px ' . $s['shadow_blur'] . 'px ' . $s['shadow_spread'] . 'px ' . $s['shadow_color'] );
	$css .= '--rdct-im-radius:' . $radius . ';--rdct-im-shadow:' . $shadows[ $s['shadow'] ] . ';--rdct-im-revealer:' . background( $s, 'reveal' ) . ';--rdct-im-overlay:' . background( $s, 'overlay' ) . ';';
	if ( 'custom' === $s['shadow'] && $s['shadow_inset'] ) { $css .= '--rdct-im-shadow:inset ' . $shadows['custom'] . ';'; }
	$css .= '--rdct-im-ease:' . ( 'custom' === $s['float_ease'] ? $s['float_custom_ease'] : $s['float_ease'] ) . ';--rdct-im-filter:blur(' . $s['blur'] . 'px) brightness(' . $s['brightness'] . '%) contrast(' . $s['contrast'] . '%) saturate(' . $s['saturation'] . '%) hue-rotate(' . $s['hue'] . 'deg);';
	foreach ( array( '', '_laptop', '_tablet', '_mobile' ) as $suffix ) {
		foreach ( array( 'width', 'max_width', 'height', 'container_width', 'fit', 'align' ) as $key ) { if ( '' !== $s[ $key . $suffix ] && 'inherit' !== $s[ $key . $suffix ] ) { $css .= '--rdct-im-' . str_replace( '_', '-', $key . $suffix ) . ':' . $s[ $key . $suffix ] . ';'; } }
	}
	return $css;
}

function render( array $input ): string {
	$s = settings( $input ); $id = $s['image_id'];
	if ( $id && function_exists( 'apply_filters' ) ) { $id = (int) apply_filters( 'wpml_object_id', $id, 'attachment', true ); }
	if ( $id && ! wp_attachment_is_image( $id ) ) { return ''; }
	$size = 'custom' === $s['image_size'] ? array( $s['custom_width'], $s['custom_height'] ) : $s['image_size'];
	$image = $id ? wp_get_attachment_image( $id, $size, false, array( 'class' => 'rdct-im__image' ) + \RadmanCTComponents\Components\ImageLoading\attributes( $s['image_loading'] ) ) : '<img class="rdct-im__image" src="' . esc_url( \RadmanCTComponents\url( 'images/components/image.svg' ) ) . '" width="960" height="640" alt="' . esc_attr__( 'Choose an image', 'radmanct-components' ) . '">';
	if ( ! $image ) { return ''; }
	$classes = 'rdct-im' . ( $s['float'] ? ' has-float' : '' ) . ( $s['animate_shadow'] ? ' has-shadow-motion' : '' ) . ( $s['label_overlay'] ? ' has-label-overlay' : '' );
	$motion = $s['parallax'] || $s['float'] || $s['animate_shadow'] || $s['reveal'] || $s['tilt'];
	$parallax = $s['parallax'] ? ' data-parallax="true" data-parallax-amount="' . $s['parallax_amount'] . '" data-parallax-direction="' . $s['parallax_direction'] . '" data-parallax-min-width="' . $s['parallax_min_width'] . '"' : '';
	if ( $s['parallax'] ) { $image = '<div class="rdct-im__parallax-frame"><div class="rdct-im__parallax-media">' . $image . '</div></div>'; }
	$html = '<div class="' . esc_attr( $classes ) . '" style="' . esc_attr( css( $s ) ) . '"' . ( $motion || $s['lightbox'] ? ' data-rdct-image' : '' ) . $parallax . ' data-reveal="' . ( $s['reveal'] ? esc_attr( $s['reveal_direction'] ) : '' ) . '" data-tilt="' . ( $s['tilt'] ? $s['stacking_factor'] : 0 ) . '"><div class="rdct-im__float"><div class="rdct-im__layout" data-side="' . esc_attr( $s['label_side'] ) . '" data-position="' . esc_attr( $s['label_position'] ) . '">';
	$label = $s['label_enabled'] && $s['label'] ? '<div class="rdct-im__label">' . wp_kses_post( $s['label'] ) . '</div>' : '';
	if ( ! $s['label_overlay'] ) { $html .= $label; }
	$html .= '<div class="rdct-im__tilt"><figure class="rdct-im__figure"><div class="rdct-im__visual">' . $image;
	if ( $s['overlay'] ) {
		$html .= '<span class="rdct-im__overlay" aria-hidden="true">';
		if ( $s['overlay_image_id'] && 'solid' === $s['overlay_background'] ) { $overlay_id = (int) apply_filters( 'wpml_object_id', $s['overlay_image_id'], 'attachment', true ); if ( wp_attachment_is_image( $overlay_id ) ) { $url = wp_get_attachment_image_url( $overlay_id, 'large' ); $html .= '<span class="rdct-im__overlay-image" style="' . esc_attr( 'background-image:url("' . addcslashes( esc_url_raw( $url ), '"\\' ) . '");background-size:' . $s['overlay_image_fit'] . ';background-repeat:' . $s['overlay_image_repeat'] . ';background-position:' . $s['overlay_position'] . ';' ) . '"></span>'; } }
		$html .= '</span>';
	}
	if ( $s['label_overlay'] ) { $html .= $label; }
	if ( $s['lines'] ) { $html .= '<span class="rdct-im__lines" aria-hidden="true">'; for ( $i = 0; $i < $s['lines_count']; ++$i ) { $html .= '<i></i>'; } $html .= '</span>'; }
	$url = $s['lightbox'] ? ( $s['url'] ?: ( $id ? wp_get_attachment_image_url( $id, 'full' ) : '' ) ) : $s['url'];
	if ( $url ) {
		$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		$html .= '<a class="rdct-im__link" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $alt ?: __( 'View image', 'radmanct-components' ) ) . '"';
		if ( $s['lightbox'] ) { $html .= ' data-rdct-image-lightbox="' . esc_attr( $s['lightbox_group'] ) . '" data-caption="' . esc_attr( wp_strip_all_tags( $s['label'] ) ) . '" data-close="' . esc_attr__( 'Close', 'radmanct-components' ) . '" data-next="' . esc_attr__( 'Next', 'radmanct-components' ) . '" data-previous="' . esc_attr__( 'Previous', 'radmanct-components' ) . '"'; }
		elseif ( $s['new_tab'] ) { $html .= ' target="_blank"'; }
		if ( $s['lightbox'] ) {
			$html .= ' data-loading="' . esc_attr__( 'Loading image...', 'radmanct-components' ) . '" data-error="' . esc_attr__( 'This image could not be loaded.', 'radmanct-components' ) . '" data-position="' . esc_attr__( 'Image %1$s of %2$s', 'radmanct-components' ) . '"';
		}
		$html .= ' rel="' . ( $s['new_tab'] ? 'noopener noreferrer ' : '' ) . ( $s['nofollow'] ? 'nofollow' : '' ) . '"></a>';
	}
	$html .= '</div>' . ( $s['reveal'] ? '<span class="rdct-im__revealer" aria-hidden="true"></span>' : '' ) . '</figure></div></div></div></div>';
	return $html;
}

function assets( array $input ): array {
	$s = settings( $input ); $styles = array( 'image' );
	foreach ( array( 'parallax' => 'parallax', 'reveal' => 'reveal', 'float' => 'float', 'tilt' => 'tilt', 'animate_shadow' => 'shadow' ) as $option => $feature ) { if ( $s[ $option ] ) { $styles[] = 'image-' . $feature; } }
	if ( $s['label_enabled'] && $s['label'] ) { $styles[] = 'image-label'; }
	if ( $s['lightbox'] ) { $styles[] = 'image-lightbox'; }
	return $styles;
}
